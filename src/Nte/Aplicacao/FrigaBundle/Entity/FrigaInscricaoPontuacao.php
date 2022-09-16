<?php

namespace Nte\Aplicacao\FrigaBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Nte\UsuarioBundle\Entity\Usuario;
use DateTime;
use Exception;

/**
 * FrigaInscricaoPontuacao
 *
 * @ORM\Table(name="friga_inscricao_pontuacao")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class FrigaInscricaoPontuacao
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
     * @ORM\Column(name="valor_informado", type="decimal", precision=10, scale=5, nullable=true)
     */
    private $valorInformado;

    /**
     * @var string
     *
     * @ORM\Column(name="valor_texto", type="text", length=65535, nullable=true)
     */
    private $valorTexto;

    /**
     * @var string
     *
     * @ORM\Column(name="valor_considerado", type="decimal", precision=10, scale=5, nullable=true)
     */
    private $valorConsiderado;

    /**
     * @var boolean
     *
     * @ORM\Column(name="considerado", type="boolean", nullable=true)
     */
    private $considerado;


    /**
     * @var string
     *
     * @ORM\Column(name="observacao", type="text", length=65535, nullable=true)
     */
    private $observacao;

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
     * @var FrigaEditalEtapa
     *
     * @ORM\ManyToOne(targetEntity="FrigaEditalEtapa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_edital_etapa", referencedColumnName="id")
     * })
     */
    private $idEditalEtapa;

    /**
     * @var FrigaEditalPontuacao
     *
     * @ORM\ManyToOne(targetEntity="FrigaEditalPontuacao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_edital_pontuacao", referencedColumnName="id")
     * })
     */
    private $idEditalPontuacao;

    /**
     * @var FrigaInscricao
     *
     * @ORM\ManyToOne(targetEntity="FrigaInscricao", inversedBy="pontuacao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_inscricao", referencedColumnName="id")
     * })
     */
    private $idInscricao;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="FrigaArquivo", inversedBy="idPontuacao")
     * @ORM\JoinTable(name="friga_inscricao_pontuacao_tem_arquivo",
     *   joinColumns={
     *     @ORM\JoinColumn(name="id_pontuacao", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="id_arquivo", referencedColumnName="id")
     *   }
     * )
     */
    private $idArquivo;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricaoPontuacaoAvaliacao", mappedBy="idInscricaoPontuacao")
     */
    private $avaliacao;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idArquivo = new ArrayCollection();
        $this->avaliacao = new ArrayCollection();
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
     * Set valorInformado
     *
     * @param string $valorInformado
     *
     * @return FrigaInscricaoPontuacao
     */
    public function setValorInformado($valorInformado)
    {
        $this->valorInformado = $valorInformado;

        return $this;
    }

    /**
     * Get valorInformado
     *
     * @return string
     */
    public function getValorInformado()
    {
        return $this->valorInformado;
    }

    /**
     * @return string
     */
    public function getValorTexto()
    {
        return $this->valorTexto;
    }

    /**
     * @param string $valorTexto
     * @return FrigaInscricaoPontuacao
     */
    public function setValorTexto($valorTexto)
    {
        $this->valorTexto = $valorTexto;
        return $this;
    }


    /**
     * Set valorConsiderado
     *
     * @param string $valorConsiderado
     *
     * @return FrigaInscricaoPontuacao
     */
    public function setValorConsiderado($valorConsiderado)
    {
        $this->valorConsiderado = $valorConsiderado;

        return $this;
    }

    /**
     * Get valorConsiderado
     *
     * @return string
     */
    public function getValorConsiderado()
    {
        return $this->valorConsiderado;
    }

    /**
     * Set considerado
     *
     * @param boolean $considerado
     *
     * @return FrigaInscricaoPontuacao
     */
    public function setConsiderado($considerado)
    {
        $this->considerado = $considerado;

        return $this;
    }

    /**
     * Get considerado
     *
     * @return boolean
     */
    public function getConsiderado()
    {
        return $this->considerado;
    }

    /**
     * Set observacao
     *
     * @param string $observacao
     *
     * @return FrigaInscricaoPontuacao
     */
    public function setObservacao($observacao)
    {
        $this->observacao = $observacao;

        return $this;
    }

    /**
     * Get observacao
     *
     * @return string
     */
    public function getObservacao()
    {
        return $this->observacao;
    }

    /**
     * Set registroDataAtualizacao
     *
     * @param \DateTime $registroDataAtualizacao
     *
     * @return FrigaInscricaoPontuacao
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
     * @return FrigaInscricaoPontuacao
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
     * Set idAvaliador
     *
     * @param Usuario $idAvaliador
     *
     * @return FrigaInscricaoPontuacao
     */
    public function setIdAvaliador(Usuario $idAvaliador = null)
    {
        $this->idAvaliador = $idAvaliador;

        return $this;
    }

    /**
     * Get idAvaliador
     *
     * @return Usuario
     */
    public function getIdAvaliador()
    {
        return $this->idAvaliador;
    }

    /**
     * Set idEditalEtapa
     *
     * @param FrigaEditalEtapa $idEditalEtapa
     *
     * @return FrigaInscricaoPontuacao
     */
    public function setIdEditalEtapa(FrigaEditalEtapa $idEditalEtapa = null)
    {
        $this->idEditalEtapa = $idEditalEtapa;

        return $this;
    }

    /**
     * Get idEditalEtapa
     *
     * @return FrigaEditalEtapa
     */
    public function getIdEditalEtapa()
    {
        return $this->idEditalEtapa;
    }

    /**
     * Set idEditalPontuacao
     *
     * @param FrigaEditalPontuacao $idEditalPontuacao
     *
     * @return FrigaInscricaoPontuacao
     */
    public function setIdEditalPontuacao(FrigaEditalPontuacao $idEditalPontuacao = null)
    {
        $this->idEditalPontuacao = $idEditalPontuacao;

        return $this;
    }

    /**
     * Get idEditalPontuacao
     *
     * @return FrigaEditalPontuacao
     */
    public function getIdEditalPontuacao()
    {
        return $this->idEditalPontuacao;
    }

    /**
     * Set idInscricao
     *
     * @param FrigaInscricao $idInscricao
     *
     * @return FrigaInscricaoPontuacao
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
     * Add idArquivo
     *
     * @param FrigaArquivo $idArquivo
     *
     * @return FrigaInscricaoPontuacao
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
     * @return ArrayCollection
     */
    public function getIdArquivo()
    {
        return $this->idArquivo;
    }

    /**
     * @return ArrayCollection
     */
    public function getAvaliacao()
    {
        return $this->avaliacao;
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
     */
    public function PrePersist()
    {
        $this->setRegistroDataCriacao(new DateTime());
        $this->setRegistroDataAtualizacao(new DateTime());
    }
}
