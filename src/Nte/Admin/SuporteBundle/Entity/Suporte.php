<?php

namespace Nte\Admin\SuporteBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaArquivo;
use Nte\UsuarioBundle\Entity\Usuario;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Event\PreUpdateEventArgs;

/**
 * Suporte
 *
 * @ORM\Table(name="suporte")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class Suporte
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
     * @var integer
     *
     * @ORM\Column(name="id_situacao", type="integer", nullable=true)
     */
    private $idSituacao;

    /**
     * @var string
     *
     * @ORM\Column(name="descricao", type="text", length=65535, nullable=true)
     */
    private $descricao;


    /**
     * @var string
     *
     * @ORM\Column(name="assunto", type="text", length=255, nullable=true)
     */
    private $assunto;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="registro_data_criacao", type="datetime", nullable=true)
     */
    private $registroDataCriacao;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="registro_data_atualizacao", type="datetime", nullable=true)
     */
    private $registroDataAtualizacao;

    /**
     * @var Usuario
     *
     * @ORM\ManyToOne(targetEntity="Nte\UsuarioBundle\Entity\Usuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_usuario_solicitante", referencedColumnName="id")
     * })
     */
    private $idUsuarioSolicitante;

    /**
     * @var Usuario
     *
     * @ORM\ManyToOne(targetEntity="Nte\UsuarioBundle\Entity\Usuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_usuario_responsavel", referencedColumnName="id", nullable=true)
     * })
     */
    private $idUsuarioResponsavel;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaArquivo", inversedBy="idSuporte")
     */
    private $idArquivo;


    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Nte\Admin\SuporteBundle\Entity\SuporteIteracao", mappedBy="idSuporte")
     */
    private $iteracao;



    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idArquivo = new ArrayCollection();
    }

	/**
	 * @param PreUpdateEventArgs $args
	 * @ORM\PreUpdate
	 */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        if ($args->hasChangedField('registroDataCriacao')) {
            $this->registroDataCriacao = $args->getOldValue('registroDataCriacao');
            $this->registroDataAtualizacao = new \DateTime();
        }
    }

    /**
     * @ORM\PrePersist
     */
    public function PrePersist()
    {
        $this->registroDataCriacao = new \DateTime();
        $this->registroDataAtualizacao = new \DateTime();
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
     * Set idSituacao
     *
     * @param integer $idSituacao
     *
     * @return Suporte
     */
    public function setIdSituacao($idSituacao)
    {
        $this->idSituacao = $idSituacao;

        return $this;
    }

    /**
     * Get idSituacao
     *
     * @return integer
     */
    public function getIdSituacao()
    {
        return $this->idSituacao;
    }

    /**
     * @return string
     */
    public function getAssunto()
    {
        return $this->assunto;
    }

    /**
     * @param string $assunto
     * @return Suporte
     */
    public function setAssunto($assunto)
    {
        $this->assunto = $assunto;
        return $this;
    }

    /**
     * Set descricao
     *
     * @param string $descricao
     *
     * @return Suporte
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
     * Set registroDataCriacao
     *
     * @param \DateTime $registroDataCriacao
     *
     * @return Suporte
     */
    public function setRegistroDataCriacao($registroDataCriacao)
    {
        $this->registroDataCriacao = $registroDataCriacao;

        return $this;
    }

    /**
     * Get registroDataCriacao
     *
     * @return \DateTime
     */
    public function getRegistroDataCriacao()
    {
        return $this->registroDataCriacao;
    }

    /**
     * Set registroDataAtualizacao
     *
     * @param \DateTime $registroDataAtualizacao
     *
     * @return Suporte
     */
    public function setRegistroDataAtualizacao($registroDataAtualizacao)
    {
        $this->registroDataAtualizacao = $registroDataAtualizacao;

        return $this;
    }

    /**
     * Get registroDataAtualizacao
     *
     * @return \DateTime
     */
    public function getRegistroDataAtualizacao()
    {
        return $this->registroDataAtualizacao;
    }

    /**
     * Set idUsuarioSolicitante
     *
     * @param Usuario $idUsuarioSolicitante
     *
     * @return Suporte
     */
    public function setIdUsuarioSolicitante(Usuario $idUsuarioSolicitante = null)
    {
        $this->idUsuarioSolicitante = $idUsuarioSolicitante;

        return $this;
    }

    /**
     * Get idUsuarioSolicitante
     *
     * @return Usuario
     */
    public function getIdUsuarioSolicitante()
    {
        return $this->idUsuarioSolicitante;
    }

    /**
     * Set idUsuarioResponsavel
     *
     * @param Usuario $idUsuarioResponsavel
     *
     * @return Suporte
     */
    public function setIdUsuarioResponsavel(Usuario $idUsuarioResponsavel = null)
    {
        $this->idUsuarioResponsavel = $idUsuarioResponsavel;

        return $this;
    }

    /**
     * Get idUsuarioResponsavel
     *
     * @return Usuario
     */
    public function getIdUsuarioResponsavel()
    {
        return $this->idUsuarioResponsavel;
    }

    /**
     * Add idArquivo
     *
     * @param FrigaArquivo $idArquivo
     *
     * @return Suporte
     */
    public function addIdArquivo(FrigaArquivo $idArquivo)
    {
        $this->idArquivo[] = $idArquivo;

        return $this;
    }

    /**
     * Remove idArquivo
     *
     * @param FrigaArquivo $idArquivo
     */
    public function removeIdArquivo(FrigaArquivo $idArquivo)
    {
        $this->idArquivo->removeElement($idArquivo);
    }

    /**
     * Get idArquivo
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIdArquivo()
    {
        return $this->idArquivo;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIteracao()
    {
        return $this->iteracao;
    }

}
