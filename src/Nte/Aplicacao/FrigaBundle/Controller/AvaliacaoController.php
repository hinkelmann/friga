<?php

namespace Nte\Aplicacao\FrigaBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Expr\Func;
use Doctrine\ORM\QueryBuilder;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaArquivo;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEdital;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalEtapa;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalPontuacao;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalUsuario;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricao;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricaoFeedback;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricaoPontuacao;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricaoPontuacaoAvaliacao;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricaoRecurso;
use Nte\Aplicacao\FrigaBundle\Form\AvaliacaoType;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalPontuacaoCategoria;
use Nte\UsuarioBundle\Entity\Usuario;
use Nte\UsuarioBundle\Form\InscricaoType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use stdClass;
use DateTime;
use Exception;

/**
 * Class AvaliacaoController
 * @package Nte\Aplicacao\FrigaBundle\Controller
 */
class AvaliacaoController extends Controller
{

    /**
     * Editais em aberto
     *
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request,$situacao)
    {
        if (!$this->isGranted('ROLE_ADMIN') AND !$this->isGranted('ROLE_AVALIADOR')) {
            return $this->redirectToRoute('nte_admin_painel_homepage');
        }
        $tmp = new ArrayCollection();

        /** @var FrigaEdital $edital */
        foreach ($this->editais() as $edital) {
            $x = $edital->getEtapa()->filter(Function (FrigaEditalEtapa $etapa) use ($situacao) {
                if($situacao==50){
                    return ($etapa->getTipo() == 3 or $etapa->getTipo() == 7) and $etapa->getPeriodoHabilitado();
                }
                else if($situacao ==100){
                    return ($etapa->getTipo() == 3 or $etapa->getTipo() == 7)
                        and $etapa->getPeriodoHabilitado() == false
                        and $etapa->getAndamentoPrazo() ==100;
                }else if($situacao == 0){
                    return ($etapa->getTipo() == 3 or $etapa->getTipo() == 7)
                        and $etapa->getPeriodoHabilitado() == false
                        and $etapa->getAndamentoPrazo() ==0;
                }
            });
            $tmp = new ArrayCollection(array_merge($tmp->toArray(), $x->toArray()));
        }

        return $this->render('NteAplicacaoFrigaBundle:avaliacao:index.html.twig', [
            'editais' => $tmp,
        ]);
    }

    /**
     *
     * @param Request $request
     * @return Response
     */
    public function indexCandidatoAction(Request $request, FrigaEditalEtapa $etapa)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            if (!$this->isGranted('ROLE_AVALIADOR')) {
                return $this->redirectToRoute('nte_admin_painel_homepage');
            }
            if (!$etapa->getPeriodoHabilitado()) {
                return $this->redirectToRoute('avaliacao_index');
            }
        }
       // $etapa->getIdEdital()->getInscricaoAvalicao()->first()->getPontuacaoSoma();
        return $this->render('NteAplicacaoFrigaBundle:avaliacao:index-candidato.html.twig', [
            'etapa' => $etapa,
            'usuario' => $etapa,
        ]);
    }

    /**
     * Traaz todos os editais que usuário pode ter acesso;
     * Se administrador, traz todos os editais.
     *
     * @param int $situacao
     * @return array|ArrayCollection|object[]
     */
    public function editais($situacao = 1)
    {
        $em = $this->getDoctrine()->getManager();
        $editais = new ArrayCollection();
        if ($this->isGranted('ROLE_ADMIN')) {
            $editais = $em->getRepository(FrigaEdital::class)->findAll(['situacao' => $situacao]);
        } else if ($this->isGranted('ROLE_AVALIADOR')) {

            /** @var ArrayCollection $tmp */
            $tmp = $this->getUser()
                ->getIdEditalUsuario()
                ->filter(function (FrigaEditalUsuario $eu) use ($situacao) {
                    return $eu->getIdEdital()->getSituacao() == $situacao;
                });
            if ($tmp->count()) {
                /** @var FrigaEditalUsuario $eu */
                foreach ($tmp as $eu) {
                    if (!$editais->contains($eu->getIdEdital())) {
                        $editais->add($eu->getIdEdital());
                    }
                };
                $editais = $editais->filter(function (FrigaEdital $edital) {
                    return $edital->getEtapa()->filter(function (FrigaEditalEtapa $etapa) {
                        return true; //$etapa->getPeriodoHabilitado();
                    })->count();
                });
            }
        }
        return $editais;
    }


    /**
     * @param Request $request
     * @param FrigaEditalEtapa $etapa
     * @param FrigaInscricao $inscricao
     * @return RedirectResponse|Response
     */
    public function formAction(Request $request, FrigaEditalEtapa $etapa, FrigaInscricao $inscricao)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            if (!$this->isGranted('ROLE_AVALIADOR')) {
                return $this->redirectToRoute('nte_admin_painel_homepage');
            }
            if (!$etapa->getPeriodoHabilitado()) {
                return $this->redirectToRoute('avaliacao_index');
            }
        }

        $em = $this->getDoctrine()->getManager();

        $recurso = new ArrayCollection();
        $etapaRetorno = $etapa;
        if ($etapa->getTipo() == 7) {
            if (!$etapa->getIdEtapa()) {
                $this->addFlash('danger', "Antes de avaliar o recurso  é necessário configurar a etapa. Entre em contato com o suporte.");
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
            $pontuacaoSalva = [];
            foreach ($request->request->get('nte_inscricao') as $chave => $valor) {

                //Manipula  a situação da entidade inscrição
                if ($chave == 'situacao') {
                    $inscricao->setIdSituacaoAnterior($inscricao->getIdSituacao());
                    $inscricao->setIdSituacao($valor);
                    $em->persist($inscricao);
                    $em->flush();

                }
                // Manipula entidade de feedback
                if ($chave == 'feedback') {
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
                if (strpos($chave, "recurso__") === 0 or strpos($chave, "recursosituacao__") === 0) {
                    foreach ($request->request->get('nte_inscricao') as $chave => $valor) {
                        if (strpos($chave, "recurso__") === 0 or strpos($chave, "recursosituacao__") === 0) {
                            if (strpos($chave, "recurso__") === 0) {
                                $idRecurso = intval(str_replace("recurso__", "", $chave));
                                $recurso = $em->find(FrigaInscricaoRecurso::class, $idRecurso);
                                $recurso->setDesfecho($valor);
                            } elseif (strpos($chave, "recursosituacao__") === 0) {
                                $idRecurso = intval(str_replace("recursosituacao__", "", $chave));
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
                if (strpos($chave, "pt__") === 0 or strpos($chave, "cat__") === 0) {

                    // Captura o valor da pontuação
                    if (strpos($chave, "pt__") === 0) {
                        $idPontuacao = intval(str_replace("pt__", "", $chave));
                        $pt = $em->find(FrigaEditalPontuacao::class, $idPontuacao);
                        $pontuacao = $this->avaliacaoAnterior($etapa, $inscricao, $avaliador, $avaliacao, $pt, null);
                        $pontuacao->setValorAvaliador(floatval($valor));
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
                    if (strpos($chave, "cat__") === 0) {
                        $idCategoria = intval(str_replace("cat__", "", $chave));
                        $idPontuacao = intval(str_replace("pt__", "", intval($valor)));
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
                if (strpos($chave, "ptanexo__") === 0 or strpos($chave, "catanexo__") === 0) {
                    $chaveAnexo = str_replace('anexo', '', $chave);
                    if (array_key_exists($chaveAnexo, $pontuacaoSalva)) {
                        $pontuacaoSalva[$chaveAnexo]->setConsiderado($valor);
                        $em->persist($pontuacaoSalva[$chaveAnexo]);
                        $em->flush();
                    }
                }
            }
            $this->addFlash('success', "Avaliação realizada com sucesso!");
            return $this->redirectToRoute('avaliacao_etapa', ['etapa' => $etapaRetorno->getId()]);
        }

        return $this->render('NteAplicacaoFrigaBundle:avaliacao:form.html.twig', [
            'form' => $form->createView(),
            'etapa' => $etapa,
            'inscricao' => $inscricao,
            'recurso' => $recurso
        ]);
    }

    /**
     * Recupera a pontuação anterior
     *
     * @param FrigaEditalEtapa $etapa
     * @param FrigaInscricao $inscricao
     * @param Usuario $avaliador
     * @param ArrayCollection $avaliacao
     * @param FrigaEditalPontuacao $item
     * @param FrigaEditalPontuacaoCategoria $categoria
     * @return FrigaInscricaoPontuacaoAvaliacao
     */
    private function avaliacaoAnterior($etapa, $inscricao, $avaliador, $avaliacao, $item, $categoria = null)
    {
        $pontuacao = $avaliacao->filter(function (FrigaInscricaoPontuacaoAvaliacao $a)
        use ($inscricao, $etapa, $item, $categoria, $avaliador) {
            $ua = $etapa->isPontuacaoMultipla() ? ($a->getIdAvaliador() == $avaliador->getId()) : true;
            if ($categoria == null) {
                return $a->getIdInscricao()->getId() == $inscricao->getId()
                    and $a->getIdEtapa()->getId() == $etapa->getId()
                    and $a->getIdEditalPontuacao() != null
                    and $a->getIdEditalPontuacao()->getId() == $item->getId()
                    and $ua;
            } else {
                return $a->getIdInscricao()->getId() == $inscricao->getId()
                    and $a->getIdEtapa()->getId() == $etapa->getId()
                    and $a->getIdEditalPontuacaoCategoria() != null
                    and $a->getIdEditalPontuacaoCategoria()->getId() == $categoria->getId()
                    and $ua;
            }
        });
        if ($pontuacao->count()) {
            $pontuacao = $pontuacao->first();
            if ($categoria != null) {
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