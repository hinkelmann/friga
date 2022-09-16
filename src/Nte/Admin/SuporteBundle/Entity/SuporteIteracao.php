<?php

namespace Nte\Admin\SuporteBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaArquivo;
use Nte\UsuarioBundle\Entity\Usuario;

use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;

/**
 * SuporteIteracao
 *
 * @ORM\Table(name="suporte_iteracao")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class SuporteIteracao
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
     * @ORM\Column(name="resposta", type="text", length=65535, nullable=true)
     */
    private $resposta;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="registro_data_atualizacao", type="datetime", nullable=true)
     */
    private $registroDataAtualizacao;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="registro_data_criacao", type="datetime", nullable=true)
     */
    private $registroDataCriacao;

    /**
     * @var Suporte
     *
     * @ORM\ManyToOne(targetEntity="Suporte", inversedBy="iteracao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_suporte", referencedColumnName="id")
     * })
     */
    private $idSuporte;

    /**
     * @var Usuario
     *
     * @ORM\ManyToOne(targetEntity="Nte\UsuarioBundle\Entity\Usuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_usuario", referencedColumnName="id")
     * })
     */
    private $idUsuario;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaArquivo", inversedBy="idSuporteIteracao")
     */
    private $idArquivo;

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
     * Set resposta
     *
     * @param string $resposta
     *
     * @return SuporteIteracao
     */
    public function setResposta($resposta)
    {
        $this->resposta = $resposta;

        return $this;
    }

    /**
     * Get resposta
     *
     * @return string
     */
    public function getResposta()
    {
        return $this->resposta;
    }

    /**
     * Set registroDataAtualizacao
     *
     * @param \DateTime $registroDataAtualizacao
     *
     * @return SuporteIteracao
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
     * Set registroDataCriacao
     *
     * @param \DateTime $registroDataCriacao
     *
     * @return SuporteIteracao
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
     * Set idSuporte
     *
     * @param Suporte $idSuporte
     *
     * @return SuporteIteracao
     */
    public function setIdSuporte(Suporte $idSuporte = null)
    {
        $this->idSuporte = $idSuporte;

        return $this;
    }

    /**
     * Get idSuporte
     *
     * @return Suporte
     */
    public function getIdSuporte()
    {
        return $this->idSuporte;
    }

    /**
     * Set idUsuario
     *
     * @param Usuario $idUsuario
     *
     * @return SuporteIteracao
     */
    public function setIdUsuario(Usuario $idUsuario = null)
    {
        $this->idUsuario = $idUsuario;

        return $this;
    }

    /**
     * Get idUsuario
     *
     * @return Usuario
     */
    public function getIdUsuario()
    {
        return $this->idUsuario;
    }

    /**
     * Add idArquivo
     *
     * @param FrigaArquivo $idArquivo
     *
     * @return SuporteIteracao
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
}
