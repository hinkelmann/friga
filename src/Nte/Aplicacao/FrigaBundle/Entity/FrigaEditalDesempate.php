<?php

namespace Nte\Aplicacao\FrigaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FrigaEditalDesempate
 *
 * @ORM\Table(name="friga_edital_desempate")
 * @ORM\Entity
 */
class FrigaEditalDesempate
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
     * @var integer
     *
     * @ORM\Column(name="posicao", type="integer", nullable=true)
     */
    private $posicao;


    /**
     * @var integer
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
     * @var integer
     *
     * @ORM\Column(name="id_contexto", type="integer", nullable=true)
     */
    private $idContexto;

    /**
     * @var integer
     *
     * @ORM\Column(name="sentido", type="integer", nullable=true)
     */
    private $sentido;

    /**
     * @var FrigaEdital
     *
     * @ORM\ManyToOne(targetEntity="FrigaEdital", inversedBy="desempate")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_edital", referencedColumnName="id")
     * })
     */
    private $idEdital;


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
     * Set posicao
     *
     * @param integer $posicao
     *
     * @return FrigaEditalDesempate
     */
    public function setPosicao($posicao)
    {
        $this->posicao = $posicao;

        return $this;
    }

    /**
     * Get posicao
     *
     * @return integer
     */
    public function getPosicao()
    {
        return $this->posicao;
    }

    /**
     * Set contexto
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
     * Get contexto
     *
     * @return string
     */
    public function getContexto()
    {
        return $this->contexto;
    }

    /**
     * Set idContexto
     *
     * @param integer $idContexto
     *
     * @return FrigaEditalDesempate
     */
    public function setIdContexto($idContexto)
    {
        $this->idContexto = $idContexto;

        return $this;
    }

    /**
     * Get idContexto
     *
     * @return integer
     */
    public function getIdContexto()
    {
        return $this->idContexto;
    }

    /**
     * Set idEdital
     *
     * @param FrigaEdital $idEdital
     *
     * @return FrigaEditalDesempate
     */
    public function setIdEdital(FrigaEdital $idEdital = null)
    {
        $this->idEdital = $idEdital;

        return $this;
    }

    /**
     * Get idEdital
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
        $obj->descricao = "";
        $obj->regra = "";
        $obj->sentido = $this->sentido == 1 ? " Maior " : " Menor ";
        switch ($this->getContexto()) {
            case FrigaInscricao::class:
                $obj->descricao = "Formulário de Inscrição";
                $obj->regra .= $obj->sentido;
                $obj->regra .= " Data de Nascimento";
                break;
            case FrigaEditalPontuacao::class:
                $obj->descricao = "Item de Pontuação";
                $obj->regra .= $obj->sentido;
                $obj->regra .= " valor da pontuação: " . $this->getContextoObjeto()->getDescricao();

                break;
            case FrigaEditalPontuacaoCategoria::class:
                $obj->descricao = "Categoria de Pontuação";
                $obj->regra .= $obj->sentido;
                $obj->regra .= " valor da categoria: " . $this->getContextoObjeto()->getDescricao();
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

        switch ($this->getContexto()) {
            case FrigaEditalPontuacao::class:
                return $this->getIdEdital()
                    ->getPontuacao()
                    ->filter(function (FrigaEditalPontuacao $p) use ($idContexto) {
                        return $p->getId() == $idContexto;
                    })->first();

            case FrigaEditalPontuacaoCategoria::class:
                return $this->getIdEdital()
                    ->getPontuacaoCategoria()
                    ->filter(function (FrigaEditalPontuacaoCategoria $p) use ($idContexto) {
                        return $p->getId() == $idContexto;
                    })->first();
        }
    }

}
