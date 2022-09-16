<?php

namespace Nte\Aplicacao\FrigaBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Exception;

/**
 * FrigaEditalPontuacaoCategoria
 *
 * @ORM\Table(name="friga_edital_pontuacao_categoria")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class FrigaEditalPontuacaoCategoria
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
     * @ORM\Column(name="pontuacao_auto", type="integer", nullable=true)
     */
    private $pontuacaoAuto;


    /**
     * @var integer
     * @ORM\Column(name="pontuacao_tipo", type="integer", nullable=true)
     */
    private $pontuacaoTipo;

    /**
     * @var boolean
     *
     * @ORM\Column(name="requisito", type="integer", nullable=true)
     */
    private $requisito;


    /**
     * @var string
     *
     * @ORM\Column(name="valor_minimo", type="decimal", precision=10, scale=5, nullable=true)
     */
    private $valorMinimo;

    /**
     * @var string
     *
     * @ORM\Column(name="valor_maximo", type="decimal", precision=10, scale=5, nullable=true)
     */
    private $valorMaximo;

    /**
     * @var string
     *
     * @ORM\Column(name="valor_texto", type="string", length=254, nullable=true)
     */
    private $valorTexto;

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
     * @var boolean
     *
     * @ORM\Column(name="bloqueio", type="boolean", nullable=true)
     */
    private $bloqueio;

    /**
     * @var boolean
     *
     * @ORM\Column(name="agrupar_pontuacao", type="boolean", nullable=true)
     */
    private $agruparPontuacao;


    /**
     * @var FrigaEdital
     *
     * @ORM\ManyToOne(targetEntity="FrigaEdital", inversedBy="pontuacaoCategoria")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_edital", referencedColumnName="id")
     * })
     */
    private $idEdital;

    /**
     * @var FrigaEditalPontuacaoCategoria
     *
     * @ORM\ManyToOne(targetEntity="FrigaEditalPontuacaoCategoria", inversedBy="filhos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_categoria", referencedColumnName="id")
     * })
     */
    private $idCategoria;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="FrigaEditalPontuacaoCategoria", mappedBy="idCategoria")
     */
    private $filhos;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalPontuacao", mappedBy="idCategoria")
     */
    private $pontuacao;

    /**
     * @var integer
     *
     * @ORM\Column(name="ordem", type="integer", nullable=true)
     */
    private $ordem;

    /**
     * FrigaEditalPontuacaoCategoria constructor.
     */
    public function __construct()
    {

        $this->pontuacao = new ArrayCollection();
    }

    /**
     *
     */
    public function __clone()
    {
        $this->idCategoria = null;
        $this->filhos =  new ArrayCollection();
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
     * @return int
     */
    public function getPontuacaoAuto()
    {
        return $this->pontuacaoAuto;
    }

    /**
     * @param int $pontuacaoAuto
     * @return FrigaEditalPontuacaoCategoria
     */
    public function setPontuacaoAuto( $pontuacaoAuto)
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
     * @return FrigaEditalPontuacaoCategoria
     */
    public function setPontuacaoTipo( $pontuacaoTipo)
    {
        $this->pontuacaoTipo = $pontuacaoTipo;
        return $this;
    }

    /**
     * @return integer
     */
    public function getRequisito()
    {
        return $this->requisito;
    }

    /**
     * @param bool $requisito
     * @return FrigaEditalPontuacaoCategoria
     */
    public function setRequisito( $requisito)
    {
        $this->requisito = $requisito;
        return $this;
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
     * @return FrigaEditalPontuacaoCategoria
     */
    public function setExplicacaoTexto( $explicacaoTexto)
    {
        $this->explicacaoTexto = $explicacaoTexto;
        return $this;
    }



    /**
     * @return string
     */
    public function getValorMinimo()
    {
        return $this->valorMinimo;
    }

    /**
     * @param string $valorMinimo
     */
    public function setValorMinimo($valorMinimo)
    {
        $this->valorMinimo = $valorMinimo;
    }

    /**
     * @return string
     */
    public function getValorMaximo()
    {
        return $this->valorMaximo;
    }

    /**
     * @param string $valorMaximo
     */
    public function setValorMaximo($valorMaximo)
    {
        $this->valorMaximo = $valorMaximo;
    }


    /**
     * Set descricao
     *
     * @param string $descricao
     *
     * @return FrigaEditalPontuacaoCategoria
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

    /**
     * @return string
     */
    public function getTitulo()
    {
        return $this->descricao;
    }

    /**
     * Set bloqueio
     *
     * @param boolean $bloqueio
     *
     * @return FrigaEditalPontuacaoCategoria
     */
    public function setBloqueio($bloqueio)
    {
        $this->bloqueio = $bloqueio;
        return $this;
    }

    /**
     * Get bloqueio
     *
     * @return boolean
     */
    public function getBloqueio()
    {
        return $this->bloqueio;
    }

    /**
     * @return bool
     */
    public function isAgruparPontuacao()
    {
        return $this->agruparPontuacao;
    }

    /**
     * @param bool $agruparPontuacao
     * @return FrigaEditalPontuacaoCategoria
     */
    public function setAgruparPontuacao($agruparPontuacao)
    {
        $this->agruparPontuacao = $agruparPontuacao;
        return $this;
    }

    /**
     * @return string
     */
    public function getValorTexto()
    {
        return $this->valorTexto;
    }

    /**
     * @param string $valorTexto
     * @return FrigaEditalPontuacaoCategoria
     */
    public function setValorTexto($valorTexto)
    {
        $this->valorTexto = $valorTexto;
        return $this;
    }


    /**
     * Set idEdital
     *
     * @param FrigaEdital $idEdital
     *
     * @return FrigaEditalPontuacaoCategoria
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
     * @return string
     */
    public function getExplicacao()
    {
        return $this->explicacao;
    }

    /**
     * @param string $explicacao
     * @return FrigaEditalPontuacaoCategoria
     */
    public function setExplicacao($explicacao)
    {
        $this->explicacao = $explicacao;
        return $this;
    }

    /**
     * Set idCategoria
     *
     * @param FrigaEditalPontuacaoCategoria $idCategoria
     *
     * @return FrigaEditalPontuacaoCategoria
     */
    public function setIdCategoria(FrigaEditalPontuacaoCategoria $idCategoria = null)
    {
        $this->idCategoria = $idCategoria;
        return $this;
    }

    /**
     * Get idCategoria
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
    public function getPontuacao()
    {
        return $this->pontuacao;
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
     * @return FrigaEditalPontuacaoCategoria
     */
    public function setOrdem($ordem)
    {
        $this->ordem = $ordem;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getFilhos()
    {
        return $this->filhos;
    }

    /**
     *
     * @return ArrayCollection
     */
    public function getPontuacaoInscricao()
    {
        return $this->pontuacao->filter(function (FrigaEditalPontuacao $pt) {
            /** @var FrigaEditalEtapa $etapa */
            foreach ($pt->getIdEtapa() as $etapa) {
                if ($etapa->getTipo() == 1) {
                    return $pt;
                }
            }
        });
    }

    /**
     * @return ArrayCollection
     */
    public function getPontuacaoFilhos(){
        $tmp = new ArrayCollection();
        /** @var FrigaEditalPontuacaoCategoria $categoria */
        foreach ($this->filhos as $categoria){
            $tmp = new ArrayCollection(
                array_merge($tmp->toArray(), $categoria->getPontuacao()->toArray())
            );
        }
        return $tmp;
    }

    public function getPontuacaoEtapa(FrigaEditalEtapa $etapa)
    {
        return $this->pontuacao->filter(function (FrigaEditalPontuacao $pt) use ($etapa) {
            return $pt->getIdEtapa()->contains($etapa);
        });
    }

    /**
     * @ORM\PreRemove
     *
     * @param LifecycleEventArgs $args
     * @throws Exception
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        if ($this->getPontuacao()->count()) {
            /** @var FrigaEditalPontuacao $pt */
            foreach ($this->getPontuacao() as $pt) {
                $pt->setIdCategoria(null);
                $args->getEntityManager()->flush();
            }
        }
        if ($this->getFilhos()->count()) {
            /** @var FrigaEditalPontuacaoCategoria $categoria */
            foreach ($this->getFilhos() as $categoria) {
                $categoria->setIdCategoria(null);
                $args->getEntityManager()->flush();
            }
        }
    }
}
