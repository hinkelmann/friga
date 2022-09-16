<?php

namespace Nte\Aplicacao\FrigaBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaArquivo;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaClassificacao;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaConvocacao;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEdital;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalCargo;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalCota;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalDesempate;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalEtapa;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalPontuacao;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalPontuacaoCategoria;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalUsuario;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricao;
use Nte\Aplicacao\FrigaBundle\Form\AvaliacaoType;
use Nte\Aplicacao\FrigaBundle\Form\FrigaClassificacaoType;
use Nte\Aplicacao\FrigaBundle\Form\FrigaEditalResultadoType;
use Nte\Aplicacao\FrigaBundle\Model\FrigaClassificacaoComprovante;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
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
class ResultadoController extends Controller
{

    /**
     * Editais em aberto
     *
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request, $situacao = 50)
    {
        if (!$this->isGranted('ROLE_ADMIN') and !$this->isGranted('ROLE_AVALIADOR')) {
            return $this->redirectToRoute('nte_admin_painel_homepage');
        }
        $tmp = new ArrayCollection();

        /** @var FrigaEdital $edital */
        foreach ($this->editais() as $edital) {
            $x = $edital->getEtapa()->filter(function (FrigaEditalEtapa $etapa) use ($situacao) {
                if ($situacao == 50) {
                    return $etapa->getTipo() == 4 and $etapa->getPeriodoHabilitado();
                } else if ($situacao == 100) {
                    return $etapa->getTipo() == 4
                        and $etapa->getPeriodoHabilitado() == false
                        and $etapa->getAndamentoPrazo() == 100;
                } else if ($situacao == 0) {
                    return $etapa->getTipo() == 4
                        and $etapa->getPeriodoHabilitado() == false
                        and $etapa->getAndamentoPrazo() == 0;
                }
            });
            $tmp = new ArrayCollection(array_merge($tmp->toArray(), $x->toArray()));
        }

        return $this->render('NteAplicacaoFrigaBundle:Resultado:index.html.twig', [
            'editais' => $tmp,
        ]);
    }

    /**
     * @param Request $request
     * @param FrigaEdital $edital
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function parcialAction(Request $request, FrigaEdital $edital)
    {
        if (!$this->isGranted('ROLE_ADMIN') and !$this->isGranted('ROLE_AVALIADOR')) {
            return $this->redirectToRoute('nte_admin_painel_homepage');
        }

        $classificacao = new ArrayCollection();

        $geral = $edital->getInscricaoValida()->getIterator();
        $geral->uasort(function (FrigaInscricao $a, FrigaInscricao $b) {
            return bccomp($b->getPontuacaoSoma(true), $a->getPontuacaoSoma(true), 6);
        });

        $geral = new ArrayCollection($geral->getArrayCopy());

        if ($edital->isResultado0() or $edital->isResultado1()) {
            /** @var FrigaEditalCargo $cargo */
            foreach ($edital->getCargo() as $cargo) {
                if ($edital->isResultado0()) {
                    if ($edital->getCota()->count()) {
                        /** @var FrigaEditalCota $lista */
                        foreach ($edital->getCota() as $lista) {
                            $obj = new stdClass();
                            $obj->nome = $cargo->getDescricao() . "/" . $lista->getDescricao();
                            $obj->cargo = $cargo;
                            $obj->lista = $lista;
                            $obj->classificacao = $geral->filter(function (FrigaInscricao $inscricao) use ($cargo, $lista) {
                                return $inscricao->getIdCargo()->getId() == $cargo->getId()
                                    and $inscricao->getIdCota()->getId() == $lista->getId();
                            });
                            $classificacao->add($obj);
                        }
                    }
                }
                if ($edital->isResultado1()) {
                    $obj = new stdClass();
                    $obj->nome = "Classificação Geral / " . $cargo->getDescricao();
                    $obj->cargo = $cargo;
                    $obj->lista = null;
                    $obj->classificacao = $geral->filter(function (FrigaInscricao $inscricao) use ($cargo) {
                        return $inscricao->getIdCargo()->getId() == $cargo->getId();
                    });
                    $classificacao->add($obj);
                }
            }
        }
        if ($edital->isResultado2()) {
            foreach ($edital->getCota() as $lista) {
                $obj = new stdClass();
                $obj->nome = "Classificação Geral/" . $lista->getDescricao();
                $obj->cargo = null;
                $obj->lista = $lista;
                $obj->classificacao = $geral->filter(function (FrigaInscricao $inscricao) use ($lista) {
                    return $inscricao->getIdCota()->getId() == $lista->getId();
                });
                $classificacao->add($obj);
            }
        }
        if ($edital->isResultado3()) {
            $obj = new stdClass();
            $obj->nome = "Classificação Geral";
            $obj->cargo = null;
            $obj->lista = null;
            $obj->classificacao = $geral;
            $classificacao->add($obj);
        }

        $form = $this->createForm(FrigaClassificacaoType::class, null);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $etapa = $em->find(FrigaEditalEtapa::class, $request->request->get('etapa'));
            if ($etapa) {
                $this->processarResultado($etapa, $classificacao);
                return $this->redirectToRoute('resultado_etapa', ['etapa' => $etapa->getId()]);
            }
        }
        return $this->render('NteAplicacaoFrigaBundle:Resultado:parcial.html.twig', [
            'classificao' => $classificacao,
            'edital' => $edital,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param FrigaEditalEtapa $etapa
     * @return RedirectResponse
     */
    public function gerarResultadoAction(Request $request, FrigaEditalEtapa $etapa)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            if ($etapa->getPeriodoHabilitado() and !$this->isGranted('ROLE_AVALIADOR')) {
                return $this->redirectToRoute('resultado_etapa', ['etapa' => $etapa->getId()]);
            }
        }
        if(!$etapa->getClassificacao()->isEmpty()){
            return $this->redirectToRoute('resultado_etapa', ['etapa' => $etapa->getId()]);
        }

        $edital = $etapa->getIdEdital();

        try {
            $classificacao = new ArrayCollection();
            $geral = $edital->getInscricaoValida()->getIterator();
            $geral->uasort(function (FrigaInscricao $a, FrigaInscricao $b) {
                return bccomp($b->getPontuacaoSoma(true), $a->getPontuacaoSoma(true), 6);
            });

            $geral = new ArrayCollection($geral->getArrayCopy());

            if ($edital->isResultado0() or $edital->isResultado1()) {
                /** @var FrigaEditalCargo $cargo */
                foreach ($edital->getCargo() as $cargo) {
                    if ($edital->isResultado0()) {
                        if ($edital->getCota()->count()) {
                            /** @var FrigaEditalCota $lista */
                            foreach ($edital->getCota() as $lista) {
                                $obj = new stdClass();
                                $obj->nome = $cargo->getDescricao() . "/" . $lista->getDescricao();
                                $obj->cargo = $cargo;
                                $obj->lista = $lista;
                                $obj->classificacao = $geral->filter(function (FrigaInscricao $inscricao) use ($cargo, $lista) {
                                    return $inscricao->getIdCargo()->getId() == $cargo->getId()
                                        and $inscricao->getIdCota()->getId() == $lista->getId();
                                });
                                $classificacao->add($obj);
                            }
                        }
                    }
                    if ($edital->isResultado1()) {
                        $obj = new stdClass();
                        $obj->nome = "Classificação Geral / " . $cargo->getDescricao();
                        $obj->cargo = $cargo;
                        $obj->lista = null;
                        $obj->classificacao = $geral->filter(function (FrigaInscricao $inscricao) use ($cargo) {
                            return $inscricao->getIdCargo()->getId() == $cargo->getId();
                        });
                        $classificacao->add($obj);
                    }
                }
            }

            if ($edital->isResultado2()) {
                foreach ($edital->getCota() as $lista) {
                    $obj = new stdClass();
                    $obj->nome = "Classificação Geral/" . $lista->getDescricao();
                    $obj->cargo = null;
                    $obj->lista = $lista;
                    $obj->classificacao = $geral->filter(function (FrigaInscricao $inscricao) use ($lista) {
                        return $inscricao->getIdCota()->getId() == $lista->getId();
                    });
                    $classificacao->add($obj);
                }
            }

            if ($edital->isResultado3()) {
                $obj = new stdClass();
                $obj->nome = "Classificação Geral";
                $obj->cargo = null;
                $obj->lista = null;
                $obj->classificacao = $geral;
                $classificacao->add($obj);
            }

            $this->processarResultado($etapa, $classificacao);

        } catch (Exception $e) {
            $this->addFlash('danger', "Erro ao processar resultado:" . $e->getMessage());
        }

        return $this->redirectToRoute('resultado_etapa', ['etapa' => $etapa->getId()]);
    }

    /**
     * @param FrigaEditalEtapa $etapa
     * @return RedirectResponse
     */
    public function removerResultadoAction(Request $request, FrigaEditalEtapa $etapa)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            if ($etapa->getPeriodoHabilitado() and !$this->isGranted('ROLE_AVALIADOR')) {
                return $this->redirectToRoute('nte_admin_painel_homepage');
            }
        }
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        try {
            //Remove o resultado anterior
            if ($etapa->getClassificacao()->count()) {
                /** @var FrigaClassificacao $resultado */
                foreach ($etapa->getClassificacao() as $resultado) {
                    $em->remove($resultado);
                    $em->flush();
                }
                $this->addFlash('success', "Classificação removida!");
            }
        } catch (Exception $e) {
            $this->addFlash('danger', "Erro ao remover classificação");
        }
        return $this->redirectToRoute('resultado_etapa', ['etapa' => $etapa->getId()]);
    }

    /**
     * @param FrigaEditalEtapa $etapa
     * @param ArrayCollection $resultado
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function processarResultado(FrigaEditalEtapa $etapa, ArrayCollection $resultado)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var stdClass $item */
        foreach ($resultado as $item) {
            $pos = 1;

            /** @var FrigaClassificacao|null $tmp */
            $tmp = null;
            $empates = [];
            /** @var FrigaInscricao $inscricao */
            foreach ($item->classificacao as $inscricao) {

                //Se etapa final, então marcar inscrição como classificado;
                if ($etapa->getFinal()) {
                    if ($inscricao->getIdSituacao() == 2
                        or $inscricao->getIdSituacao() == 4
                        or $inscricao->getIdSituacao() == 7
                    ) {
                        $inscricao->setIdSituacaoAnterior($inscricao->getIdSituacao());
                        $inscricao->setIdSituacao(6);
                        $em->persist($inscricao);
                        $em->flush();
                    }
                }

                $classificacao = new FrigaClassificacao();
                $classificacao->setIdInscricao($inscricao)
                    ->setValor($inscricao->getPontuacaoSoma(true))
                    ->setIdEtapa($etapa)
                    ->setPosicaoAnterior(-1)
                    ->setIdEdital($etapa->getIdEdital())
                    ->setIdSituacao($inscricao->getIdSituacao());
                if ($item->lista) {
                    $classificacao->setIdCota($item->lista);
                }
                if ($item->cargo) {
                    $classificacao->setIdCargo($item->cargo);
                }

                //Não mostrar não homologados, desclassificados...
                if ($etapa->getDesconsiderarInscricao()) {
                    if ($inscricao->getIdSituacao() == 2
                        or $inscricao->getIdSituacao() == 4
                        or $inscricao->getIdSituacao() == 6
                        or $inscricao->getIdSituacao() == 7
                    ) {

                        $classificacao->setPosicao($pos);
                        $pos++;
                        $em->persist($classificacao);
                        $em->flush();
                    } else {
                        continue;
                    }

                } else {
                    //se não homologado, não avaliado, ou desclassificado então ultimas posicões
                    if ($inscricao->getIdSituacao() == 0
                        or $inscricao->getIdSituacao() == 1
                        or $inscricao->getIdSituacao() == 3
                        or $inscricao->getIdSituacao() == 5) {
                        $classificacao->setPosicao(999999);
                    } else {
                        $classificacao->setPosicao($pos);
                        $pos++;
                    }
                    $em->persist($classificacao);
                    $em->flush();
                }
                // Verifica se existe posição na lista de classificação
                // A pontuação da posição anterior é comparada com a pontuação posição atual
                // existindo condição de empate, ativa-se a flag da empate nas duas posições
                if ($classificacao->getPosicao() != 999999) {
                    if ($tmp and bccomp($tmp->getValor(), $classificacao->getValor(), 6) == 0) {
                        $tmp->setEmpate(1);
                        $classificacao->setEmpate(1);
                        $em->persist($classificacao);
                        $em->persist($tmp);
                        $em->flush();

                        if (array_key_exists((string)$classificacao->getValor(), $empates)) {
                            if (!$empates[(string)$classificacao->getValor()]->contains($tmp)) {
                                $empates[(string)$classificacao->getValor()]->add($tmp);
                            }
                            if (!$empates[(string)$classificacao->getValor()]->contains($classificacao)) {
                                $empates[(string)$classificacao->getValor()]->add($classificacao);
                            }
                        } else {
                            $empates[(string)$classificacao->getValor()] = new ArrayCollection();
                            $empates[(string)$classificacao->getValor()]->add($tmp);
                            $empates[(string)$classificacao->getValor()]->add($classificacao);
                        };
                    }
                    $tmp = $classificacao;
                }
            }

            //Aplica os critérios de desempate
            if (count($empates)) {

                /**
                 * @var float $pontuacao
                 * @var  ArrayCollection $classificacao
                 */
                foreach ($empates as $pontuacao => $classificacao) {
                    $criterio = $etapa->getIdEdital()->getDesempate();
                    $c = $classificacao->toArray();
                    $pos = $classificacao->first()->getPosicao();
                    foreach ($c as $cc) {
                        $cc->setPosicaoAnterior($cc->getPosicao());
                    }
                    uasort($c, function ($a, $b) use ($criterio) {
                        return $this->condicaoEmpate($criterio, $a, $b);
                    });
                    foreach ($c as $chave => $cc) {
                        $cc->setPosicao($pos);
                        $em->persist($cc);
                        $em->flush();
                        $pos++;
                    }
                }

            }
        }
        $this->addFlash('success', "Classificação processada!");
    }

    /**
     * Verifica a condição de empate
     *
     * @param ArrayCollection $criterio
     * @param FrigaClassificacao $a
     * @param FrigaClassificacao $b
     * @return  mixed
     */
    public function condicaoEmpate(ArrayCollection $criterio, FrigaClassificacao $a, FrigaClassificacao $b)
    {
        //Se não houver critério, então desempate manual.
        if ($criterio->isEmpty()) {
            $b->setEmpate(1);
            $a->setEmpate(1);
            return 0;
        }

        $valorA = 0;
        $valorB = 0;

        $obs = $criterio->first()->getObj()->regra;
        $logico = 0;
        switch ($criterio->first()->getContexto()) {
            case FrigaInscricao::class:
                $prop = "get" . ucfirst($criterio->first()->getPropriedade());
                $valorA = $a->getIdInscricao()->$prop();
                $valorB = $b->getIdInscricao()->$prop();

                $xA = $valorA;
                $xB = $valorB;

                //Valor A
                if (is_object($valorA)) {
                    if (get_class($valorA) == 'DateTime') {
                        $xA = $valorA->format('d/m/Y');
                    } else {
                        $xA = serialize($valorA);
                    }
                }

                //Valor B
                if (is_object($valorB)) {
                    if (get_class($valorB) == 'DateTime') {
                        $xB = $valorB->format('d/m/Y');
                    } else {
                        $xB = serialize($valorB);
                    }
                }

                $obs .= " (" . $a->getIdInscricao()->getNome() . ": " . $xA;
                $obs .= "|";
                $obs .= $b->getIdInscricao()->getNome() . ": " . $xB . ")";
                //$obs .= " (" . $xA . " " . $criterio->first()->getObj()->sentido . " " . $xB . ") ";
                $logico = ($valorB <=> $valorA);
                break;
            case FrigaEditalPontuacao::class:
                $valorA = $a->getIdInscricao()->getPontuacaoAvaliacaoItemValor($criterio->first()->getContextoObjeto());
                $valorB = $b->getIdInscricao()->getPontuacaoAvaliacaoItemValor($criterio->first()->getContextoObjeto());
                $obs .= " (" . $a->getIdInscricao()->getNome() . ": " . $valorA;
                $obs .= "|";
                $obs .= $b->getIdInscricao()->getNome() . ": " . $valorB . ")";
                $logico = bccomp($valorB, $valorA, 5);
                break;
            case FrigaEditalPontuacaoCategoria::class:
                $valorA = $a->getIdInscricao()->getPontuacaoSomaCategoria(true, $criterio->first()->getContextoObjeto());
                $valorB = $b->getIdInscricao()->getPontuacaoSomaCategoria(true, $criterio->first()->getContextoObjeto());

                $obs .= " (" . $a->getIdInscricao()->getNome() . ": " . $valorA;
                $obs .= "|";
                $obs .= $b->getIdInscricao()->getNome() . ": " . $valorB . ")";
                $logico = bccomp($valorB, $valorA, 5);
                break;
        }

        if ($criterio->first()->getSentido() != $logico and $logico == 0) {
            $tmp = clone $criterio;
            $tmp->removeElement($criterio->first());
            if ($tmp->count()) {
                return $this->condicaoEmpate($tmp, $a, $b);
            }
            $b->setEmpate(1);
            $a->setEmpate(1);
            return 0;
        } else if ($criterio->first()->getSentido() == $logico) {
            $b->setObservacao($obs);
            $b->setEmpate(0);
            $a->setEmpate(0);
            return 1;
        } else {
            $a->setObservacao($obs);
            $b->setEmpate(0);
            $a->setEmpate(0);
            return -1;
        }
    }

    /**
     * Altera a posição de duas inscrições
     * @param integer $comparacao
     * @param FrigaClassificacao $a
     * @param FrigaClassificacao $b
     */
    public function condicaoEmpateComparacao($comparacao, $obs, FrigaClassificacao $a, FrigaClassificacao $b)
    {
        $a->setObservacao($obs);
        $b->setObservacao($obs);
        if ($comparacao) {
            $a->setPosicaoAnterior($a->getPosicao());
            $a->setPosicao($b->getPosicao());
            $b->setPosicaoAnterior($b->getPosicao());
            $b->setPosicao($a->getPosicaoAnterior());
        } else {
            $b->setPosicaoAnterior($b->getPosicao());
            $b->setPosicao($a->getPosicao());
            $a->setPosicaoAnterior($a->getPosicao());
            $a->setPosicao($b->getPosicaoAnterior());
        }

    }

    /**
     * @param FrigaEditalEtapa $etapa
     * @return ArrayCollection
     * @throws Exception
     */
    private function gerarObjetoClassificacao(FrigaEditalEtapa $etapa)
    {

        $edital = $etapa->getIdEdital();
        $classificacao = new ArrayCollection();

        $geral = $etapa->getClassificacao()->getIterator();

        $geral->uasort(function (FrigaClassificacao $a, FrigaClassificacao $b) {
            return $a->getPosicao() <=> $b->getPosicao();
        });
        $geral = new ArrayCollection($geral->getArrayCopy());

        if ($edital->isResultado0() or $edital->isResultado1()) {
            /** @var FrigaEditalCargo $cargo */
            foreach ($edital->getCargo() as $cargo) {
                if ($edital->isResultado0()) {
                    if ($edital->getCota()->count()) {
                        /** @var FrigaEditalCota $lista */
                        foreach ($edital->getCota() as $lista) {
                            $obj = new stdClass();
                            $obj->nome = $cargo->getDescricao() . "/" . $lista->getDescricao();
                            $obj->cargo = $cargo;
                            $obj->lista = $lista;
                            $obj->classificacao = $geral->filter(function (FrigaClassificacao $c) use ($cargo, $lista) {
                                return $c->getIdCargo()->getId() == $cargo->getId()
                                    and $c->getIdCota()->getId() == $lista->getId();
                            });
                            $classificacao->add($obj);
                        }
                    }
                }
                if ($edital->isResultado1()) {
                    $obj = new stdClass();
                    $obj->nome = "Classificação Geral / " . $cargo->getDescricao();
                    $obj->cargo = $cargo;
                    $obj->lista = null;
                    $obj->classificacao = $geral->filter(function (FrigaClassificacao $c) use ($cargo) {
                        return $c->getIdCargo()->getId() == $cargo->getId()
                            and $c->getIdCota() == null;
                    });
                    $classificacao->add($obj);
                }
            }
        }
        if ($edital->isResultado2()) {
            foreach ($edital->getCota() as $lista) {
                $obj = new stdClass();
                $obj->nome = "Classificação Geral/" . $lista->getDescricao();
                $obj->cargo = null;
                $obj->lista = $lista;
                $obj->classificacao = $geral->filter(function (FrigaClassificacao $c) use ($lista) {
                    return $c->getIdCota()->getId() == $lista->getId()
                        and $c->getIdCargo() == null;
                });
                $classificacao->add($obj);
            }
        }
        if ($edital->isResultado3()) {
            $obj = new stdClass();
            $obj->nome = "Classificação Geral";
            $obj->cargo = null;
            $obj->lista = null;
            $obj->classificacao = $geral->filter(function (FrigaClassificacao $c) {
                return $c->getIdCota() == null and $c->getIdCargo() == null;
            });
            $classificacao->add($obj);
        }
        return $classificacao;
    }

    /**
     * @param Request $request
     * @param FrigaEditalEtapa $etapa
     * @return Response
     * @throws Exception
     */
    public function indexEtapaAction(Request $request, FrigaEditalEtapa $etapa)
    {
        return $this->render('NteAplicacaoFrigaBundle:Resultado:index-etapa.html.twig', [
            'etapa' => $etapa,
            'edital' => $etapa->getIdEdital(),
            'classificacao' => $this->gerarObjetoClassificacao($etapa)
        ]);
    }


    /**
     * @param Request $request
     * @param FrigaEditalEtapa $etapa
     * @param FrigaClassificacao $c
     * @return RedirectResponse
     */
    public function subirPosicaoAction(Request $request, FrigaEditalEtapa $etapa, FrigaClassificacao $c)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        if ($c->getEmpate() and $c->getPosicao() > 1) {


            $posAnterior = $em->getRepository(FrigaClassificacao::class)
                ->createQueryBuilder('c')
                ->where('c.empate = 1 and c.idEtapa = :etapa')
                ->andWhere('c.posicao = :posicao')
                ->setParameter('etapa', $etapa)
                ->setParameter('posicao', $c->getPosicao() - 1);

            if($etapa->getIdEdital()->isResultado3() and is_null($c->getIdCota())){
                $posAnterior->andWhere('c.idCota is null');
            }else{
                $posAnterior->andWhere('c.idCota = :cota')->setParameter('cota', $c->getIdCota());
            }
            if($etapa->getIdEdital()->isResultado3() and is_null($c->getIdCargo())){
                $posAnterior->andWhere('c.idCargo is null');
            }else{
                $posAnterior->andWhere('c.idCargo = :cargo')->setParameter('cargo', $c->getIdCargo());
            }

            $posAnterior = new ArrayCollection($posAnterior->getQuery()->getResult());

            /** @var FrigaClassificacao $posAnterior */
            $posAnterior = $posAnterior->first();



            if ($posAnterior) {
                //Troca a posiçãq anterior para  proxima
                $posAnterior->setPosicao($c->getPosicao());
                $c->setPosicao($c->getPosicao() - 1);

                //Configura a posição anterior
                if ($posAnterior->getPosicaoAnterior() == -1) {
                    $posAnterior->setPosicaoAnterior($posAnterior->getPosicao());
                }
                if ($c->getPosicaoAnterior() == -1) {
                    $c->setPosicaoAnterior($c->getPosicao());
                }
                $em->persist($c);
                $em->persist($posAnterior);
                $em->flush();
            }
        }
        return $this->redirectToRoute('resultado_etapa', ['etapa' => $etapa->getId()]);
    }

    /**
     * @param Request $request
     * @param FrigaEditalEtapa $etapa
     * @return RedirectResponse
     */
    public function confirmarPosicaoAction(Request $request, FrigaEditalEtapa $etapa)
    {
        $em = $this->getDoctrine()->getManager();
        if ($request->request->get('c') and $request->request->get('o')) {
            /** @var FrigaClassificacao $c */
            $c = $em->find(FrigaClassificacao::class, $request->request->get('c'));
            if ($c and $c->getEmpate()) {
                $c->setObservacao($request->request->get('o'));
                $c->setEmpate(null);
                $em->persist($c);
                $em->flush();
            }
        }

        return $this->redirectToRoute('resultado_etapa', ['etapa' => $etapa->getId()]);
    }

    /**
     *
     * @param Request $request
     * @return Response
     */
    public function formComprovanteAction(Request $request, FrigaEditalEtapa $etapa, FrigaArquivo $arquivo)
    {
        return $this->render('NteAplicacaoFrigaBundle:avaliacao:form-comprovante.html.twig', [
            'etapa' => $etapa,
            'arquivo' => $arquivo,
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
            $editais = $em->getRepository(FrigaEdital::class)->findAll();
        } else if ($this->isGranted('ROLE_AVALIADOR')) {

            /** @var ArrayCollection $tmp */
            $tmp = $this->getUser()
                ->getIdEditalUsuario();
            /*->filter(function (FrigaEditalUsuario $eu) use ($situacao) {
                return $eu->getIdEdital()->getSituacao() == $situacao;
            });*/
            if ($tmp->count()) {
                /** @var FrigaEditalUsuario $eu */
                foreach ($tmp as $eu) {
                    if (!$editais->contains($eu->getIdEdital())) {
                        $editais->add($eu->getIdEdital());
                    }
                };
                $editais = $editais->filter(function (FrigaEdital $edital) {
                    return $edital->getEtapa()->filter(function (FrigaEditalEtapa $etapa) {
                        return true; // $etapa->getPeriodoHabilitado();
                    })->count();
                });
            }
        }
        return $editais;
    }

    /**
     * @param Request $request
     * @param FrigaEditalEtapa $etapa
     * @return BinaryFileResponse
     */
    public function exportarCsvAction(Request $request, FrigaEditalEtapa $etapa)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var QueryBuilder $convocacao */
        $convocacao = $em
            ->getRepository(FrigaClassificacao::class)
            ->createQueryBuilder('c')
            ->innerJoin('c.idInscricao', 'i')
            ->where('c.idEtapa = :etapa')
            ->setParameter('etapa', $etapa)
            ->getQuery()->getResult();

        $arquivo = "/tmp/classificacao-" . $etapa->getId() . ".csv";
        if (is_file($arquivo)) {
            unlink($arquivo);
        }
        $out = fopen($arquivo, 'w');

        fputcsv($out, [
            'Etapa',
            'Cargo',
            'Lista',
            'Inscricao',
            'Nome',
            'E-mail',
            'Situacao',
            "Pontuacao",
            "Classificacao",
            "Observacao",
        ]);
        /** @var FrigaClassificacao $p */
        foreach ($convocacao as $p) {
            if ($p->getPosicao() == 999999) {
                $p->setPosicao("-");
            }
            fputcsv($out, [
                $p->getIdEtapa()->getDescricao(),
                $p->getIdCargo() ? $p->getIdCargo()->getDescricao() : "",
                $p->getIdCota() ? $p->getIdCota()->getDescricao() : "",
                $p->getIdInscricao()->getUuid(),
                strtoupper($p->getIdInscricao()->getNome()),
                $p->getIdInscricao()->getContatoEmail(),
                $p->getObjsituacao()->descricao,
                $p->getValor(),
                $p->getPosicao(),
                $p->getObservacao(),
            ]);
        }
        fclose($out);
        return $this->file($arquivo);
    }

    /**
     * @param Request $request
     * @param FrigaEditalEtapa $etapa
     * @return Response
     * @throws Exception
     */
    public function impresaoAction(Request $request, FrigaEditalEtapa $etapa)
    {
        $em = $this->getDoctrine()->getManager();

        return $this->render('NteAplicacaoFrigaBundle:Resultado:resultado-final-impressao.html.twig', [
            'etapa' => $etapa,
            'edital' => $etapa->getIdEdital(),
            'classificacao' => $this->gerarObjetoClassificacao($etapa),
        ]);
    }

}