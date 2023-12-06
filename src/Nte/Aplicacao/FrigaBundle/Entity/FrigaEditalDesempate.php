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

use Doctrine\ORM\Mapping as ORM;

/**
 * FrigaEditalDesempate.
 *
 * @ORM\Table(name="friga_edital_desempate")
 *
 * @ORM\Entity
 */
class FrigaEditalDesempate
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
     * @ORM\Column(name="posicao", type="integer", nullable=true)
     */
    private $posicao;

    /**
     * @var int
     *
     * @ORM\Column(name="tipo", type="integer", nullable=true)
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
     * @ORM\Column(name="propriedade", type="string", length=255, nullable=true)
     */
    private $propriedade;

    /**
     * @var int
     *
     * @ORM\Column(name="id_contexto", type="integer", nullable=true)
     */
    private $idContexto;

    /**
     * @var int
     *
     * @ORM\Column(name="sentido", type="integer", nullable=true)
     */
    private $sentido;

    /**
     * @var FrigaEdital
     *
     * @ORM\ManyToOne(targetEntity="FrigaEdital", inversedBy="desempate")
     *
     * @ORM\JoinColumns({
     *
     *   @ORM\JoinColumn(name="id_edital", referencedColumnName="id")
     * })
     */
    private $idEdital;

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
     * Set posicao.
     *
     * @param int $posicao
     *
     * @return FrigaEditalDesempate
     */
    public function setPosicao($posicao)
    {
        $this->posicao = $posicao;

        return $this;
    }

    /**
     * Get posicao.
     *
     * @return int
     */
    public function getPosicao()
    {
        return $this->posicao;
    }

    /**
     * Set contexto.
     *
     * @param string $contexto
     *
     * @return FrigaEditalDesempate
     */
    public function setContexto($contexto)
    {
        $this->contexto = $contexto;

        return $this;
    }

    /**
     * Get contexto.
     *
     * @return string
     */
    public function getContexto()
    {
        return $this->contexto;
    }

    /**
     * Set idContexto.
     *
     * @param int $idContexto
     *
     * @return FrigaEditalDesempate
     */
    public function setIdContexto($idContexto)
    {
        $this->idContexto = $idContexto;

        return $this;
    }

    /**
     * Get idContexto.
     *
     * @return int
     */
    public function getIdContexto()
    {
        return $this->idContexto;
    }

    /**
     * Set idEdital.
     *
     * @return FrigaEditalDesempate
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
     * @return int
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * @param int $tipo
     *
     * @return FrigaEditalDesempate
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * @return string
     */
    public function getPropriedade()
    {
        return $this->propriedade;
    }

    /**
     * @param string $propriedade
     *
     * @return FrigaEditalDesempate
     */
    public function setPropriedade($propriedade)
    {
        $this->propriedade = $propriedade;

        return $this;
    }

    /**
     * @return int
     */
    public function getSentido()
    {
        return $this->sentido;
    }

    /**
     * @param int $sentido
     *
     * @return FrigaEditalDesempate
     */
    public function setSentido($sentido)
    {
        $this->sentido = $sentido;

        return $this;
    }

    public function getObj()
    {
        $obj = new \stdClass();
        $obj->descricao = '';
        $obj->regra = '';
        $obj->sentido = 1 == $this->sentido ? ' Maior ' : ' Menor ';

        switch ($this->getContexto()) {
            case FrigaInscricao::class:
                $obj->descricao = 'Formulário de Inscrição';

                switch ($this->getPropriedade()) {
                    case 'dataNascimento':
                        $obj->regra .= $obj->sentido;
                        $obj->regra .= ' Data de Nascimento';
                        break;

                    case 'nome':
                        ;
                        $obj->regra .= 'Nome - Ordem alfabética';
                        $obj->regra .= (1 == $this->sentido ? ' Z-a' : ' A-z ');
                        break;

                    case 'matriculaIndiceDesempenho':
                        $obj->regra .= ' Valor do índice de desempenho acadêmico';
                        break;
                    default:
                        $obj->regra .= ' ' . $this->getPropriedade();
                        break;
                }

                break;
            case FrigaEditalPontuacao::class:
                $obj->descricao = 'Item de Pontuação';
                $obj->regra .= $obj->sentido;
                $obj->regra .= ' valor da pontuação: ' . $this->getContextoObjeto()->getDescricao();

                break;
            case FrigaEditalPontuacaoCategoria::class:
                $obj->descricao = 'Categoria de Pontuação';
                $obj->regra .= $obj->sentido;
                $obj->regra .= ' valor da categoria: ' . $this->getContextoObjeto()->getDescricao();
                break;
        }

        return $obj;
    }

    public function getContextoObjeto()
    {
        if ($this->getTipo()) {
            return null;
        }
        $idContexto = $this->getIdContexto();

        $obj = false;
        switch ($this->getContexto()) {
            case FrigaEditalPontuacao::class:
                $obj = $this->getIdEdital()
                    ->getPontuacao()
                    ->filter(function(FrigaEditalPontuacao $p) use ($idContexto) {
                        return $p->getId() == $idContexto;
                    })->first();
                break;

            case FrigaEditalPontuacaoCategoria::class:
                $obj = $this->getIdEdital()
                    ->getPontuacaoCategoria()
                    ->filter(function(FrigaEditalPontuacaoCategoria $p) use ($idContexto) {
                        return $p->getId() == $idContexto;
                    })->first();
                break;
        }
        if (false === $obj) {
            $obj = new FrigaEditalPontuacao();
            $obj->setDescricao('Indefinido');
        }

        return $obj;
    }
}
