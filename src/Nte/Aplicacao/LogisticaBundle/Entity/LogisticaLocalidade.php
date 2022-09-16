<?php

namespace Nte\Aplicacao\LogisticaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LogisticaLocalidade
 *
 * @ORM\Table(name="logistica_localidade")
 * @ORM\Entity
 */
class LogisticaLocalidade
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
     * @var string|null
     *
     * @ORM\Column(name="localidade_nome", type="string", length=255, nullable=true)
     */
    private $localidadeNome;

    /**
     * @var int|null
     *
     * @ORM\Column(name="localidade_ibge", type="integer", nullable=true)
     */
    private $localidadeIbge;

    /**
     * @var string|null
     *
     * @ORM\Column(name="cep", type="string", length=16, nullable=true)
     */
    private $cep;

    /**
     * @var string|null
     *
     * @ORM\Column(name="tipo", type="string", length=1, nullable=true)
     */
    private $tipo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="estado_sigla", type="string", length=2, nullable=true)
     */
    private $estadoSigla;

    /**
     * @var string|null
     *
     * @ORM\Column(name="estado_nome", type="string", length=255, nullable=true)
     */
    private $estadoNome;

    /**
     * @var int|null
     *
     * @ORM\Column(name="estado_ibge", type="integer", nullable=true)
     */
    private $estadoIbge;

    /**
     * @var int|null
     *
     * @ORM\Column(name="regiao_id", type="integer", nullable=true)
     */
    private $regiaoId = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="regiao_nome", type="string", length=50, nullable=true)
     */
    private $regiaoNome;

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
    public function getLocalidadeNome()
    {
        return $this->localidadeNome;
    }

    /**
     * @param string|null $localidadeNome
     */
    public function setLocalidadeNome($localidadeNome)
    {
        $this->localidadeNome = $localidadeNome;
    }

    /**
     * @return int|null
     */
    public function getLocalidadeIbge()
    {
        return $this->localidadeIbge;
    }

    /**
     * @param int|null $localidadeIbge
     */
    public function setLocalidadeIbge($localidadeIbge)
    {
        $this->localidadeIbge = $localidadeIbge;
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
    public function getEstadoSigla()
    {
        return $this->estadoSigla;
    }

    /**
     * @param string|null $estadoSigla
     */
    public function setEstadoSigla($estadoSigla)
    {
        $this->estadoSigla = $estadoSigla;
    }

    /**
     * @return string|null
     */
    public function getEstadoNome()
    {
        return $this->estadoNome;
    }

    /**
     * @param string|null $estadoNome
     */
    public function setEstadoNome($estadoNome)
    {
        $this->estadoNome = $estadoNome;
    }

    /**
     * @return int|null
     */
    public function getEstadoIbge()
    {
        return $this->estadoIbge;
    }

    /**
     * @param int|null $estadoIbge
     */
    public function setEstadoIbge($estadoIbge)
    {
        $this->estadoIbge = $estadoIbge;
    }

    /**
     * @return int|null
     */
    public function getRegiaoId()
    {
        return $this->regiaoId;
    }

    /**
     * @param int|null $regiaoId
     */
    public function setRegiaoId($regiaoId)
    {
        $this->regiaoId = $regiaoId;
    }

    /**
     * @return string|null
     */
    public function getRegiaoNome()
    {
        return $this->regiaoNome;
    }

    /**
     * @param string|null $regiaoNome
     */
    public function setRegiaoNome($regiaoNome)
    {
        $this->regiaoNome = $regiaoNome;
    }

}
