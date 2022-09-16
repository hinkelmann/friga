<?php

namespace Nte\UsuarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Event\PreUpdateEventArgs;

/**
 * Mensagem
 *
 * @ORM\Table(name="fos_mensagem")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class Mensagem
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
	 * @ORM\Column(name="mensagem", type="text", length=65535, nullable=true)
	 */
	private $mensagem = 'NULL';

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="oculto_de", type="boolean", nullable=true)
	 */
	private $ocultoDe = 'NULL';

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="oculto_para", type="boolean", nullable=true)
	 */
	private $ocultoPara = 'NULL';

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="data_leitura", type="datetime", nullable=true)
	 */
	private $dataLeitura = 'NULL';

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="registro_data_atualizacao", type="datetime", nullable=true)
	 */
	private $registroDataAtualizacao = 'NULL';

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="registro_data_criacao", type="datetime", nullable=true)
	 */
	private $registroDataCriacao = 'NULL';

	/**
	 * @var Usuario
	 *
	 * @ORM\ManyToOne(targetEntity="Usuario")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="id_usuario_de", referencedColumnName="id")
	 * })
	 */
	private $idUsuarioDe;

	/**
	 * @var Usuario
	 *
	 * @ORM\ManyToOne(targetEntity="Usuario")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="id_usuario_para", referencedColumnName="id")
	 * })
	 */
	private $idUsuarioPara;


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
	 * Set mensagem
	 *
	 * @param string $mensagem
	 *
	 * @return Mensagem
	 */
	public function setMensagem($mensagem)
	{
		$this->mensagem = $mensagem;

		return $this;
	}

	/**
	 * Get mensagem
	 *
	 * @return string
	 */
	public function getMensagem()
	{
		return $this->mensagem;
	}

	/**
	 * Set ocultoDe
	 *
	 * @param boolean $ocultoDe
	 *
	 * @return Mensagem
	 */
	public function setOcultoDe($ocultoDe)
	{
		$this->ocultoDe = $ocultoDe;

		return $this;
	}

	/**
	 * Get ocultoDe
	 *
	 * @return boolean
	 */
	public function getOcultoDe()
	{
		return $this->ocultoDe;
	}

	/**
	 * Set ocultoPara
	 *
	 * @param boolean $ocultoPara
	 *
	 * @return Mensagem
	 */
	public function setOcultoPara($ocultoPara)
	{
		$this->ocultoPara = $ocultoPara;

		return $this;
	}

	/**
	 * Get ocultoPara
	 *
	 * @return boolean
	 */
	public function getOcultoPara()
	{
		return $this->ocultoPara;
	}

	/**
	 * Set dataLeitura
	 *
	 * @param \DateTime $dataLeitura
	 *
	 * @return Mensagem
	 */
	public function setDataLeitura($dataLeitura)
	{
		$this->dataLeitura = $dataLeitura;

		return $this;
	}

	/**
	 * Get dataLeitura
	 * @return \DateTime
	 */
	public function getDataLeitura()
	{
		return $this->dataLeitura;
	}

	/**
	 * Set registroDataAtualizacao
	 *
	 * @param \DateTime $registroDataAtualizacao
	 *
	 * @return Mensagem
	 */
	public function setRegistroDataAtualizacao($registroDataAtualizacao)
	{
		$this->registroDataAtualizacao = $registroDataAtualizacao;

		return $this;
	}

	/**
	 * Get registroDataAtualizacao
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
	 * @return Mensagem
	 */
	public function setRegistroDataCriacao($registroDataCriacao)
	{
		$this->registroDataCriacao = $registroDataCriacao;

		return $this;
	}

	/**
	 * Get registroDataCriacao
	 * @return \DateTime
	 */
	public function getRegistroDataCriacao()
	{
		return $this->registroDataCriacao;
	}

	/**
	 * Set idUsuarioDe
	 *
	 * @param Usuario $idUsuarioDe
	 *
	 * @return Mensagem
	 */
	public function setIdUsuarioDe(Usuario $idUsuarioDe = null)
	{
		$this->idUsuarioDe = $idUsuarioDe;

		return $this;
	}

	/**
	 * Get idUsuarioDe
	 * @return Usuario
	 */
	public function getIdUsuarioDe()
	{
		return $this->idUsuarioDe;
	}

	/**
	 * Set idUsuarioPara
	 *
	 * @param Usuario $idUsuarioPara
	 *
	 * @return Mensagem
	 */
	public function setIdUsuarioPara(Usuario $idUsuarioPara = null)
	{
		$this->idUsuarioPara = $idUsuarioPara;

		return $this;
	}

	/**
	 * Get idUsuarioPara
	 * @return Usuario
	 */
	public function getIdUsuarioPara()
	{
		return $this->idUsuarioPara;
	}

	/**
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

}
