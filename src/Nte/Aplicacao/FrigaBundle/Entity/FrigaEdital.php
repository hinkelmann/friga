<?php

/*
 * This file is part of  Friga - https://nte.ufsm.br/friga.
 * (c) Friga
 * Friga is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Friga is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Friga.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Nte\Aplicacao\FrigaBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Nte\UsuarioBundle\Entity\Usuario;

/**
 * FrigaEdital.
 *
 * @ORM\Table(name="friga_edital")
 *
 * @ORM\Entity(repositoryClass="Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalRepository")
 *
 * @ORM\HasLifecycleCallbacks()
 */
class FrigaEdital
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     *
     * @ORM\Id
     *
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
     * @ORM\Column(name="numero", type="string", length=255, nullable=true)
     */
    private $numero;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="url_processo", type="string", length=255, nullable=true)
     */
    private $urlProcesso;

    /**
     * @var FrigaEditalCategoria
     *
     * @ORM\ManyToOne(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalCategoria", inversedBy="edital")
     *
     * @ORM\JoinColumns({
     *
     *   @ORM\JoinColumn(name="id_categoria", referencedColumnName="id", nullable=true)
     * })
     */
    private $idCategoria;

    /**
     * @var int
     *
     * @ORM\Column(name="anexo0", type="integer", nullable=true)
     */
    private $anexo0;

    /**
     * @var int
     *
     * @ORM\Column(name="anexo1", type="integer", nullable=true)
     */
    private $anexo1;

    /**
     * @var int
     *
     * @ORM\Column(name="anexo2", type="integer", nullable=true)
     */
    private $anexo2;

    /**
     * @var int
     *
     * @ORM\Column(name="anexo3", type="integer", nullable=true)
     */
    private $anexo3;

    /**
     * @var int
     *
     * @ORM\Column(name="anexo4", type="integer", nullable=true)
     */
    private $anexo4;

    /**
     * @var int
     *
     * @ORM\Column(name="anexo5", type="integer", nullable=true)
     */
    private $anexo5;

    /**
     * @var int
     *
     * @ORM\Column(name="doc0", type="integer", nullable=true)
     */
    private $doc0;

    /**
     * @var int
     *
     * @ORM\Column(name="doc1", type="integer", nullable=true)
     */
    private $doc1;

    /**
     * @var int
     *
     * @ORM\Column(name="doc2", type="integer", nullable=true)
     */
    private $doc2;

    /**
     * @var int
     *
     * @ORM\Column(name="doc3", type="integer", nullable=true)
     */
    private $doc3;

    /**
     * @var int
     *
     * @ORM\Column(name="doc4", type="integer", nullable=true)
     */
    private $doc4;

    /**
     * @var int
     *
     * @ORM\Column(name="doc5", type="integer", nullable=true)
     */
    private $doc5;

    /**
     * @var int
     *
     * @ORM\Column(name="doc6", type="integer", nullable=true)
     */
    private $doc6;

    /**
     * @var int
     *
     * @ORM\Column(name="doc7", type="integer", nullable=true)
     */
    private $doc7;

    /**
     * @var int
     *
     * @ORM\Column(name="doc8", type="integer", nullable=true)
     */
    private $doc8;

    /**
     * @var int
     *
     * @ORM\Column(name="doc9", type="integer", nullable=true)
     */
    private $doc9;

    /**
     * @var int
     *
     * @ORM\Column(name="doc10", type="integer", nullable=true)
     */
    private $doc10;

    /**
     * @var int
     *
     * @ORM\Column(name="doc11", type="integer", nullable=true)
     */
    private $doc11;

    /**
     * @var int
     *
     * @ORM\Column(name="doc12", type="integer", nullable=true)
     */
    private $doc12;

    /**
     * @var int
     *
     * @ORM\Column(name="doc13", type="integer", nullable=true)
     */
    private $doc13;

    /**
     * @var int
     *
     * @ORM\Column(name="doc14", type="integer", nullable=true)
     */
    private $doc14;

    /**
     * @var int
     *
     * @ORM\Column(name="doc15", type="integer", nullable=true)
     */
    private $doc15;

    /**
     * @var int
     *
     * @ORM\Column(name="tipo_inscricao", type="integer", nullable=true)
     */
    private $tipoInscricao;

    /**
     * @var int
     *
     * @ORM\Column(name="modelo_inscricao", type="integer", nullable=true)
     */
    private $modeloInscricao;

    /**
     * @var int
     *
     * @ORM\Column(name="projeto_participante_min", type="integer", nullable=true)
     */
    private $projetoParticipanteMin;

    /**
     * @var int
     *
     * @ORM\Column(name="projeto_participante_max", type="integer", nullable=true)
     */
    private $projetoParticipanteMax;

    /**
     * @var int
     *
     * @ORM\Column(name="tipo_inscricao_limite", type="integer", nullable=true)
     */
    private $tipoInscricaoLimite;

    /**
     * @var int
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
     * @var bool
     *
     * @ORM\Column(name="resultado0", type="boolean", nullable=true)
     */
    private $resultado0;

    /**
     * @var bool
     *
     * @ORM\Column(name="resultado1", type="boolean", nullable=true)
     */
    private $resultado1;

    /**
     * @var bool
     *
     * @ORM\Column(name="resultado2", type="boolean", nullable=true)
     */
    private $resultado2;

    /**
     * @var bool
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
     * @var int
     *
     * @ORM\Column(name="publico", type="integer", nullable=true)
     */
    private $publico;

    /**
     * @var int
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
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalUsuario", mappedBy="idEdital")
     */
    private $idEditalUsuario;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalUsuarioConvite", mappedBy="idEdital")
     */
    private $idEditalUsuarioConvite;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="FrigaArquivo", inversedBy="idEdital")
     *
     * @ORM\JoinTable(name="friga_edital_tem_arquivo",
     *   joinColumns={
     *
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
     *
     * @ORM\OrderBy({"descricao" = "ASC"})
     */
    private $cargo;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalCota", mappedBy="idEdital")
     *
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
     * @ORM\OneToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalEtapaCategoria", mappedBy="idEdital")
     */
    private $idEtapaCategoria;

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
     *
     * @ORM\OrderBy({"nome" = "ASC", "idSituacao" = "DESC"})
     */
    private $inscricao;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Nte\UsuarioBundle\Entity\ImpedimentoDeclaracao", mappedBy="idEdital")
     */
    private $idImpedimentoDeclaracao;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->idEditalUsuario = new ArrayCollection();
        $this->idEditalUsuarioConvite = new ArrayCollection();
        $this->idArquivo = new ArrayCollection();
        $this->etapa = new ArrayCollection();
        $this->idEtapaCategoria = new ArrayCollection();
        $this->cargo = new ArrayCollection();
        $this->cota = new ArrayCollection();
        $this->desempate = new ArrayCollection();
        $this->idImpedimentoDeclaracao = new ArrayCollection();

        $this->resultado0 = true;
        $this->resultado1 = false;
        $this->resultado2 = false;
        $this->resultado3 = false;
    }

    public function __clone()
    {
        $this->idEditalUsuario = new ArrayCollection();
        $this->idArquivo = new ArrayCollection();
        $this->etapa = new ArrayCollection();
        $this->idEtapaCategoria = new ArrayCollection();
        $this->cargo = new ArrayCollection();
        $this->cota = new ArrayCollection();
        $this->desempate = new ArrayCollection();
        $this->idImpedimentoDeclaracao = new ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set titulo.
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
     * Get titulo.
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
     *
     * @return FrigaEdital
     */
    public function setSubtitulo($subtitulo)
    {
        $this->subtitulo = $subtitulo;

        return $this;
    }

    /**
     * Set dataPublicacaoOficial.
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
     * Get dataPublicacaoOficial.
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
     *
     * @return FrigaEdital
     */
    public function setSituacao($situacao)
    {
        $this->situacao = $situacao;

        return $this;
    }

    /**
     * @return string
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * @param string $numero
     *
     * @return FrigaEdital
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrlProcesso()
    {
        return $this->urlProcesso;
    }

    /**
     * @param string $urlProcesso
     *
     * @return FrigaEdital
     */
    public function setUrlProcesso($urlProcesso)
    {
        $this->urlProcesso = $urlProcesso;

        return $this;
    }

    /**
     * Set url.
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
     * Get url.
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
     *
     * @return FrigaEdital
     */
    public function setIdCategoria($idCategoria)
    {
        $this->idCategoria = $idCategoria;

        return $this;
    }

    /**
     * Set anexo0.
     *
     * @param int $anexo0
     *
     * @return FrigaEdital
     */
    public function setAnexo0($anexo0)
    {
        $this->anexo0 = $anexo0;

        return $this;
    }

    /**
     * Get anexo0.
     *
     * @return int
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
     * Set anexo1.
     *
     * @param int $anexo1
     *
     * @return FrigaEdital
     */
    public function setAnexo1($anexo1)
    {
        $this->anexo1 = $anexo1;

        return $this;
    }

    /**
     * Get anexo1.
     *
     * @return int
     */
    public function getAnexo1()
    {
        return $this->anexo1;
    }

    /**
     * Set tipoInscricao.
     *
     * @param int $tipoInscricao
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
     *
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
     *
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
     *
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
     *
     * @return FrigaEdital
     */
    public function setProjetoParticipanteMax($projetoParticipanteMax)
    {
        $this->projetoParticipanteMax = $projetoParticipanteMax;

        return $this;
    }

    /**
     * Get tipoInscricao.
     *
     * @return int
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
     *
     * @return FrigaEdital
     */
    public function setPermitirEstrangeiro($permitirEstrangeiro)
    {
        $this->permitirEstrangeiro = $permitirEstrangeiro;

        return $this;
    }

    /**
     * Set sobre.
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
     * Get sobre.
     *
     * @return string
     */
    public function getSobre()
    {
        return $this->sobre;
    }

    /**
     * Set info1.
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
     * Get info1.
     *
     * @return string
     */
    public function getInfo1()
    {
        return $this->info1;
    }

    /**
     * Set info2.
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
     * Get info2.
     *
     * @return string
     */
    public function getInfo2()
    {
        return $this->info2;
    }

    /**
     * Set info3.
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
     * Get info3.
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
     *
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
     *
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
     *
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
     *
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
     *
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
     *
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
     *
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
     *
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
     *
     * @return FrigaEdital
     */
    public function setTermoCompromissoSituacao($termoCompromissoSituacao)
    {
        $this->termoCompromissoSituacao = $termoCompromissoSituacao;

        return $this;
    }

    /**
     * Set uuid.
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
     * Get uuid.
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
     * Set registroDataCriacao.
     *
     * @param \DateTime $registroDataCriacao
     *
     * @return FrigaEdital
     */
    public function setRegistroDataCriacao($registroDataCriacao)
    {
        $this->registroDataCriacao = $registroDataCriacao;

        return $this;
    }

    /**
     * Get registroDataCriacao.
     *
     * @return \DateTime
     */
    public function getRegistroDataCriacao()
    {
        return $this->registroDataCriacao;
    }

    /**
     * Set registroDataAtualizacao.
     *
     * @param \DateTime $registroDataAtualizacao
     *
     * @return FrigaEdital
     */
    public function setRegistroDataAtualizacao($registroDataAtualizacao)
    {
        $this->registroDataAtualizacao = $registroDataAtualizacao;

        return $this;
    }

    /**
     * Get registroDataAtualizacao.
     *
     * @return \DateTime
     */
    public function getRegistroDataAtualizacao()
    {
        return $this->registroDataAtualizacao;
    }

    /**
     * Add idArquivo.
     *
     * @return FrigaEdital
     */
    public function addIdArquivo(FrigaArquivo $idArquivo)
    {
        $this->idArquivo[] = $idArquivo;

        return $this;
    }

    /**
     * Remove idArquivo.
     */
    public function removeIdArquivo(FrigaArquivo $idArquivo)
    {
        $this->idArquivo->removeElement($idArquivo);
    }

    /**
     * Get idArquivo.
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
     *
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
     *
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
     *
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
     *
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
     *
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
     *
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
     *
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
     *
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
     *
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
     *
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
     *
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
     *
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
     *
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
     *
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
     *
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
     *
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
     *
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
     *
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
     *
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
     *
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
     *
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
     *
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
     *
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
     *
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
     *
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
     *
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
     *
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
     *
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
     *
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
     * @return ArrayCollection
     */
    public function getIdEtapaCategoria()
    {
        $criteria = Criteria::create();
        $criteria->orderBy(['ordem' => Criteria::ASC]);

        return $this->idEtapaCategoria->matching($criteria);
    }

    /**
     * @return ArrayCollection
     */
    public function getIdEditalUsuarioConvite()
    {
        return $this->idEditalUsuarioConvite;
    }

    /**
     * @return ArrayCollection
     */
    public function getIdEditalUsuarioConviteArquivo()
    {
        $tmp = new ArrayCollection();

        /** @var FrigaEditalUsuarioConvite $item */
        foreach ($this->idEditalUsuarioConvite as $item) {
            foreach ($item->getIdArquivo() as $subitem) {
                if (!$tmp->contains($subitem)) {
                    $tmp->add($subitem);
                }
            }
        }

        return $tmp;
    }

    /**
     * @return ArrayCollection
     */
    public function getIdEditalUsuarioArquivo()
    {
        $tmp = new ArrayCollection();
        /** @var FrigaEditalUsuario $item */
        foreach ($this->idEditalUsuario as $item) {
            foreach ($item->getIdArquivo() as $subitem) {
                if (!$tmp->contains($subitem)) {
                    $tmp->add($subitem);
                }
            }
        }

        return $tmp;
    }

    /**
     * @return ArrayCollection
     */
    public function getIdEditalUsuarioConviteSituacao($sit = [null, 0, 1])
    {
        $criteria = Criteria::create();
        $criteria->where($criteria::expr()->in('aceite', $sit));
        foreach ($sit as $item) {
        }

        return $this->idEditalUsuarioConvite->matching($criteria);
    }

    /**
     * @return ArrayCollection
     */
    public function getIdEditalUsuarioConvitePendente($situacao = null)
    {
        $criteria = Criteria::create();
        $criteria->where($criteria::expr()->eq('aceite', $situacao));

        return $this->idEditalUsuarioConvite->matching($criteria);
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
        return $this->pontuacaoCategoria->filter(function(FrigaEditalPontuacaoCategoria $c) {
            return null == $c->getIdCategoria();
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
    public function getIdImpedimentoDeclaracao(Usuario $usuario = null)
    {
        if (!\is_null($usuario)) {
            $criteria = Criteria::create()->where(Criteria::expr()->eq('idUsuario', $usuario));

            return $this->idImpedimentoDeclaracao->matching($criteria);
        }

        return $this->idImpedimentoDeclaracao;
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
        if (!\is_null($usuario) and \is_object($usuario)) {
            foreach ($usuario->getIdEditalCargo() as $cargo) {
                $tmp[] = $cargo->getId();
            }
        }

        return $tmp;
    }

    public function getArrayIdEditalCargo()
    {
        $tmp = [];
        /** @var FrigaEditalCargo $item */
        foreach ($this->getCargo() as $item) {
            $tmp[] = $item->getId();
        }

        return $tmp;
    }

    public function getInscricaoInvalida()
    {
        //Filtra todos que não são anulados
        return $this->getInscricao()->filter(
            function(FrigaInscricao $inscricao) {
                return -999 === $inscricao->getIdSituacao();
            });
    }

    /**
     * @return ArrayCollection
     *
     * @throws \Exception
     */
    public function getInscricaoValida($usuario = false, FrigaEditalEtapaCategoria $cat = null)
    {
        $criteria = Criteria::create();
        $criteria->andWhere(Criteria::expr()->neq('idSituacao', -999));
        if (!\is_null($cat) and !\is_null($cat->getPeriodoInscricao())) {
            $criteria->andWhere(Criteria::expr()->eq('idEtapa', $cat->getPeriodoInscricao()));
        }

        $tmp = $this->getInscricao()->matching($criteria);
        if (false !== $usuario) {
            $cargo = $this->getUsuarioEditalCargos($usuario);
            $tmp = $tmp->filter(
                function(FrigaInscricao $inscricao) use ($cargo) {
                    return !\is_null($inscricao->getIdCargo()) and \in_array($inscricao->getIdCargo()->getId(), $cargo);
                });
        }
        //Separa os inscrições em sentido positivo
        $tmp1 = $tmp->filter(function(FrigaInscricao $inscricao) {
            return 0 == $inscricao->getIdSituacao()
                or 2 == $inscricao->getIdSituacao()
                or $inscricao->getIdSituacao() >= 4;
        })->getIterator();

        //Ordena as inscrições positivas
        /* $tmp1->uasort(function (FrigaInscricao $a, FrigaInscricao $b) {
             if($a->getIdSituacao() != $b->getIdSituacao()){
                 return $a->getIdSituacao() <=> $b->getIdSituacao();
             }elseif (strnatcmp($a->getNome(),$b->getNome())){
                 return strnatcmp($a->getNome(),$b->getNome());
             }
         });*/

        //Separa as inscrições negativas
        $tmp2 = $tmp->filter(function(FrigaInscricao $inscricao) {
            return 1 == $inscricao->getIdSituacao()
                or 3 == $inscricao->getIdSituacao();
        });

        return new ArrayCollection(\array_merge($tmp1->getArrayCopy(), $tmp2->toArray()));
    }

    /**
     * @return ArrayCollection
     */
    public function getInscricaoAvalicao()
    {
        return $this->getInscricao()->filter(
            function(FrigaInscricao $inscricao) {
                return -999 !== $inscricao->getIdSituacao()
                    // $inscricao->getIdSituacao() !== 1 and
                    and 3 !== $inscricao->getIdSituacao();
            });
    }

    /**
     * @param int $situacao
     * @param null $cargo
     *
     * @return ArrayCollection
     */
    public function getInscricaoSituacao($situacao = 0, $cargo = null)
    {
        return $this->getInscricao()->filter(
            function(FrigaInscricao $inscricao) use ($situacao, $cargo) {
                if ($cargo) {
                    return $inscricao->getIdSituacao() === $situacao
                        and $cargo->getId() === $inscricao->getIdCargo()->getId();
                } else {
                    return $inscricao->getIdSituacao() === $situacao;
                }
            });
    }

    /**
     * @param null $etapa
     *
     * @return ArrayCollection
     */
    public function getInscricaoRecurso($etapa = null)
    {
        return $this->getInscricao()->filter(
            function(FrigaInscricao $inscricao) use ($etapa) {
                if ($etapa) {
                    if ($inscricao->getRecursosEtapa($etapa)->count()) {
                        return $inscricao;
                    }
                } else {
                    return $inscricao->getRecursos()->count();
                }
            });
    }

    /**
     * @return ArrayCollection
     */
    public function getIdEditalUsuarioOrdem()
    {
        $tmp = $this->idEditalUsuario->getIterator();
        $tmp->uasort(function(FrigaEditalUsuario $a, FrigaEditalUsuario $b) {
            return \strcmp(\strtoupper($a->getIdUsuario()->getNome()), \strtoupper($b->getIdUsuario()->getNome()));
        });

        // dump(new ArrayCollection($tmp->getArrayCopy()));
        return $tmp;
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
        return $this->idEditalUsuario->filter(function(FrigaEditalUsuario $feu) {
            return $feu->isAvaliador() and $feu->isTermoCompromisso();
        });
    }

    /**
     * @return ArrayCollection
     *
     * @throws \Exception
     */
    public function getEtapaCronologica($categoria = false)
    {
        $etapas = $this->etapa;
        $criteria = Criteria::create();
        if (false !== $categoria) {
            $criteria->andWhere(Criteria::expr()->eq('idEtapaCategoria', $categoria));
        }
        $criteria->orderBy(['dataInicial' => Criteria::ASC]);

        return $etapas->matching($criteria);
    }

    /**
     * @return ArrayCollection
     */
    public function getEtapaAvaliacao($habilitacao = null)
    {
        return $this->etapa->filter(function(FrigaEditalEtapa $etapa) use ($habilitacao) {
            if (null != $habilitacao) {
                return $etapa->getPeriodoHabilitado() == $habilitacao
                    and (3 == $etapa->getTipo() or 7 == $etapa->getTipo());
            }

            return 3 == $etapa->getTipo() or 7 == $etapa->getTipo();
        });
    }

    /**
     * @return ArrayCollection
     */
    public function getEtapaConvocacao($habilitacao = null)
    {
        return $this->etapa->filter(function(FrigaEditalEtapa $etapa) use ($habilitacao) {
            if (null != $habilitacao) {
                return $etapa->getPeriodoHabilitado() == $habilitacao
                    and 5 == $etapa->getTipo();
            }

            return 5 == $etapa->getTipo();
        });
    }

    /**
     * @return ArrayCollection
     */
    public function getEtapaClassificacao($habilitacao = null)
    {
        return $this->etapa->filter(function(FrigaEditalEtapa $etapa) use ($habilitacao) {
            if (null != $habilitacao) {
                return $etapa->getPeriodoHabilitado() == $habilitacao
                    and 4 == $etapa->getTipo();
            }

            return 4 == $etapa->getTipo();
        });
    }

    /**
     * @return ArrayCollection
     */
    public function getEtapaAvaliacaoClassificacao()
    {
        return $this->etapa->filter(function(FrigaEditalEtapa $etapa) {
            return @$etapa->getAndamentoPrazo() >= 0
                and @$etapa->getAndamentoPrazo() < 100
                and (3 == $etapa->getTipo()
                    or 4 == $etapa->getTipo()
                    or 7 == $etapa->getTipo());
        });
    }

    public function getPontuacaoInscricao()
    {
        $pt = new ArrayCollection();
        /** @var FrigaEditalPontuacao $pontuacao */
        foreach ($this->getPontuacao() as $pontuacao) {
            /** @var FrigaEditalEtapa $etapa */
            foreach ($pontuacao->getIdEtapa() as $etapa) {
                if (1 == $etapa->getTipo()) {
                    if (!$pt->contains($pontuacao)) {
                        $pt->add($pontuacao);
                    }
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
                    if (1 != $etapa->getTipo()) {
                        if ($pt->contains($pontuacao)) {
                            $pt->removeElement($pontuacao);
                        }
                    }
                }
            }
        }

        return $pt;
    }

    /**
     * @ORM\PreUpdate
     *
     * @throws \Exception
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        if ($args->hasChangedField('registroDataCriacao')) {
            $this->setRegistroDataCriacao($args->getOldValue('registroDataCriacao'));
            $this->setRegistroDataAtualizacao(new \DateTime());
        }
    }

    /**
     * @ORM\PrePersist
     *
     * @throws \Exception
     */
    public function PrePersist()
    {
        $this->setRegistroDataCriacao(new \DateTime());
        $this->setRegistroDataAtualizacao(new \DateTime());
    }

    /**
     * @ORM\PreRemove
     *
     * @throws \Exception
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        if ($this->getPontuacao()->count()) {
            foreach ($this->getPontuacao() as $item) {
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
        if ($this->getEtapa()->count()) {
            /** @var FrigaEditalEtapa $item */
            foreach ($this->getEtapa() as $item) {
                foreach ($item->getFilhos() as $subitem) {
                    $args->getEntityManager()->remove($subitem);
                }
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
            if (1 == $etapa->getTipo()) {
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
            if (4 == $etapa->getTipo()) {
                if (false === $tmp) {
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
            $tmp = new ArrayCollection(\array_merge($tmp->toArray(), $item->getRecurso()->toArray()));
        }

        return $tmp;
    }

    /**
     * @param int $situacao
     *
     * @return ArrayCollection
     */
    public function getRecursosSituacao($situacao = 0)
    {
        return $this->getRecursos()->filter(function(FrigaInscricaoRecurso $r) use ($situacao) {
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
            ->diff(new \DateTime())
            ->days;

        if (0 == $contagemDias) {
            return 0;
        }
        $porcentagem = ($contagemDias * 100) / $total;
        $dt0 = new \DateTime();
        if ($dt0 < $this->getPeriodoInscricao()->getDataInicial()) {
            return 0;
        }

        return $porcentagem > 100 ? 100 : \round($porcentagem, 2);
    }

    /**
     * @return bool
     *
     * @throws \Exception
     */
    public function getPeriodoInscricaoHabilitado()
    {
        if ($this->getPeriodoInscricao()) {
            $dt = new \DateTime();

            return $dt > $this->getPeriodoInscricao()->getDataInicial()
                and $dt < $this->getPeriodoInscricao()->getDataFinal();
        }

        return false;
    }

    /**
     * @return null|FrigaEditalEtapa
     */
    public function getPeriodoInscricaoAtual()
    {
        $dt = new \DateTime();

        /** @var FrigaEditalEtapa $etapa */
        foreach ($this->etapa as $etapa) {
            if (1 == $etapa->getTipo()
                and $dt > $etapa->getDataInicial()
                and $dt < $etapa->getDataFinal()
            ) {
                return $etapa;
            }
        }

        return null;
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

    /**
     * @return ArrayCollection
     */
    public function getIdArquivoPublico()
    {
        $dt = new \DateTime();

        return $this->idArquivo->filter(function(FrigaArquivo $a) use ($dt) {
            return $a->getDataPublicacao()->getTimestamp() <= $dt->getTimestamp();
        });
    }

    public function getIdEtapaAvaliacao()
    {
        $criteria = Criteria::create();
        $criteria->andWhere(Criteria::expr()->in('tipo', [3, 4, 5, 7]));
        $criteria->andWhere(Criteria::expr()->lte('dataDivulgacao', new \DateTime()));
        $criteria->orderBy(['dataDivulgacao' => Criteria::DESC]);

        return $this->etapa->matching($criteria);
    }

    /**
     * @return array
     *
     * @throws \Exception
     */
    public function getInformacaoPublico()
    {
        $tmp = [];
        $criteria = Criteria::create();
        $criteria->andWhere(Criteria::expr()->in('tipo', [4, 5, 7, 8, 9]));
        $criteria->andWhere(Criteria::expr()->lte('dataDivulgacao', new \DateTime()));
        $criteria->orderBy(['dataDivulgacao' => Criteria::DESC]);
        $etapas = $this->etapa->matching($criteria);

        $criteria = Criteria::create();
        $criteria->andWhere(Criteria::expr()->lte('dataPublicacao', (new \DateTime())->format('Y-m-d H:i:s')));
        $criteria->orderBy(['dataPublicacao' => Criteria::DESC]);
        $arquivo = $this->idArquivo->matching($criteria);

        /** @var FrigaEditalEtapa $item */
        foreach ($etapas as $item) {
            if (
                (4 == $item->getTipo() and $item->getClassificacao()->isEmpty())
                or (5 == $item->getTipo() and $item->getConvocacao()->isEmpty())
                or (7 == $item->getTipo() and \is_null($item->getIdEtapa()))
                or (7 == $item->getTipo() and !\is_null($item->getIdEtapa()) and $item->getIdEtapa()->getRecurso()->isEmpty())
                or (8 == $item->getTipo() and $this->getInscricaoValida()->isEmpty())
                or (9 == $item->getTipo() and $this->getIdEditalUsuarioBanca()->isEmpty())
            ) {
                continue;
            }
            if (5 != $item->getTipo() or (5 == $item->getTipo() and 1 != $item->getFinal())) {
                $obj = new \stdClass();
                $obj->etapa = true;
                $obj->arquivo = false;
                $obj->final = false;
                $obj->id = $item->getId();
                $obj->tipo = $item->getTipo();
                $obj->titulo = $item->getDescricao();
                $obj->data = $item->getDataDivulgacao();
                $obj->timestamp = $item->getDataDivulgacao()->getTimestamp();

                $tmp[] = $obj;
            } elseif (5 == $item->getTipo() and 1 == $item->getFinal()) {
                /** @var FrigaConvocacao $subitem */
                foreach ($item->getConvocacao() as $subitem) {
                    $obj = new \stdClass();
                    $obj->etapa = true;
                    $obj->arquivo = false;
                    $obj->final = true;
                    $obj->id = $item->getId();
                    $obj->tipo = $item->getTipo();
                    $obj->titulo = $item->getDescricao();
                    $obj->data = $subitem->getRegistroDataCriacao();
                    $obj->timestamp = $subitem->getRegistroDataCriacao()->getTimestamp();
                    $tmp[$subitem->getRegistroDataCriacao()->format('Ymd')] = $obj;
                }
            }
        }

        /** @var FrigaArquivo $item */
        foreach ($arquivo as $item) {
            $obj = new \stdClass();
            $obj->etapa = false;
            $obj->arquivo = true;
            $obj->final = false;
            $obj->id = $item->getId();
            $obj->tipo = 0;
            $obj->titulo = $item->getTitulo();
            $obj->data = $item->getDataPublicacao();
            $obj->timestamp = $item->getDataPublicacao()->getTimestamp();
            $tmp[] = $obj;
        }

        \uasort($tmp, function($a, $b) {
            return $b->timestamp <=> $a->timestamp;
        });

        return $tmp;
    }
    /*
     * etapa.tipo == 4 or etapa.tipo == 5  or etapa.tipo == 7 or etapa.tipo == 8  or etapa.tipo == 9
     */
}
