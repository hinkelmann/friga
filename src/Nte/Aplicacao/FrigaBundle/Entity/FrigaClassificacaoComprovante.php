<?php

namespace Nte\Aplicacao\FrigaBundle\Entity;

use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Nte\UsuarioBundle\Entity\Usuario;

/**
 * FrigaClassificacaoComprovante
 *
 * @ORM\Table(name="friga_classificacao_comprovante")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class FrigaClassificacaoComprovante
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
	 * @ORM\Column(name="uuid", type="string", length=255, nullable=true)
	 */
	private $uuid;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="posicao", type="integer", nullable=true)
	 */
	private $posicao = 0;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="valor", type="decimal", precision=10, scale=5, nullable=true)
	 */
	private $valor = 0;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="registro_data_criacao", type="string", length=255, nullable=true)
	 */
	private $registroDataCriacao;

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
	 * @var FrigaEdital
	 *
	 * @ORM\ManyToOne(targetEntity="FrigaEdital")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="id_edital", referencedColumnName="id")
	 * })
	 */
	private $idEdital;

	/**
	 * @var FrigaEditalCargo
	 *
	 * @ORM\ManyToOne(targetEntity="FrigaEditalCargo")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="id_cargo", referencedColumnName="id")
	 * })
	 */
	private $idCargo;

	/**
	 * @var FrigaInscricao
	 *
	 * @ORM\ManyToOne(targetEntity="FrigaInscricao")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="id_inscricao", referencedColumnName="id")
	 * })
	 */
	private $idInscricao;


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
	 * Set uuid
	 *
	 * @param string $uuid
	 *
	 * @return FrigaClassificacaoComprovante
	 */
	public function setUuid($uuid)
	{
		$this->uuid = $uuid;

		return $this;
	}

	/**
	 * Get uuid
	 *
	 * @return string
	 */
	public function getUuid()
	{
		return $this->uuid;
	}

	/**
	 * Set posicao
	 *
	 * @param integer $posicao
	 *
	 * @return FrigaClassificacaoComprovante
	 */
	public function setPosicao($posicao)
	{
		$this->posicao = $posicao;

		return $this;
	}

	/**
	 * Get posicao
	 *
	 * @return integer
	 */
	public function getPosicao()
	{
		return $this->posicao;
	}

	/**
	 * Set valor
	 *
	 * @param string $valor
	 *
	 * @return FrigaClassificacaoComprovante
	 */
	public function setValor($valor)
	{
		$this->valor = $valor;

		return $this;
	}

	/**
	 * Get valor
	 *
	 * @return string
	 */
	public function getValor()
	{
		return $this->valor;
	}

	/**
	 * Set registroDataCriacao
	 *
	 * @param string $registroDataCriacao
	 *
	 * @return FrigaClassificacaoComprovante
	 */
	public function setRegistroDataCriacao($registroDataCriacao)
	{
		$this->registroDataCriacao = $registroDataCriacao;

		return $this;
	}

	/**
	 * Get registroDataCriacao
	 *
	 * @return string
	 */
	public function getRegistroDataCriacao()
	{
		return $this->registroDataCriacao;
	}

	/**
	 * Set idUsuario
	 *
	 * @param Usuario $idUsuario
	 *
	 * @return FrigaClassificacaoComprovante
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
	 * Set idEdital
	 *
	 * @param FrigaEdital $idEdital
	 *
	 * @return FrigaClassificacaoComprovante
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
	 * Set idCargo
	 *
	 * @param FrigaEditalCargo $idCargo
	 *
	 * @return FrigaClassificacaoComprovante
	 */
	public function setIdCargo(FrigaEditalCargo $idCargo = null)
	{
		$this->idCargo = $idCargo;

		return $this;
	}

	/**
	 * Get idCargo
	 *
	 * @return FrigaEditalCargo
	 */
	public function getIdCargo()
	{
		return $this->idCargo;
	}

	/**
	 * Set idInscricao
	 *
	 * @param FrigaInscricao $idInscricao
	 *
	 * @return FrigaClassificacaoComprovante
	 */
	public function setIdInscricao(FrigaInscricao $idInscricao = null)
	{
		$this->idInscricao = $idInscricao;

		return $this;
	}

	/**
	 * Get idInscricao
	 *
	 * @return FrigaInscricao
	 */
	public function getIdInscricao()
	{
		return $this->idInscricao;
	}

	/**
	 * @param PreUpdateEventArgs $args
	 *
	 * @ORM\PreUpdate
	 */
	public function preUpdate(PreUpdateEventArgs $args)
	{
		if ($args->hasChangedField('registroDataCriacao'))
		{
			$this->setRegistroDataCriacao($args->getOldValue('registroDataCriacao'));
		}
	}

	/**
	 * @ORM\PrePersist
	 */
	public function PrePersist()
	{
		$this->setRegistroDataCriacao(new \DateTime());
	}
}
