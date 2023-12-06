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

namespace Nte\Aplicacao\FrigaBundle\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaClassificacao;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalCargo;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalCota;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalEtapa;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalPontuacao;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalPontuacaoCategoria;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricao;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CronCommand extends ContainerAwareCommand
{
    /** @var EntityManager */
    protected $em;

    protected function configure()
    {
        $this
            ->setName('friga:resultado:publicar')
            ->setDescription('Publicar resultados no site');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine')->getManager();

        $etapas = new ArrayCollection($this->em->createQueryBuilder()
            ->select('e')
            ->from(FrigaEditalEtapa::class, 'e')
            ->leftJoin('e.classificacao', 'c')
            ->where('e.tipo = 4')
            ->getQuery()->getResult());

        /** @var FrigaEditalEtapa $etapa */
        foreach ($etapas as $etapa) {
            echo "\n";
            echo '[' . $etapa->getClassificacao()->count() . "]\t";
            if ($etapa->getClassificacao()->count()) {
                echo "Feito\t";
            } else {
                // $etapa->divulgacao() && $etapa->cron() ?? $this->processarResultado($etapa);
                echo "...\t";
            }
            echo $etapa->getDescricao();
        }

        echo "\o\n";
    }

    public function processarResultado(FrigaEditalEtapa $etapa, ArrayCollection $resultado)
    {
        $em = $this->getDoctrine()->getManager();
        //Remove o resultado anterior
        if ($etapa->getClassificacao()->count()) {
            /** @var FrigaClassificacao $resultado */
            foreach ($etapa->getClassificacao() as $resultado) {
                $em->remove($resultado);
                $em->flush();
            }
        }
        /** @var \stdClass $item */
        foreach ($resultado as $item) {
            $pos = 1;

            /** @var FrigaClassificacao|null $tmp */
            $tmp = null;
            $empates = [];
            /** @var FrigaInscricao $inscricao */
            foreach ($item->classificacao as $inscricao) {
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
                //A pontuação da posição anterior é comparada com a pontuação posição atual
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
    }

    /**
     * Verifica a condição de empate.
     *
     * @return mixed
     */
    public function condicaoEmpate(ArrayCollection $criterio, FrigaClassificacao $a, FrigaClassificacao $b)
    {
        $valorA = 0;
        $valorB = 0;
        $obs = $criterio->first()->getObj()->regra;

        $logico = 0;
        switch ($criterio->first()->getContexto()) {
            case FrigaInscricao::class:
                $prop = 'get' . \ucfirst($criterio->first()->getPropriedade());
                $valorA = $a->getIdInscricao()->$prop();
                $valorB = $b->getIdInscricao()->$prop();

                $xA = $valorA;
                $xB = $valorB;

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
     * @return ArrayCollection
     *
     * @throws \Exception
     */
    private function gerarObjetoClassificacao(FrigaEditalEtapa $etapa)
    {
        $edital = $etapa->getIdEdital();
        $classificacao = new ArrayCollection();

        $geral = $etapa->getClassificacao()->getIterator();

        $geral->uasort(function(FrigaClassificacao $a, FrigaClassificacao $b) {
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
                            $obj = new \stdClass();
                            $obj->nome = $cargo->getDescricao() . '/' . $lista->getDescricao();
                            $obj->cargo = $cargo;
                            $obj->lista = $lista;
                            $obj->classificacao = $geral->filter(function(FrigaClassificacao $c) use ($cargo, $lista) {
                                return $c->getIdCargo()->getId() == $cargo->getId()
                                    and $c->getIdCota()->getId() == $lista->getId();
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
                    $obj->classificacao = $geral->filter(function(FrigaClassificacao $c) use ($cargo) {
                        return $c->getIdCargo()->getId() == $cargo->getId()
                            and null == $c->getIdCota();
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
                $obj->classificacao = $geral->filter(function(FrigaClassificacao $c) use ($lista) {
                    return $c->getIdCota()->getId() == $lista->getId()
                        and null == $c->getIdCargo();
                });
                $classificacao->add($obj);
            }
        }
        if ($edital->isResultado3()) {
            $obj = new \stdClass();
            $obj->nome = 'Classificação Geral';
            $obj->cargo = null;
            $obj->lista = null;
            $obj->classificacao = $geral->filter(function(FrigaClassificacao $c) {
                return null == $c->getIdCota() and null == $c->getIdCargo();
            });
            $classificacao->add($obj);
        }

        return $classificacao;
    }
}
