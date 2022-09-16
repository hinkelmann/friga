<?php

namespace Nte\Aplicacao\FrigaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FosRegra
 *
 * @ORM\Table(name="fos_regra")
 * @ORM\Entity
 */
class Regra
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
     * @ORM\Column(name="regra", type="string", length=255, nullable=true)
     */
    private $regra = 'NULL';

    /**
     * @var string
     *
     * @ORM\Column(name="titulo", type="string", length=255, nullable=true)
     */
    private $titulo = 'NULL';

    /**
     * @var string
     *
     * @ORM\Column(name="descricao", type="text", length=65535, nullable=true)
     */
    private $descricao = 'NULL';



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
     * Set regra
     *
     * @param string $regra
     *
     * @return Regra
     */
    public function setRegra($regra)
    {
        $this->regra = $regra;

        return $this;
    }

    /**
     * Get regra
     *
     * @return string
     */
    public function getRegra()
    {
        return $this->regra;
    }

    /**
     * Set titulo
     *
     * @param string $titulo
     *
     * @return Regra
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
     * Set descricao
     *
     * @param string $descricao
     *
     * @return Regra
     */
    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;

        return $this;
    }

    /**
     * Get descricao
     *
     * @return string
     */
    public function getDescricao()
    {
        return $this->descricao;
    }
}
