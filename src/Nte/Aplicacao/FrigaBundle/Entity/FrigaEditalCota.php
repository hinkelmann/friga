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
use Doctrine\ORM\Mapping as ORM;

/**
 * FrigaEditalCargo.
 *
 * @ORM\Table(name="friga_edital_lista")
 *
 * @ORM\Entity
 */
class FrigaEditalCota
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
     * @var string
     *
     * @ORM\Column(name="descricao", type="text", length=65535, nullable=true)
     */
    private $descricao;

    /**
     * @var string
     *
     * @ORM\Column(name="ponto_corte", type="integer", nullable=true)
     */
    private $pontoCorte;

    /**
     * @var FrigaEdital
     *
     * @ORM\ManyToOne(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaEdital", inversedBy="cota")
     *
     * @ORM\JoinColumns({
     *
     *   @ORM\JoinColumn(name="id_edital", referencedColumnName="id")
     * })
     */
    private $idEdital;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricao", mappedBy="idCota")
     */
    protected $idEditalUsuarioInscrito;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->idEditalUsuarioInscrito = new ArrayCollection();
    }

    public function __clone()
    {
        $this->idEditalUsuarioInscrito = new ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set descricao.
     *
     * @param string $descricao
     *
     * @return FrigaEditalCargo
     */
    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;

        return $this;
    }

    /**
     * @return string
     */
    public function getPontoCorte()
    {
        return $this->pontoCorte;
    }

    /**
     * @param string $pontoCorte
     *
     * @return FrigaEditalCota
     */
    public function setPontoCorte($pontoCorte)
    {
        $this->pontoCorte = $pontoCorte;

        return $this;
    }

    /**
     * Get descricao.
     *
     * @return string
     */
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * Set idEdital.
     *
     * @return FrigaEditalCargo
     */
    public function setIdEdital(FrigaEdital $idEdital = null)
    {
        $this->idEdital = $idEdital;

        return $this;
    }

    /**
     * Get idEdital.
     *
     * @return FrigaEdital
     */
    public function getIdEdital()
    {
        return $this->idEdital;
    }

    /**
     * @return ArrayCollection
     */
    public function getIdEditalUsuarioInscrito()
    {
        return $this->idEditalUsuarioInscrito;
    }
}
