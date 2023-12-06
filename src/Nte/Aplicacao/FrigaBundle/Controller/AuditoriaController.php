<?php

/*
 * This file is part of  Friga - https://nte.ufsm.br/friga.
 * (c) Friga
 * Friga is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Friga is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Friga.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Nte\Aplicacao\FrigaBundle\Controller;

use Doctrine\ORM\EntityManager;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaArquivo;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaConvocacao;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEdital;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalEtapa;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalUsuario;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalUsuarioConvite;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricao;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricaoPontuacao;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricaoRecurso;
use Nte\Aplicacao\FrigaBundle\Entity\Log;
use Nte\UsuarioBundle\Entity\Usuario;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AuditoriaController extends Controller
{
    /**
     * @return RedirectResponse|Response|null
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function indexAction(Request $request, Usuario $usuario = null)
    {
        if (!$this->isGranted('ROLE_AUDITOR')) {
            return $this->redirectToRoute('nte_admin_painel_homepage');
        }
        $form = $this->createFormBuilder(null)
            ->add('cpf', TextType::class)
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var EntityManager $em */
            $em = $this->getDoctrine()->getManager();

            $cpf = $form->get('cpf')->getData();
            $uri = \str_replace('/app.php/', '', $request->server->get('REQUEST_URI'));
            $log = new Log();
            $log->setMetodo($request->getMethod())
                ->setMsg('AUDITORIA: BUSCA PELO CPF:' . $cpf)
                ->setDominio($request->server->get('HTTP_HOST'))
                ->setUri($uri)
                ->setIdUsuario($this->getUser())
                ->setInterface(1);

            $em->persist($log);
            $em->flush();

            $usuario = $em->getRepository(Usuario::class)->findOneBy(['cpf' => $cpf]);
            if ($usuario) {
                $log = new Log();
                $log->setMetodo($request->getMethod())
                    ->setContexto(Usuario::class)
                    ->setId($usuario->getId())
                    ->setOperacao(0)
                    ->setMsg("AUDITORIA: USUARIO ENCONTRADO|{id:{$usuario->getId()}, nome:\"{$usuario->getNome()}\"}")
                    ->setDominio($request->server->get('HTTP_HOST'))
                    ->setUri($uri)
                    ->setIdUsuario($this->getUser())
                    ->setInterface(1);
                $em->persist($log);
                $em->flush();
            }
        }

        return $this->render('@NteAplicacaoFriga/Auditoria/index-pessoal.html.twig', [
            'form' => $form->createView(),
            'usuario' => $usuario,
        ]);
    }

    /**
     * @return Response|null
     */
    public function indexUsuarioAction(Request $request)
    {
        if (!$this->isGranted('ROLE_AUDITOR')) {
            return $this->redirectToRoute('nte_admin_painel_homepage');
        }

        return $this->render('@NteAplicacaoFriga/Auditoria/index-usuario.html.twig', [
        ]);
    }

    /**
     * @return Response|null
     */
    public function indexLogAction(Request $request)
    {
        if (!$this->isGranted('ROLE_AUDITOR')) {
            return $this->redirectToRoute('nte_admin_painel_homepage');
        }

        return $this->render('@NteAplicacaoFriga/Auditoria/index-log.html.twig', [
            'dt0' => new \DateTime(),
            'dt1' => new \DateTime(),
        ]);
    }

    /**
     * @return JsonResponse
     *
     * @throws \Exception
     */
    public function getUsuarioIndexAction(Request $request)
    {
        if (!$this->isGranted('ROLE_AUDITOR')) {
            return $this->json([]);
        }
        $papel = $request->query->get('papel') ?? -1;
        $funcao = $request->query->get('funcao') ?? -1;
        $edital = $request->query->get('edital') ?? -1;
        $dt0 = $request->query->get('dt0') ?? -1;
        $dt1 = $request->query->get('dt1') ?? -1;

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQueryBuilder()
            ->select('u')
            ->from(Usuario::class, 'u')
            ->orderBy('u.nome', 'asc');

        if ($request->query->get('search')['value']) {
            $str = '%' . \str_replace(' ', '%', $request->query->get('search')['value']) . '%';
            $query->andWhere('(u.nome like :pesquisa or u.username like :pesquisa  or u.cpf like :pesquisa  or u.email like :pesquisa )');
            $query->setParameter('pesquisa', $str);
        }
        if (-1 != $edital and -1 == $funcao) {
            $query->leftJoin('u.idEditalUsuario', 'eu')
                ->leftJoin('u.idEditalUsuarioConvite', 'euc')
                ->leftJoin('u.inscricao', 'ui')
            ;
            if (-2 == $edital) {
                $query->andWhere('(eu.idEdital is null and euc.idEdital is null and ui.idEdital is null  )');
            } else {
                $query->andWhere('(eu.idEdital = :edital or euc.idEdital = :edital or (ui.idEdital = :edital and ui.idSituacao != -999) )')
                    ->setParameter('edital', $edital);
            }
        }
        if (-1 != $funcao) {
            switch ($funcao) {
                case 1:
                    $query->innerJoin('u.inscricao', 'ui')
                    ->andWhere('ui.idSituacao != -999');
                    break;
                case 2:
                    $query->innerJoin('u.idEditalUsuario', 'eu')
                        ->andWhere('eu.administrador = 1');
                    break;
                case 3:
                    $query->innerJoin('u.idEditalUsuario', 'eu')
                        ->andWhere('eu.avaliador = 1');
                    break;
                case 4:
                    $query->innerJoin('u.idEditalUsuario', 'eu')
                        ->andWhere('eu.resultado = 1');
                    break;
                case 5:
                    $query->innerJoin('u.idEditalUsuario', 'eu')
                        ->andWhere('eu.convocacao = 1');
                    break;
            }
            if (1 == $funcao and -1 != $edital) {
                $query->andWhere('ui.idEdital = :edital')
                    ->setParameter('edital', $edital);
            }
            if (1 != $funcao and -1 != $edital) {
                $query->andWhere('eu.idEdital = :edital')
                    ->setParameter('edital', $edital);
            }
        }

        if (-1 != $papel) {
            $query->andWhere('u.roles like :papel')
                ->setParameter('papel', '%' . $papel . '%');
        }

        if (-1 != $dt0 and -1 != $dt1
            and '' != $dt0 and '' != $dt1) {
            $dt0 = new \DateTime($request->query->get('dt0'));
            $dt1 = new \DateTime($request->query->get('dt1'));
            $dt0->setTime(0, 0, 0);
            $dt1->setTime(23, 59, 59);

            $query->andWhere('u.lastLogin between :dt0 and :dt1')
                ->setParameter('dt0', $dt0)
                ->setParameter('dt1', $dt1);
        }

        $ordem = $request->query->get('order')[0]['dir'];
        $pageSize = $request->query->get('length');
        $paginator = new \Doctrine\ORM\Tools\Pagination\Paginator($query);
        $totalItems = \count($paginator);
        $pagesCount = \ceil($totalItems / $pageSize);

        $paginator->getQuery()
            ->setFirstResult($request->query->get('start'))
            ->setMaxResults($pageSize);

        $obj = new \stdClass();

        $obj = new \stdClass();
        $obj->iTotalRecords = $totalItems;
        $obj->iTotalDisplayRecords = $totalItems;
        $obj->sColumns = '';
        $obj->sEcho = '';
        $obj->aaData = [];

        /** @var Usuario $item */
        foreach ($paginator as $item) {
            $editais = [];
            if ($item->getInscicaoValida()->count()) {
                /** @var FrigaInscricao $subitem */
                foreach ($item->getInscicaoValida() as $subitem) {
                    if (-1 != $edital and $subitem->getIdEdital()->getId() != $edital) {
                        continue;
                    }
                    if (-1 != $funcao and $funcao > 1) {
                        continue;
                    }
                    $obja = new \stdClass();
                    $obja->id = $subitem->getIdEdital()->getId();
                    $obja->titulo = $subitem->getIdEdital()->getNumero() . ' - ' . $subitem->getIdEdital()->getTitulo();
                    $obja->contexto = 'CANDIDATO';
                    $obja->f0 = null;
                    $obja->f1 = null;
                    $obja->f2 = null;
                    $obja->f3 = null;
                    $editais[] = $obja;
                }
            }
            if ($item->getIdEditalUsuario()->count()) {
                /** @var FrigaEditalUsuario $subitem */
                foreach ($item->getIdEditalUsuario() as $subitem) {
                    if (-1 != $edital and $subitem->getIdEdital()->getId() != $edital) {
                        continue;
                    }
                    if (-1 != $funcao and 1 == $funcao) {
                        continue;
                    }
                    $obja = new \stdClass();
                    $obja->id = $subitem->getIdEdital()->getId();
                    $obja->titulo = $subitem->getIdEdital()->getNumero() . ' - ' . $subitem->getIdEdital()->getTitulo();
                    $obja->contexto = 'BANCA';
                    $obja->f0 = $subitem->isAdministrador();
                    $obja->f1 = $subitem->isAvaliador();
                    $obja->f2 = $subitem->isResultado();
                    $obja->f3 = $subitem->isConvocacao();
                    $editais[] = $obja;
                }
            }
            $obj0 = new \stdClass();
            $obj0->id = $item->getId();
            $obj0->cpf = $item->getCpf();
            $obj0->nome = \strtoupper($item->getNome());
            $obj0->papel = $item->getRolesExtenco();
            $obj0->edital = $editais;
            $obj0->img = $this->generateUrl('arquivo_download', ['id' => $item->getImg()]);
            $obj0->dt = \is_null($item->getLastLogin()) ? 'Nunca acessou' : $item->getLastLogin()->format('d/m/Y H:i:s');
            $obj->aaData[] = $obj0;
        }

        return $this->json($obj);
    }

    /**
     * @return JsonResponse
     *
     * @throws \Exception
     */
    public function getLogIndexAction(Request $request)
    {
        if (!$this->isGranted('ROLE_AUDITOR')) {
            return $this->json([]);
        }
        $interface = $request->query->get('interface') ?? -1;
        $contexto = $request->query->get('ctx') ?? -1;
        $op = $request->query->get('op') ?? -1;
        $usuario = $request->query->get('usuario') ?? -1;
        $dt0 = new \DateTime($request->query->get('dt0'));
        $dt1 = new \DateTime($request->query->get('dt1'));
        $dt0->setTime(0, 0, 0);
        $dt1->setTime(23, 59, 59);

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQueryBuilder()
            ->select('l')
            ->from(Log::class, 'l')
            ->where('l.registroDataCriacao between :dt0 and :dt1')
            ->setParameter('dt0', $dt0)
            ->setParameter('dt1', $dt1)
            ->orderBy('l.id', 'desc');
        if (-1 == $interface) {
            $query->andWhere('l.interface in (0,1,2)');
        } else {
            $query->andWhere('l.interface = :interface')
                ->setParameter('interface', $interface);
        }
        if (-1 != $usuario) {
            $query->andWhere('l.idUsuario = :u')
                ->setParameter('u', $usuario);
        }
        if (-1 != $contexto) {
            switch ($contexto) {
                case 'candidato':
                    $query->andWhere("l.uri like '/candidato/%'");
                    break;
                case 'auditoria':
                    $query->andWhere("l.uri like '/app/auditoria/%'");
                    break;
                case 'colaborador-geral':
                    $query->andWhere("l.uri like '/app/%'");
                    break;
                case 'colaborador-edital':
                    $query->andWhere("l.uri like '/app/edital/%'");
                    break;
                case 'colaborador-avaliacao':
                    $str = " (l.uri like '/app/avaliacao/%') ";
                    $str .= " or (l.uri like '/app/convocacao/%') ";
                    $str .= " or (l.uri like '/app/resultado/%') ";
                    $str .= " or (l.uri like '/app/usuario/perfil%') ";
                    $str .= " or (l.uri like '/app/relatorio/%') ";
                    $query->andWhere($str);
                    break;
            }
        }
        if (-1 != $op) {
            $query->andWhere('l.metodo = :op')
                ->setParameter('op', $op);
        }

        $ordem = $request->query->get('order')[0]['dir'];
        $pageSize = $request->query->get('length');
        $paginator = new \Doctrine\ORM\Tools\Pagination\Paginator($query);
        $totalItems = \count($paginator);
        $pagesCount = \ceil($totalItems / $pageSize);

        $paginator->getQuery()
            ->setFirstResult($request->query->get('start'))
            ->setMaxResults($pageSize);

        $obj = new \stdClass();
        $obj->iTotalRecords = $totalItems;
        $obj->iTotalDisplayRecords = $totalItems;
        $obj->sColumns = '';
        $obj->sEcho = '';
        $obj->aaData = [];

        /** @var Log $item */
        foreach ($paginator as $item) {
            $obj->aaData[] = $item->getStd();
        }

        return $this->json($obj);
    }

    /**
     * @return Response|null
     */
    public function resumoInscricaoAction(Request $request, FrigaInscricao $inscricao, $contexto)
    {
        if (!$this->isGranted('ROLE_AUDITOR')) {
            return $this->redirectToRoute('nte_admin_painel_homepage');
        }

        return $this->render('@NteAplicacaoFriga/Auditoria/resumo-inscricao.html.twig', [
            'inscricao' => $inscricao,
            'contexto' => $contexto,
        ]);
    }

    /**
     * @return Response|null
     */
    public function indexEditalAction(Request $request)
    {
        if (!$this->isGranted('ROLE_AUDITOR')) {
            return $this->redirectToRoute('nte_admin_painel_homepage');
        }
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $editais = $em->createQueryBuilder()
            ->select('e')
            ->from(FrigaEdital::class, 'e')
            ->where('e.situacao != -1')
            ->getQuery()->getResult();

        return $this->render('@NteAplicacaoFriga/Auditoria/index-edital.html.twig', [
            'editais' => $editais,
        ]);
    }

    public function indexEditalUsuarioAction(Request $request, FrigaEdital $edital)
    {
        if (!$this->isGranted('ROLE_AUDITOR')) {
            return $this->redirectToRoute('nte_admin_painel_homepage');
        }

        return $this->render('@NteAplicacaoFriga/Auditoria/index-edital-usuario.html.twig', [
            'edital' => $edital,
        ]);
    }

    public function indexEditalInscricaoAction(Request $request, FrigaEdital $edital)
    {
        if (!$this->isGranted('ROLE_AUDITOR')) {
            return $this->redirectToRoute('nte_admin_painel_homepage');
        }

        return $this->render('@NteAplicacaoFriga/Auditoria/index-edital-inscricao.html.twig', [
            'edital' => $edital,
        ]);
    }

    public function indexEditalRecursoAction(Request $request, FrigaEdital $edital)
    {
        if (!$this->isGranted('ROLE_AUDITOR')) {
            return $this->redirectToRoute('nte_admin_painel_homepage');
        }

        return $this->render('@NteAplicacaoFriga/Auditoria/index-edital-recurso.html.twig', [
            'edital' => $edital,
        ]);
    }

    public function indexEditalArquivoAction(Request $request, FrigaEdital $edital)
    {
        if (!$this->isGranted('ROLE_AUDITOR')) {
            return $this->redirectToRoute('nte_admin_painel_homepage');
        }

        return $this->render('@NteAplicacaoFriga/Auditoria/index-edital-arquivo.html.twig', [
            'edital' => $edital,
        ]);
    }

    public function indexEditalLogsAction(Request $request, FrigaEdital $edital)
    {
        if (!$this->isGranted('ROLE_AUDITOR')) {
            return $this->redirectToRoute('nte_admin_painel_homepage');
        }
        $logs = $this->getLog($edital);

        return $this->render('@NteAplicacaoFriga/Auditoria/index-edital-log.html.twig', [
            'edital' => $edital,
            'logs' => $logs,
        ]);
    }

    /**
     * @return BinaryFileResponse|RedirectResponse
     */
    public function logsCsvAction(Request $request, FrigaEdital $edital)
    {
        if (!$this->isGranted('ROLE_AUDITOR')) {
            return $this->redirectToRoute('nte_admin_painel_homepage');
        }
        $cabecalho = [
            0 => 'ID',
            1 => 'DATA',
            2 => 'USUARIO',
            3 => 'USUARIO_CPF',
            4 => 'OPERACAO',
            5 => 'METODO',
            6 => 'URI',
            7 => 'CONTEXTO_PRIMARIO',
            8 => 'CONTEXTO_SECUNDARIO',
            9 => 'MSG',
            10 => 'USUARIO_AFETADO_NOME',
            11 => 'USUARIO_AFETADO_CPF',
        ];
        $arquivo = '/tmp/logs-edital-' . $edital->getUuid() . '.csv';
        if (\is_file($arquivo)) {
            \unlink($arquivo);
        }
        $out = \fopen($arquivo, 'w');
        \fputcsv($out, $cabecalho);
        foreach ($this->getLog($edital) as $log) {
            $linha = [
                0 => $log->id,
                1 => $log->data->format('Y-m-d H:i:s'),
                2 => \strtoupper($log->idUsuario->getNome()),
                3 => $log->idUsuario->getCpf(),
                4 => $log->operacao,
                5 => $log->item->getMetodo(),
                6 => $log->item->getUri(),
                7 => $log->contexto,
                8 => $log->contextoSecundario,
                9 => $log->msg,
                10 => \is_null($log->idUsuario2) ? '' : \strtoupper($log->idUsuario2->getNome()),
                11 => \is_null($log->idUsuario2) ? '' : $log->idUsuario2->getCpf(),
            ];
            \fputcsv($out, $linha);
        }
        \fclose($out);

        return $this->file($arquivo);
    }

    public function getLog(FrigaEdital $edital)
    {
        $inscricoes = [];
        $recursos = [];
        $etapas = [];
        $arquivos = [];
        $convocacoes = [];
        $pontuacoes = [];
        $categorias = [];
        $convites = [];

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $urlConfiguracaoEdital = [
            '/app/edital/remover/{uuid}', '/app/edital/{uuid}/exportador', '/app/edital/{uuid}/exportar/csv/logs', '/app/edital/{uuid}/importar', '/app/edital/{uuid}/config', '/app/edital/{uuid}/termo', '/app/edital/{uuid}/inscricoes', '/app/edital/{uuid}/resultados', '/app/edital/{uuid}/arquivo', '/app/edital/{uuid}/avaliador', '/app/edital/{uuid}/api/avaliador/lista', '/app/edital/{uuid}/avaliador/criar', '/app/edital/{uuid}/avaliador/convidar', '/app/edital/{uuid}/avaliador/convidar/{convite}', '/app/edital/{uuid}/avaliador/convidar/remover/{convite}', '/app/edital/{uuid}/avaliador/convidar/enviar/{convite}', '/app/edital/{uuid}/avaliador/editar/{avaliador}', '/app/edital/{uuid}/avaliador/remover/{avaliador}', '/app/edital/{uuid}/avaliador/adicionar/{usuario}/cargo', '/app/edital/{uuid}/etapa', '/app/edital/{uuid}/etapa/novo/{tipo}', '/app/edital/{uuid}/etapa/{etapa}/editar', '/app/edital/{uuid}/etapa/{etapa}/remover', '/app/edital/{uuid}/etapa-categoria/criar', '/app/edital/{uuid}/etapa-categiria/{categoria}/editar', '/app/edital/{uuid}/etapa-categoria/{categoria}/remover', '/app/edital/{uuid}/pontuacao', '/app/edital/{uuid}/pontuacao/novo', '/app/edital/{uuid}/pontuacao/{pontuacao}/editar', '/app/edital/{uuid}/pontuacao/{pontuacao}/remover', '/app/edital/{uuid}/pontuacao-categoria/novo', '/app/edital/{uuid}/pontuacao-categoria/{pontuacaoCategoria}/editar', '/app/edital/{uuid}/pontuacao-categoria/{pontuacaoCategoria}/remover', '/app/edital/{uuid}/pontuacao-categoria-peso/novo', '/app/edital/{uuid}/pontuacao-categoria-peso/{pontuacaoCategoria}/editar', '/app/edital/{uuid}/cargo/', '/app/edital/{uuid}/cargo/novo', '/app/edital/{uuid}/cargo/{cargo}/editar', '/app/edital/{uuid}/cargo/{cargo}/remover', '/app/edital/{uuid}/cota/', '/app/edital/{uuid}/cota/novo', '/app/edital/{uuid}/cota/{cota}/editar', '/app/edital/{uuid}/cota/{cota}/remover', '/app/edital/{uuid}/desempate/', '/app/edital/{uuid}/desempate/ordem/{criterio}/{tipo}', '/app/edital/{uuid}/desempate/novo/{tipo}', '/app/edital/{uuid}/desempate/{criterio}/editar', '/app/edital/{uuid}/desempate/{criterio}/remover', '/app/edital/{uuid}/logs',
        ];
        $urlAvalicao = [
            '/app/avaliacao/etapa/{etapa}', '/app/avaliacao/etapa/{etapa}/exportar/csv', '/app/resultado/parcial/{etapa}', '/app/resultado/etapa/{etapa}/', '/app/resultado/etapa/{etapa}/confirmar-posicao', '/app/resultado/etapa/{etapa}/remover', '/app/resultado/etapa/{etapa}/gerar', '/app/resultado/etapa/{etapa}/form', '/app/resultado/etapa/{etapa}/impressao', '/app/resultado/etapa/{etapa}/exportar/csv', '/app/convocacao/etapa/{etapa}', '/app/convocacao/etapa/{etapa}/impressao/relacao', '/app/convocacao/etapa/{etapa}/impressao/relacao-contato', '/app/convocacao/etapa/{etapa}/impressao/presenca', '/app/convocacao/etapa/{etapa}/exportar/csv', '/app/convocacao/etapa/{etapa}/exportar/moodle',
        ];
        $urlAvalicaoCandidato = [
            '/app/avaliacao/etapa/{etapa}/inscricao/{uuid}', '/app/convocacao/etapa/{etapa}/inscricao/{uuid}', '/app/relatorio/inscricao-perfil/{uuid}',
        ];
        $urlArquivo = [
            '/app/arquivo/visualizar/{id}',
        ];
        $urlResultadoAjuste = [
            '/app/resultado/etapa/{etapa}/posicao/{uuid}',
        ];
        $urlCandidato = [
            '/candidato/cancelar/{uuid}', '/candidato/inscricacao-concluida/{uuid}', '/candidato/inscricacao-realizada/{uuid}', '/candidato/inscricao-projeto/{uuid}',
        ];
        $urlCandidatoEtapa = [
            '/candidato/recursos/{etapa}/formulario/{uuid}',
        ];
        $param = [
            '/candidato/inscricacao/' . $edital->getUuid(), '/app/relatorio/resumo/' . $edital->getUuid(), '/app/relatorio/andamento/' . $edital->getUuid(), '/app/relatorio/convocacao/' . $edital->getUuid(), '/app/relatorio/recurso/' . $edital->getUuid(), '/app/relatorio/inscritos/' . $edital->getUuid(),
        ];
        /** @var FrigaInscricao $item */
        foreach ($edital->getInscricao() as $item) {
            $inscricoes[$item->getUuid()] = $item;
            /** @var FrigaArquivo $subitem */
            foreach ($item->getIdArquivo() as $subitem) {
                $arquivos[$subitem->getId()] = $subitem;
            }
            /** @var FrigaInscricaoPontuacao $subitem */
            foreach ($item->getPontuacao() as $subitem) {
                /** @var FrigaArquivo $subsubitem */
                foreach ($subitem->getIdArquivo() as $subsubitem) {
                    $arquivos[$subsubitem->getId()] = $subsubitem;
                }
                $pt = $subitem->getIdEditalPontuacao();
                $cat = $pt->getIdCategoria();
                $categorias[$cat->getId()] = $cat;
                $pontuacoes[$pt->getId()] = $pt;
            }
        }

        /** @var FrigaEditalEtapa $item */
        foreach ($edital->getEtapa() as $item) {
            $etapas[$item->getId()] = $item;

            /** @var FrigaConvocacao $subitem */
            foreach ($item->getConvocacao() as $subitem) {
                $convocacoes[$subitem->getId()] = $subitem;
            }
        }

        /** @var FrigaInscricaoRecurso $item */
        foreach ($edital->getRecursos() as $item) {
            $recursos[$item->getUuid()] = $item;
            $param[] = \str_replace('{uuid}', $item->getUuid(), '/candidato/recursos/{uuid}/ver/');
        }
        /** @var FrigaEditalUsuarioConvite $item */
        foreach ($edital->getIdEditalUsuarioConvite() as $item) {
            $convite[$item->getId()] = $item;
        }

        foreach ($urlConfiguracaoEdital as $url) {
            $param[] = \str_replace('{uuid}', $edital->getUuid(), $url);
        }

        /**
         * @var $id
         * @var $arquivo
         */
        foreach ($arquivos as $id => $arquivo) {
            foreach ($urlArquivo as $url) {
                $param[] = \str_replace('{id}', $id, $url);
            }
        }
        foreach ($etapas as $id => $etapa) {
            foreach ($urlAvalicao as $url) {
                $param[] = \str_replace('{etapa}', $id, $url);
            }
            foreach ($inscricoes as $uiid => $inscricao) {
                foreach ($urlAvalicaoCandidato as $url) {
                    $url = \str_replace('{etapa}', $id, $url);
                    $url = \str_replace('{uuid}', $uiid, $url);
                    $param[] = $url;
                }
                foreach ($urlCandidato as $url) {
                    $url = \str_replace('{uuid}', $uiid, $url);
                    $param[] = $url;
                }
                foreach ($urlCandidatoEtapa as $url) {
                    $url = \str_replace('{etapa}', $id, $url);
                    $url = \str_replace('{uuid}', $uiid, $url);
                    $param[] = $url;
                }
            }
        }

        $aux = new \stdClass();
        $aux->etapa = $etapas;
        $aux->inscricao = $inscricoes;
        $aux->recurso = $recursos;
        $aux->arquivo = $arquivos;
        $aux->pontuacao = $pontuacoes;
        $aux->categoria = $categorias;
        $aux->convocacao = $convocacoes;
        $aux->convite = $convites;
        $aux->edital = $edital;

        $logs = $em->createQueryBuilder()
            ->select('l')
            ->from(Log::class, 'l')
            ->Where('l.uri in (:uri)')
            ->setParameter('uri', $param)
            ->getQuery()
            ->getResult();

        $tmp = [];

        /** @var Log $log */
        foreach ($logs as $log) {
            $ctx = $log->getCtxAux($aux);

            $obj = new \stdClass();
            $obj->id = $log->getId();
            $obj->data = $log->getRegistroDataCriacao();
            $obj->idUsuario = $log->getIdUsuario();
            $obj->idUsuario2 = $ctx->usuario;
            $obj->msg = $ctx->msg;
            $obj->contexto = $ctx->contexto;
            $obj->contextoSecundario = $ctx->contextoSecundario;
            $obj->usuario = \is_null($ctx->usuario) ? '-' : $ctx->usuario->getNome();
            $obj->operacao = $ctx->operacao;
            $obj->item = $log;
            $tmp[] = $obj;
        }

        return $tmp;
    }

    /**
     * @return BinaryFileResponse
     */
    public function usuarioCsvAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $usuarios = $em->createQueryBuilder()
            ->select('u')
            ->from(Usuario::class, 'u')
            ->orderBy('u.nome', 'asc')
            ->getQuery()->getResult();

        $editais = [];
        /** @var Usuario $item */
        foreach ($usuarios as $item) {
            $editais[$item->getId()] = [];

            if ($item->getInscicaoValida()->count()) {
                /** @var FrigaInscricao $subitem */
                foreach ($item->getInscicaoValida() as $subitem) {
                    $obja = new \stdClass();
                    $obja->uuid = $subitem->getIdEdital()->getUuid();
                    $obja->titulo = $subitem->getIdEdital()->getNumero() . ' - ' . $subitem->getIdEdital()->getTitulo();
                    $obja->contexto = 'CANDIDATO';
                    $obja->ff = 'SIM';
                    $obja->f0 = 'NÃO';
                    $obja->f1 = 'NÃO';
                    $obja->f2 = 'NÃO';
                    $obja->f3 = 'NÃO';
                    $editais[$item->getId()][] = $obja;
                }
            }
            if ($item->getIdEditalUsuario()->count()) {
                /** @var FrigaEditalUsuario $subitem */
                foreach ($item->getIdEditalUsuario() as $subitem) {
                    $obja = new \stdClass();
                    $obja->uuid = $subitem->getIdEdital()->getUuid();
                    $obja->titulo = $subitem->getIdEdital()->getNumero() . ' - ' . $subitem->getIdEdital()->getTitulo();
                    $obja->ff = 'NÃO';
                    $obja->f0 = $subitem->isAdministrador() ? 'SIM' : 'NÃO';
                    $obja->f1 = $subitem->isAvaliador() ? 'SIM' : 'NÃO';
                    $obja->f2 = $subitem->isResultado() ? 'SIM' : 'NÃO';
                    $obja->f3 = $subitem->isConvocacao() ? 'SIM' : 'NÃO';
                    $editais[$item->getId()][] = $obja;
                }
            }
        }
        $cabecalho = [
            0 => 'ID',
            1 => 'NOME',
            2 => 'USUARIO',
            3 => 'EMAIL',
            4 => 'CPF',
            5 => 'ULTIMO_LOGIN',
            6 => 'EDITAL_UUID',
            7 => 'EDITAL_TITULO',
            8 => 'EDITAL_CANDIDATO',
            9 => 'EDITAL_BANCA_ADMINISTRADOR',
            10 => 'EDITAL_BANCA_AVALIACAO',
            11 => 'EDITAL_BANCA_RESULTADO',
            12 => 'EDITAL_BANCA_CONVOCACAO',
        ];

        $arquivo = '/tmp/usuario-edital.csv';
        if (\is_file($arquivo)) {
            \unlink($arquivo);
        }
        $out = \fopen($arquivo, 'w');
        \fputcsv($out, $cabecalho);
        /** @var Usuario $item */
        foreach ($usuarios as $item) {
            if (\count($editais[$item->getId()])) {
                /** @var \stdClass $subitem */
                foreach ($editais[$item->getId()] as $subitem) {
                    $linha = [
                        0 => $item->getId(),
                        1 => \strtoupper($item->getNome()),
                        2 => $item->getUsername(),
                        3 => $item->getEmail(),
                        4 => $item->getCpf(),
                        5 => \is_null($item->getLastLogin()) ? 'Nunca Acessou' : $item->getLastLogin()->format('Y-m-d H:i:s'),
                        6 => $subitem->uuid,
                        7 => $subitem->titulo,
                        8 => $subitem->ff,
                        9 => $subitem->f0,
                        10 => $subitem->f1,
                        11 => $subitem->f2,
                        12 => $subitem->f3,
                    ];
                    \fputcsv($out, $linha);
                }
            } else {
                $linha = [
                    0 => $item->getId(),
                    1 => \strtoupper($item->getNome()),
                    2 => $item->getUsername(),
                    3 => $item->getEmail(),
                    4 => $item->getCpf(),
                    5 => \is_null($item->getLastLogin()) ? 'Nunca Acessou' : $item->getLastLogin()->format('Y-m-d H:i:s'),
                    6 => '',
                    7 => '',
                    8 => '',
                    9 => '',
                    10 => '',
                    11 => '',
                    12 => '',
                ];
                \fputcsv($out, $linha);
            }
        }
        \fclose($out);

        return $this->file($arquivo);
    }

    /**
     * @return BinaryFileResponse|RedirectResponse
     */
    public function usuarioCsv2Action(Request $request)
    {
        if (!$this->isGranted('ROLE_AUDITOR')) {
            return $this->redirectToRoute('nte_admin_painel_homepage');
        }
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $usuarios = $em->createQueryBuilder()
            ->select('u')
            ->from(Usuario::class, 'u')
            ->orderBy('u.nome', 'asc')
            ->getQuery()->getResult();

        $cabecalho = [
            0 => 'ID',
            1 => 'NOME',
            2 => 'USUARIO',
            3 => 'EMAIL',
            4 => 'CPF',
            5 => 'ULTIMO_LOGIN',
            13 => 'PAPEL_EDITAL_RELATORIO_GERENCIAL',
            14 => 'PAPEL_ADMINISTRADOR',
            15 => 'PAPEL_ADMINISTRADOR_CONTA_USUARIO',
            16 => 'PAPEL_EDITAL_ADMINISTRADOR_EDITAL',
            17 => 'PAPEL_EDITAL_DOWNLOAD_ARQUIVO',
            18 => 'PAPEL_SUPORTE_TECNICO',
            19 => 'PAPEL_EDITAL_AVALIADOR',
            20 => 'PAPEL_AUDITOR',
            21 => 'PAPEL_COMUM',
        ];

        $arquivo = '/tmp/usuario-papel.csv';
        if (\is_file($arquivo)) {
            \unlink($arquivo);
        }
        $out = \fopen($arquivo, 'w');
        \fputcsv($out, $cabecalho);
        /** @var Usuario $item */
        foreach ($usuarios as $item) {
            $linha = [
                0 => $item->getId(),
                1 => \strtoupper($item->getNome()),
                2 => $item->getUsername(),
                3 => $item->getEmail(),
                4 => $item->getCpf(),
                5 => \is_null($item->getLastLogin()) ? 'Nunca Acessou' : $item->getLastLogin()->format('Y-m-d H:i:s'),
                13 => $item->hasRole('ROLE_GERENCIAL') ? 'SIM' : 'NÃO',
                14 => $item->hasRole('ROLE_ADMIN') ? 'SIM' : 'NÃO',
                15 => $item->hasRole('ROLE_ADMIN_USER') ? 'SIM' : 'NÃO',
                16 => $item->hasRole('ROLE_ADMIN_EDITAL') ? 'SIM' : 'NÃO',
                17 => $item->hasRole('ROLE_ADMIN_ARQUIVO') ? 'SIM' : 'NÃO',
                18 => $item->hasRole('ROLE_SUPORTE') ? 'SIM' : 'NÃO',
                19 => $item->hasRole('ROLE_AVALIADOR') ? 'SIM' : 'NÃO',
                20 => $item->hasRole('ROLE_AUDITOR') ? 'SIM' : 'NÃO',
                21 => $item->hasRole('ROLE_USER') ? 'SIM' : 'NÃO',
            ];
            \fputcsv($out, $linha);
        }
        \fclose($out);

        return $this->file($arquivo);
    }

    /**
     * @return JsonResponse
     */
    public function apiListaUsuarioAction(Request $request)
    {
        if (!$this->isGranted('ROLE_AUDITOR')) {
            return $this->json([]);
        }
        $str = '%' . \str_replace(' ', '%', $request->request->get('termo')) . '%';

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQueryBuilder()
            ->select('u')
            ->from(Usuario::class, 'u')
            ->andWhere('(u.nome like :pesquisa or u.email like :pesquisa  or u.cpf like :pesquisa or u.username like :pesquisa )')
            ->setParameter('pesquisa', $str)
            ->setMaxResults(15)
            ->orderBy('u.nome', 'asc');

        return $this->json(\array_map(function($a) {
            /** @var Usuario $a */
            return ['id' => $a->getId(), 'text' => $a->getCpf() . ' - ' . $a->getNome()];
        }, $query->getQuery()->getResult()));
    }

    /**
     * @return JsonResponse
     */
    public function apiListaEditalAction(Request $request)
    {
        if (!$this->isGranted('ROLE_AUDITOR')) {
            return $this->json([]);
        }
        $str = '%' . \str_replace(' ', '%', $request->request->get('termo')) . '%';

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQueryBuilder()
            ->select('e')
            ->from(FrigaEdital::class, 'e')
            ->andWhere('(e.titulo like :pesquisa or e.url like :pesquisa or e.numero like :pesquisa )')
            ->andWhere('e.situacao != -1')
            ->setParameter('pesquisa', $str)
            //->setMaxResults(15)
            ->orderBy('e.id', 'desc');

        $tmp = [
            ['id' => -1, 'text' => '-- Edital --'],
            ['id' => -2, 'text' => 'SEM VINCULO COM EDITAL'],
        ];

        /** @var FrigaEdital $item */
        foreach ($query->getQuery()->getResult() as $item) {
            $tmp[] = ['id' => $item->getId(), 'text' => $item->getNumero() . ' - ' . $item->getTitulo()];
        }

        return $this->json($tmp);
    }
}
