<?php

namespace Nte\Aplicacao\FrigaBundle\Entity;

use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Nte\UsuarioBundle\Entity\Usuario;

/**
 * FrigaClassificacao
 *
 * @ORM\Table(name="friga_classificacao")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class FrigaClassificacao
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
     * @var float
     *
     * @ORM\Column(name="valor", type="decimal", precision=10, scale=5, nullable=true)
     */
    private $valor;

    /**
     * @var integer
     *
     * @ORM\Column(name="posicao", type="integer", nullable=true)
     */
    private $posicao;

    /**
     * @var integer
     *
     * @ORM\Column(name="posicao_anterior", type="integer", nullable=true)
     */
    private $posicaoAnterior;

    /**
     * @var boolean
     *
     * @ORM\Column(name="empate", type="boolean", nullable=true)
     */
    private $empate;

    /**
     * @var string
     *
     * @ORM\Column(name="uuid", type="string", length=255, nullable=true)
     */
    private $uuid;

    /**
     * @var string
     *
     * @ORM\Column(name="observacao", type="text", nullable=true)
     */
    private $observacao;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_situacao", type="integer", nullable=true)
     */
    private $idSituacao;



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
     * @var FrigaEdital
     *
     * @ORM\ManyToOne(targetEntity="FrigaEdital")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_edital", referencedColumnName="id")
     * })
     */
    private $idEdital;

    /**
     * @var FrigaEditalEtapa
     *
     * @ORM\ManyToOne(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalEtapa", inversedBy="classificacao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_etapa", referencedColumnName="id")
     * })
     */
    private $idEtapa;

    /**
     * @var FrigaEditalCargo
     *
     * @ORM\ManyToOne(targetEntity="FrigaEditalCargo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_cargo", referencedColumnName="id")
     * })
     */
    private $idCargo;

    /**
     * @var FrigaEditalCota
     *
     * @ORM\ManyToOne(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalCota")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_cota", referencedColumnName="id")
     * })
     */
    private $idCota;

    /**
     * @var FrigaInscricao
     *
     * @ORM\ManyToOne(targetEntity="FrigaInscricao", inversedBy="classificacao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_inscricao", referencedColumnName="id")
     * })
     */
    private $idInscricao;

    /**
     * @var Usuario
     *
     * @ORM\ManyToOne(targetEntity="Nte\UsuarioBundle\Entity\Usuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_usuario", referencedColumnName="id")
     * })
     */
    private $idUsuario;


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
     * Set valor
     *
     * @param string $valor
     *
     * @return FrigaClassificacao
     */
    public function setValor($valor)
    {
        $this->valor = $valor;

        return $this;
    }

    /**
     * Get valor
     *
     * @return string
     */
    public function getValor()
    {
        return $this->valor;
    }

    /**
     * Set posicao
     *
     * @param integer $posicao
     *
     * @return FrigaClassificacao
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
     * Set empate
     *
     * @param boolean $empate
     *
     * @return FrigaClassificacao
     */
    public function setEmpate($empate)
    {
        $this->empate = $empate;

        return $this;
    }

    /**
     * Get empate
     *
     * @return boolean
     */
    public function getEmpate()
    {
        return $this->empate;
    }

    /**
     * Set uuid
     *
     * @param string $uuid
     *
     * @return FrigaClassificacao
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * Get uuid
     *
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Set registroDataCriacao
     *
     * @param \DateTime $registroDataCriacao
     *
     * @return FrigaClassificacao
     */
    public function setRegistroDataCriacao($registroDataCriacao)
    {
        $this->registroDataCriacao = $registroDataCriacao;

        return $this;
    }

    /**
     * Get registroDataCriacao
     *
     * @return \DateTime
     */
    public function getRegistroDataCriacao()
    {
        return $this->registroDataCriacao;
    }

    /**
     * Set registroDataAtualizacao
     *
     * @param \DateTime $registroDataAtualizacao
     *
     * @return FrigaClassificacao
     */
    public function setRegistroDataAtualizacao($registroDataAtualizacao)
    {
        $this->registroDataAtualizacao = $registroDataAtualizacao;

        return $this;
    }

    /**
     * Get registroDataAtualizacao
     *
     * @return \DateTime
     */
    public function getRegistroDataAtualizacao()
    {
        return $this->registroDataAtualizacao;
    }

    /**
     * Set idEdital
     *
     * @param FrigaEdital $idEdital
     *
     * @return FrigaClassificacao
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
     * Set idCargo
     *
     * @param FrigaEditalCargo $idCargo
     *
     * @return FrigaClassificacao
     */
    public function setIdCargo(FrigaEditalCargo $idCargo = null)
    {
        $this->idCargo = $idCargo;

        return $this;
    }

    /**
     * Get idCargo
     *
     * @return FrigaEditalCargo
     */
    public function getIdCargo()
    {
        return $this->idCargo;
    }

    /**
     * Set idInscricao
     *
     * @param FrigaInscricao $idInscricao
     *
     * @return FrigaClassificacao
     */
    public function setIdInscricao(FrigaInscricao $idInscricao = null)
    {
        $this->idInscricao = $idInscricao;

        return $this;
    }

    /**
     * Get idInscricao
     *
     * @return FrigaInscricao
     */
    public function getIdInscricao()
    {
        return $this->idInscricao;
    }

    /**
     * @return int
     */
    public function getPosicaoAnterior()
    {
        return $this->posicaoAnterior;
    }

    /**
     * @param int $posicaoAnterior
     * @return FrigaClassificacao
     */
    public function setPosicaoAnterior($posicaoAnterior)
    {
        $this->posicaoAnterior = $posicaoAnterior;
        return $this;
    }

    /**
     * @return FrigaEditalEtapa
     */
    public function getIdEtapa()
    {
        return $this->idEtapa;
    }

    /**
     * @param FrigaEditalEtapa $idEtapa
     * @return FrigaClassificacao
     */
    public function setIdEtapa($idEtapa)
    {
        $this->idEtapa = $idEtapa;
        return $this;
    }

    /**
     * @return FrigaEditalCota
     */
    public function getIdCota()
    {
        return $this->idCota;
    }

    /**
     * @param FrigaEditalCota $idCota
     * @return FrigaClassificacao
     */
    public function setIdCota($idCota)
    {
        $this->idCota = $idCota;
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
     * @return FrigaClassificacao
     */
    public function setObservacao($observacao)
    {
        $this->observacao = $observacao;
        return $this;
    }

    /**
     * @return Usuario
     */
    public function getIdUsuario()
    {
        return $this->idUsuario;
    }

    /**
     * @param Usuario $idUsuario
     * @return FrigaClassificacao
     */
    public function setIdUsuario($idUsuario)
    {
        $this->idUsuario = $idUsuario;
        return $this;
    }

    /**
     * @return int
     */
    public function getIdSituacao()
    {
        return $this->idSituacao;
    }

    /**
     * @param int $idSituacao
     * @return FrigaClassificacao
     */
    public function setIdSituacao($idSituacao)
    {
        $this->idSituacao = $idSituacao;
        return $this;
    }

    /**
     * @return \stdClass
     */
    public function getObjsituacao()
    {
        $obj = new \stdClass();
        $obj->id = $this->idSituacao;
        $obj->descricao = "";
        $obj->icone = "";
        $obj->css = "label label-info";
        $obj->cssAlert = "alert alert-info";
        switch ($this->idSituacao) {
            case -999:
                $obj->descricao = "Inscrição Cancelada";
                $obj->css = "label label-dark";
                $obj->icone = "fa fa-clock-o";
                $obj->cssAlert = "alert alert-dark";
                break;
            case 0:
                $obj->descricao = "Inscrição Realizada";
                $obj->css = "label label-success";
                $obj->icone = "fa fa-clock-o";
                $obj->cssAlert = "alert alert-success";
                break;
            case 1:
                $obj->descricao = "Inscrição Não Homologada";
                $obj->css = "label label-danger";
                $obj->icone = "fa fa-times";
                $obj->cssAlert = "alert alert-danger";
                break;
            case 2:
                $obj->descricao = "Inscrição Homologada";
                $obj->css = "label label-info";
                $obj->icone = "fa fa-clock-o";
                $obj->cssAlert = "alert alert-success";
                break;
            case 3:
                $obj->descricao = "Desclassificado";
                $obj->css = "label label-danger";
                $obj->icone = "fa fa-clock-o";
                $obj->cssAlert = "alert alert-danger";
                break;
            case 4:
                $obj->descricao = "Em avaliação";
                $obj->css = "label label-danger";
                $obj->icone = "fa fa-clock-o";
                $obj->cssAlert = "alert alert-danger";
                break;
            case 5:
                $obj->descricao = "Aguardando Recurso";
                $obj->css = "label label-inverse";
                $obj->icone = "fa fa-clock-o";
                $obj->cssAlert = "alert alert-inverse";
                break;
            case 6:
                $obj->descricao = "Classificado";
                $obj->css = "label label-success";
                $obj->icone = "fa fa-clock-o";
                $obj->cssAlert = "alert alert-success";
                break;
            case 7:
                $obj->descricao = "Convocado";
                $obj->css = "label label-success";
                $obj->icone = "fa fa-clock-o";
                $obj->cssAlert = "alert alert-success";
                break;
        }
        return $obj;
    }

    /**
     * @ORM\PreUpdate
     *
     * @param PreUpdateEventArgs $args
     * @throws \Exception
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        if ($args->hasChangedField('registroDataCriacao')) {
            $this->setRegistroDataCriacao($args->getOldValue('registroDataCriacao'));
        }
        $this->setRegistroDataAtualizacao(new \DateTime());
    }

    /**
     * @ORM\PrePersist
     */
    public function PrePersist()
    {
        $this->uuid = uniqid();
        $this->setRegistroDataCriacao(new \DateTime());
        $this->setRegistroDataAtualizacao(new \DateTime());
    }
}
