<?php

namespace Nte\Aplicacao\FrigaBundle\Entity;


use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Expr;
use Nte\UsuarioBundle\Entity\Usuario;
use DateTime;
use Exception;

/**
 * FrigaEdital
 *
 * @ORM\Table(name="friga_edital")
 * @ORM\Entity(repositoryClass="Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class FrigaEdital
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
     * @ORM\Column(name="titulo", type="text", length=65535, nullable=true)
     */
    private $titulo;

    /**
     * @var string
     *
     * @ORM\Column(name="subtitulo", type="text", length=65535, nullable=true)
     */
    private $subtitulo;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="data_publicacao_oficial", type="datetime", nullable=true)
     */
    private $dataPublicacaoOficial;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @var FrigaEditalCategoria
     *
     * @ORM\ManyToOne(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalCategoria", inversedBy="edital")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_categoria", referencedColumnName="id", nullable=true)
     * })
     */
    private $idCategoria;

    /**
     * @var integer
     *
     * @ORM\Column(name="anexo0", type="integer", nullable=true)
     */
    private $anexo0;

    /**
     * @var integer
     *
     * @ORM\Column(name="anexo1", type="integer", nullable=true)
     */
    private $anexo1;

    /**
     * @var integer
     *
     * @ORM\Column(name="anexo2", type="integer", nullable=true)
     */
    private $anexo2;

    /**
     * @var integer
     *
     * @ORM\Column(name="anexo3", type="integer", nullable=true)
     */
    private $anexo3;

    /**
     * @var integer
     *
     * @ORM\Column(name="anexo4", type="integer", nullable=true)
     */
    private $anexo4;


    /**
     * @var integer
     *
     * @ORM\Column(name="anexo5", type="integer", nullable=true)
     */
    private $anexo5;


    /**
     * @var integer
     *
     * @ORM\Column(name="doc0", type="integer", nullable=true)
     */
    private $doc0;

    /**
     * @var integer
     *
     * @ORM\Column(name="doc1", type="integer", nullable=true)
     */
    private $doc1;

    /**
     * @var integer
     *
     * @ORM\Column(name="doc2", type="integer", nullable=true)
     */
    private $doc2;

    /**
     * @var integer
     *
     * @ORM\Column(name="doc3", type="integer", nullable=true)
     */
    private $doc3;

    /**
     * @var integer
     *
     * @ORM\Column(name="doc4", type="integer", nullable=true)
     */
    private $doc4;

    /**
     * @var integer
     *
     * @ORM\Column(name="doc5", type="integer", nullable=true)
     */
    private $doc5;

    /**
     * @var integer
     *
     * @ORM\Column(name="doc6", type="integer", nullable=true)
     */
    private $doc6;

    /**
     * @var integer
     *
     * @ORM\Column(name="doc7", type="integer", nullable=true)
     */
    private $doc7;

    /**
     * @var integer
     *
     * @ORM\Column(name="doc8", type="integer", nullable=true)
     */
    private $doc8;

    /**
     * @var integer
     *
     * @ORM\Column(name="doc9", type="integer", nullable=true)
     */
    private $doc9;

    /**
     * @var integer
     *
     * @ORM\Column(name="doc10", type="integer", nullable=true)
     */
    private $doc10;

    /**
     * @var integer
     *
     * @ORM\Column(name="doc11", type="integer", nullable=true)
     */
    private $doc11;

    /**
     * @var integer
     *
     * @ORM\Column(name="doc12", type="integer", nullable=true)
     */
    private $doc12;

    /**
     * @var integer
     *
     * @ORM\Column(name="doc13", type="integer", nullable=true)
     */
    private $doc13;

    /**
     * @var integer
     *
     * @ORM\Column(name="doc14", type="integer", nullable=true)
     */
    private $doc14;

    /**
     * @var integer
     *
     * @ORM\Column(name="doc15", type="integer", nullable=true)
     */
    private $doc15;

    /**
     * @var integer
     *
     * @ORM\Column(name="tipo_inscricao", type="integer", nullable=true)
     */
    private $tipoInscricao;

    /**
     * @var integer
     *
     * @ORM\Column(name="modelo_inscricao", type="integer", nullable=true)
     */
    private $modeloInscricao;

    /**
     * @var integer
     *
     * @ORM\Column(name="projeto_participante_min", type="integer", nullable=true)
     */
    private $projetoParticipanteMin;

    /**
     * @var integer
     *
     * @ORM\Column(name="projeto_participante_max", type="integer", nullable=true)
     */
    private $projetoParticipanteMax;

    /**
     * @var integer
     *
     * @ORM\Column(name="tipo_inscricao_limite", type="integer", nullable=true)
     */
    private $tipoInscricaoLimite;

    /**
     * @var integer
     *
     * @ORM\Column(name="permitir_estrangeiro", type="integer", nullable=true)
     */
    private $permitirEstrangeiro;


    /**
     * @var string
     *
     * @ORM\Column(name="sobre", type="text", length=65535, nullable=true)
     */
    private $sobre;

    /**
     * @var string
     *
     * @ORM\Column(name="info1", type="text", length=65535, nullable=true)
     */
    private $info1;

    /**
     * @var string
     *
     * @ORM\Column(name="info2", type="text", length=65535, nullable=true)
     */
    private $info2;

    /**
     * @var string
     *
     * @ORM\Column(name="info3", type="text", length=65535, nullable=true)
     */
    private $info3;

    /**
     * @var string
     *
     * @ORM\Column(name="info4", type="text", length=65535, nullable=true)
     */
    private $info4;

    /**
     * @var string
     *
     * @ORM\Column(name="info5", type="text", length=65535, nullable=true)
     */
    private $info5;

    /**
     * @var string
     *
     * @ORM\Column(name="info6", type="text", length=65535, nullable=true)
     */
    private $info6;


    /**
     * @var string
     *
     * @ORM\Column(name="info7", type="text", length=65535, nullable=true)
     */
    private $info7;


    /**
     * @var string
     *
     * @ORM\Column(name="info8", type="text", length=65535, nullable=true)
     */
    private $info8;

    /**
     * @var string
     *
     * @ORM\Column(name="info9", type="text", length=65535, nullable=true)
     */
    private $info9;

    /**
     * @var string
     *
     * @ORM\Column(name="info10", type="text", length=65535, nullable=true)
     */
    private $info10;

    /**
     * @var string
     *
     * @ORM\Column(name="info11", type="text", length=65535, nullable=true)
     */
    private $info11;

    /**
     * @var string
     *
     * @ORM\Column(name="info12", type="text", length=65535, nullable=true)
     */
    private $info12;

    /**
     * @var string
     *
     * @ORM\Column(name="info13", type="text", length=65535, nullable=true)
     */
    private $info13;

    /**
     * @var string
     *
     * @ORM\Column(name="termo_compromisso", type="text", length=65535, nullable=true)
     */
    private $termoCompromisso;

    /**
     * @var string
     *
     * @ORM\Column(name="termo_compromisso_situacao", type="integer", nullable=true)
     */
    private $termoCompromissoSituacao;

    /**
     * @var boolean
     *
     * @ORM\Column(name="resultado0", type="boolean", nullable=true)
     */
    private $resultado0;

    /**
     * @var boolean
     *
     * @ORM\Column(name="resultado1", type="boolean", nullable=true)
     */
    private $resultado1;

    /**
     * @var boolean
     *
     * @ORM\Column(name="resultado2", type="boolean", nullable=true)
     */
    private $resultado2;


    /**
     * @var boolean
     *
     * @ORM\Column(name="resultado3", type="boolean", nullable=true)
     */
    private $resultado3;


    /**
     * @var string
     *
     * @ORM\Column(name="uuid", type="string", length=255, nullable=true)
     */
    private $uuid;

    /**
     * @var integer
     *
     * @ORM\Column(name="publico", type="integer", nullable=true)
     */
    private $publico;

    /**
     * @var integer
     *
     * @ORM\Column(name="situacao", type="integer", nullable=true)
     */
    private $situacao;

    /**
     * @var string
     *
     * @ORM\Column(name="campo_cargo_titulo", type="text", length=65535, nullable=true)
     */
    private $campoCargoTitulo;

    /**
     * @var string
     *
     * @ORM\Column(name="campo_lista_titulo", type="text", length=65535, nullable=true)
     */
    private $campoListaTitulo;

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
     * @ORM\OneToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalUsuario", mappedBy="idEdital")
     */
    private $idEditalUsuario;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="FrigaArquivo", inversedBy="idEdital")
     * @ORM\JoinTable(name="friga_edital_tem_arquivo",
     *   joinColumns={
     *     @ORM\JoinColumn(name="id_edital", referencedColumnName="id")
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
     * @ORM\OneToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalCargo", mappedBy="idEdital")
     * @ORM\OrderBy({"descricao" = "ASC"})
     */
    private $cargo;


    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalCota", mappedBy="idEdital")
     * @ORM\OrderBy({"descricao" = "ASC"})
     */
    private $cota;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalEtapa", mappedBy="idEdital")
     */
    private $etapa;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalPontuacao", mappedBy="idEdital")
     */
    private $pontuacao;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalPontuacaoCategoria", mappedBy="idEdital")
     */
    private $pontuacaoCategoria;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalDesempate", mappedBy="idEdital")
     */
    private $desempate;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricao", mappedBy="idEdital")
     */
    private $inscricao;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idUsuario = new ArrayCollection();
        $this->idArquivo = new ArrayCollection();
        $this->etapa = new ArrayCollection();
        $this->cargo = new ArrayCollection();
        $this->cota = new ArrayCollection();
        $this->desempate = new ArrayCollection();
    }


    public function __clone()
    {
        $this->idUsuario = new ArrayCollection();
        $this->idArquivo = new ArrayCollection();
        $this->etapa = new ArrayCollection();
        $this->cargo = new ArrayCollection();
        $this->cota = new ArrayCollection();
        $this->desempate = new ArrayCollection(); ;
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
     * Set titulo
     *
     * @param string $titulo
     *
     * @return FrigaEdital
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
     * @return string
     */
    public function getSubtitulo()
    {
        return $this->subtitulo;
    }

    /**
     * @param string $subtitulo
     * @return FrigaEdital
     */
    public function setSubtitulo($subtitulo)
    {
        $this->subtitulo = $subtitulo;
        return $this;
    }


    /**
     * Set dataPublicacaoOficial
     *
     * @param \DateTime $dataPublicacaoOficial
     *
     * @return FrigaEdital
     */
    public function setDataPublicacaoOficial($dataPublicacaoOficial)
    {
        $this->dataPublicacaoOficial = $dataPublicacaoOficial;

        return $this;
    }

    /**
     * Get dataPublicacaoOficial
     *
     * @return \DateTime
     */
    public function getDataPublicacaoOficial()
    {
        return $this->dataPublicacaoOficial;
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
     * @return FrigaEdital
     */
    public function setSituacao($situacao)
    {
        $this->situacao = $situacao;
        return $this;
    }


    /**
     * Set url
     *
     * @param string $url
     *
     * @return FrigaEdital
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return FrigaEditalCategoria
     */
    public function getIdCategoria()
    {
        return $this->idCategoria;
    }

    /**
     * @param FrigaEditalCategoria $idCategoria
     * @return FrigaEdital
     */
    public function setIdCategoria($idCategoria)
    {
        $this->idCategoria = $idCategoria;
        return $this;
    }


    /**
     * Set anexo0
     *
     * @param integer $anexo0
     *
     * @return FrigaEdital
     */
    public function setAnexo0($anexo0)
    {
        $this->anexo0 = $anexo0;

        return $this;
    }

    /**
     * Get anexo0
     *
     * @return integer
     */
    public function getAnexo0()
    {
        return $this->anexo0;
    }

    /**
     * @return ArrayCollection
     */
    public function getCota()
    {
        return $this->cota;
    }


    /**
     * Set anexo1
     *
     * @param integer $anexo1
     *
     * @return FrigaEdital
     */
    public function setAnexo1($anexo1)
    {
        $this->anexo1 = $anexo1;

        return $this;
    }

    /**
     * Get anexo1
     *
     * @return integer
     */
    public function getAnexo1()
    {
        return $this->anexo1;
    }

    /**
     * Set tipoInscricao
     *
     * @param integer $tipoInscricao
     *
     * @return FrigaEdital
     */
    public function setTipoInscricao($tipoInscricao)
    {
        $this->tipoInscricao = $tipoInscricao;

        return $this;
    }

    /**
     * @return int
     */
    public function getTipoInscricaoLimite()
    {
        return $this->tipoInscricaoLimite;
    }

    /**
     * @param int $tipoInscricaoLimite
     * @return FrigaEdital
     */
    public function setTipoInscricaoLimite($tipoInscricaoLimite)
    {
        $this->tipoInscricaoLimite = $tipoInscricaoLimite;
        return $this;
    }

    /**
     * @return int
     */
    public function getModeloInscricao()
    {
        return $this->modeloInscricao;
    }

    /**
     * @param int $modeloInscricao
     * @return FrigaEdital
     */
    public function setModeloInscricao($modeloInscricao)
    {
        $this->modeloInscricao = $modeloInscricao;
        return $this;
    }

    /**
     * @return int
     */
    public function getProjetoParticipanteMin()
    {
        return $this->projetoParticipanteMin;
    }

    /**
     * @param int $projetoParticipanteMin
     * @return FrigaEdital
     */
    public function setProjetoParticipanteMin($projetoParticipanteMin)
    {
        $this->projetoParticipanteMin = $projetoParticipanteMin;
        return $this;
    }

    /**
     * @return int
     */
    public function getProjetoParticipanteMax()
    {
        return $this->projetoParticipanteMax;
    }

    /**
     * @param int $projetoParticipanteMax
     * @return FrigaEdital
     */
    public function setProjetoParticipanteMax($projetoParticipanteMax)
    {
        $this->projetoParticipanteMax = $projetoParticipanteMax;
        return $this;
    }


    /**
     * Get tipoInscricao
     *
     * @return integer
     */
    public function getTipoInscricao()
    {
        return $this->tipoInscricao;
    }

    /**
     * @return int
     */
    public function getPermitirEstrangeiro()
    {
        return $this->permitirEstrangeiro;
    }

    /**
     * @param int $permitirEstrangeiro
     * @return FrigaEdital
     */
    public function setPermitirEstrangeiro($permitirEstrangeiro)
    {
        $this->permitirEstrangeiro = $permitirEstrangeiro;
        return $this;
    }


    /**
     * Set sobre
     *
     * @param string $sobre
     *
     * @return FrigaEdital
     */
    public function setSobre($sobre)
    {
        $this->sobre = $sobre;

        return $this;
    }

    /**
     * Get sobre
     *
     * @return string
     */
    public function getSobre()
    {
        return $this->sobre;
    }

    /**
     * Set info1
     *
     * @param string $info1
     *
     * @return FrigaEdital
     */
    public function setInfo1($info1)
    {
        $this->info1 = $info1;

        return $this;
    }

    /**
     * Get info1
     *
     * @return string
     */
    public function getInfo1()
    {
        return $this->info1;
    }

    /**
     * Set info2
     *
     * @param string $info2
     *
     * @return FrigaEdital
     */
    public function setInfo2($info2)
    {
        $this->info2 = $info2;

        return $this;
    }

    /**
     * Get info2
     *
     * @return string
     */
    public function getInfo2()
    {
        return $this->info2;
    }

    /**
     * Set info3
     *
     * @param string $info3
     *
     * @return FrigaEdital
     */
    public function setInfo3($info3)
    {
        $this->info3 = $info3;

        return $this;
    }

    /**
     * Get info3
     *
     * @return string
     */
    public function getInfo3()
    {
        return $this->info3;
    }

    /**
     * @return string
     */
    public function getInfo4()
    {
        return $this->info4;
    }

    /**
     * @param string $info4
     * @return FrigaEdital
     */
    public function setInfo4($info4)
    {
        $this->info4 = $info4;
        return $this;
    }

    /**
     * @return string
     */
    public function getInfo5()
    {
        return $this->info5;
    }

    /**
     * @param string $info5
     * @return FrigaEdital
     */
    public function setInfo5($info5)
    {
        $this->info5 = $info5;
        return $this;
    }

    /**
     * @return string
     */
    public function getInfo6()
    {
        return $this->info6;
    }

    /**
     * @param string $info6
     * @return FrigaEdital
     */
    public function setInfo6($info6)
    {
        $this->info6 = $info6;
        return $this;
    }

    /**
     * @return string
     */
    public function getInfo7()
    {
        return $this->info7;
    }

    /**
     * @param string $info7
     * @return FrigaEdital
     */
    public function setInfo7($info7)
    {
        $this->info7 = $info7;
        return $this;
    }

    /**
     * @return string
     */
    public function getInfo8()
    {
        return $this->info8;
    }

    /**
     * @param string $info8
     * @return FrigaEdital
     */
    public function setInfo8($info8)
    {
        $this->info8 = $info8;
        return $this;
    }

    /**
     * @return string
     */
    public function getInfo9()
    {
        return $this->info9;
    }

    /**
     * @param string $info9
     * @return FrigaEdital
     */
    public function setInfo9($info9)
    {
        $this->info9 = $info9;
        return $this;
    }

    /**
     * @return string
     */
    public function getInfo10()
    {
        return $this->info10;
    }

    /**
     * @param string $info10
     * @return FrigaEdital
     */
    public function setInfo10($info10)
    {
        $this->info10 = $info10;
        return $this;
    }

    /**
     * @return string
     */
    public function getTermoCompromisso()
    {
        return $this->termoCompromisso;
    }

    /**
     * @param string $termoCompromisso
     * @return FrigaEdital
     */
    public function setTermoCompromisso($termoCompromisso)
    {
        $this->termoCompromisso = $termoCompromisso;
        return $this;
    }

    /**
     * @return string
     */
    public function getTermoCompromissoSituacao()
    {
        return $this->termoCompromissoSituacao;
    }

    /**
     * @param string $termoCompromissoSituacao
     * @return FrigaEdital
     */
    public function setTermoCompromissoSituacao($termoCompromissoSituacao)
    {
        $this->termoCompromissoSituacao = $termoCompromissoSituacao;
        return $this;
    }



    /**
     * Set uuid
     *
     * @param string $uuid
     *
     * @return FrigaEdital
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
     * @return int
     */
    public function getPublico()
    {
        return $this->publico;
    }

    /**
     * @param int $publico
     *
     * @return FrigaEdital
     */
    public function setPublico($publico)
    {
        $this->publico = $publico;

        return $this;
    }


    /**
     * Set registroDataCriacao
     *
     * @param DateTime $registroDataCriacao
     *
     * @return FrigaEdital
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
     * @param DateTime $registroDataAtualizacao
     *
     * @return FrigaEdital
     */
    public function setRegistroDataAtualizacao($registroDataAtualizacao)
    {
        $this->registroDataAtualizacao = $registroDataAtualizacao;

        return $this;
    }

    /**
     * Get registroDataAtualizacao
     *
     * @return DateTime
     */
    public function getRegistroDataAtualizacao()
    {
        return $this->registroDataAtualizacao;
    }

    /**
     * Add idUsuario
     *
     * @param Usuario $idUsuario
     *
     * @return FrigaEdital
     */
    public function addIdUsuario(Usuario $idUsuario)
    {
        $this->idUsuario[] = $idUsuario;
        $idUsuario->addIdEdital($this);
        return $this;
    }

    /**
     * Remove idUsuario
     *
     * @param Usuario $idUsuario
     */
    public function removeIdUsuario(Usuario $idUsuario)
    {
        $this->idUsuario->removeElement($idUsuario);
        $idUsuario->removeIdEdital($this);
    }


    /**
     * Get idUsuario
     *
     * @return ArrayCollection
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
     * @return FrigaEdital
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
     * @return int
     */
    public function getAnexo2()
    {
        return $this->anexo2;
    }

    /**
     * @param int $anexo2
     * @return FrigaEdital
     */
    public function setAnexo2($anexo2)
    {
        $this->anexo2 = $anexo2;
        return $this;
    }

    /**
     * @return int
     */
    public function getAnexo3()
    {
        return $this->anexo3;
    }

    /**
     * @param int $anexo3
     * @return FrigaEdital
     */
    public function setAnexo3($anexo3)
    {
        $this->anexo3 = $anexo3;
        return $this;
    }

    /**
     * @return int
     */
    public function getAnexo4()
    {
        return $this->anexo4;
    }

    /**
     * @param int $anexo4
     * @return FrigaEdital
     */
    public function setAnexo4($anexo4)
    {
        $this->anexo4 = $anexo4;
        return $this;
    }

    /**
     * @return int
     */
    public function getAnexo5()
    {
        return $this->anexo5;
    }

    /**
     * @param int $anexo5
     * @return FrigaEdital
     */
    public function setAnexo5($anexo5)
    {
        $this->anexo5 = $anexo5;
        return $this;
    }

    /**
     * @return int
     */
    public function getDoc0()
    {
        return $this->doc0;
    }

    /**
     * @param int $doc0
     * @return FrigaEdital
     */
    public function setDoc0($doc0)
    {
        $this->doc0 = $doc0;
        return $this;
    }

    /**
     * @return int
     */
    public function getDoc1()
    {
        return $this->doc1;
    }

    /**
     * @param int $doc1
     * @return FrigaEdital
     */
    public function setDoc1($doc1)
    {
        $this->doc1 = $doc1;
        return $this;
    }

    /**
     * @return int
     */
    public function getDoc2()
    {
        return $this->doc2;
    }

    /**
     * @param int $doc2
     * @return FrigaEdital
     */
    public function setDoc2($doc2)
    {
        $this->doc2 = $doc2;
        return $this;
    }

    /**
     * @return int
     */
    public function getDoc3()
    {
        return $this->doc3;
    }

    /**
     * @param int $doc3
     * @return FrigaEdital
     */
    public function setDoc3($doc3)
    {
        $this->doc3 = $doc3;
        return $this;
    }

    /**
     * @return int
     */
    public function getDoc4()
    {
        return $this->doc4;
    }

    /**
     * @param int $doc4
     * @return FrigaEdital
     */
    public function setDoc4($doc4)
    {
        $this->doc4 = $doc4;
        return $this;
    }

    /**
     * @return int
     */
    public function getDoc5()
    {
        return $this->doc5;
    }

    /**
     * @param int $doc5
     * @return FrigaEdital
     */
    public function setDoc5($doc5)
    {
        $this->doc5 = $doc5;
        return $this;
    }

    /**
     * @return int
     */
    public function getDoc6()
    {
        return $this->doc6;
    }

    /**
     * @param int $doc6
     * @return FrigaEdital
     */
    public function setDoc6($doc6)
    {
        $this->doc6 = $doc6;
        return $this;
    }

    /**
     * @return int
     */
    public function getDoc7()
    {
        return $this->doc7;
    }

    /**
     * @param int $doc7
     * @return FrigaEdital
     */
    public function setDoc7($doc7)
    {
        $this->doc7 = $doc7;
        return $this;
    }

    /**
     * @return int
     */
    public function getDoc8()
    {
        return $this->doc8;
    }

    /**
     * @param int $doc8
     * @return FrigaEdital
     */
    public function setDoc8($doc8)
    {
        $this->doc8 = $doc8;
        return $this;
    }

    /**
     * @return int
     */
    public function getDoc9()
    {
        return $this->doc9;
    }

    /**
     * @param int $doc9
     * @return FrigaEdital
     */
    public function setDoc9($doc9)
    {
        $this->doc9 = $doc9;
        return $this;
    }

    /**
     * @return int
     */
    public function getDoc10()
    {
        return $this->doc10;
    }

    /**
     * @param int $doc10
     * @return FrigaEdital
     */
    public function setDoc10($doc10)
    {
        $this->doc10 = $doc10;
        return $this;
    }

    /**
     * @return int
     */
    public function getDoc11()
    {
        return $this->doc11;
    }

    /**
     * @param int $doc11
     * @return FrigaEdital
     */
    public function setDoc11($doc11)
    {
        $this->doc11 = $doc11;
        return $this;
    }

    /**
     * @return int
     */
    public function getDoc12()
    {
        return $this->doc12;
    }

    /**
     * @param int $doc12
     * @return FrigaEdital
     */
    public function setDoc12($doc12)
    {
        $this->doc12 = $doc12;
        return $this;
    }

    /**
     * @return int
     */
    public function getDoc13()
    {
        return $this->doc13;
    }

    /**
     * @param int $doc13
     * @return FrigaEdital
     */
    public function setDoc13($doc13)
    {
        $this->doc13 = $doc13;
        return $this;
    }

    /**
     * @return int
     */
    public function getDoc14()
    {
        return $this->doc14;
    }

    /**
     * @param int $doc14
     * @return FrigaEdital
     */
    public function setDoc14($doc14)
    {
        $this->doc14 = $doc14;
        return $this;
    }

    /**
     * @return int
     */
    public function getDoc15()
    {
        return $this->doc15;
    }

    /**
     * @param int $doc15
     * @return FrigaEdital
     */
    public function setDoc15($doc15)
    {
        $this->doc15 = $doc15;
        return $this;
    }


    /**
     * @return string
     */
    public function getInfo11()
    {
        return $this->info11;
    }

    /**
     * @param string $info11
     * @return FrigaEdital
     */
    public function setInfo11($info11)
    {
        $this->info11 = $info11;
        return $this;
    }

    /**
     * @return string
     */
    public function getInfo12()
    {
        return $this->info12;
    }

    /**
     * @param string $info12
     * @return FrigaEdital
     */
    public function setInfo12($info12)
    {
        $this->info12 = $info12;
        return $this;
    }

    /**
     * @return string
     */
    public function getInfo13()
    {
        return $this->info13;
    }

    /**
     * @param string $info13
     * @return FrigaEdital
     */
    public function setInfo13($info13)
    {
        $this->info13 = $info13;
        return $this;
    }


    /**
     * @return bool
     */
    public function isResultado0()
    {
        return $this->resultado0;
    }

    /**
     * @param bool $resultado0
     * @return FrigaEdital
     */
    public function setResultado0($resultado0)
    {
        $this->resultado0 = $resultado0;
        return $this;
    }

    /**
     * @return bool
     */
    public function isResultado1()
    {
        return $this->resultado1;
    }

    /**
     * @param bool $resultado1
     * @return FrigaEdital
     */
    public function setResultado1($resultado1)
    {
        $this->resultado1 = $resultado1;
        return $this;
    }

    /**
     * @return bool
     */
    public function isResultado2()
    {
        return $this->resultado2;
    }

    /**
     * @param bool $resultado2
     * @return FrigaEdital
     */
    public function setResultado2($resultado2)
    {
        $this->resultado2 = $resultado2;
        return $this;
    }

    /**
     * @return bool
     */
    public function isResultado3()
    {
        return $this->resultado3;
    }

    /**
     * @param bool $resultado3
     * @return FrigaEdital
     */
    public function setResultado3($resultado3)
    {
        $this->resultado3 = $resultado3;
        return $this;
    }


    /**
     * @return string
     */
    public function getCampoCargoTitulo()
    {
        return $this->campoCargoTitulo;
    }

    /**
     * @param string $campoCargoTitulo
     * @return FrigaEdital
     */
    public function setCampoCargoTitulo($campoCargoTitulo)
    {
        $this->campoCargoTitulo = $campoCargoTitulo;
        return $this;
    }

    /**
     * @return string
     */
    public function getCampoListaTitulo()
    {
        return $this->campoListaTitulo;
    }

    /**
     * @param string $campoListaTitulo
     * @return FrigaEdital
     */
    public function setCampoListaTitulo($campoListaTitulo)
    {
        $this->campoListaTitulo = $campoListaTitulo;
        return $this;
    }


    /**
     * @return ArrayCollection
     */
    public function getCargo()
    {
        return $this->cargo;
    }

    /**
     * @return ArrayCollection
     */
    public function getEtapa()
    {
        return $this->etapa;
    }

    /**
     * @return mixed
     */
    public function getPontuacao()
    {
        return $this->pontuacao;
    }

    /**
     * @return ArrayCollection
     */
    public function getPontuacaoCategoria()
    {
        return $this->pontuacaoCategoria;
    }

    /**
     * @return ArrayCollection
     */
    public function getPontuacaoCategoriaPeso()
    {
        return $this->pontuacaoCategoria->filter(function (FrigaEditalPontuacaoCategoria $c) {
            return $c->getIdCategoria() == null;
        });
    }


    /**
     * @return ArrayCollection
     */
    public function getDesempate()
    {
        $c = new Criteria();
        $c->orderBy(['posicao' => 'asc']);
        return $this->desempate->matching($c);
    }

    /**
     * @return ArrayCollection
     */
    public function getInscricao()
    {
        return $this->inscricao;
    }

    public function getUsuarioEditalCargos($usuario)
    {
        $tmp = [];
        $criteria = new Criteria();
        $expr = new Comparison('idUsuario', Comparison::EQ, $usuario);
        $criteria->where($expr);
        $usuario = $this->idEditalUsuario->matching($criteria)->first();
        if (!is_null($usuario)) {
            foreach ($usuario->getIdEditalCargo() as $cargo) {
                $tmp[] = $cargo->getId();
            }
        }
        return $tmp;
    }

    public function getInscricaoInvalida()
    {
        //Filtra todos que não são anulados
        return $this->getInscricao()->filter(
            function (FrigaInscricao $inscricao) {
                return $inscricao->getIdSituacao() === -999;
            });
    }

    /**
     * @return ArrayCollection
     * @throws Exception
     */
    public function getInscricaoValida($usuario = false)
    {
        //Filtra todos que não são anulados
        $tmp = $this->getInscricao()->filter(
            function (FrigaInscricao $inscricao) {
                return $inscricao->getIdSituacao() !== -999;
            });

        if ($usuario !== false) {
            $cargo = $this->getUsuarioEditalCargos($usuario);
            $tmp = $tmp->filter(
                function (FrigaInscricao $inscricao) use ($cargo) {
                    return in_array($inscricao->getIdCargo()->getId(), $cargo);
                });
        }
        //Separa os inscrições em sentido positivo
        $tmp1 = $tmp->filter(function (FrigaInscricao $inscricao) {
            return $inscricao->getIdSituacao() == 0
                or $inscricao->getIdSituacao() == 2
                or $inscricao->getIdSituacao() >= 4;
        })->getIterator();

        //Ordena as inscrições positivas
        $tmp1->uasort(function (FrigaInscricao $a, FrigaInscricao $b) {
            return $a->getIdSituacao() <=> $b->getIdSituacao();
        });

        //Separa as inscrições negativas
        $tmp2 = $tmp->filter(function (FrigaInscricao $inscricao) {
            return $inscricao->getIdSituacao() == 1
                or $inscricao->getIdSituacao() == 3;
        });
        return new ArrayCollection(array_merge($tmp1->getArrayCopy(), $tmp2->toArray()));
    }

    /**
     * @return ArrayCollection
     */
    public function getInscricaoAvalicao()
    {
        return $this->getInscricao()->filter(
            function (FrigaInscricao $inscricao) {
                return $inscricao->getIdSituacao() !== -999 and
                    // $inscricao->getIdSituacao() !== 1 and
                    $inscricao->getIdSituacao() !== 3;
            });
    }


    /**
     * @param int $situacao
     * @param null $cargo
     * @return ArrayCollection
     */
    public function getInscricaoSituacao($situacao = 0, $cargo = null)
    {
        return $this->getInscricao()->filter(
            function (FrigaInscricao $inscricao) use ($situacao, $cargo) {
                if ($cargo) {
                    return $inscricao->getIdSituacao() === $situacao and
                        $cargo->getId() === $inscricao->getIdCargo()->getId();
                } else {
                    return $inscricao->getIdSituacao() === $situacao;
                }
            });
    }

    /**
     * @param null $etapa
     * @return ArrayCollection
     */
    public function getInscricaoRecurso($etapa = null)
    {
        return $this->getInscricao()->filter(
            function (FrigaInscricao $inscricao) use ($etapa) {
                if ($etapa) {
                    if ($inscricao->getRecursosEtapa($etapa)->count()) {
                        return $inscricao;
                    };
                } else {
                    return $inscricao->getRecursos()->count();
                }
            });
    }


    /**
     * @return ArrayCollection
     */
    public function getIdEditalUsuario()
    {
        return $this->idEditalUsuario;
    }

    /**
     * @return ArrayCollection
     */
    public function getIdEditalUsuarioBanca()
    {
        return $this->idEditalUsuario->filter(function (FrigaEditalUsuario $feu) {
            return !$feu->isAdministrador();
        });
    }

    /**
     * @return ArrayCollection
     * @throws Exception
     */
    public function getEtapaCronologica()
    {
        $etapa = $this->etapa->getIterator();
        $etapa->uasort(function (FrigaEditalEtapa $a, FrigaEditalEtapa $b) {
            return $a->getDataInicial() <=> $b->getDataInicial();
        });
        return new ArrayCollection($etapa->getArrayCopy());
    }

    /**
     * @return ArrayCollection
     */
    public function getEtapaAvaliacao($habilitacao = null)
    {
        return $this->etapa->filter(function (FrigaEditalEtapa $etapa) use ($habilitacao) {
            if ($habilitacao != null) {
                return $etapa->getPeriodoHabilitado() == $habilitacao and
                    ($etapa->getTipo() == 3 or $etapa->getTipo() == 7);
            }
            return $etapa->getTipo() == 3 or $etapa->getTipo() == 7;
        });
    }


    /**
     * @return ArrayCollection
     */
    public function getEtapaConvocacao($habilitacao = null)
    {
        return $this->etapa->filter(function (FrigaEditalEtapa $etapa) use ($habilitacao) {
            if ($habilitacao != null) {
                return $etapa->getPeriodoHabilitado() == $habilitacao
                    and $etapa->getTipo() == 5;
            }
            return $etapa->getTipo() == 5;
        });
    }

    /**
     * @return ArrayCollection
     */
    public function getEtapaClassificacao($habilitacao = null)
    {
        return $this->etapa->filter(function (FrigaEditalEtapa $etapa) use ($habilitacao) {
            if ($habilitacao != null) {
                return $etapa->getPeriodoHabilitado() == $habilitacao
                    and $etapa->getTipo() == 4;
            }
            return $etapa->getTipo() == 4;
        });
    }

    /**
     * @return ArrayCollection
     */
    public function getEtapaAvaliacaoClassificacao()
    {
        return $this->etapa->filter(function (FrigaEditalEtapa $etapa) {
            return @$etapa->getAndamentoPrazo() >= 0
                and @$etapa->getAndamentoPrazo() < 100
                and ($etapa->getTipo() == 3
                    or $etapa->getTipo() == 4
                    or $etapa->getTipo() == 7);
        });
    }

    public function getPontuacaoInscricao()
    {
        $pt = new ArrayCollection();
        /** @var FrigaEditalPontuacao $pontuacao */
        foreach ($this->getPontuacao() as $pontuacao) {
            /** @var FrigaEditalEtapa $etapa */
            foreach ($pontuacao->getIdEtapa() as $etapa) {
                if ($etapa->getTipo() == 1) {
                    if (!$pt->contains($pontuacao))
                        $pt->add($pontuacao);
                }
            }
        }
        return $pt;
    }

    public function getPontuacaoCategoriaInscricao()
    {


        $pt = new ArrayCollection();

        /** @var FrigaEditalPontuacaoCategoria $categoria */
        foreach ($this->getPontuacaoCategoria() as $categoria) {

            /** @var FrigaEditalPontuacao $pontuacao */
            foreach ($categoria->getPontuacao() as $pontuacao) {

                /** @var FrigaEditalEtapa $etapa */
                foreach ($pontuacao->getIdEtapa() as $etapa) {

                    if ($etapa->getTipo() != 1) {
                        if ($pt->contains($pontuacao))
                            $pt->removeElement($pontuacao);
                    }
                }
            }
        }
        return $pt;
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
            $this->setRegistroDataAtualizacao(new DateTime());
        }
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

    /**
     * @ORM\PreRemove
     *
     * @param LifecycleEventArgs $args
     * @throws Exception
     */
    public function preRemove(LifecycleEventArgs $args)
    {

        if ($this->getEtapa()->count()) {
            foreach ($this->getEtapa() as $item) {
                $args->getEntityManager()->remove($item);
            }
        }
        $args->getEntityManager()->flush();
        if ($this->getPontuacaoCategoria()->count()) {
            foreach ($this->getPontuacaoCategoria() as $item) {
                $args->getEntityManager()->remove($item);
            }
        }

        $args->getEntityManager()->flush();
        if ($this->getPontuacao()->count()) {
            foreach ($this->getPontuacao() as $item) {
                $args->getEntityManager()->remove($item);
            }
        }
        $args->getEntityManager()->flush();
        if ($this->getCargo()->count()) {
            foreach ($this->getCargo() as $item) {
                $args->getEntityManager()->remove($item);
            }
        }
        if ($this->getCota()->count()) {
            foreach ($this->getCota() as $item) {
                $args->getEntityManager()->remove($item);
            }
        }
        if ($this->getIdEditalUsuario()->count()) {
            foreach ($this->getIdEditalUsuario() as $item) {
                $args->getEntityManager()->remove($item);
            }
        }
        $args->getEntityManager()->flush();
    }

    /**
     * @return mixed|FrigaEditalEtapa
     */
    public function getPeriodoInscricao()
    {
        /** @var FrigaEditalEtapa $etapa */
        foreach ($this->etapa as $etapa) {
            if ($etapa->getTipo() == 1) {
                return $etapa;
            }
        }
        return false;
    }


    /**
     * @return mixed|FrigaEditalEtapa
     */
    public function getResultadoFinal()
    {
        $tmp = false;
        /** @var FrigaEditalEtapa $etapa */
        foreach ($this->etapa as $etapa) {
            if ($etapa->getTipo() == 4) {
                if ($tmp === false) {
                    $tmp = $etapa;
                } else {
                    if ($etapa->getDataDivulgacao() > $tmp->getDataDivulgacao()) {
                        $tmp = $etapa;
                    }
                }
            }
        }
        return $tmp;
    }

    /**
     * @return ArrayCollection
     */
    public function getRecursos()
    {
        $tmp = new ArrayCollection();
        /** @var FrigaEditalEtapa $item */
        foreach ($this->etapa as $item) {
            $tmp = new ArrayCollection(array_merge($tmp->toArray(), $item->getRecurso()->toArray()));
        }
        return $tmp;
    }

    /**
     * @param int $situacao
     * @return ArrayCollection
     */
    public function getRecursosSituacao($situacao = 0)
    {
        return $this->getRecursos()->filter(function (FrigaInscricaoRecurso $r) use ($situacao) {
            return $r->getIdSituacao() === $situacao;
        });
    }


    public function getAndamentoPrazo()
    {
        if (!$this->getPeriodoInscricao() or !$this->getResultadoFinal()) {
            return 0;
        }

        $total = $this->getPeriodoInscricao()
            ->getDataInicial()
            ->diff($this->getResultadoFinal()->getDataFinal())
            ->days;

        $contagemDias = $this->getPeriodoInscricao()
            ->getDataInicial()
            ->diff(new DateTime())
            ->days;

        if ($contagemDias == 0) {
            return 0;
        }
        $porcentagem = ($contagemDias * 100) / $total;
        $dt0 = new DateTime();
        if ($dt0 < $this->getPeriodoInscricao()->getDataInicial()) {
            return 0;
        }

        return $porcentagem > 100 ? 100 : round($porcentagem, 2);
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function getPeriodoInscricaoHabilitado()
    {
        if ($this->getPeriodoInscricao()) {
            $dt = new DateTime();
            return $dt > $this->getPeriodoInscricao()->getDataInicial()
                and $dt < $this->getPeriodoInscricao()->getDataFinal();
        }
        return false;
    }

    public function getBanca()
    {
        $tmp = new ArrayCollection();

        /** @var FrigaEditalUsuario $eu */
        foreach ($this->getIdEditalUsuario() as $eu) {
            if ($eu->getIdUsuario()->hasRole('ROLE_AVALIADOR')) {
                if (!$tmp->contains($eu->getIdUsuario())) {
                    $tmp->add($eu->getIdUsuario());
                }
            }
        }
        return $tmp;
    }
}


