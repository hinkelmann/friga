<?php

namespace Nte\Aplicacao\FrigaBundle\Entity;

use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Nte\Admin\SuporteBundle\Entity\Suporte;
use Nte\Admin\SuporteBundle\Entity\SuporteIteracao;
use Nte\UsuarioBundle\Entity\Usuario;

use Exception;
use DateTime;

/**
 * FrigaArquivo
 *
 * @ORM\Table(name="friga_arquivo")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class FrigaArquivo
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
	 * @ORM\Column(name="tipo", type="string", length=255, nullable=true)
	 */
	private $tipo;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="contexto", type="string", length=255, nullable=true)
	 */
	private $contexto;

    /**
     * @var string
     *
     * @ORM\Column(name="id_contexto", type="string", length=255, nullable=true)
     */
    private $idContexto;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="titulo", type="string", length=255, nullable=true)
	 */
	private $titulo;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="nome", type="string", length=255, nullable=true)
	 */
	private $nome;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="path", type="string", length=255, nullable=true)
	 */
	private $path;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="hash", type="string", length=255, nullable=true)
	 */
	private $hash;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="mime_type", type="string", length=255, nullable=true)
	 */
	private $mimeType;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="atributos", type="string", length=255, nullable=true)
	 */
	private $atributo;

	/**
	 * @var DateTime
	 *
	 * @ORM\Column(name="data_publicacao", type="datetime", nullable=true)
	 */
	private $dataPublicacao;

	/**
	 * @var DateTime
	 *
	 * @ORM\Column(name="registro_data_atualizacao", type="datetime", nullable=true)
	 */
	private $registroDataAtualizacao;

	/**
	 * @var DateTime
	 *
	 * @ORM\Column(name="registro_data_criacao", type="datetime", nullable=true)
	 */
	private $registroDataCriacao;

	/**
	 * @var Usuario
	 *
	 * @ORM\ManyToOne(targetEntity="Nte\UsuarioBundle\Entity\Usuario", inversedBy="idArquivo")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="id_usuario", referencedColumnName="id")
	 * })
	 */
	private $idUsuario;

	/**
	 * @var ArrayCollection
	 *
	 * @ORM\ManyToMany(targetEntity="FrigaEdital", mappedBy="idArquivo")
	 */
	private $idEdital;

	/**
	 * @var ArrayCollection
	 *
	 * @ORM\ManyToMany(targetEntity="FrigaInscricaoPontuacao", mappedBy="idArquivo")
	 */
	private $idPontuacao;

	/**
	 * @var ArrayCollection
	 *
	 * @ORM\ManyToMany(targetEntity="FrigaInscricaoRecurso", mappedBy="idArquivo")
	 */
	private $idInscricaoRecurso;

    /**
	 * @var ArrayCollection
	 *
	 * @ORM\ManyToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricaoProjetoParticipante", mappedBy="idArquivo")
	 */
	private $idProjetoParticipante;

	/**
	 * @var ArrayCollection
	 *
	 * @ORM\ManyToMany(targetEntity="FrigaInscricao", mappedBy="idArquivo")
	 */
	private $idInscricao;

	/**
	 * @var ArrayCollection
	 *
	 * @ORM\ManyToMany(targetEntity="Nte\Admin\SuporteBundle\Entity\SuporteIteracao", mappedBy="idArquivo")
	 */
	private $idSuporteIteracao;

	/**
	 * @var ArrayCollection
	 *
	 * @ORM\ManyToMany(targetEntity="Nte\Admin\SuporteBundle\Entity\Suporte", mappedBy="idArquivo")
	 */
	private $idSuporte;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->idEdital           = new ArrayCollection();
		$this->idPontuacao        = new ArrayCollection();
		$this->idInscricaoRecurso = new ArrayCollection();
		$this->idInscricao        = new ArrayCollection();
		$this->idSuporteIteracao  = new ArrayCollection();
		$this->idSuporte          = new ArrayCollection();
		$this->idProjetoParticipante = new ArrayCollection();
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
	 * Set tipo
	 *
	 * @param string $tipo
	 *
	 * @return FrigaArquivo
	 */
	public function setTipo($tipo)
	{
		$this->tipo = $tipo;

		return $this;
	}

	/**
	 * Get tipo
	 *
	 * @return string
	 */
	public function getTipo()
	{
		return $this->tipo;
	}

	/**
	 * Set contexto
	 *
	 * @param string $contexto
	 *
	 * @return FrigaArquivo
	 */
	public function setContexto($contexto)
	{
		$this->contexto = $contexto;

		return $this;
	}

	/**
	 * Get contexto
	 *
	 * @return string
	 */
	public function getContexto()
	{
		return $this->contexto;
	}

	/**
	 * Set titulo
	 *
	 * @param string $titulo
	 *
	 * @return FrigaArquivo
	 */
	public function setTitulo($titulo)
	{
		$this->titulo = $titulo;

		return $this;
	}

	/**
	 * Get titulo
	 *
	 * @return string
	 */
	public function getTitulo()
	{
		return $this->titulo;
	}

	/**
	 * Set nome
	 *
	 * @param string $nome
	 *
	 * @return FrigaArquivo
	 */
	public function setNome($nome)
	{
		$this->nome = $nome;

		return $this;
	}

	/**
	 * Get nome
	 *
	 * @return string
	 */
	public function getNome()
	{
		return $this->nome;
	}

	/**
	 * Set path
	 *
	 * @param string $path
	 *
	 * @return FrigaArquivo
	 */
	public function setPath($path)
	{
		$this->path = $path;

		return $this;
	}

	/**
	 * Get path
	 *
	 * @return string
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * Set hash
	 *
	 * @param string $hash
	 *
	 * @return FrigaArquivo
	 */
	public function setHash($hash)
	{
		$this->hash = $hash;

		return $this;
	}

	/**
	 * Get hash
	 *
	 * @return string
	 */
	public function getHash()
	{
		return $this->hash;
	}

	/**
	 * Set MimeType
	 *
	 * @param string $mimeType
	 *
	 * @return FrigaArquivo
	 */
	public function setMimeType($mimeType)
	{
		$this->mimeType = $mimeType;

		return $this;
	}

	/**
	 * Get MimeType
	 *
	 * @return string
	 */
	public function getMimeType()
	{
		return $this->mimeType;
	}

	/**
	 * @return string
	 */
	public function getAtributo()
	{
		return $this->atributo;
	}

	/**
	 * @param string $atributo
	 *
	 * @return FrigaArquivo
	 */
	public function setAtributo($atributo)
	{
		$this->atributo = $atributo;

		return $this;
	}


	/**
	 * @return DateTime
	 */
	public function getDataPublicacao()
	{
		return $this->dataPublicacao;
	}

	/**
	 * @param DateTime $dataPublicacao
	 *
	 * @return FrigaArquivo
	 */
	public function setDataPublicacao($dataPublicacao)
	{
		$this->dataPublicacao = $dataPublicacao;

		return $this;
	}

	/**
	 * Set registroDataAtualizacao
	 *
	 * @param DateTime $registroDataAtualizacao
	 *
	 * @return FrigaArquivo
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
	 * @param DateTime $registroDataCriacao
	 *
	 * @return FrigaArquivo
	 */
	public function setRegistroDataCriacao($registroDataCriacao)
	{
		$this->registroDataCriacao = $registroDataCriacao;

		return $this;
	}

	/**
	 * Get registroDataCriacao
	 *
	 * @return DateTime
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
	 * @return FrigaArquivo
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
	 * Add idEdital
	 *
	 * @param FrigaEdital $idEdital
	 *
	 * @return FrigaArquivo
	 */
	public function addIdEdital(FrigaEdital $idEdital)
	{
		$this->idEdital[] = $idEdital;

		return $this;
	}

	/**
	 * Remove idEdital
	 *
	 * @param FrigaEdital $idEdital
	 */
	public function removeIdEdital(FrigaEdital $idEdital)
	{
		$this->idEdital->removeElement($idEdital);
	}

	/**
	 * Get idEdital
	 *
	 * @return ArrayCollection
	 */
	public function getIdEdital()
	{
		return $this->idEdital;
	}

	/**
	 * Add idPontuacao
	 *
	 * @param FrigaInscricaoPontuacao $idPontuacao
	 *
	 * @return FrigaArquivo
	 */
	public function addIdPontuacao(FrigaInscricaoPontuacao $idPontuacao)
	{
		$this->idPontuacao[] = $idPontuacao;

		return $this;
	}

	/**
	 * Remove idPontuacao
	 *
	 * @param FrigaInscricaoPontuacao $idPontuacao
	 */
	public function removeIdPontuacao(FrigaInscricaoPontuacao $idPontuacao)
	{
		$this->idPontuacao->removeElement($idPontuacao);
	}

	/**
	 * Get idPontuacao
	 *
	 * @return ArrayCollection
	 */
	public function getIdPontuacao()
	{
		return $this->idPontuacao;
	}

	/**
	 * Add idInscricaoRecurso
	 *
	 * @param FrigaInscricaoRecurso $idInscricaoRecurso
	 *
	 * @return FrigaArquivo
	 */
	public function addIdInscricaoRecurso(FrigaInscricaoRecurso $idInscricaoRecurso)
	{
		$this->idInscricaoRecurso[] = $idInscricaoRecurso;

		return $this;
	}

	/**
	 * Remove idInscricaoRecurso
	 *
	 * @param FrigaInscricaoRecurso $idInscricaoRecurso
	 */
	public function removeIdInscricaoRecurso(FrigaInscricaoRecurso $idInscricaoRecurso)
	{
		$this->idInscricaoRecurso->removeElement($idInscricaoRecurso);
	}

	/**
	 * Get idInscricaoRecurso
	 *
	 * @return ArrayCollection
	 */
	public function getIdInscricaoRecurso()
	{
		return $this->idInscricaoRecurso;
	}

	/**
	 * Add idInscricao
	 *
	 * @param FrigaInscricao $idInscricao
	 *
	 * @return FrigaArquivo
	 */
	public function addIdInscricao(FrigaInscricao $idInscricao)
	{
		$this->idInscricao[] = $idInscricao;

		return $this;
	}

	/**
	 * Remove idInscricao
	 *
	 * @param FrigaInscricao $idInscricao
	 */
	public function removeIdInscricao(FrigaInscricao $idInscricao)
	{
		$this->idInscricao->removeElement($idInscricao);
	}

	/**
	 * Get idInscricao
	 *
	 * @return ArrayCollection
	 */
	public function getIdInscricao()
	{
		return $this->idInscricao;
	}

	/**
	 * Add idSuporteIteracao
	 *
	 * @param SuporteIteracao $idSuporteIteracao
	 *
	 * @return FrigaArquivo
	 */
	public function addIdSuporteIteracao(SuporteIteracao $idSuporteIteracao)
	{
		$this->idSuporteIteracao[] = $idSuporteIteracao;

		return $this;
	}

	/**
	 * Remove idSuporteIteracao
	 *
	 * @param SuporteIteracao $idSuporteIteracao
	 */
	public function removeIdSuporteIteracao(SuporteIteracao $idSuporteIteracao)
	{
		$this->idSuporteIteracao->removeElement($idSuporteIteracao);
	}

	/**
	 * Get idSuporteIteracao
	 *
	 * @return ArrayCollection
	 */
	public function getIdSuporteIteracao()
	{
		return $this->idSuporteIteracao;
	}

	/**
	 * Add idSuporte
	 *
	 * @param Suporte $idSuporte
	 *
	 * @return FrigaArquivo
	 */
	public function addIdSuporte(Suporte $idSuporte)
	{
		$this->idSuporte[] = $idSuporte;

		return $this;
	}

	/**
	 * Remove idSuporte
	 *
	 * @param Suporte $idSuporte
	 */
	public function removeIdSuporte(Suporte $idSuporte)
	{
		$this->idSuporte->removeElement($idSuporte);
	}

	/**
	 * Get idSuporte
	 *
	 * @return ArrayCollection
	 */
	public function getIdSuporte()
	{
		return $this->idSuporte;
	}

    /**
     * Add idProjetoParticipante
     *
     * @param FrigaInscricaoProjetoParticipante $idProjetoParticipante
     *
     * @return FrigaArquivo
     */
    public function addIdProjetoParticipante(FrigaInscricaoProjetoParticipante $idProjetoParticipante)
    {
        if (!$this->idProjetoParticipante->contains($idProjetoParticipante)) {
            $this->idProjetoParticipante->add($idProjetoParticipante);
        }
        return $this;
    }

    /**
     * Remove idProjetoParticipante
     *
     * @param FrigaInscricaoProjetoParticipante $idProjetoParticipante
     */
    public function removeIdProjetoParticipante(FrigaInscricaoProjetoParticipante $idProjetoParticipante)
    {
        if ($this->idProjetoParticipante->contains($idProjetoParticipante)) {
            $this->idProjetoParticipante->removeElement($idProjetoParticipante);
        }
    }

    /**
     * Get idProjetoParticipante
     *
     * @return ArrayCollection
     */
    public function getIdProjetoParticipante()
    {
        return $this->idProjetoParticipante;
    }

    /**
     * @return string
     */
    public function getIdContexto()
    {
        return $this->idContexto;
    }

    /**
     * @param string $idContexto
     * @return FrigaArquivo
     */
    public function setIdContexto($idContexto)
    {
        $this->idContexto = $idContexto;
        return $this;
    }


    /**
     *
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
			$this->setRegistroDataAtualizacao(new DateTime());
		}
	}

	/**
	 * @ORM\PrePersist
	 */
	public function PrePersist()
	{
		$this->setRegistroDataCriacao(new DateTime());
		$this->setRegistroDataAtualizacao(new DateTime());
	}

    public function getPeriodoDivulgacao()
    {
        return (new DateTime()) > $this->getDataPublicacao();
    }
}
