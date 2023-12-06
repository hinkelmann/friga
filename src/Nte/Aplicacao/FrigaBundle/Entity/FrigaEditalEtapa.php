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
use Doctrine\ORM\Mapping as ORM;

/**
 * FrigaEditalEtapa.
 *
 * @ORM\Table(name="friga_edital_etapa")
 *
 * @ORM\Entity
 */
class FrigaEditalEtapa
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
     * @var int
     *
     * @ORM\Column(name="tipo", type="integer", nullable=true)
     */
    private $tipo;

    /**
     * @var int
     *
     * @ORM\Column(name="ordem", type="integer", nullable=true)
     */
    private $ordem;

    /**
     * @var int
     *
     * @ORM\Column(name="cron", type="integer", nullable=true)
     */
    private $cron;

    /**
     * @var int
     *
     * @ORM\Column(name="final", type="integer", nullable=true)
     */
    private $final;

    /**
     * @var int
     *
     * @ORM\Column(name="oculto", type="integer", nullable=true)
     */
    private $oculto;

    /**
     * @var int
     *
     * @ORM\Column(name="qtd_classificado", type="integer", nullable=true)
     */
    private $qtdClassificado;

    /**
     * @var float|null
     *
     * @ORM\Column(name="valor_maximo", type="decimal", precision=10, scale=5, nullable=true)
     */
    private $valorMaximo;

    /**
     * @var float|null
     *
     * @ORM\Column(name="valor_minimo", type="decimal", precision=10, scale=5, nullable=true)
     */
    private $valorMinimo;

    /**
     * @var int
     *
     * @ORM\Column(name="desconsiderar_inscricao", type="integer", nullable=true)
     */
    private $desconsiderarInscricao;

    /**
     * @var string
     *
     * @ORM\Column(name="descricao", type="text", length=65535, nullable=true)
     */
    private $descricao;

    /**
     * @var string
     *
     * @ORM\Column(name="observacao", type="text", length=65535, nullable=true)
     */
    private $observacao;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="data_inicial", type="datetime", nullable=true)
     */
    private $dataInicial;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="data_final", type="datetime", nullable=true)
     */
    private $dataFinal;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="data_divulgacao", type="datetime", nullable=true)
     */
    private $dataDivulgacao;

    /**
     * @var FrigaEdital
     *
     * @ORM\ManyToOne(targetEntity="FrigaEdital", inversedBy="etapa")
     *
     * @ORM\JoinColumns({
     *
     *   @ORM\JoinColumn(name="id_edital", referencedColumnName="id")
     * })
     */
    private $idEdital;

    /**
     * @var FrigaEditalEtapa
     *
     * @ORM\ManyToOne(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalEtapa", inversedBy="filhos")
     *
     * @ORM\JoinColumns({
     *
     *   @ORM\JoinColumn(name="id_etapa", referencedColumnName="id", nullable=true)
     * })
     */
    private $idEtapa;

    /**
     * @var FrigaEditalEtapaCategoria
     *
     * @ORM\ManyToOne(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalEtapaCategoria", inversedBy="idEtapa")
     *
     * @ORM\JoinColumns({
     *
     *   @ORM\JoinColumn(name="id_etapa_categoria", referencedColumnName="id")
     * })
     */
    private $idEtapaCategoria;

    /**
     * @var bool
     *
     * @ORM\Column(name="bloqueio", type="boolean", nullable=true)
     */
    private $bloqueio;

    /**
     * @var bool
     *
     * @ORM\Column(name="pontuacao_multipla", type="boolean", nullable=true)
     */
    private $pontuacaoMultipla;

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
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalPontuacao", mappedBy="idEtapa")
     */
    private $idPontuacao;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaConvocacao", mappedBy="idEtapa")
     */
    private $convocacao;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricaoPontuacaoAvaliacao", mappedBy="idEtapa")
     */
    private $avaliacao;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaClassificacao", mappedBy="idEtapa")
     */
    private $classificacao;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricaoRecurso", mappedBy="idEditalEtapa")
     */
    private $recurso;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricao", mappedBy="idEtapa")
     */
    private $inscricao;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalEtapa", mappedBy="idEtapa")
     */
    private $filhos;

    /**
     * FrigaEditalEtapa constructor.
     */
    public function __construct()
    {
        $this->idPontuacao = new ArrayCollection();
        $this->convocacao = new ArrayCollection();
        $this->recurso = new ArrayCollection();
        $this->avaliacao = new ArrayCollection();
        $this->inscricao = new ArrayCollection();
        $this->classificacao = new ArrayCollection();
        $this->filhos = new ArrayCollection();

        $this->dataInicial = new \DateTime();
        $this->dataFinal = new \DateTime();
        $this->dataDivulgacao = new \DateTime();
    }

    public function __clone()
    {
        $this->idPontuacao = new ArrayCollection();
        $this->convocacao = new ArrayCollection();
        $this->recurso = new ArrayCollection();
        $this->inscricao = new ArrayCollection();
        $this->avaliacao = new ArrayCollection();
        $this->classificacao = new ArrayCollection();
        $this->idEtapaCategoria = null;
        $this->filhos = new ArrayCollection();
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
     * Set tipo.
     *
     * @param int $tipo
     *
     * @return FrigaEditalEtapa
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo.
     *
     * @return int
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set ordem.
     *
     * @param int $ordem
     *
     * @return FrigaEditalEtapa
     */
    public function setOrdem($ordem)
    {
        $this->ordem = $ordem;

        return $this;
    }

    /**
     * Get ordem.
     *
     * @return int
     */
    public function getOrdem()
    {
        return $this->ordem;
    }

    /**
     * Set descricao.
     *
     * @param string $descricao
     *
     * @return FrigaEditalEtapa
     */
    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;

        return $this;
    }

    /**
     * Get descricao.
     *
     * @return string
     */
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * Set dataInicial.
     *
     * @param \DateTime $dataInicial
     *
     * @return FrigaEditalEtapa
     */
    public function setDataInicial($dataInicial)
    {
        $this->dataInicial = $dataInicial;

        return $this;
    }

    /**
     * Get dataInicial.
     *
     * @return \DateTime
     */
    public function getDataInicial()
    {
        return $this->dataInicial;
    }

    /**
     * Set dataFinal.
     *
     * @param \DateTime $dataFinal
     *
     * @return FrigaEditalEtapa
     */
    public function setDataFinal($dataFinal)
    {
        $this->dataFinal = $dataFinal;

        return $this;
    }

    /**
     * Get dataFinal.
     *
     * @return \DateTime
     */
    public function getDataFinal()
    {
        return $this->dataFinal;
    }

    /**
     * Set dataDivulgacao.
     *
     * @param \DateTime $dataDivulgacao
     *
     * @return FrigaEditalEtapa
     */
    public function setDataDivulgacao($dataDivulgacao)
    {
        $this->dataDivulgacao = $dataDivulgacao;

        return $this;
    }

    /**
     * Get dataDivulgacao.
     *
     * @return \DateTime
     */
    public function getDataDivulgacao()
    {
        return $this->dataDivulgacao;
    }

    /**
     * Set idEdital.
     *
     * @return FrigaEditalEtapa
     */
    public function setIdEdital(FrigaEdital $idEdital = null)
    {
        $this->idEdital = $idEdital;

        return $this;
    }

    /**
     * @return bool
     */
    public function isBloqueio()
    {
        return $this->bloqueio;
    }

    /**
     * @param bool $bloqueio
     *
     * @return FrigaEditalEtapa
     */
    public function setBloqueio($bloqueio)
    {
        $this->bloqueio = $bloqueio;

        return $this;
    }

    /**
     * @return int
     */
    public function getFinal()
    {
        return $this->final;
    }

    /**
     * @param int $final
     *
     * @return FrigaEditalEtapa
     */
    public function setFinal($final)
    {
        $this->final = $final;

        return $this;
    }

    /**
     * @return int
     */
    public function getCron()
    {
        return $this->cron;
    }

    /**
     * @param int $cron
     *
     * @return FrigaEditalEtapa
     */
    public function setCron($cron)
    {
        $this->cron = $cron;

        return $this;
    }

    /**
     * Get idEdital.
     *
     * @return FrigaEdital
     */
    public function getIdEdital()
    {
        return $this->idEdital;
    }

    /**
     * tipo 1 - Inscricao
     * tipo 2 - Candidato informa pontuação
     * tipo 3 - Avaliador informa a pontuação.
     *
     * @param int $tipo
     *
     * @return ArrayCollection
     */
    public function getPontuacaoRelativa($tipo = 1)
    {
        return $this->getIdPontuacao()->filter(function(FrigaEditalPontuacao $pontuacao) use ($tipo) {
            return $pontuacao->getIdEtapa()->filter(function(FrigaEditalEtapa $etapa) use ($tipo) {
                return $etapa->getTipo() == $tipo;
            })->count();
        });
    }

    /**
     * @return bool
     */
    public function getPontuacaoRelativaUsuario()
    {
        return $this->getPontuacaoRelativa(1)->count() > 0
            or $this->getPontuacaoRelativa(2)->count() > 0;
    }

    public function getPeriodoHabilitado()
    {
        $dt = new \DateTime();

        return $dt > $this->getDataInicial()
            and $dt < $this->getDataFinal();
    }

    public function getPeriodoDivulgacao()
    {
        if (8 == $this->tipo or 9 == $this->tipo) {
            return (new \DateTime()) > $this->getDataInicial();
        }

        //dump($this);
        return (new \DateTime()) > $this->getDataDivulgacao();
    }

    public function getAndamentoPrazo()
    {
        $total = $this->dataInicial->diff($this->dataFinal)->days;
        $contagemDias = $this->dataInicial->diff(new \DateTime())->days;
        $porcentagem = ($contagemDias * 100) / ($total > 0 ? $total : 1);

        $dt0 = new \DateTime();
        if ($dt0 < $this->dataInicial) {
            return 0;
        }

        return $porcentagem > 100 ? 100 : \round($porcentagem, 2);
    }

    /**
     * @return $this
     */
    public function addIdPontuacao(FrigaEditalPontuacao $idPontuacao)
    {
        $this->idPontuacao = $idPontuacao;

        return $this;
    }

    public function removeIdPontuacao(FrigaEditalPontuacao $idPontuacao)
    {
        if ($this->idPontuacao->contains($idPontuacao)) {
            $this->idPontuacao->removeElement($idPontuacao);
        }
    }

    /**
     * @return ArrayCollection
     */
    public function getIdPontuacao()
    {
        return $this->idPontuacao;
    }

    /**
     * @return ArrayCollection
     */
    public function getConvocacao()
    {
        return $this->convocacao;
    }

    /**
     * @return ArrayCollection
     */
    public function getAvaliacao()
    {
        return $this->avaliacao;
    }

    /**
     * @return bool
     */
    public function isPontuacaoMultipla()
    {
        return $this->pontuacaoMultipla;
    }

    /**
     * @return FrigaEditalEtapa
     */
    public function getIdEtapa()
    {
        return $this->idEtapa;
    }

    /**
     * @return ArrayCollection
     */
    public function getInscricao()
    {
        return $this->inscricao;
    }

    /**
     * @return FrigaEditalEtapaCategoria
     */
    public function getIdEtapaCategoria()
    {
        return $this->idEtapaCategoria;
    }

    /**
     * @param FrigaEditalEtapaCategoria $idEtapaCategoria
     *
     * @return FrigaEditalEtapa
     */
    public function setIdEtapaCategoria($idEtapaCategoria)
    {
        $this->idEtapaCategoria = $idEtapaCategoria;

        return $this;
    }

    /**
     * @return int
     */
    public function getDesconsiderarInscricao()
    {
        return $this->desconsiderarInscricao;
    }

    /**
     * @param int $desconsiderarInscricao
     *
     * @return FrigaEditalEtapa
     */
    public function setDesconsiderarInscricao($desconsiderarInscricao)
    {
        $this->desconsiderarInscricao = $desconsiderarInscricao;

        return $this;
    }

    /**
     * @param FrigaEdital $idEtapa
     *
     * @return FrigaEditalEtapa
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
     *
     * @return FrigaEditalEtapa
     */
    public function setObservacao($observacao)
    {
        $this->observacao = $observacao;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getValorMaximo()
    {
        return $this->valorMaximo;
    }

    /**
     * @param float|null $valorMaximo
     *
     * @return FrigaEditalEtapa
     */
    public function setValorMaximo($valorMaximo)
    {
        $this->valorMaximo = $valorMaximo;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getValorMinimo()
    {
        return $this->valorMinimo;
    }

    /**
     * @param float|null $valorMinimo
     *
     * @return FrigaEditalEtapa
     */
    public function setValorMinimo($valorMinimo)
    {
        $this->valorMinimo = $valorMinimo;

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
     * @return FrigaEditalEtapa
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
     * @return FrigaEditalEtapa
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
     * @return FrigaEditalEtapa
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
     * @return FrigaEditalEtapa
     */
    public function setResultado3($resultado3)
    {
        $this->resultado3 = $resultado3;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getClassificacao()
    {
        return $this->classificacao;
    }

    /**
     * @return ArrayCollection
     */
    public function getClassificacaoCargo($usuario = false)
    {
        if (false === $usuario) {
            return $this->classificacao;
        }
        $cargo = $this->idEdital->getUsuarioEditalCargos($usuario);

        return $this->classificacao->filter(
            function(FrigaClassificacao $item) use ($cargo) {
                return !\is_null($item->getIdCargo())
                    and \in_array($item->getIdCargo()->getId(), $cargo);
            });
    }

    /**
     * @return ArrayCollection
     */
    public function getRecurso()
    {
        return $this->recurso;
    }

    /**
     * @return ArrayCollection
     */
    public function getFilhos()
    {
        return $this->filhos;
    }

    /**
     * @return int
     */
    public function getOculto()
    {
        return $this->oculto;
    }

    /**
     * @param int $oculto
     *
     * @return FrigaEditalEtapa
     */
    public function setOculto($oculto)
    {
        $this->oculto = $oculto;

        return $this;
    }

    /**
     * @return int
     */
    public function getQtdClassificado()
    {
        return $this->qtdClassificado;
    }

    /**
     * @param int $qtdClassificado
     *
     * @return FrigaEditalEtapa
     */
    public function setQtdClassificado($qtdClassificado)
    {
        $this->qtdClassificado = $qtdClassificado;

        return $this;
    }

    /**
     * @return null|FrigaEditalEtapa
     */
    public function getFilhoRecursoAvaliacao()
    {
        return $this->filhos->filter(function(FrigaEditalEtapa $e) {
            return 7 == $e->getTipo();
        })->first();
    }

    /**
     * @param bool $pontuacaoMultipla
     *
     * @return FrigaEditalEtapa
     */
    public function setPontuacaoMultipla($pontuacaoMultipla)
    {
        $this->pontuacaoMultipla = $pontuacaoMultipla;

        return $this;
    }

    /**
     * @return false|mixed|FrigaEditalEtapa
     *
     * @throws \Exception
     */
    public function getIdEtapaClassificacao()
    {
        $categoria = false;
        if (!\is_null($this->idEtapa)) {
            return $this->idEtapa;
        }
        if (!\is_null($this->idEtapaCategoria)) {
            $categoria = $this->idEtapaCategoria;
        }

        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('tipo', 4))
            ->andWhere(Criteria::expr()->lte('dataFinal', $this->dataFinal));

        $etapas = $this->idEdital
            ->getEtapaCronologica($categoria)
            ->matching($criteria);

        if ($etapas->isEmpty()) {
            return $this;
        }

        return $etapas->last();
    }

    public function getObjTipo()
    {
        $obj = new \stdClass();
        $obj->id = $this->getTipo();
        $obj->descricao = $this->getTipoDescricao();
        $obj->icone = '';
        $obj->css0 = '';
        $obj->css1 = '';
        switch ($this->getTipo()) {
            case 0:
                $obj->icone = 'fa fa-clock-o';
                $obj->css0 = '';
                $obj->css1 = '';
                break;
            case 1:
                $obj->icone = 'fa fa-user-plus';
                $obj->css0 = '';
                $obj->css1 = '';
                break;
            case 2:
                $obj->icone = 'fa fa-user-secret';
                $obj->css0 = '';
                $obj->css1 = '';
                break;
            case 3:
                $obj->icone = 'fa fa-check-circle';
                $obj->css0 = '';
                $obj->css1 = '';
                break;
            case 4:
                $obj->icone = 'fa fa-trophy';
                $obj->css0 = '';
                $obj->css1 = '';
                break;
            case 5:
                $obj->icone = 'fa fa-calendar-check-o';
                $obj->css0 = '';
                $obj->css1 = '';
                break;
            case 6:
                $obj->icone = 'fa fa-tint';
                $obj->css0 = '';
                $obj->css1 = '';
                break;
            case 7:
                $obj->icone = 'fa fa-gavel ';
                $obj->css0 = '';
                $obj->css1 = '';
                break;
            case 8:
                $obj->icone = 'fa fa-list ';
                $obj->css0 = '';
                $obj->css1 = '';
                break;

            case 9:
                $obj->icone = 'fa fa-th ';
                $obj->css0 = '';
                $obj->css1 = '';
                break;
        }

        return $obj;
    }

    public function getTipoDescricao()
    {
        switch ($this->getTipo()) {
            case 0:
                return 'Cronograma';
            case 1:
                return 'Inscrição';
            case 2:
                return 'Ações do candidato';
            case 3:
                return 'Ações do avaliador';
            case 4:
                return 'Classificação';
            case 5:
                return 'Convocação';
            case 6:
                return 'Recurso - Candidato';
            case 7:
                return 'Recurso - Avaliador';
            case 8:
                return 'Lista - Candidato';
            case 9:
                return 'Lista - Avaliador';
        }
    }

    /**
     * @return string|null
     */
    public function getRoute()
    {
        switch ($this->getTipo()) {
            case 3:
            case 7:
                return 'avaliacao_etapa';
            case 4:
                return 'resultado_etapa';
            case 5:
                return 'convocacao_etapa';
        }

        return null;
    }

    public function getConvocacaoData()
    {
        $tmp = [];
        /** @var FrigaConvocacao $convocacao */
        foreach ($this->convocacao as $convocacao) {
            $tmp[$convocacao->getRegistroDataCriacao()->format('Y-m-d')][] = $convocacao;
        }
        \krsort($tmp);

        return new ArrayCollection($tmp);
    }

    /**
     * @return \stdClass
     */
    public function getPR()
    {
        $base = $this->idEdital->getDataPublicacaoOficial();
        $obj = new \stdClass();
        $obj->dataDivulgacao = !\is_null($this->dataDivulgacao) ? $base->diff($this->dataDivulgacao) : null;
        $obj->dataInicial = !\is_null($this->dataInicial) ? $base->diff($this->dataInicial) : null;
        $obj->dataFinal = !\is_null($this->dataFinal) ? $base->diff($this->dataFinal) : null;

        return $obj;
    }

    public function setPR($p, $base)
    {
        $this->dataDivulgaca = clone $base;
        $this->dataInicial = clone $base;
        $this->dataFinal = clone $base;

        if (!\is_null($p->dataDivulgacao)) {
            $this->dataDivulgacao->add($p->dataDivulgacao)->setTime(0, 0, 2);
        } else {
            $this->dataDivulgacao = null;
        }
        if (!\is_null($p->dataInicial)) {
            $this->dataInicial->add($p->dataInicial)->setTime(0, 0, 2);
        } else {
            $this->dataInicial = null;
        }
        if (!\is_null($p->dataFinal)) {
            $this->dataFinal->add($p->dataFinal)->setTime(23, 59, 59);
        } else {
            $this->dataFinal = null;
        }
    }

    public function getSituacao()
    {
        $dt = new \DateTime();
        if ($dt > $this->getDataFinal()) {
            return -1;
        }
        if ($dt > $this->getDataInicial() and $dt < $this->getDataFinal()) {
            return 0;
        }
        if ($dt < $this->getDataInicial()) {
            return 1;
        }
    }
}
