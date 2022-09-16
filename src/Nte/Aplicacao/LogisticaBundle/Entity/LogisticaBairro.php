<?php

namespace Nte\Aplicacao\LogisticaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LogisticaBairro
 *
 * @ORM\Table(name="logistica_bairro")
 * @ORM\Entity
 */
class LogisticaBairro
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="id_cidade", type="integer", nullable=false)
     */
    private $idCidade = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="nome", type="string", length=72, nullable=false)
     */
    private $nome;

    /**
     * @var string|null
     *
     * @ORM\Column(name="abreviacao", type="string", length=36, nullable=true)
     */
    private $abreviacao;

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
     * @return int
     */
    public function getIdCidade()
    {
        return $this->idCidade;
    }

    /**
     * @param int $idCidade
     */
    public function setIdCidade($idCidade)
    {
        $this->idCidade = $idCidade;
    }

    /**
     * @return string
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * @param string $nome
     */
    public function setNome($nome)
    {
        $this->nome = $nome;
    }

    /**
     * @return string|null
     */
    public function getAbreviacao()
    {
        return $this->abreviacao;
    }

    /**
     * @param string|null $abreviacao
     */
    public function setAbreviacao($abreviacao)
    {
        $this->abreviacao = $abreviacao;
    }



}
