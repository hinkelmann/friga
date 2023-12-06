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
use Nte\Aplicacao\FrigaBundle\Entity\FrigaClassificacao;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalCargo;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalCota;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalEtapa;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricao;

trait ClassificacaoTrait
{
    /**
     * @return ArrayCollection
     *
     * @throws \Exception
     */
    private function gerarObjetoClassificacao(FrigaEditalEtapa $etapa)
    {
        $edital = $etapa->getIdEdital();
        $classificacao = new ArrayCollection();

        if ($this->isGranted('ROLE_ADMIN')) {
            $geral = $etapa->getClassificacaoCargo()->getIterator();
            $editalCargoUsuario = $edital->getArrayIdEditalCargo();
        } else {
            $geral = $etapa->getClassificacaoCargo($this->getUser())->getIterator();
            $editalCargoUsuario = $edital->getUsuarioEditalCargos($this->getUser());
        }

        $geral->uasort(function(FrigaClassificacao $a, FrigaClassificacao $b) {
            return $a->getPosicao() <=> $b->getPosicao();
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

    /**
     * @return ArrayCollection
     *
     * @throws \Exception
     */
    private function gerarObjetoParcial(FrigaEditalEtapa $etapa)
    {
        $classificacao = new ArrayCollection();

        $edital = $etapa->getIdEdital();

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

        return $classificacao;
    }
}
