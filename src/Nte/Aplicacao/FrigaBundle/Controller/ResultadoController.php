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
use Doctrine\ORM\QueryBuilder;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaArquivo;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaClassificacao;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEdital;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalCargo;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalCota;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalEtapa;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalEtapaUsuario;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalPontuacao;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalPontuacaoCategoria;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalUsuario;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricao;
use Nte\Aplicacao\FrigaBundle\Form\FrigaClassificacaoType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AvaliacaoController.
 */
class ResultadoController extends Controller
{
    use ClassificacaoTrait;

    /**
     * Editais em aberto.
     *
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
            $x = $edital->getEtapa()->filter(function(FrigaEditalEtapa $etapa) use ($situacao) {
                if (50 == $situacao) {
                    return 4 == $etapa->getTipo() and $etapa->getPeriodoHabilitado();
                } elseif (100 == $situacao) {
                    return 4 == $etapa->getTipo()
                        and false == $etapa->getPeriodoHabilitado()
                        and 100 == $etapa->getAndamentoPrazo();
                } elseif (0 == $situacao) {
                    return 4 == $etapa->getTipo()
                        and false == $etapa->getPeriodoHabilitado()
                        and 0 == $etapa->getAndamentoPrazo();
                }
            });
            $tmp = new ArrayCollection(\array_merge($tmp->toArray(), $x->toArray()));
        }

        return $this->render('NteAplicacaoFrigaBundle:Resultado:index.html.twig', [
            'editais' => $tmp,
        ]);
    }

    /**
     * @return RedirectResponse|Response
     *
     * @throws \Exception
     */
    public function parcialAction(Request $request, FrigaEditalEtapa $etapa)
    {
        if (!$this->isGranted('ROLE_ADMIN') and !$this->isGranted('ROLE_AVALIADOR')) {
            return $this->redirectToRoute('nte_admin_painel_homepage');
        }

        $classificacao = $this->gerarObjetoParcial($etapa);
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
            'edital' => $etapa->getIdEdital(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return RedirectResponse
     */
    public function gerarResultadoAction(Request $request, FrigaEditalEtapa $etapa)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            if ($etapa->getPeriodoHabilitado() and !$this->isGranted('ROLE_AVALIADOR')) {
                return $this->redirectToRoute('resultado_etapa', ['etapa' => $etapa->getId()]);
            }
        }

        if ($this->isGranted('ROLE_ADMIN')) {
            $geral = $etapa->getClassificacaoCargo();
        } else {
            $geral = $etapa->getClassificacaoCargo($this->getUser());
        }

        if (!$geral->isEmpty()) {
            return $this->redirectToRoute('resultado_etapa', ['etapa' => $etapa->getId()]);
        }

        $edital = $etapa->getIdEdital();

        try {
            $classificacao = new ArrayCollection();
            if ($this->isGranted('ROLE_ADMIN')) {
                $geral = $edital->getInscricaoValida(false, $etapa->getIdEtapaCategoria())->getIterator();
                $editalCargoUsuario = $edital->getArrayIdEditalCargo();
            } else {
                $geral = $edital->getInscricaoValida($this->getUser(), $etapa->getIdEtapaCategoria())->getIterator();
                $editalCargoUsuario = $edital->getUsuarioEditalCargos($this->getUser());
            }
            $geral->uasort(function(FrigaInscricao $a, FrigaInscricao $b) {
                return \bccomp($b->getPontuacaoSoma(true), $a->getPontuacaoSoma(true), 6);
            });

            $geral = new ArrayCollection($geral->getArrayCopy());

            if ($edital->isResultado0() or $edital->isResultado1()) {
                /** @var FrigaEditalCargo $cargo */
                foreach ($edital->getCargo() as $cargo) {
                    if (!\in_array($cargo->getId(), $editalCargoUsuario)) {
                        continue;
                    }
                    if ($edital->isResultado0()) {
                        if ($edital->getCota()->count()) {
                            /** @var FrigaEditalCota $lista */
                            foreach ($edital->getCota() as $lista) {
                                $obj = new \stdClass();
                                $obj->nome = $cargo->getDescricao() . '/' . $lista->getDescricao();
                                $obj->cargo = $cargo;
                                $obj->lista = $lista;
                                $obj->classificacao = $geral->filter(function(FrigaInscricao $inscricao) use ($cargo, $lista) {
                                    return $inscricao->getIdCargo()->getId() == $cargo->getId()
                                        and $inscricao->getIdCota()->getId() == $lista->getId();
                                });
                                $classificacao->add($obj);
                            }
                        }
                    }
                    if ($edital->isResultado1()) {
                        $obj = new \stdClass();
                        $obj->nome = 'Classificação Geral / ' . $cargo->getDescricao();
                        $obj->cargo = $cargo;
                        $obj->lista = null;
                        $obj->classificacao = $geral->filter(function(FrigaInscricao $inscricao) use ($cargo) {
                            return $inscricao->getIdCargo()->getId() == $cargo->getId();
                        });
                        $classificacao->add($obj);
                    }
                }
            }

            if ($edital->isResultado2()) {
                foreach ($edital->getCota() as $lista) {
                    $obj = new \stdClass();
                    $obj->nome = 'Classificação Geral/' . $lista->getDescricao();
                    $obj->cargo = null;
                    $obj->lista = $lista;
                    $obj->classificacao = $geral->filter(function(FrigaInscricao $inscricao) use ($lista) {
                        return $inscricao->getIdCota()->getId() == $lista->getId();
                    });
                    $classificacao->add($obj);
                }
            }

            if ($edital->isResultado3()) {
                $obj = new \stdClass();
                $obj->nome = 'Classificação Geral';
                $obj->cargo = null;
                $obj->lista = null;
                $obj->classificacao = $geral;
                $classificacao->add($obj);
            }

            $this->processarResultado($etapa, $classificacao);
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Erro ao processar resultado:' . $e->getMessage());
        }

        return $this->redirectToRoute('resultado_etapa', ['etapa' => $etapa->getId()]);
    }

    /**
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
            if ($this->isGranted('ROLE_ADMIN')) {
                $geral = $etapa->getClassificacaoCargo();
            } else {
                $geral = $etapa->getClassificacaoCargo($this->getUser());
            }
            //Remove o resultado anterior
            if ($geral->count()) {
                /** @var FrigaClassificacao $resultado */
                foreach ($geral as $resultado) {
                    $em->remove($resultado);
                    $em->flush();
                }
                $this->addFlash('success', 'Classificação removida!');
            }
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Erro ao remover classificação');
        }

        return $this->redirectToRoute('resultado_etapa', ['etapa' => $etapa->getId()]);
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function processarResultado(FrigaEditalEtapa $etapa, ArrayCollection $resultado)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var \stdClass $item */
        foreach ($resultado as $item) {
            $pos = 1;

            /** @var FrigaClassificacao|null $tmp */
            $tmp = null;
            $empates = [];
            /** @var FrigaInscricao $inscricao */
            foreach ($item->classificacao as $inscricao) {
                $etapaUsuario = new FrigaEditalEtapaUsuario($etapa, $inscricao, $this->getUser());
                $em->persist($etapaUsuario);
                $em->flush();

                //Se etapa final, então marcar inscrição como classificado;
                if ($etapa->getFinal()) {
                    if (2 == $inscricao->getIdSituacao()
                        or 4 == $inscricao->getIdSituacao()
                        or 7 == $inscricao->getIdSituacao()
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
                    if (2 == $inscricao->getIdSituacao()
                        or 4 == $inscricao->getIdSituacao()
                        or 6 == $inscricao->getIdSituacao()
                        or 7 == $inscricao->getIdSituacao()
                    ) {
                        $classificacao->setPosicao($pos);
                        ++$pos;
                        $em->persist($classificacao);
                        $em->flush();
                    } else {
                        continue;
                    }
                } else {
                    //se não homologado, não avaliado, ou desclassificado então ultimas posicões
                    if (0 == $inscricao->getIdSituacao()
                        or 1 == $inscricao->getIdSituacao()
                        or 3 == $inscricao->getIdSituacao()
                        or 5 == $inscricao->getIdSituacao()) {
                        $classificacao->setPosicao(999999);
                    } else {
                        $classificacao->setPosicao($pos);
                        ++$pos;
                    }
                    $em->persist($classificacao);
                    $em->flush();
                }
                // Verifica se existe posição na lista de classificação
                // A pontuação da posição anterior é comparada com a pontuação posição atual
                // existindo condição de empate, ativa-se a flag da empate nas duas posições
                if (999999 != $classificacao->getPosicao()) {
                    if ($tmp and 0 == \bccomp($tmp->getValor(), $classificacao->getValor(), 6)) {
                        $tmp->setEmpate(1);
                        $classificacao->setEmpate(1);
                        $em->persist($classificacao);
                        $em->persist($tmp);
                        $em->flush();

                        if (\array_key_exists((string) $classificacao->getValor(), $empates)) {
                            if (!$empates[(string) $classificacao->getValor()]->contains($tmp)) {
                                $empates[(string) $classificacao->getValor()]->add($tmp);
                            }
                            if (!$empates[(string) $classificacao->getValor()]->contains($classificacao)) {
                                $empates[(string) $classificacao->getValor()]->add($classificacao);
                            }
                        } else {
                            $empates[(string) $classificacao->getValor()] = new ArrayCollection();
                            $empates[(string) $classificacao->getValor()]->add($tmp);
                            $empates[(string) $classificacao->getValor()]->add($classificacao);
                        }
                    }
                    $tmp = $classificacao;
                }
            }

            //Aplica os critérios de desempate
            if (\count($empates)) {
                /**
                 * @var float $pontuacao
                 * @var ArrayCollection $classificacao
                 */
                foreach ($empates as $pontuacao => $classificacao) {
                    $criterio = $etapa->getIdEdital()->getDesempate();
                    $c = $classificacao->toArray();
                    $pos = $classificacao->first()->getPosicao();
                    foreach ($c as $cc) {
                        $cc->setPosicaoAnterior($cc->getPosicao());
                    }
                    \uasort($c, function($a, $b) use ($criterio) {
                        return $this->condicaoEmpate($criterio, $a, $b);
                    });
                    foreach ($c as $chave => $cc) {
                        $cc->setPosicao($pos);
                        $em->persist($cc);
                        $em->flush();
                        ++$pos;
                    }
                }
            }
        }
        $this->addFlash('success', 'Classificação processada!');
    }

    /**
     * Verifica a condição de empate.
     *
     * @return mixed
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
            // Critérios com base nas propriedades da entidade inscrição
            case FrigaInscricao::class:
                $prop = 'get' . \ucfirst($criterio->first()->getPropriedade());
                $valorA = $a->getIdInscricao()->$prop();
                $valorB = $b->getIdInscricao()->$prop();

                $xA = $valorA;
                $xB = $valorB;
                $obs = '';

                switch ($criterio->first()->getPropriedade()) {
                    case 'dataNascimento':
                        //Valor A
                        if (\is_object($valorA)) {
                            if ('DateTime' == \get_class($valorA)) {
                                $xA = $valorA->format('d/m/Y');
                            } else {
                                $xA = \serialize($valorA);
                            }
                        }
                        //Valor B
                        if (\is_object($valorB)) {
                            if ('DateTime' == \get_class($valorB)) {
                                $xB = $valorB->format('d/m/Y');
                            } else {
                                $xB = \serialize($valorB);
                            }
                        }
                        $obs .= ' (' . $a->getIdInscricao()->getNome() . ': ' . $xA;
                        $obs .= '|';
                        $obs .= $b->getIdInscricao()->getNome() . ': ' . $xB . ')';
                        //$obs .= " (" . $xA . " " . $criterio->first()->getObj()->sentido . " " . $xB . ") ";
                        $logico = ($valorB <=> $valorA);
                        break;

                    case 'nome':
                        $logico = \strnatcmp(\strtolower($valorB), \strtolower($valorA));
                        break;

                    case 'matriculaIndiceDesempenho':
                        $logico = \bccomp(\floatval($valorB), \floatval($valorA), 6);
                        break;
                    default:
                        $logico = 0;
                        break;
                }

                break;
            case FrigaEditalPontuacao::class:
                $valorA = $a->getIdInscricao()->getPontuacaoAvaliacaoItemValor($criterio->first()->getContextoObjeto());
                $valorB = $b->getIdInscricao()->getPontuacaoAvaliacaoItemValor($criterio->first()->getContextoObjeto());
                $obs .= ' (' . $a->getIdInscricao()->getNome() . ': ' . $valorA;
                $obs .= '|';
                $obs .= $b->getIdInscricao()->getNome() . ': ' . $valorB . ')';
                $logico = \bccomp($valorB, $valorA, 5);
                break;
            case FrigaEditalPontuacaoCategoria::class:
                $valorA = $a->getIdInscricao()->getPontuacaoSomaCategoria(true, $criterio->first()->getContextoObjeto());
                $valorB = $b->getIdInscricao()->getPontuacaoSomaCategoria(true, $criterio->first()->getContextoObjeto());

                $obs .= ' (' . $a->getIdInscricao()->getNome() . ': ' . $valorA;
                $obs .= '|';
                $obs .= $b->getIdInscricao()->getNome() . ': ' . $valorB . ')';
                $logico = \bccomp($valorB, $valorA, 5);
                break;
        }

        if ($criterio->first()->getSentido() != $logico and 0 == $logico) {
            $tmp = clone $criterio;
            $tmp->removeElement($criterio->first());
            if ($tmp->count()) {
                return $this->condicaoEmpate($tmp, $a, $b);
            }
            $b->setEmpate(1);
            $a->setEmpate(1);

            return 0;
        } elseif ($criterio->first()->getSentido() == $logico) {
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
     * Altera a posição de duas inscrições.
     *
     * @param int $comparacao
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
     * @return Response
     *
     * @throws \Exception
     */
    public function indexEtapaAction(Request $request, FrigaEditalEtapa $etapa)
    {
        if (
            $this->isGranted('ROLE_ADMIN')
            or $this->getUser()->getPermissaoEdital($etapa->getIdEdital())
        ) {
            $geral = $etapa->getClassificacaoCargo();
        } else {
            $geral = $etapa->getClassificacaoCargo($this->getUser());
        }

        return $this->render('NteAplicacaoFrigaBundle:Resultado:index-etapa.html.twig', [
            'etapa' => $etapa,
            'edital' => $etapa->getIdEdital(),
            'geral' => $geral,
            'classificacao' => $this->gerarObjetoClassificacao($etapa),
        ]);
    }

    /**
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

            if ($etapa->getIdEdital()->isResultado3() and \is_null($c->getIdCota())) {
                $posAnterior->andWhere('c.idCota is null');
            } else {
                $posAnterior->andWhere('c.idCota = :cota')->setParameter('cota', $c->getIdCota());
            }
            if ($etapa->getIdEdital()->isResultado3() and \is_null($c->getIdCargo())) {
                $posAnterior->andWhere('c.idCargo is null');
            } else {
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
                if (-1 == $posAnterior->getPosicaoAnterior()) {
                    $posAnterior->setPosicaoAnterior($posAnterior->getPosicao());
                }
                if (-1 == $c->getPosicaoAnterior()) {
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
     *
     * @return array|ArrayCollection|object[]
     */
    public function editais($situacao = 1)
    {
        $em = $this->getDoctrine()->getManager();
        $editais = new ArrayCollection();
        if ($this->isGranted('ROLE_ADMIN')) {
            $editais = $em->getRepository(FrigaEdital::class)->findAll();
        } elseif ($this->isGranted('ROLE_AVALIADOR')) {
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
                }
                $editais = $editais->filter(function(FrigaEdital $edital) {
                    return $edital->getEtapa()->filter(function(FrigaEditalEtapa $etapa) {
                        return true; // $etapa->getPeriodoHabilitado();
                    })->count();
                });
            }
        }

        return $editais;
    }

    /**
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

        $arquivo = '/tmp/classificacao-' . $etapa->getId() . '.csv';
        if (\is_file($arquivo)) {
            \unlink($arquivo);
        }
        $out = \fopen($arquivo, 'w');

        \fputcsv($out, [
            'Etapa',
            'Cargo',
            'Lista',
            'Inscricao',
            'Nome',
            'E-mail',
            'Situacao',
            'Pontuacao',
            'Classificacao',
            'Observacao',
        ]);
        /** @var FrigaClassificacao $p */
        foreach ($convocacao as $p) {
            if (999999 == $p->getPosicao()) {
                $p->setPosicao('-');
            }
            \fputcsv($out, [
                $p->getIdEtapa()->getDescricao(),
                $p->getIdCargo() ? $p->getIdCargo()->getDescricao() : '',
                $p->getIdCota() ? $p->getIdCota()->getDescricao() : '',
                $p->getIdInscricao()->getUuid(),
                \strtoupper($p->getIdInscricao()->getNome()),
                $p->getIdInscricao()->getContatoEmail(),
                $p->getObjsituacao()->descricao,
                $p->getValor(),
                $p->getPosicao(),
                $p->getObservacao(),
            ]);
        }
        \fclose($out);

        return $this->file($arquivo);
    }

    /**
     * @return Response
     *
     * @throws \Exception
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
