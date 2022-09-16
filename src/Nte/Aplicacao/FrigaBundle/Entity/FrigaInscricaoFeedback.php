<?php

namespace Nte\Aplicacao\FrigaBundle\Entity;

use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Nte\UsuarioBundle\Entity\Usuario;

/**
 * FrigaInscricaoFeedback
 *
 * @ORM\Table(name="friga_inscricao_feedback")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class FrigaInscricaoFeedback
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
	 * @ORM\Column(name="situacao", type="integer", nullable=true)
	 */
	private $situacao;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="feedback", type="text", length=65535, nullable=true)
	 */
	private $feedback;

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
     * @var Usuario
     *
     * @ORM\ManyToOne(targetEntity="Nte\UsuarioBundle\Entity\Usuario", inversedBy="avaliacao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_avaliador", referencedColumnName="id")
     * })
     */
    private $idAvaliador;

    /**
     * @var FrigaEditalEtapa
     *
     * @ORM\ManyToOne(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalEtapa", inversedBy="avaliacao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_etapa", referencedColumnName="id")
     * })
     */
    private $idEtapa;

    /**
     * @var FrigaInscricao
     *
     * @ORM\ManyToOne(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricao", inversedBy="feedback")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_inscricao", referencedColumnName="id")
     * })
     */
    private $idInscricao;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return FrigaInscricaoFeedback
     */
    public function setId(int $id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getSituacao()
    {
        return $this->situacao;
    }

    /**
     * @param int $situacao
     * @return FrigaInscricaoFeedback
     */
    public function setSituacao( $situacao)
    {
        $this->situacao = $situacao;
        return $this;
    }

    /**
     * @return string
     */
    public function getFeedback()
    {
        return $this->feedback;
    }

    /**
     * @param string $feedback
     * @return FrigaInscricaoFeedback
     */
    public function setFeedback( $feedback)
    {
        $this->feedback = $feedback;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getRegistroDataAtualizacao()
    {
        return $this->registroDataAtualizacao;
    }

    /**
     * @param \DateTime $registroDataAtualizacao
     * @return FrigaInscricaoFeedback
     */
    public function setRegistroDataAtualizacao($registroDataAtualizacao)
    {
        $this->registroDataAtualizacao = $registroDataAtualizacao;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getRegistroDataCriacao()
    {
        return $this->registroDataCriacao;
    }

    /**
     * @param \DateTime $registroDataCriacao
     * @return FrigaInscricaoFeedback
     */
    public function setRegistroDataCriacao( $registroDataCriacao)
    {
        $this->registroDataCriacao = $registroDataCriacao;
        return $this;
    }

    /**
     * @return Usuario
     */
    public function getIdAvaliador()
    {
        return $this->idAvaliador;
    }

    /**
     * @param Usuario $idAvaliador
     * @return FrigaInscricaoFeedback
     */
    public function setIdAvaliador($idAvaliador)
    {
        $this->idAvaliador = $idAvaliador;
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
     * @return FrigaInscricaoFeedback
     */
    public function setIdEtapa( $idEtapa)
    {
        $this->idEtapa = $idEtapa;
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
     * @return FrigaInscricaoFeedback
     */
    public function setIdInscricao( $idInscricao)
    {
        $this->idInscricao = $idInscricao;
        return $this;
    }

    /**
     * @param PreUpdateEventArgs $args
     * @throws \Exception
     * @ORM\PreUpdate
     */
	public function preUpdate(PreUpdateEventArgs $args)
	{
		if ($args->hasChangedField('registroDataCriacao'))
		{
			$this->setRegistroDataCriacao($args->getOldValue('registroDataCriacao'));
		}
			$this->setRegistroDataAtualizacao(new \DateTime());
	}

	/**
	 * @ORM\PrePersist
	 */
	public function PrePersist()
	{
		$this->setRegistroDataCriacao(new \DateTime());
		$this->setRegistroDataAtualizacao(new \DateTime());
	}
}
