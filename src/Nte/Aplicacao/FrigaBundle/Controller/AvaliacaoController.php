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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEdital;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalEtapa;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalEtapaUsuario;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalPontuacao;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalPontuacaoCategoria;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalUsuario;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricao;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricaoFeedback;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricaoPontuacao;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricaoPontuacaoAvaliacao;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricaoRecurso;
use Nte\Aplicacao\FrigaBundle\Form\AvaliacaoType;
use Nte\UsuarioBundle\Entity\Usuario;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AvaliacaoController.
 */
class AvaliacaoController extends Controller
{
    /**
     * Editais em aberto.
     *
     * @return Response
     */
    public function indexAction(Request $request, $situacao)
    {
        if (!$this->isGranted('ROLE_ADMIN') and !$this->isGranted('ROLE_AVALIADOR')) {
            return $this->redirectToRoute('nte_admin_painel_homepage');
        }
        $tmp = new ArrayCollection();

        /** @var FrigaEdital $edital */
        foreach ($this->editais() as $edital) {
            $x = $edital->getEtapa()->filter(function(FrigaEditalEtapa $etapa) use ($situacao) {
                if (50 == $situacao) {
                    return (3 == $etapa->getTipo() or 7 == $etapa->getTipo()) and $etapa->getPeriodoHabilitado();
                } elseif (100 == $situacao) {
                    return (3 == $etapa->getTipo() or 7 == $etapa->getTipo())
                        and false == $etapa->getPeriodoHabilitado()
                        and 100 == $etapa->getAndamentoPrazo();
                } elseif (0 == $situacao) {
                    return (3 == $etapa->getTipo() or 7 == $etapa->getTipo())
                        and false == $etapa->getPeriodoHabilitado()
                        and 0 == $etapa->getAndamentoPrazo();
                }
            });
            $tmp = new ArrayCollection(\array_merge($tmp->toArray(), $x->toArray()));
        }

        return $this->render('NteAplicacaoFrigaBundle:avaliacao:index.html.twig', [
            'editais' => $tmp,
        ]);
    }

    /**
     * @return Response
     */
    public function indexCandidatoAction(Request $request, FrigaEditalEtapa $etapa)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            if ($this->getUser()->getPermissaoEtapaTermo($etapa)) {
                $this->addFlash('info', 'Você ainda não enviou a declaração de impedimento ou não impedimento deste edital.');

                return $this->redirectToRoute('nte_usuario_declaracao', ['uuid' => $etapa->getIdEdital()->getUuid()]);
            }
            if (!$this->getUser()->getPermissaoEtapa($etapa)) {
                $this->addFlash('danger', 'Você não tem permissão para realizar a avaliação!');

                return $this->redirectToRoute('nte_admin_painel_homepage');
            }
            if (!$etapa->getPeriodoHabilitado()) {
                $this->addFlash('danger', 'Avaliação fora do período.');

                return $this->redirectToRoute('nte_admin_painel_homepage');
            }
        }

        return $this->render('NteAplicacaoFrigaBundle:avaliacao:index-candidato.html.twig', [
            'etapa' => $etapa,
            'usuario' => $etapa,
        ]);
    }

    /**
     * @return BinaryFileResponse
     */
    public function exportarCsvAction(Request $request, FrigaEditalEtapa $etapa)
    {
        $pt = $etapa->getIdEdital()->getPontuacao();

        $cabecalho = [
            -1 => 'Inscricao',
            -2 => 'Nome',
            -3 => 'E-mail',
            -4 => 'Cargo',
            -5 => 'Lista',
            -6 => 'Etapa',
        ];
        /** @var FrigaEditalPontuacao $item */
        foreach ($pt as $item) {
            if (\is_null($item->getIdCategoria())) {
                continue;
            }
            $cabecalho[$item->getId()] = $item->getTitulo();
        }

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQueryBuilder()
            ->select('i')
            ->from(FrigaInscricao::class, 'i')
            ->where('i.idSituacao != -999')
            ->andWhere('i.idEdital = :edital')
            ->setParameter('edital', $etapa->getIdEdital())
            ->orderBy('i.nome', 'asc');

        //Etapa de recursos
        if (7 == $etapa->getTipo()) {
            $query->innerJoin('i.recursos', 'ir')
                ->andWhere('ir.idEditalEtapa = :recursoetapa')
                ->setParameter('recursoetapa', $etapa);
        }

        // Filtra por categorias da etapa
        if (!\is_null($etapa->getIdEtapaCategoria())) {
            $query->innerJoin('i.idEtapa', 'ie')
                ->andWhere('ie.idEtapaCategoria = :categoria')
                ->setParameter('categoria', $etapa->getIdEtapaCategoria());
        }

        $inscricao = $query->getQuery()->getResult();
        $arquivo = '/tmp/avaliacao-' . $etapa->getId() . '.csv';
        if (\is_file($arquivo)) {
            \unlink($arquivo);
        }
        $out = \fopen($arquivo, 'w');

        \fputcsv($out, $cabecalho);

        /** @var FrigaInscricao $item */
        foreach ($inscricao as $item) {
            $linha = [
                -1 => $item->getUuid(),
                -2 => \strtoupper($item->getNome()),
                -3 => $item->getContatoEmail(),
                -4 => $item->getIdCargo() ? $item->getIdCargo()->getDescricao() : '',
                -5 => $item->getIdCota() ? $item->getIdCota()->getDescricao() : '',
                -6 => $etapa->getDescricao(),
            ];

            /**
             * @var int $id
             * @var int $cid
             */
            foreach ($cabecalho as $id => $cid) {
                if (!\array_key_exists($id, $linha)) {
                    $linha[$id] = 0;
                }
            }

            /** @var FrigaInscricaoPontuacao $subitem */
            foreach ($item->getPontuacao() as $subitem) {
                $linha[$subitem->getIdEditalPontuacao()->getId()] = $subitem->getValorInformado();
            }

            \fputcsv($out, $linha);
        }
        \fclose($out);

        return $this->file($arquivo);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @throws \Exception
     */
    public function apiIndexAction(Request $request, FrigaEditalEtapa $etapa)
    {
        $cargo = $request->query->get('cargo') ?? -1;
        $cota = $request->query->get('cota') ?? -1;
        $situacao = $request->query->get('situacao') ?? -1;
        $usuario = $request->query->get('usuario') ?? -1;
        $dt0 = new \DateTime($request->query->get('dt0'));
        $dt1 = new \DateTime($request->query->get('dt1'));
        $dt0->setTime(0, 0, 0);
        $dt1->setTime(23, 59, 59);

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQueryBuilder()
            ->where('i.idSituacao != -999')
            ->andWhere('i.idEdital = :edital')
            ->setParameter('edital', $etapa->getIdEdital())
            ->orderBy('i.nome', 'asc');

        // exit();
        //Etapa de recursos
        if (7 == $etapa->getTipo()) {
            $query->select('ir')
                ->from(FrigaInscricaoRecurso::class, 'ir')
                ->innerJoin('ir.idInscricao', 'i')
                ->andWhere('ir.idEditalEtapa = :recursoetapa')
                ->setParameter('recursoetapa', $etapa->getIdEtapa()->getId());
        } else {
            $query->select('i')
                ->from(FrigaInscricao::class, 'i');
        }

        // Filtra por categorias da etapa
        if (!\is_null($etapa->getIdEtapaCategoria())) {
            $query->innerJoin('i.idEtapa', 'ie')
                ->andWhere('ie.idEtapaCategoria = :categoria')
                ->setParameter('categoria', $etapa->getIdEtapaCategoria());
        }

        // Filtra por  cargos do usuário
        if (!$this->isGranted('ROLE_ADMIN')) {
            $cargos = $etapa->getIdEdital()->getUsuarioEditalCargos($this->getUser());
            $query->andWhere('i.idCargo in (:cargos)')->setParameter('cargos', $cargos);
        }

        // filtra por cota
        if (-1 != $cota) {
            $query->andWhere('i.idCota = :cota')
                ->setParameter('cota', $cota);
        }

        // filtra por cargo
        if (-1 != $cargo) {
            $query->andWhere('i.idCargo = :cargo')
                ->setParameter('cargo', $cargo);
        }

        // filtra por situação
        if (-1 != $situacao) {
            $query->andWhere('i.idSituacao = :situacao')
                ->setParameter('situacao', $situacao);
        }

        // filtra por avaliador
        if (-1 != $usuario) {
            $query->innerJoin('i.idEditalEtapaUsuario', 'eeu')
                ->andWhere('eeu.idUsuario = :usuario')
                ->setParameter('usuario', $usuario);
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

        /** @var FrigaInscricao|FrigaInscricaoRecurso $item */
        foreach ($paginator as $item) {
            $in = 7 == $etapa->getTipo() ? $item->getIdInscricao() : $item;
            $obj0 = new \stdClass();
            $obj0->uuid = $in->getUuid();
            $obj0->id = $in->getId();
            $obj0->titulo = $in->getNome();
            if (1 == $etapa->getIdEdital()->getModeloInscricao()) {
                $obj0->titulo = $in->getProjetoTitulo();
            }
            $obj0->titulo = \strtoupper($obj0->titulo);
            $obj0->cota = !\is_null($in->getIdCota()) ? $in->getIdCota()->getDescricao() : null;
            $obj0->cargo = !\is_null($in->getIdCargo()) ? $in->getIdCargo()->getDescricao() : null;
            $obj0->situacao = $item->getObjsituacao();
            $obj0->ptCandidato = $in->getPontuacaoSoma();
            $obj0->pteCandidato = $in->getPontuacaoSoma(false, null, true);
            $obj0->ptAvaliador = $in->getPontuacaoSoma(true);
            $obj0->pteAvaliador = $in->getPontuacaoSoma(true, null, true);
            $obj0->url0 = $this->generateUrl('avaliacao_etapa_candidato', [
                'etapa' => $etapa->getId(),
                'uuid' => $in->getUuid(),
            ]);
            $obj->aaData[] = $obj0;
        }

        return $this->json($obj);
    }

    /**
     * Traaz todos os editais que usuário pode ter acesso;
     * Se administrador, traz todos os editais.
     *
     * @param int $situacao
     *
     * @return array|ArrayCollection|object[]
     */
    public function editais($situacao = 1)
    {
        $em = $this->getDoctrine()->getManager();
        $editais = new ArrayCollection();
        if ($this->isGranted('ROLE_ADMIN')) {
            $editais = $em->getRepository(FrigaEdital::class)->findAll(['situacao' => $situacao]);
        } elseif ($this->isGranted('ROLE_AVALIADOR')) {
            /** @var ArrayCollection $tmp */
            $tmp = $this->getUser()
                ->getIdEditalUsuario()
                ->filter(function(FrigaEditalUsuario $eu) use ($situacao) {
                    return $eu->getIdEdital()->getSituacao() == $situacao;
                });
            if ($tmp->count()) {
                /** @var FrigaEditalUsuario $eu */
                foreach ($tmp as $eu) {
                    if (!$editais->contains($eu->getIdEdital())) {
                        $editais->add($eu->getIdEdital());
                    }
                }
                $editais = $editais->filter(function(FrigaEdital $edital) {
                    return $edital->getEtapa()->filter(function(FrigaEditalEtapa $etapa) {
                        return true; //$etapa->getPeriodoHabilitado();
                    })->count();
                });
            }
        }

        return $editais;
    }

    /**
     * @return RedirectResponse|Response
     */
    public function formAction(Request $request, FrigaEditalEtapa $etapa, FrigaInscricao $inscricao)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        if (!$this->isGranted('ROLE_ADMIN')) {
            if (!$this->getUser()->getPermissaoEtapa($etapa)) {
                $this->addFlash('danger', 'Você não tem permissão para realizar a avaliação!');

                return $this->redirectToRoute('nte_admin_painel_homepage');
            }
            if (!$etapa->getPeriodoHabilitado()) {
                $this->addFlash('danger', 'Avaliação fora do período.');

                return $this->redirectToRoute('nte_admin_painel_homepage');
            }
        }

        $recurso = new ArrayCollection();
        $etapaRetorno = $etapa;
        if (7 == $etapa->getTipo()) {
            if (!$etapa->getIdEtapa()) {
                $this->addFlash('danger', 'Antes de avaliar o recurso  é necessário configurar a etapa. Entre em contato com o responsável pelo edital.');

                return $this->redirectToRoute('avaliacao_etapa', ['etapa' => $etapa->getId()]);
            }
            $etapa = $etapa->getIdEtapa();
            $recursos = $em->getRepository(FrigaInscricaoRecurso::class)
                ->createQueryBuilder('a')
                ->Where('a.idEditalEtapa = :etapa')
                ->andWhere('a.idInscricao   = :inscricao')
                ->setParameter('etapa', $etapa)
                ->setParameter('inscricao', $inscricao)
                ->getQuery()->getResult();
            $recurso = new ArrayCollection($recursos);
        }

        $avaliacao = $em->getRepository(FrigaInscricaoPontuacaoAvaliacao::class)
            ->createQueryBuilder('a')
            ->Where('a.idEtapa = :etapa')
            ->andWhere('a.idInscricao   = :inscricao')
            ->setParameter('etapa', $etapa)
            ->setParameter('inscricao', $inscricao)
            ->getQuery()->getResult();
        $avaliacao = new ArrayCollection($avaliacao);

        $feedback = $em->getRepository(FrigaInscricaoFeedback::class)
            ->createQueryBuilder('f')
            ->Where('f.idEtapa = :etapa')
            ->andWhere('f.idInscricao   = :inscricao')
            ->setParameter('etapa', $etapa)
            ->setParameter('inscricao', $inscricao);

        if ($etapa->isPontuacaoMultipla()) {
            $feedback->andWhere('f.idAvaliador = :avaliador')
                ->setParameter('avaliador', $this->getUser());
        }

        /** @var FrigaInscricaoFeedback $feedback */
        $feedback = $feedback->getQuery()->getResult();

        /** @var Usuario $avaliador */
        $avaliador = $this->getUser();

        $form = $this->createForm(AvaliacaoType::class, null, [
            'em' => $this->getDoctrine()->getManager(),
            'etapa' => $etapa,
            'inscricao' => $inscricao,
            'avaliacao' => $avaliacao,
            'usuario' => $avaliador,
            'feedback' => $feedback ? $feedback[0]->getFeedback() : null,
            'recurso' => $recurso,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->getUser()->getPermissaoEtapa($etapa)) {
                $this->addFlash('danger', 'Você não tem permissão para realizar a avaliação!');

                return $this->redirectToRoute('avaliacao_etapa', ['etapa' => $etapaRetorno->getId()]);
            }
            $pontuacaoSalva = [];
            foreach ($request->request->get('nte_inscricao') as $chave => $valor) {
                //Manipula  a situação da entidade inscrição
                if ('situacao' == $chave) {
                    $inscricao->setIdSituacaoAnterior($inscricao->getIdSituacao());
                    $inscricao->setIdSituacao($valor);
                    $em->persist($inscricao);
                    $em->flush();
                }
                // Manipula entidade de feedback
                if ('feedback' == $chave) {
                    if ($valor) {
                        if (!$feedback) {
                            $feedback = new FrigaInscricaoFeedback();
                            $feedback->setIdInscricao($inscricao)->setIdEtapa($etapa);
                        } else {
                            $feedback = $feedback[0];
                        }
                        $feedback->setIdAvaliador($avaliador);
                        $feedback->setFeedback($valor);
                        $em->persist($feedback);
                        $em->flush();
                    } else {
                        if ($feedback) {
                            $em->remove($feedback[0]);
                            $em->flush();
                        }
                    }
                }
                //Manipula a entidade Recurso
                if (0 === \strpos($chave, 'recurso__') or 0 === \strpos($chave, 'recursosituacao__')) {
                    foreach ($request->request->get('nte_inscricao') as $chave => $valor) {
                        if (0 === \strpos($chave, 'recurso__') or 0 === \strpos($chave, 'recursosituacao__')) {
                            if (0 === \strpos($chave, 'recurso__')) {
                                $idRecurso = \intval(\str_replace('recurso__', '', $chave));
                                $recurso = $em->find(FrigaInscricaoRecurso::class, $idRecurso);
                                $recurso->setDesfecho($valor);
                            } elseif (0 === \strpos($chave, 'recursosituacao__')) {
                                $idRecurso = \intval(\str_replace('recursosituacao__', '', $chave));
                                $recurso = $em->find(FrigaInscricaoRecurso::class, $idRecurso);
                                $recurso->setIdSituacao($valor);
                            }
                            $em->persist($recurso);
                            $em->flush();
                        }
                    }
                }

                $idCategoria = false;
                $pt = false;
                //Filtra os itens de pontuação enviados pelo formulário
                if (0 === \strpos($chave, 'pt__') or 0 === \strpos($chave, 'cat__')) {
                    // Captura o valor da pontuação
                    if (0 === \strpos($chave, 'pt__')) {
                        $idPontuacao = \intval(\str_replace('pt__', '', $chave));
                        $pt = $em->find(FrigaEditalPontuacao::class, $idPontuacao);
                        $pontuacao = $this->avaliacaoAnterior($etapa, $inscricao, $avaliador, $avaliacao, $pt, null);
                        $pontuacao->setValorAvaliador(\floatval($valor));
                        if ($etapa->getPontuacaoRelativaUsuario()) {
                            if ($inscricao->getPontuacaoItem($idPontuacao)) {
                                $pontuacao->setIdInscricaoPontuacao($inscricao->getPontuacaoItem($idPontuacao));
                                $pontuacao->setValorInscricao($inscricao->getPontuacaoItem($idPontuacao)->getValorInformado());
                            } else {
                                $pontuacao->setIdInscricaoPontuacao(null);
                                $pontuacao->setValorInscricao(0);
                            }
                        }
                    }
                    // Captura o valor da pontuação através da categoria
                    if (0 === \strpos($chave, 'cat__')) {
                        $idCategoria = \intval(\str_replace('cat__', '', $chave));
                        $idPontuacao = \intval(\str_replace('pt__', '', \intval($valor)));
                        $pt = $em->find(FrigaEditalPontuacao::class, $idPontuacao);
                        $categoria = $em->find(FrigaEditalPontuacaoCategoria::class, $idCategoria);
                        $pontuacao = $this->avaliacaoAnterior($etapa, $inscricao, $avaliador, $avaliacao, $pt, $categoria);
                        if ($pt) {
                            $pontuacao->setIdInscricaoPontuacao($inscricao->getPontuacaoItem($idPontuacao));
                            $pontuacao->setValorAvaliador($pt->getValorMaximo());
                            if ($etapa->getPontuacaoRelativaUsuario()) {
                                if ($inscricao->getPontuacaoCategoriaItem($idCategoria)) {
                                    $pontuacao->setIdInscricaoPontuacao($inscricao->getPontuacaoCategoriaItem($idCategoria));
                                    $pontuacao->setValorInscricao($inscricao->getPontuacaoCategoriaItem($idCategoria)->getValorInformado());
                                } else {
                                    $pontuacao->setIdInscricaoPontuacao(null);
                                    $pontuacao->setValorInscricao(0);
                                }
                            }
                        } else {
                            $pontuacao->setIdInscricaoPontuacao(null);
                            $pontuacao->setValorAvaliador(0);
                        }
                    }
                    if ($pontuacao) {
                        $em->persist($pontuacao);
                        $em->flush();
                        $pontuacaoSalva[$chave] = $pontuacao;
                    }
                }
            }

            //Persisiste valor do anexo;
            foreach ($request->request->get('nte_inscricao') as $chave => $valor) {
                if (0 === \strpos($chave, 'ptanexo__') or 0 === \strpos($chave, 'catanexo__')) {
                    $chaveAnexo = \str_replace('anexo', '', $chave);
                    if (\array_key_exists($chaveAnexo, $pontuacaoSalva)) {
                        $pontuacaoSalva[$chaveAnexo]->setConsiderado($valor);
                        $em->persist($pontuacaoSalva[$chaveAnexo]);
                        $em->flush();
                    }
                }
            }
            $etapaUsuario = new FrigaEditalEtapaUsuario($etapa, $inscricao, $this->getUser());
            $em->persist($etapaUsuario);
            $em->flush();
            $this->addFlash('success', 'Avaliação realizada com sucesso!');

            return $this->redirectToRoute('avaliacao_etapa', ['etapa' => $etapaRetorno->getId()]);
        }

        return $this->render('NteAplicacaoFrigaBundle:avaliacao:form.html.twig', [
            'form' => $form->createView(),
            'etapa' => $etapa,
            'inscricao' => $inscricao,
            'recurso' => $recurso,
        ]);
    }

    /**
     * Recupera a pontuação anterior.
     *
     * @param FrigaEditalEtapa $etapa
     * @param FrigaInscricao $inscricao
     * @param Usuario $avaliador
     * @param ArrayCollection $avaliacao
     * @param FrigaEditalPontuacao $item
     * @param FrigaEditalPontuacaoCategoria $categoria
     *
     * @return FrigaInscricaoPontuacaoAvaliacao
     */
    private function avaliacaoAnterior($etapa, $inscricao, $avaliador, $avaliacao, $item, $categoria = null)
    {
        $pontuacao = $avaliacao->filter(function(FrigaInscricaoPontuacaoAvaliacao $a) use ($inscricao, $etapa, $item, $categoria, $avaliador) {
            $ua = $etapa->isPontuacaoMultipla() ? ($a->getIdAvaliador() == $avaliador->getId()) : true;
            if (null == $categoria) {
                return $a->getIdInscricao()->getId() == $inscricao->getId()
                    and $a->getIdEtapa()->getId() == $etapa->getId()
                    and null != $a->getIdEditalPontuacao()
                    and $a->getIdEditalPontuacao()->getId() == $item->getId()
                    and $ua;
            } else {
                return $a->getIdInscricao()->getId() == $inscricao->getId()
                    and $a->getIdEtapa()->getId() == $etapa->getId()
                    and null != $a->getIdEditalPontuacaoCategoria()
                    and $a->getIdEditalPontuacaoCategoria()->getId() == $categoria->getId()
                    and $ua;
            }
        });
        if ($pontuacao->count()) {
            $pontuacao = $pontuacao->first();
            if (null != $categoria) {
                //  dump($item);
                $pontuacao->setIdEditalPontuacao($item);
            }
        } else {
            $pontuacao = new FrigaInscricaoPontuacaoAvaliacao();
            $pontuacao->setIdInscricao($inscricao)
                ->setIdAvaliador($avaliador)
                ->setIdEtapa($etapa);
        }
        $pontuacao->setConsiderado(!$etapa->getPontuacaoRelativaUsuario())
            ->setIdEditalPontuacao($item)
            ->setIdEditalPontuacaoCategoria($categoria);

        return $pontuacao;
    }
}
