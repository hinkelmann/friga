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

namespace Nte\Aplicacao\LogisticaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LogisticaLogradouro.
 *
 * @ORM\Table(name="logistica_logradouro")
 *
 * @ORM\Entity
 */
class LogisticaLogradouro
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
     * @var string|null
     *
     * @ORM\Column(name="tipo", type="string", length=255, nullable=true)
     */
    private $tipo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nome", type="string", length=255, nullable=true)
     */
    private $nome;

    /**
     * @var string|null
     *
     * @ORM\Column(name="cep", type="string", length=16, nullable=true)
     */
    private $cep;

    /**
     * @var LogisticaBairro
     *
     * @ORM\ManyToOne(targetEntity="Nte\Aplicacao\LogisticaBundle\Entity\LogisticaBairro")
     *
     * @ORM\JoinColumns({
     *
     *   @ORM\JoinColumn(name="id_bairro_fim", referencedColumnName="id")
     * })
     */
    private $idBairroFim;

    /**
     * @var LogisticaBairro
     *
     * @ORM\ManyToOne(targetEntity="Nte\Aplicacao\LogisticaBundle\Entity\LogisticaBairro")
     *
     * @ORM\JoinColumns({
     *
     *   @ORM\JoinColumn(name="id_bairro_inicio", referencedColumnName="id")
     * })
     */
    private $idBairroInicio;

    /**
     * @var LogisticaLocalidade
     *
     * @ORM\ManyToOne(targetEntity="Nte\Aplicacao\LogisticaBundle\Entity\LogisticaLocalidade")
     *
     * @ORM\JoinColumns({
     *
     *   @ORM\JoinColumn(name="id_cidade", referencedColumnName="id")
     * })
     */
    private $idCidade;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * @param string|null $tipo
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }

    /**
     * @return string|null
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * @param string|null $nome
     */
    public function setNome($nome)
    {
        $this->nome = $nome;
    }

    /**
     * @return string|null
     */
    public function getCep()
    {
        return $this->cep;
    }

    /**
     * @param string|null $cep
     */
    public function setCep($cep)
    {
        $this->cep = $cep;
    }

    /**
     * @return LogisticaBairro
     */
    public function getIdBairroFim()
    {
        return $this->idBairroFim;
    }

    /**
     * @param LogisticaBairro $idBairroFim
     */
    public function setIdBairroFim($idBairroFim)
    {
        $this->idBairroFim = $idBairroFim;
    }

    /**
     * @return LogisticaBairro
     */
    public function getIdBairroInicio()
    {
        return $this->idBairroInicio;
    }

    /**
     * @param LogisticaBairro $idBairroInicio
     */
    public function setIdBairroInicio($idBairroInicio)
    {
        $this->idBairroInicio = $idBairroInicio;
    }

    /**
     * @return LogisticaLocalidade
     */
    public function getIdCidade()
    {
        return $this->idCidade;
    }

    /**
     * @param LogisticaLocalidade $idCidade
     */
    public function setIdCidade($idCidade)
    {
        $this->idCidade = $idCidade;
    }
}
