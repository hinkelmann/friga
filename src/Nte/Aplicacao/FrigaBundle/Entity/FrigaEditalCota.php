<?php

namespace Nte\Aplicacao\FrigaBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;


use Doctrine\ORM\Mapping as ORM;

/**
 * FrigaEditalCargo
 *
 * @ORM\Table(name="friga_edital_lista")
 * @ORM\Entity
 */
class FrigaEditalCota
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
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
     * @ORM\JoinColumns({
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
     * Constructor
     */
    public function __construct()
    {
        $this->idEditalUsuario = new ArrayCollection();
        $this->idEditalUsuarioInscrito = new ArrayCollection();
    }
    public function __clone()
    {
        $this->idEditalUsuario = new ArrayCollection();
        $this->idEditalUsuarioInscrito = new ArrayCollection();
    }


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set descricao
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
     * @return FrigaEditalCota
     */
    public function setPontoCorte($pontoCorte)
    {
        $this->pontoCorte = $pontoCorte;
        return $this;
    }


    /**
     * Get descricao
     *
     * @return string
     */
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * Set idEdital
     *
     * @param FrigaEdital $idEdital
     *
     * @return FrigaEditalCargo
     */
    public function setIdEdital(FrigaEdital $idEdital = null)
    {
        $this->idEdital = $idEdital;

        return $this;
    }

    /**
     * Get idEdital
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
     * Add idEditalUsuario
     *
     * @param FrigaEditalUsuario $idEditalUsuario
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
     * Remove idEditalUsuario
     *
     * @param FrigaEditalUsuario $idEditalUsuario
     */
    public function removeIdEditalUsuario(FrigaEditalUsuario $idEditalUsuario)
    {
        $this->idEditalUsuario->removeElement($idEditalUsuario);
        $idEditalUsuario->removeIdEditalCargo($this);
    }

    /**
     * Get idEditalUsuario
     *
     * @return ArrayCollection
     */
    public function getIdEditalUsuario()
    {
        return $this->idEditalUsuario;
    }

    /**
     * @return ArrayCollection
     */
    public function getIdEditalUsuarioInscrito()
    {
        return $this->idEditalUsuarioInscrito;
    }

}
