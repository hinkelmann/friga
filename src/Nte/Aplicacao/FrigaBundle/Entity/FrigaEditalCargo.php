<?php

namespace Nte\Aplicacao\FrigaBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;


use Doctrine\ORM\Mapping as ORM;

/**
 * FrigaEditalCargo
 *
 * @ORM\Table(name="friga_edital_cargo")
 * @ORM\Entity
 */
class FrigaEditalCargo
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
     * @ORM\JoinColumns({
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
     * @ORM\OneToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricao", mappedBy="idCargo")
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
     * @return string
     */
    public function getPontoCorte()
    {
        return $this->pontoCorte;
    }

    /**
     * @param string $pontoCorte
     * @return FrigaEditalCargo
     */
    public function setPontoCorte($pontoCorte)
    {
        $this->pontoCorte = $pontoCorte;
        return $this;
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


    /**
     * @return ArrayCollection
     */
    public function getInscricaoValida()
    {
        return $this->getIdEditalUsuarioInscrito()->filter(
            function (FrigaInscricao $inscricao) {
                return $inscricao->getIdSituacao() !== -999;
            });
    }

    /**
     * @return ArrayCollection
     */
    public function getInscricaoSituacao($situacao = 0)
    {
        return $this->getIdEditalUsuarioInscrito()->filter(
            function (FrigaInscricao $inscricao) use ($situacao) {
                return $inscricao->getIdSituacao() === $situacao;
            });
    }

}
