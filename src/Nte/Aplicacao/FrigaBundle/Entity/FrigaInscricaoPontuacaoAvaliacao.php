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
 * @ORM\Table(name="friga_inscricao_pontuacao_avaliacao")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class FrigaInscricaoPontuacaoAvaliacao
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
     * @var FrigaInscricao
     *
     * @ORM\ManyToOne(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricao", inversedBy="avaliacao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_inscricao", referencedColumnName="id")
     * })
     */
    private $idInscricao;


    /**
     * @var FrigaInscricaoPontuacao
     *
     * @ORM\ManyToOne(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricaoPontuacao", inversedBy="avaliacao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_inscricao_pontuacao", referencedColumnName="id")
     * })
     */
    private $idInscricaoPontuacao;

    /**
     * @var FrigaEditalPontuacao
     *
     * @ORM\ManyToOne(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalPontuacao", inversedBy="avaliacao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_edital_pontuacao", referencedColumnName="id")
     * })
     */
    private $idEditalPontuacao;

    /**
     * @var FrigaEditalPontuacaoCategoria
     *
     * @ORM\ManyToOne(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalPontuacaoCategoria")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_edital_pontuacao_categoria", referencedColumnName="id")
     * })
     */
    private $idEditalPontuacaoCategoria;

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
     * @var boolean
     *
     * @ORM\Column(name="considerado", type="boolean", nullable=true)
     */
    private $considerado;

    /**
     * @var string
     *
     * @ORM\Column(name="valor_avaliador", type="decimal", precision=10, scale=5, nullable=true)
     */
    private $valorAvaliador;

    /**
     * @var string
     * @ORM\Column(name="valor_inscricao", type="decimal", precision=10, scale=5, nullable=true)
     */
    private $valorInscricao;


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
     * Constructor
     */
    public function __construct()
    {

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
     * @return FrigaInscricaoPontuacaoAvaliacao
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * @return FrigaInscricaoPontuacaoAvaliacao
     */
    public function setIdInscricao($idInscricao)
    {
        $this->idInscricao = $idInscricao;
        return $this;
    }

    /**
     * @return FrigaInscricaoPontuacao
     */
    public function getIdInscricaoPontuacao()
    {
        return $this->idInscricaoPontuacao;
    }

    /**
     * @param FrigaInscricaoPontuacao $idInscricaoPontuacao
     * @return FrigaInscricaoPontuacaoAvaliacao
     */
    public function setIdInscricaoPontuacao($idInscricaoPontuacao)
    {
        $this->idInscricaoPontuacao = $idInscricaoPontuacao;
        return $this;
    }

    /**
     * @return FrigaEditalPontuacao
     */
    public function getIdEditalPontuacao()
    {
        return $this->idEditalPontuacao;
    }

    /**
     * @param FrigaEditalPontuacao $idEditalPontuacao
     * @return FrigaInscricaoPontuacaoAvaliacao
     */
    public function setIdEditalPontuacao($idEditalPontuacao)
    {
        $this->idEditalPontuacao = $idEditalPontuacao;
        return $this;
    }

    /**
     * @return FrigaEditalPontuacaoCategoria
     */
    public function getIdEditalPontuacaoCategoria()
    {
        return $this->idEditalPontuacaoCategoria;
    }

    /**
     * @param FrigaEditalPontuacaoCategoria $idEditalPontuacaoCategoria
     * @return FrigaInscricaoPontuacaoAvaliacao
     */
    public function setIdEditalPontuacaoCategoria($idEditalPontuacaoCategoria)
    {
        $this->idEditalPontuacaoCategoria = $idEditalPontuacaoCategoria;
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
     * @return FrigaInscricaoPontuacaoAvaliacao
     */
    public function setIdAvaliador($idAvaliador)
    {
        $this->idAvaliador = $idAvaliador;
        return $this;
    }

    /**
     * @return bool
     */
    public function isConsiderado()
    {
        return $this->considerado;
    }

    /**
     * @param bool $considerado
     * @return FrigaInscricaoPontuacaoAvaliacao
     */
    public function setConsiderado($considerado)
    {
        $this->considerado = $considerado;
        return $this;
    }

    /**
     * @return string
     */
    public function getValorAvaliador()
    {
        return $this->valorAvaliador;
    }

    /**
     * @param string $valorAvaliador
     * @return FrigaInscricaoPontuacaoAvaliacao
     */
    public function setValorAvaliador($valorAvaliador)
    {
        $this->valorAvaliador = $valorAvaliador;
        return $this;
    }

    /**
     * @return string
     */
    public function getValorInscricao()
    {
        return $this->valorInscricao;
    }

    /**
     * @param string $valorInscricao
     * @return FrigaInscricaoPontuacaoAvaliacao
     */
    public function setValorInscricao($valorInscricao)
    {
        $this->valorInscricao = $valorInscricao;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdEtapa()
    {
        return $this->idEtapa;
    }

    /**
     * @param mixed $idEtapa
     * @return FrigaInscricaoPontuacaoAvaliacao
     */
    public function setIdEtapa($idEtapa)
    {
        $this->idEtapa = $idEtapa;
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
     * @return FrigaInscricaoPontuacaoAvaliacao
     */
    public function setObservacao($observacao)
    {
        $this->observacao = $observacao;
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
     * @return FrigaInscricaoPontuacaoAvaliacao
     */
    public function setRegistroDataAtualizacao($registroDataAtualizacao)
    {
        $this->registroDataAtualizacao = $registroDataAtualizacao;
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
     * @return FrigaInscricaoPontuacaoAvaliacao
     */
    public function setRegistroDataCriacao($registroDataCriacao)
    {
        $this->registroDataCriacao = $registroDataCriacao;
        return $this;
    }


    /**
     * @ORM\PreUpdate
     *
     * @param PreUpdateEventArgs $args
     * @throws
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
