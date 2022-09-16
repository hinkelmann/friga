<?php

namespace Nte\Aplicacao\FrigaBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;
use DateTime;
use Nte\UsuarioBundle\Entity\Usuario;
use Exception;

/**
 * FrigaEditalPontuacao
 *
 * @ORM\Table(name="friga_inscricao_convocacao")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class FrigaConvocacao
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
     * @var DateTime
     *
     * @ORM\Column(name="data", type="datetime", nullable=true)
     */
    private $data;

    /**
     * @var string
     *
     * @ORM\Column(name="observacao", type="text", nullable=true)
     */
    private $observacao;

    /**
     * @var boolean
     *
     * @ORM\Column(name="ausente", type="boolean", nullable=true)
     */
    private $ausente;

    /**
     * @var FrigaInscricao
     *
     * @ORM\ManyToOne(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricao", inversedBy="convocacao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_inscricao", referencedColumnName="id")
     * })
     */
    private $idInscricao;


    /**
     * @var FrigaEditalEtapa
     *
     * @ORM\ManyToOne(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalEtapa", inversedBy="convocacao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_etapa", referencedColumnName="id")
     * })
     */
    private $idEtapa;

    /**
     * @var Usuario
     *
     * @ORM\ManyToOne(targetEntity="Nte\UsuarioBundle\Entity\Usuario", inversedBy="convocacao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_usuario", referencedColumnName="id")
     * })
     */
    private $idUsuario;


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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return FrigaConvocacao
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param DateTime $data
     * @return FrigaConvocacao
     */
    public function setData($data)
    {
        $this->data = $data;
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
     * @return FrigaConvocacao
     */
    public function setObservacao($observacao)
    {
        $this->observacao = $observacao;
        return $this;
    }

    /**
     * @return bool
     */
    public function isAusente()
    {
        return $this->ausente;
    }

    /**
     * @param bool $ausente
     * @return FrigaConvocacao
     */
    public function setAusente($ausente)
    {
        $this->ausente = $ausente;
        return $this;
    }

    /**
     * @return FrigaInscricao
     */
    public function getIdInscricao()
    {
        return $this->idInscricao;
    }

    /**
     * @param FrigaInscricao $idInscricao
     * @return FrigaConvocacao
     */
    public function setIdInscricao( $idInscricao)
    {
        $this->idInscricao = $idInscricao;
        return $this;
    }

    /**
     * @return FrigaEditalEtapa
     */
    public function getIdEtapa()
    {
        return $this->idEtapa;
    }

    /**
     * @param FrigaEditalEtapa $idEtapa
     * @return FrigaConvocacao
     */
    public function setIdEtapa($idEtapa)
    {
        $this->idEtapa = $idEtapa;
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
     * @return FrigaConvocacao
     */
    public function setIdUsuario( $idUsuario)
    {
        $this->idUsuario = $idUsuario;
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
     * @return FrigaConvocacao
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
     * @return FrigaConvocacao
     */
    public function setRegistroDataAtualizacao($registroDataAtualizacao)
    {
        $this->registroDataAtualizacao = $registroDataAtualizacao;
        return $this;
    }

    /**
     * @ORM\PreUpdate
     *
     * @param PreUpdateEventArgs $args
     * @throws Exception
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        if ($args->hasChangedField('registroDataCriacao'))
        {
            $this->setRegistroDataCriacao($args->getOldValue('registroDataCriacao'));
            $this->setRegistroDataAtualizacao(new \DateTime());
        }
    }

    /**
     * @ORM\PrePersist
     */
    public function PrePersist()
    {
        $this->uuid = uniqid();
        $this->setRegistroDataCriacao(new \DateTime());
        $this->setRegistroDataAtualizacao(new \DateTime());
    }
}
