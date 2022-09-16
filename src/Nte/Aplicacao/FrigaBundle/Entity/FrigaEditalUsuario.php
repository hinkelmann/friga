<?php

namespace Nte\Aplicacao\FrigaBundle\Entity;

use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use DateTime;
use Exception;
use Nte\UsuarioBundle\Entity\Usuario;

/**
 * FrigaEditalUsuario
 *
 * @ORM\Table(name="friga_edital_usuario")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class FrigaEditalUsuario
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
     * @var FrigaEdital
     *
     * @ORM\ManyToOne(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaEdital", inversedBy="idEditalUsuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_edital", referencedColumnName="id")
     * })$idPontuacao
     */
    private $idEdital;


    /**
     * @var Usuario
     *
     * @ORM\ManyToOne(targetEntity="Nte\UsuarioBundle\Entity\Usuario", inversedBy="idEditalUsuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_usuario", referencedColumnName="id")
     * })
     */
    private $idUsuario;

    /**
     * @var boolean
     * @ORM\Column(name="administrador", type="boolean", nullable=true)
     */
    private $administrador;

    /**
     * @var boolean
     * @ORM\Column(name="termo_compromisso", type="boolean", nullable=true)
     */
    private $termoCompromisso;

    /**
     * @var DateTime
     * @ORM\Column(name="termo_compromisso_data", type="datetime", nullable=true)
     */
    private $termoCompromissoData;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="registro_data_criacao", type="datetime", nullable=true)
     */
    private $registroDataCriacao;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="registro_data_atualizacao", type="datetime", nullable=true)
     */
    private $registroDataAtualizacao;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalCargo", inversedBy="idEditalUsuario")
     * @ORM\JoinTable(name="friga_edital_usuario_tem_cargo",
     *   joinColumns={
     *     @ORM\JoinColumn(name="id_edital_usuario", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="id_cargo", referencedColumnName="id")
     *   }
     * )
     */
    protected $idEditalCargo;

    /**
     * FrigaEditalUsuario constructor.
     */
    public function __construct()
    {
        $this->idEditalCargo = new ArrayCollection();
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
     * @return FrigaEditalUsuario
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
     * @return FrigaEditalUsuario
     */
    public function setIdEdital($idEdital)
    {
        $this->idEdital = $idEdital;
        return $this;
    }

    /**
     * @return Usuario
     */
    public function getIdUsuario()
    {
        return $this->idUsuario;
    }

    /**
     * @param Usuario $idUsuario
     * @return FrigaEditalUsuario
     */
    public function setIdUsuario($idUsuario)
    {
        $this->idUsuario = $idUsuario;
        return $this;
    }

    /**
     * @return bool
     */
    public function isAdministrador()
    {
        return $this->administrador;
    }

    /**
     * @param bool $administrador
     * @return FrigaEditalUsuario
     */
    public function setAdministrador($administrador)
    {
        $this->administrador = $administrador;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getRegistroDataCriacao()
    {
        return $this->registroDataCriacao;
    }

    /**
     * @param DateTime $registroDataCriacao
     * @return FrigaEditalUsuario
     */
    public function setRegistroDataCriacao($registroDataCriacao)
    {
        $this->registroDataCriacao = $registroDataCriacao;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getRegistroDataAtualizacao()
    {
        return $this->registroDataAtualizacao;
    }

    /**
     * @param DateTime $registroDataAtualizacao
     * @return FrigaEditalUsuario
     */
    public function setRegistroDataAtualizacao($registroDataAtualizacao)
    {
        $this->registroDataAtualizacao = $registroDataAtualizacao;
        return $this;
    }

    /**
     * Add idEditalCargo
     *
     * @param FrigaEditalCargo $idEditalCargo
     *
     * @return FrigaEditalUsuario
     */
    public function addIdEditalCargo(FrigaEditalCargo $idEditalCargo)
    {
        $this->idEditalCargo->add($idEditalCargo);

        return $this;
    }

    /**
     * Remove idEditalCargo
     *
     * @param FrigaEditalCargo $idEditalCargo
     */
    public function removeIdEditalCargo(FrigaEditalCargo $idEditalCargo)
    {
        $this->idEditalCargo->removeElement($idEditalCargo);
    }

    /**
     * @return bool
     */
    public function isTermoCompromisso()
    {
        return $this->termoCompromisso;
    }

    /**
     * @param bool $termoCompromisso
     * @return FrigaEditalUsuario
     */
    public function setTermoCompromisso($termoCompromisso)
    {
        $this->termoCompromisso = $termoCompromisso;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getTermoCompromissoData()
    {
        return $this->termoCompromissoData;
    }

    /**
     * @param DateTime $termoCompromissoData
     * @return FrigaEditalUsuario
     */
    public function setTermoCompromissoData( $termoCompromissoData)
    {
        $this->termoCompromissoData = $termoCompromissoData;
        return $this;
    }



    /**
     * Get idEditalCargo
     *
     * @return ArrayCollection
     */
    public function getIdEditalCargo()
    {
        return $this->idEditalCargo;
    }


    /**
     * @ORM\PreUpdate
     *
     * @param PreUpdateEventArgs $args
     * @throws Exception
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        if ($args->hasChangedField('registroDataCriacao')) {
            $this->setRegistroDataCriacao($args->getOldValue('registroDataCriacao'));
        }
        $this->setRegistroDataAtualizacao(new DateTime());
    }

    /**
     * @ORM\PrePersist
     *
     * @throws Exception
     */
    public function PrePersist()
    {
        $this->setRegistroDataCriacao(new DateTime());
        $this->setRegistroDataAtualizacao(new DateTime());
    }
}
