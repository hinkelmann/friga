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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * FrigaEditalPontuacao.
 *
 * @ORM\Table(name="friga_edital_pontuacao")
 *
 * @ORM\Entity
 */
class FrigaEditalPontuacao
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
     * @var bool
     *
     * @ORM\Column(name="upload", type="boolean", nullable=true)
     */
    private $upload;

    /**
     * @var int
     *
     * @ORM\Column(name="pontuacao_auto", type="integer", nullable=true)
     */
    private $pontuacaoAuto;

    /**
     * @var int
     *
     * @ORM\Column(name="pontuacao_tipo", type="integer", nullable=true)
     */
    private $pontuacaoTipo;

    /**
     * @var bool
     *
     * @ORM\Column(name="requisito", type="integer", nullable=true)
     */
    private $requisito;

    /**
     * @var string
     *
     * @ORM\Column(name="titulo", type="string", length=255, nullable=true)
     */
    private $titulo;

    /**
     * @var string
     *
     * @ORM\Column(name="descricao", type="text", length=65535, nullable=true)
     */
    private $descricao;

    /**
     * @var string
     *
     * @ORM\Column(name="explicacao", type="text", length=65535, nullable=true)
     */
    private $explicacao;

    /**
     * @var string
     *
     * @ORM\Column(name="explicacao_texto", type="text", length=65535, nullable=true)
     */
    private $explicacaoTexto;

    /**
     * @var float
     *
     * @ORM\Column(name="valor_maximo", type="decimal", precision=10, scale=5, nullable=true)
     */
    private $valorMaximo;

    /**
     * @var float
     *
     * @ORM\Column(name="valor_minimo", type="decimal", precision=10, scale=5, nullable=true)
     */
    private $valorMinimo;

    /**
     * @var float
     *
     * @ORM\Column(name="valor_intervalo", type="decimal", precision=10, scale=5, nullable=true)
     */
    private $valorIntervalo;

    /**
     * @var string
     *
     * @ORM\Column(name="valor_texto", type="string", length=255, nullable=true)
     */
    private $valorTexto;

    /**
     * @var bool
     *
     * @ORM\Column(name="valor_alteracao", type="boolean", nullable=true)
     */
    private $valorAlteracao;

    /**
     * @var int
     *
     * @ORM\Column(name="ordem", type="integer", nullable=true)
     */
    private $ordem;

    /**
     * @var FrigaEdital
     *
     * @ORM\ManyToOne(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaEdital", inversedBy="pontuacao")
     *
     * @ORM\JoinColumns({
     *
     *   @ORM\JoinColumn(name="id_edital", referencedColumnName="id")
     * })
     */
    private $idEdital;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalEtapa", inversedBy="idPontuacao")
     *
     * @ORM\JoinTable(name="friga_edital_pontuacao_tem_etapa",
     *   joinColumns={
     *
     *     @ORM\JoinColumn(name="id_pontuacao", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="id_etapa", referencedColumnName="id")
     *   }
     * )
     */
    private $idEtapa;

    /**
     * @var FrigaEditalPontuacaoCategoria
     *
     * @ORM\ManyToOne(targetEntity="FrigaEditalPontuacaoCategoria", inversedBy="pontuacao")
     *
     * @ORM\JoinColumns({
     *
     *   @ORM\JoinColumn(name="id_categoria", referencedColumnName="id")
     * })
     */
    private $idCategoria;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricaoPontuacaoAvaliacao", mappedBy="idEditalPontuacao")
     */
    private $avaliacao;

    public function __construct()
    {
        $this->idEtapa = new ArrayCollection();
        $this->avaliacao = new ArrayCollection();
    }

    public function __clone()
    {
        $this->idCategoria = null;
        $this->idEtapa = new ArrayCollection();
        $this->avaliacao = new ArrayCollection();
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
     * Set upload.
     *
     * @param bool $upload
     *
     * @return FrigaEditalPontuacao
     */
    public function setUpload($upload)
    {
        $this->upload = $upload;

        return $this;
    }

    /**
     * Get upload.
     *
     * @return bool
     */
    public function getUpload()
    {
        return $this->upload;
    }

    /**
     * @return int
     */
    public function getPontuacaoAuto()
    {
        return $this->pontuacaoAuto;
    }

    /**
     * @param int $pontuacaoAuto
     *
     * @return FrigaEditalPontuacao
     */
    public function setPontuacaoAuto($pontuacaoAuto)
    {
        $this->pontuacaoAuto = $pontuacaoAuto;

        return $this;
    }

    /**
     * @return int
     */
    public function getPontuacaoTipo()
    {
        return $this->pontuacaoTipo;
    }

    /**
     * @param int $pontuacaoTipo
     *
     * @return FrigaEditalPontuacao
     */
    public function setPontuacaoTipo($pontuacaoTipo)
    {
        $this->pontuacaoTipo = $pontuacaoTipo;

        return $this;
    }

    /**
     * Set requisito.
     *
     * @param bool $requisito
     *
     * @return FrigaEditalPontuacao
     */
    public function setRequisito($requisito)
    {
        $this->requisito = $requisito;

        return $this;
    }

    /**
     * Get requisito.
     *
     * @return bool
     */
    public function getRequisito()
    {
        return $this->requisito;
    }

    /**
     * Set titulo.
     *
     * @param string $titulo
     *
     * @return FrigaEditalPontuacao
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
     * Set descricao.
     *
     * @param string $descricao
     *
     * @return FrigaEditalPontuacao
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
     * Set explicacao.
     *
     * @param string $explicacao
     *
     * @return FrigaEditalPontuacao
     */
    public function setExplicacao($explicacao)
    {
        $this->explicacao = $explicacao;

        return $this;
    }

    /**
     * Get explicacao.
     *
     * @return string
     */
    public function getExplicacao()
    {
        return $this->explicacao;
    }

    /**
     * @return string
     */
    public function getExplicacaoTexto()
    {
        return $this->explicacaoTexto;
    }

    /**
     * @param string $explicacaoTexto
     *
     * @return FrigaEditalPontuacao
     */
    public function setExplicacaoTexto($explicacaoTexto)
    {
        $this->explicacaoTexto = $explicacaoTexto;

        return $this;
    }

    /**
     * Set valorMaximo.
     *
     * @param float $valorMaximo
     *
     * @return FrigaEditalPontuacao
     */
    public function setValorMaximo($valorMaximo)
    {
        $this->valorMaximo = $valorMaximo;

        return $this;
    }

    /**
     * Get valorMaximo.
     *
     * @return float
     */
    public function getValorMaximo()
    {
        return $this->valorMaximo;
    }

    /**
     * Set valorMinimo.
     *
     * @param float $valorMinimo
     *
     * @return FrigaEditalPontuacao
     */
    public function setValorMinimo($valorMinimo)
    {
        $this->valorMinimo = $valorMinimo;

        return $this;
    }

    /**
     * Get valorMinimo.
     *
     * @return float
     */
    public function getValorMinimo()
    {
        return $this->valorMinimo;
    }

    /**
     * @return float
     */
    public function getValorIntervalo()
    {
        return $this->valorIntervalo;
    }

    /**
     * @param string $valorIntervalo
     */
    public function setValorIntervalo($valorIntervalo)
    {
        $this->valorIntervalo = $valorIntervalo;
    }

    /**
     * Set valorTexto.
     *
     * @param string $valorTexto
     *
     * @return FrigaEditalPontuacao
     */
    public function setValorTexto($valorTexto)
    {
        $this->valorTexto = $valorTexto;

        return $this;
    }

    /**
     * Get valorTexto.
     *
     * @return string
     */
    public function getValorTexto()
    {
        return $this->valorTexto;
    }

    /**
     * Set valorAlteracao.
     *
     * @param bool $valorAlteracao
     *
     * @return FrigaEditalPontuacao
     */
    public function setValorAlteracao($valorAlteracao)
    {
        $this->valorAlteracao = $valorAlteracao;

        return $this;
    }

    /**
     * Get valorAlteracao.
     *
     * @return bool
     */
    public function getValorAlteracao()
    {
        return $this->valorAlteracao;
    }

    /**
     * Set idEdital.
     *
     * @return FrigaEditalPontuacao
     */
    public function setIdEdital(FrigaEdital $idEdital = null)
    {
        $this->idEdital = $idEdital;

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
     * @return $this
     */
    public function addIdEtapa(FrigaEditalEtapa $idEtapa)
    {
        $this->idEtapa[] = $idEtapa;

        return $this;
    }

    public function removeIdEtapa(FrigaEditalEtapa $idEtapa)
    {
        if ($this->idEtapa->contains($idEtapa)) {
            $this->idEtapa->removeElement($idEtapa);
        }
    }

    /**
     * @return ArrayCollection
     */
    public function getIdEtapa()
    {
        return $this->idEtapa;
    }

    /**
     * Set idCategoria.
     *
     * @return FrigaEditalPontuacao
     */
    public function setIdCategoria(FrigaEditalPontuacaoCategoria $idCategoria = null)
    {
        $this->idCategoria = $idCategoria;

        return $this;
    }

    /**
     * @return int
     */
    public function getOrdem()
    {
        return $this->ordem;
    }

    /**
     * @param int $ordem
     *
     * @return FrigaEditalPontuacao
     */
    public function setOrdem($ordem)
    {
        $this->ordem = $ordem;

        return $this;
    }

    /**
     * Get idCategoria.
     *
     * @return FrigaEditalPontuacaoCategoria
     */
    public function getIdCategoria()
    {
        return $this->idCategoria;
    }

    /**
     * @return ArrayCollection
     */
    public function getAvaliacao()
    {
        return $this->avaliacao;
    }

    /**
     *  Requisito.
     */
    public function getObjRequisito()
    {
        $obj = new \stdClass();
        switch ($this->requisito) {
            case 0:
                $obj->id = $this->requisito;
                $obj->descricao = 'Não obrigatório';
                break;

            case 1:
                $obj->id = $this->requisito;
                $obj->descricao = 'Anexo Obrigatório';
                break;

            case 2:
                $obj->id = $this->requisito;
                $obj->descricao = 'Texto Obrigatório';
                break;
            case 3:
                $obj->id = $this->requisito;
                $obj->descricao = 'Anexo e Texto Obrigatório';
                break;
        }

        return $obj;
    }

    /**
     * @return \stdClass
     */
    public function getObjPontuacaoTipo()
    {
        $obj = new \stdClass();
        switch ($this->pontuacaoTipo) {
            case 0:
                $obj->id = $this->pontuacaoTipo;
                $obj->descricao = 'Nenhum - Não é necessário comprovar pontuação';
                break;

            case 1:
                $obj->id = $this->pontuacaoTipo;
                $obj->descricao = 'Anexo - O candidato poderá anexar seus arquivos para comprovar a pontuação';
                break;

            case 2:
                $obj->id = $this->pontuacaoTipo;
                $obj->descricao = 'Texto - O candidato poderá enviar um texto para comprovar a pontuação';
                break;

            case 3:
                $obj->id = $this->pontuacaoTipo;
                $obj->descricao = 'Texto e Anexo - O candidato poderá enviar um texto e anexar seus arquivos para comprovar a pontuação';
                break;
        }

        return $obj;
    }

    /**
     * @return array
     */
    public function getFormChoiceOptions()
    {
        $min = \floatval($this->valorMinimo);
        $max = \floatval($this->valorMaximo);
        $intervalo = \floatval($this->valorIntervalo);

        /**
         * $precisao = (int) strpos(strrev($min), ".");
         * if(bccomp($min, $intervalo, 4) !== 1){
         * $precisao = 4;
         * };.
         */
        $precisao = 4;

        $options = [];
        $options['Sem Pontuação'] = 0;
        if (0 !== \bccomp($min, $max, 4)
            and null != $this->valorIntervalo
            or !$this->getIdCategoria()->isAgruparPontuacao()
        ) {
            $i = $min;
            $loop = 0;
            while (1 !== \bccomp($i, $max, 4)) {
                $options[$i . ' ' . $this->getValorTexto()] = $i;
                $i = \floatval(\bcadd($i, $intervalo, $precisao));
                if ($loop > 600) {
                    break;
                }
                ++$loop;
            }
            if (-1 === \bccomp($options[\array_key_last($options)], $max, 4)) {
                $options[$this->valorMaximo + 0 . ' ' . $this->getValorTexto()] = $max;
            } elseif (1 === \bccomp($options[\array_key_last($options)], $max, 4)) {
                unset($options[\array_key_last($options)]);
                $options[$this->valorMaximo + 0 . ' ' . $this->getValorTexto()] = $max;
            }
        } else {
            $options[$this->valorMaximo + 0 . ' ' . $this->getValorTexto() . ' - ' . $this->titulo] = $this->id;
        }

        return $options;
    }
}
