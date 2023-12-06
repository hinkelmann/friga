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

namespace Nte\UsuarioBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaArquivo;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEdital;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalUsuario;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricao;
use Nte\UsuarioBundle\Entity\Impedimento;
use Nte\UsuarioBundle\Entity\ImpedimentoDeclaracao;
use Nte\UsuarioBundle\Entity\Usuario;
use Nte\UsuarioBundle\Form\DeclaracaoType;
use Nte\UsuarioBundle\Form\PrimeiroAcessoType;
use Nte\UsuarioBundle\Form\UsuarioComumType;
use Nte\UsuarioBundle\Form\UsuarioType;
use setasign\Fpdi\Tcpdf\Fpdi;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UsuarioController extends Controller
{
    use AssinaturaPDF;

    /**
     * Index dos usuários.
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->json([]);
        }

        return $this->render('NteUsuarioBundle:Usuario:index.html.twig', [
        ]);
    }

    /**
     * @return JsonResponse
     *
     * @throws \Exception
     */
    public function apiIndexAction(Request $request)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->json([]);
        }
        $papel = $request->query->get('papel') ?? -1;
        $funcao = $request->query->get('funcao') ?? -1;
        $escolaridade = $request->query->get('edital') ?? -1;
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
                ->leftJoin('u.inscricao', 'ui');
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
            }
            if ($item->getIdEditalUsuario()->count()) {
            }
            $obj0 = new \stdClass();
            $obj0->id = $item->getId();
            $obj0->cpf = $item->getCpf();
            $obj0->username = $item->getUsername();
            $obj0->email = $item->getEmail();
            $obj0->telefone1 = $item->getContatoTelefone1();
            $obj0->telefone2 = $item->getContatoTelefone2();
            $obj0->profissao = $item->getProfissao();
            $obj0->escolaridade = $item->getEscolaridade();
            $obj0->nome = \strtoupper($item->getNome());
            $obj0->ativo = $item->isEnabled();
            $obj0->papel = $item->getRolesExtenco();
            $obj0->banca = $item->getIdEditalUsuario()->count();
            $obj0->inscricao = $item->getInscicaoValida()->count();
            $obj0->img = $this->generateUrl('arquivo_download', ['id' => $item->getImg()]);
            $obj0->dt = \is_null($item->getLastLogin()) ? 'Nunca acessou' : $item->getLastLogin()->format('d/m/Y H:i:s');
            $obj->aaData[] = $obj0;
        }

        return $this->json($obj);
    }

    /**
     * Listar usuários.
     *
     * @return Response
     */
    public function listaAction()
    {
        $em = $this->getDoctrine()->getManager();
        $u = $em->getRepository(Usuario::class)
            ->findby(['enabled' => 1], ['lastLogin' => 'desc'], 15);

        return $this->render('NteUsuarioBundle:Usuario:lista.html.twig', ['usuarios' => $u]);
    }

    /**
     * Visualizar Usuário.
     *
     * @return Response
     */
    public function perfilAction(Usuario $usuario)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $usuario = $this->getUser();
        }

        return $this->render('@NteUsuario/Usuario/perfil.html.twig', [
            'usuario' => $usuario,
        ]);
    }

    /**
     * Ativar/desativar Usuário.
     *
     * @return JsonResponse
     */
    public function ativarAction(Request $request, Usuario $usuario)
    {
        $obj = new \stdClass();
        $obj->error = false;
        $obj->msg = 'Alteração realizada com sucesso!';
        if ($this->isGranted('ROLE_ADMIN_USER')) {
            $usuario->setEnabled(!$usuario->isEnabled());
            $em = $this->getDoctrine()->getManager();
            $em->persist($usuario);
            $em->flush();
        } else {
            $obj->msg = 'Você não possui permissões para alterar a ativar/desativar usuário';
            $obj->error = true;
        }

        return $this->json($obj);
    }

    /**
     * Editar Usuário.
     *
     * @return RedirectResponse|Response
     */
    public function editarAction(Request $request, Usuario $usuario)
    {
        if ($this->isGranted('ROLE_ADMIN_USER') or $usuario->getId() == $this->getUser()->getId()) {
            if ($this->isGranted('ROLE_ADMIN_USER')) {
                $form = $this->createForm(UsuarioType::class, $usuario);
            } else {
                $form = $this->createForm(UsuarioComumType::class, $usuario);
            }
            $form->handleRequest($request);
            if ($form->isValid() && $form->isSubmitted()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($usuario);
                $em->flush();

                return $this->redirect($this->generateUrl('nte_usuario_index'));
            }

            return $this->render('NteUsuarioBundle:Usuario:form.html.twig', [
                'entity' => $usuario,
                'form' => $form->createView(),
            ]);
        } else {
            return $this->redirectToRoute('nte_usuario_perfil', ['id' => $usuario->getId()]);
        }
    }

    /**
     * Editar Usuário.
     *
     * @return RedirectResponse|Response
     */
    public function editarPrimeiroAcessoAction(Request $request)
    {
        /** @var Usuario $usuario */
        $usuario = $this->getUser();
        $form = $this->createForm(PrimeiroAcessoType::class, $usuario);
        $form->handleRequest($request);
        if ($form->isValid() && $form->isSubmitted()) {
            $usuario
                ->setUpdate(0)
                ->setUpdateDate(new \DateTime());
            $usuario->setEnabled(true);
            $em = $this->getDoctrine()->getManager();
            $em->persist($usuario);
            $em->flush();

            return $this->redirect($this->generateUrl('nte_admin_painel_homepage'));
        }

        return $this->render('NteUsuarioBundle:Usuario:form-primeiro-acesso.html.twig', [
            'entity' => $usuario,
            'form' => $form->createView(),
        ]);
    }

    public function novoAction(Request $request)
    {
        $entity = new Usuario();
        $form = $this->createForm(UsuarioType::class, $entity);
        $form->handleRequest($request);
        if ($form->isValid() && $form->isSubmitted()) {
            $entity->setPlainPassword($form->get('plainPassword')->getData());
            $entity->setEnabled(true);
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('nte_usuario_index'));
        }

        return $this->render('NteUsuarioBundle:Usuario:form.html.twig', [
                'entity' => $entity,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @return JsonResponse
     */
    public function apiListaUsuarioAction(Request $request)
    {
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

    public function usuarioImpedimento(Usuario $a, Usuario $b, $op)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $qtd = $em->createQueryBuilder()
            ->select('count(i)')
            ->from(Impedimento::class, 'i')
            ->where('(i.idUsuario0 = :a and i.idUsuario1 = :b) or (i.idUsuario0 = :b and i.idUsuario1 = :a)')
            ->setParameter('a', $a)
            ->setParameter('b', $b)
            ->getQuery()->getSingleScalarResult();

        if (!\intval($qtd)) {
            $impedimento = new Impedimento($a, $b, $op);
            $em->persist($impedimento);
            $em->flush();
        }
    }

    /**
     * @return Response|RedirectResponse
     */
    public function declaracaoAction(Request $request, FrigaEdital $edital)
    {
        /** @var FrigaEditalUsuario|false $data */
        $data = $this->getUser()->getEtapaEditalUsuario($edital)->first();
        if (\is_bool($data) or !\is_null($data->isTermoCompromisso())) {
            return $this->redirectToRoute('nte_admin_painel_homepage');
        }
        if ($edital->getPeriodoInscricaoHabilitado()) {
            $this->addFlash('danger', 'Você não pode declarar impedimento ou não impedimento durante o período de inscrição.');

            return $this->redirectToRoute('nte_admin_painel_homepage');
        }
        $form = $this->createForm(DeclaracaoType::class, $data, ['user' => $this->getUser()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() and $this->checarAssinatura($data)) {
            /** @var EntityManager $em */
            $em = $this->getDoctrine()->getManager();
            $str = $this->txtJustificativa();
            foreach ($request->request->get('friga_usuario') as $rid => $item) {
                if ('_token' != $rid and (0 == \strpos($rid, 'inscricao')) and \in_array($item, [1, 2, 3, 4])) {
                    $uuid = \str_replace('inscricao__', '', $rid);
                    $inscricao = $em->getRepository(FrigaInscricao::class)->findOneByUuid($uuid);
                    $this->usuarioImpedimento($this->getUser(), $inscricao->getIdUsuario(), $str[$item]);
                }
            }
            $data->setTermoCompromissoData(new \DateTime());
            $em->persist($data);
            $em->flush();

            return $this->redirectToRoute('nte_admin_painel_homepage');
        }

        return $this->render('NteUsuarioBundle:Usuario:form-declaracao.html.twig', [
            'editalUsuario' => $data,
            'edital' => $edital,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return string[]
     */
    public function txtJustificativa()
    {
        return [
            0 => 'Não impedido', 1 => 'Cônjuge de candidato(a) ou companheiro(a), mesmo que divorciado ou separado judicialmente', 2 => 'Ascendente ou descendente de candidato(a), até segundo grau, ou colateral até o quarto grau, seja o parentesco por consanguinidade, afinidade ou adoção', 3 => 'Autoridade ou servidor que tenha amizade íntima ou inimizade notória com algum dos interessados ou com os respectivos cônjuges, companheiros, parentes e afins até o terceiro grau', 4 => 'Outras situações de impedimento ou suspeição previstas na legislação vigente',
        ];
    }

    /**
     * @return JsonResponse
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function declaracaoImpedimentoAction(Request $request, FrigaInscricao $frigaInscricao)
    {
        $str = $this->txtJustificativa();
        $obj = new \stdClass();
        $obj->error = false;
        $obj->msg = 'Declaração realizada com sucesso!';

        $justificativa = $request->request->get('justificativa');
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var Usuario $u */
        $u = $this->getUser();

        /** @var ImpedimentoDeclaracao|false $impedimento */
        $impedimento = $frigaInscricao->getIdImpedimentoDeclaracao($u)->first();

        if ('0' == $justificativa) {
            foreach ($frigaInscricao->getIdImpedimentoDeclaracao($u) as $item) {
                $em->remove($item);
                $em->flush();
            }
        } elseif ($impedimento) {
            $impedimento->setJustificativa($str[$justificativa]);
            $em->persist($impedimento);
            $em->flush();
        } else {
            $impedimento = new ImpedimentoDeclaracao($u, $frigaInscricao, $str[$justificativa]);
            $em->persist($impedimento);
            $em->flush();
        }

        return $this->json($obj);
    }

    public function declaracaoPdfAction(Request $request, FrigaEdital $edital, int $tipo = 1)
    {
        $declaracao = '@NteUsuario/Usuario/declaracao-impedimento.html.twig';

        $impedimento = new ArrayCollection();

        if ($tipo) {
            $declaracao = '@NteUsuario/Usuario/declaracao-impedimento-nao.html.twig';
        }

        $arquivo = '/tmp/declaracao.pdf';
        if (\is_file($arquivo)) {
            \unlink($arquivo);
        }
        $pdf = new Fpdi();
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(15, 15, 15, 15);
        $pdf->SetAutoPageBreak(true, 40);
        //$pdf->SetFont('helvetica');
        $pdf->SetFont('dejavusans');
        $pdf->setFontSize('9px');
        // $pdf->SetTextColor(113, 102, 80);

        $html = '<br><br>';
        $html .= '<p  style="text-align:center">';
        $html .= '______________________________________<br>';
        $html .= \strtoupper($this->getUser()->getNome());
        $html .= '<br>';
        $html .= 'CPF: ' . $this->getUser()->getCpf();
        $html .= '</p>';
        $html .= '<style>div{font-size: 9px;}</style>';

        //$pdf->setHtmlVSpace($tagvs);
        $pdf->AddPage();
        $pdf->WriteHTML($this->renderView($declaracao, [
            'edital' => $edital,
            'usuario' => $this->getUser(),
            'impedimento' => $impedimento,
        ]));
        $pdf->WriteHTML($html, false, true, true, true, 'rigth');
        $pdf->Output($arquivo, 'F');

        return $this->file($arquivo);
    }

    /**
     * @return bool
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function checarAssinatura(FrigaEditalUsuario $editalUsuario)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $certs = [];
        /** @var FrigaArquivo $item */
        foreach ($editalUsuario->getIdArquivo() as $item) {
            $remover = false;
            $arquivo = '/media/frigadata/' . $item->getNome();
            $cert = $this->extract_pkcs7_signatures($arquivo);
            $declaracao = $item->isParseTextTipo($editalUsuario->isTermoCompromisso());
            if (\count($cert)) {
                $certs[] = $cert;
            } else {
                $remover = true;
                $this->addFlash('danger', 'Documento inválido - Não foi localizada uma assinatura digital no arquivo em anexo.');
            }
            if (!$declaracao) {
                $remover = true;
                $this->addFlash('danger', 'Documento inválido - A declaração em anexo não corresponde com a opção selecionado no formulário');
            }
            if ($remover) {
                $editalUsuario->removeIdArquivo($item);
                $em->remove($item);
                $em->flush();
                \unlink($arquivo);
            }
        }
        if (\count($certs) and $declaracao) {
            return true;
        }

        return false;
    }
}
