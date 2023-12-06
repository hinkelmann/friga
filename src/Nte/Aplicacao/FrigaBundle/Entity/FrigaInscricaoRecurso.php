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
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Nte\UsuarioBundle\Entity\Usuario;

/**
 * FrigaInscricaoRecurso.
 *
 * @ORM\Table(name="friga_inscricao_recurso")
 *
 * @ORM\Entity
 *
 * @ORM\HasLifecycleCallbacks()
 */
class FrigaInscricaoRecurso
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
     * @ORM\Column(name="id_situacao", type="integer", nullable=true)
     */
    private $idSituacao;

    /**
     * @var string
     *
     * @ORM\Column(name="justificativa", type="text", length=65535, nullable=true)
     */
    private $justificativa;

    /**
     * @var string
     *
     * @ORM\Column(name="desfecho", type="text", length=65535, nullable=true)
     */
    private $desfecho;
    /**
     * @var string
     *
     * @ORM\Column(name="uuid", type="text", length=65535, nullable=true)
     */
    private $uuid;

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
     * @var Usuario
     *
     * @ORM\ManyToOne(targetEntity="Nte\UsuarioBundle\Entity\Usuario")
     *
     * @ORM\JoinColumns({
     *
     *   @ORM\JoinColumn(name="id_julgador", referencedColumnName="id")
     * })
     */
    private $idJulgador;

    /**
     * @var FrigaEditalEtapa
     *
     * @ORM\ManyToOne(targetEntity="FrigaEditalEtapa", inversedBy="recurso")
     *
     * @ORM\JoinColumns({
     *
     *   @ORM\JoinColumn(name="id_edital_etapa", referencedColumnName="id")
     * })
     */
    private $idEditalEtapa;

    /**
     * @var FrigaInscricao
     *
     * @ORM\ManyToOne(targetEntity="FrigaInscricao",inversedBy="recursos")
     *
     * @ORM\JoinColumns({
     *
     *   @ORM\JoinColumn(name="id_inscricao", referencedColumnName="id")
     * })
     */
    private $idInscricao;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="FrigaArquivo", inversedBy="idInscricaoRecurso")
     *
     * @ORM\JoinTable(name="friga_inscricao_recurso_tem_arquivo",
     *   joinColumns={
     *
     *     @ORM\JoinColumn(name="id_inscricao_recurso", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="id_arquivo", referencedColumnName="id")
     *   }
     * )
     */
    private $idArquivo;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->idArquivo = new ArrayCollection();
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
     * Set idSituacao.
     *
     * @param int $idSituacao
     *
     * @return FrigaInscricaoRecurso
     */
    public function setIdSituacao($idSituacao)
    {
        $this->idSituacao = $idSituacao;

        return $this;
    }

    /**
     * Get idSituacao.
     *
     * @return int
     */
    public function getIdSituacao()
    {
        return $this->idSituacao;
    }

    /**
     * Set justificativa.
     *
     * @param string $justificativa
     *
     * @return FrigaInscricaoRecurso
     */
    public function setJustificativa($justificativa)
    {
        $this->justificativa = $justificativa;

        return $this;
    }

    /**
     * Get justificativa.
     *
     * @return string
     */
    public function getJustificativa()
    {
        return $this->justificativa;
    }

    /**
     * Set desfecho.
     *
     * @param string $desfecho
     *
     * @return FrigaInscricaoRecurso
     */
    public function setDesfecho($desfecho)
    {
        $this->desfecho = $desfecho;

        return $this;
    }

    /**
     * Get desfecho.
     *
     * @return string
     */
    public function getDesfecho()
    {
        return $this->desfecho;
    }

    /**
     * Set registroDataCriacao.
     *
     * @param \DateTime $registroDataCriacao
     *
     * @return FrigaInscricaoRecurso
     */
    public function setRegistroDataCriacao($registroDataCriacao)
    {
        $this->registroDataCriacao = $registroDataCriacao;

        return $this;
    }

    /**
     * Get registroDataCriacao.
     *
     * @return \DateTime
     */
    public function getRegistroDataCriacao()
    {
        return $this->registroDataCriacao;
    }

    /**
     * Set registroDataAtualizacao.
     *
     * @param \DateTime $registroDataAtualizacao
     *
     * @return FrigaInscricaoRecurso
     */
    public function setRegistroDataAtualizacao($registroDataAtualizacao)
    {
        $this->registroDataAtualizacao = $registroDataAtualizacao;

        return $this;
    }

    /**
     * Get registroDataAtualizacao.
     *
     * @return \DateTime
     */
    public function getRegistroDataAtualizacao()
    {
        return $this->registroDataAtualizacao;
    }

    /**
     * Set idJulgador.
     *
     * @return FrigaInscricaoRecurso
     */
    public function setIdJulgador(Usuario $idJulgador = null)
    {
        $this->idJulgador = $idJulgador;

        return $this;
    }

    /**
     * Get idJulgador.
     *
     * @return Usuario
     */
    public function getIdJulgador()
    {
        return $this->idJulgador;
    }

    /**
     * Set idEditalEtapa.
     *
     * @param FrigaEditalEtapa $idEditalEtapa
     *
     * @return FrigaInscricaoRecurso
     */
    public function setIdEditalEtapa($idEditalEtapa)
    {
        $this->idEditalEtapa = $idEditalEtapa;

        return $this;
    }

    /**
     * Get idEditalEtapa.
     *
     * @return FrigaEditalEtapa
     */
    public function getIdEditalEtapa()
    {
        return $this->idEditalEtapa;
    }

    /**
     * Set idInscricao.
     *
     * @param FrigaInscricao $idInscricao
     *
     * @return FrigaInscricaoRecurso
     */
    public function setIdInscricao($idInscricao)
    {
        $this->idInscricao = $idInscricao;

        return $this;
    }

    /**
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * @param string $uuid
     *
     * @return FrigaInscricaoRecurso
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * Get idInscricao.
     *
     * @return FrigaInscricao
     */
    public function getIdInscricao()
    {
        return $this->idInscricao;
    }

    /**
     * Add idArquivo.
     *
     * @return FrigaInscricaoRecurso
     */
    public function addIdArquivo(FrigaArquivo $idArquivo)
    {
        $this->idArquivo[] = $idArquivo;

        return $this;
    }

    /**
     * Remove idArquivo.
     */
    public function removeIdArquivo(FrigaArquivo $idArquivo)
    {
        $this->idArquivo->removeElement($idArquivo);
    }

    /**
     * Get idArquivo.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIdArquivo()
    {
        return $this->idArquivo;
    }

    /**
     * @return \stdClass
     */
    public function getObjsituacao()
    {
        $obj = new \stdClass();
        $obj->id = $this->idSituacao;
        $obj->descricao = '';
        $obj->icone = '';
        $obj->css = 'label label-info';
        $obj->cssAlert = 'alert alert-info';
        switch ($this->idSituacao) {
            case -1:
                $obj->descricao = 'Indeferido';
                $obj->css = 'label label-danger';
                $obj->icone = 'fa fa-clock-o';
                $obj->cssAlert = 'alert alert-danger';
                break;
            case 0:
                $obj->descricao = 'Aguardando julgamento';
                $obj->css = 'label label-warning';
                $obj->icone = 'fa fa-clock-o';
                $obj->cssAlert = 'alert alert-warning';
                break;
            case 1:
                $obj->descricao = 'Deferido';
                $obj->css = 'label label-success';
                $obj->icone = 'fa fa-check';
                $obj->cssAlert = 'alert alert-success';
                break;
        }

        return $obj;
    }

    /**
     * @ORM\PreUpdate
     *
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
     *
     * @throws \Exception
     */
    public function PrePersist()
    {
        $this->uuid = \uniqid();
        $this->setRegistroDataCriacao(new \DateTime());
        $this->setRegistroDataAtualizacao(new \DateTime());
    }
}
