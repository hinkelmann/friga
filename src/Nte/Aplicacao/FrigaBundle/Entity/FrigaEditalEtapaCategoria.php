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

namespace Nte\Aplicacao\FrigaBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;

/**
 * FrigaEditalEtapaCategoria.
 *
 * @ORM\Table(name="friga_edital_etapa_categoria")
 *
 * @ORM\Entity
 *
 * @ORM\HasLifecycleCallbacks()
 */
class FrigaEditalEtapaCategoria
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     *
     * @ORM\Id
     *
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var FrigaEdital
     *
     * @ORM\ManyToOne(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaEdital", inversedBy="idEtapaCategoria")
     *
     * @ORM\JoinColumns({
     *
     *   @ORM\JoinColumn(name="id_edital", referencedColumnName="id")
     * })
     */
    private $idEdital;

    /**
     * @var int
     *
     * @ORM\Column(name="ordem", type="integer", nullable=true)
     */
    private $ordem;

    /**
     * @var string
     *
     * @ORM\Column(name="descricao", type="text", length=65535, nullable=true)
     */
    private $descricao;

    /**
     * @var string
     *
     * @ORM\Column(name="observacao", type="text", length=65535, nullable=true)
     */
    private $observacao;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalEtapa", mappedBy="idEtapaCategoria")
     */
    private $idEtapa;

    public function __construct()
    {
        $this->idEtapa = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return FrigaEditalEtapaCategoria
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return FrigaEdital
     */
    public function getIdEdital()
    {
        return $this->idEdital;
    }

    /**
     * @param FrigaEdital $idEdital
     *
     * @return FrigaEditalEtapaCategoria
     */
    public function setIdEdital($idEdital)
    {
        $this->idEdital = $idEdital;

        return $this;
    }

    /**
     * @return int
     */
    public function getOrdem()
    {
        return $this->ordem;
    }

    /**
     * @param int $ordem
     *
     * @return FrigaEditalEtapaCategoria
     */
    public function setOrdem($ordem)
    {
        $this->ordem = $ordem;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * @param string $descricao
     *
     * @return FrigaEditalEtapaCategoria
     */
    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;

        return $this;
    }

    /**
     * @return string
     */
    public function getObservacao()
    {
        return $this->observacao;
    }

    /**
     * @param string $observacao
     *
     * @return FrigaEditalEtapaCategoria
     */
    public function setObservacao($observacao)
    {
        $this->observacao = $observacao;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getIdEtapa()
    {
        return $this->idEtapa;
    }

    /**
     * @ORM\PreRemove
     *
     * @throws \Exception
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        if ($this->idEtapa->count()) {
            /** @var FrigaEditalEtapa $item */
            foreach ($this->idEtapa as $item) {
                $item->setIdEtapaCategoria(null);
                $args->getObjectManager()->persist($item);
                $args->getObjectManager()->flush();
            }
        }
    }

    /**
     * @return null|FrigaEditalEtapa
     */
    public function getPeriodoInscricao()
    {
        /** @var FrigaEditalEtapa $etapa */
        foreach ($this->idEtapa as $etapa) {
            if (1 == $etapa->getTipo()) {
                return $etapa;
            }
        }

        return null;
    }

    /**
     * @return ArrayCollection
     *
     * @throws \Exception
     */
    public function getEtapaCronologica()
    {
        $etapa = $this->idEtapa->getIterator();
        $etapa->uasort(function(FrigaEditalEtapa $a, FrigaEditalEtapa $b) {
            return $a->getDataInicial() <=> $b->getDataInicial();
        });

        return new ArrayCollection($etapa->getArrayCopy());
    }
}
