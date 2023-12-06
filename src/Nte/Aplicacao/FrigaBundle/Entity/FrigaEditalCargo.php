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
 * @ORM\Table(name="friga_edital_cargo")
 *
 * @ORM\Entity
 */
class FrigaEditalCargo
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
     * @ORM\Column(name="ativo", type="integer", nullable=true)
     */
    private $ativo;

    /**
     * @var string
     *
     * @ORM\Column(name="ponto_corte", type="integer", nullable=true)
     */
    private $pontoCorte;

    /**
     * @var FrigaEdital
     *
     * @ORM\ManyToOne(targetEntity="FrigaEdital", inversedBy="cargo")
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
     * @ORM\ManyToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalUsuario", mappedBy="idEditalCargo")
     */
    protected $idEditalUsuario;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalUsuarioConvite", mappedBy="idEditalCargo")
     */
    protected $idEditalUsuarioConvite;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricao", mappedBy="idCargo")
     */
    protected $idEditalUsuarioInscrito;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->idEditalUsuario = new ArrayCollection();
        $this->idEditalUsuarioConvite = new ArrayCollection();
        $this->idEditalUsuarioInscrito = new ArrayCollection();
    }

    public function __clone()
    {
        $this->idEditalUsuario = new ArrayCollection();
        $this->idEditalUsuarioConvite = new ArrayCollection();
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
     * @return string
     */
    public function getPontoCorte()
    {
        return $this->pontoCorte;
    }

    /**
     * @param string $pontoCorte
     *
     * @return FrigaEditalCargo
     */
    public function setPontoCorte($pontoCorte)
    {
        $this->pontoCorte = $pontoCorte;

        return $this;
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
     * @return string
     */
    public function getAtivo()
    {
        return $this->ativo;
    }

    /**
     * @param string $ativo
     *
     * @return FrigaEditalCargo
     */
    public function setAtivo($ativo)
    {
        $this->ativo = $ativo;

        return $this;
    }

    /**
     * Add idEditalUsuario.
     *
     * @return FrigaEditalCargo
     */
    public function addIdEditalUsuario(FrigaEditalUsuario $idEditalUsuario)
    {
        if (!$this->idEditalUsuario->contains($idEditalUsuario)) {
            $this->idEditalUsuario->add($idEditalUsuario->addIdEditalCargo($this));
        }

        return $this;
    }

    /**
     * Remove idEditalUsuario.
     */
    public function removeIdEditalUsuario(FrigaEditalUsuario $idEditalUsuario)
    {
        $this->idEditalUsuario->removeElement($idEditalUsuario);
    }

    /**
     * Get idEditalUsuario.
     *
     * @return ArrayCollection
     */
    public function getIdEditalUsuario()
    {
        return $this->idEditalUsuario;
    }

    /**
     * Add idEditalUsuario.
     *
     * @return FrigaEditalCargo
     */
    public function addIdEditalUsuarioConvite(FrigaEditalUsuarioConvite $idEditalUsuarioConvite)
    {
        if (!$this->idEditalUsuarioConvite->contains($idEditalUsuarioConvite)) {
            $this->idEditalUsuarioConvite->add($idEditalUsuarioConvite->addIdEditalCargo($this));
        }

        return $this;
    }

    /**
     * Remove idEditalUsuario.
     */
    public function removeIdEditalUsuarioConvite(FrigaEditalUsuarioConvite $idEditalUsuarioConvite)
    {
        $this->idEditalUsuarioConvite->removeElement($idEditalUsuarioConvite);
    }

    /**
     * Get idEditalUsuario.
     *
     * @return ArrayCollection
     */
    public function getIdEditalUsuarioConvite()
    {
        return $this->idEditalUsuarioConvite;
    }

    /**
     * @return ArrayCollection
     */
    public function getIdEditalUsuarioInscrito()
    {
        return $this->idEditalUsuarioInscrito;
    }

    /**
     * @return ArrayCollection
     */
    public function getInscricaoValida()
    {
        return $this->getIdEditalUsuarioInscrito()->filter(
            function(FrigaInscricao $inscricao) {
                return -999 !== $inscricao->getIdSituacao();
            });
    }

    /**
     * @return ArrayCollection
     */
    public function getInscricaoSituacao($situacao = 0)
    {
        return $this->getIdEditalUsuarioInscrito()->filter(
            function(FrigaInscricao $inscricao) use ($situacao) {
                return $inscricao->getIdSituacao() === $situacao;
            });
    }
}
