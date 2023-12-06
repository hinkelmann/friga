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
 * @ORM\Table(name="friga_inscricao_projeto_participante")
 *
 * @ORM\Entity
 *
 * @ORM\HasLifecycleCallbacks()
 */
class FrigaInscricaoProjetoParticipante
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
     * @var Usuario
     *
     * @ORM\ManyToOne(targetEntity="Nte\UsuarioBundle\Entity\Usuario", inversedBy="idProjetoParticipante")
     *
     * @ORM\JoinColumns({
     *
     *   @ORM\JoinColumn(name="id_usuario", referencedColumnName="id")
     * })
     */
    private $idUsuario;

    /**
     * @var FrigaInscricao
     *
     * @ORM\ManyToOne(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricao",inversedBy="idProjetoParticipante")
     *
     * @ORM\JoinColumns({
     *
     *   @ORM\JoinColumn(name="id_inscricao", referencedColumnName="id")
     * })
     */
    private $idInscricao;

    /**
     * @var string
     *
     * @ORM\Column(name="nome", type="string", length=255, nullable=true)
     */
    private $nome;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="observacao", type="text", length=65535, nullable=true)
     */
    private $observacao;

    /**
     * @var string
     *
     * @ORM\Column(name="uuid", type="string", length=255, nullable=true)
     */
    private $uuid;

    /**
     * @var int
     *
     * @ORM\Column(name="confirmacao", type="integer",  nullable=true)
     */
    private $confirmacao;

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
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaArquivo", inversedBy="idProjetoParticipante")
     *
     * @ORM\JoinTable(name="friga_inscricao_projeto_participante_tem_arquivo",
     *   joinColumns={
     *
     *     @ORM\JoinColumn(name="id_projeto_participante", referencedColumnName="id")
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
        // $this->confirmacao = 0;
        $this->idArquivo = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return FrigaInscricaoProjetoParticipante
     */
    public function setId($id)
    {
        $this->id = $id;

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
     *
     * @return FrigaInscricaoProjetoParticipante
     */
    public function setIdUsuario($idUsuario)
    {
        $this->idUsuario = $idUsuario;

        return $this;
    }

    /**
     * @return FrigaInscricao
     */
    public function getIdInscricao()
    {
        return $this->idInscricao;
    }

    /**
     * @param FrigaInscricao $idInscricao
     *
     * @return FrigaInscricaoProjetoParticipante
     */
    public function setIdInscricao($idInscricao)
    {
        $this->idInscricao = $idInscricao;

        return $this;
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
     *
     * @return FrigaInscricaoProjetoParticipante
     */
    public function setNome($nome)
    {
        $this->nome = $nome;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return FrigaInscricaoProjetoParticipante
     */
    public function setEmail($email)
    {
        $this->email = $email;

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
     *
     * @return FrigaInscricaoProjetoParticipante
     */
    public function setObservacao($observacao)
    {
        $this->observacao = $observacao;

        return $this;
    }

    /**
     * @return int
     */
    public function getConfirmacao()
    {
        return $this->confirmacao;
    }

    /**
     * @param int $confirmacao
     *
     * @return FrigaInscricaoProjetoParticipante
     */
    public function setConfirmacao($confirmacao)
    {
        $this->confirmacao = $confirmacao;

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
     * @return FrigaInscricaoProjetoParticipante
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getRegistroDataCriacao()
    {
        return $this->registroDataCriacao;
    }

    /**
     * @param \DateTime $registroDataCriacao
     *
     * @return FrigaInscricaoProjetoParticipante
     */
    public function setRegistroDataCriacao($registroDataCriacao)
    {
        $this->registroDataCriacao = $registroDataCriacao;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getRegistroDataAtualizacao()
    {
        return $this->registroDataAtualizacao;
    }

    /**
     * @param \DateTime $registroDataAtualizacao
     *
     * @return FrigaInscricaoProjetoParticipante
     */
    public function setRegistroDataAtualizacao($registroDataAtualizacao)
    {
        $this->registroDataAtualizacao = $registroDataAtualizacao;

        return $this;
    }

    /**
     * Add idArquivo.
     *
     * @return FrigaInscricaoProjetoParticipante
     */
    public function addIdArquivo(FrigaArquivo $idArquivo)
    {
        if (!$this->idArquivo->contains($idArquivo)) {
            $this->idArquivo->add($idArquivo);
        }

        return $this;
    }

    /**
     * Remove idArquivo.
     */
    public function removeIdArquivo(FrigaArquivo $idArquivo)
    {
        if ($this->idArquivo->contains($idArquivo)) {
            $this->idArquivo->removeElement($idArquivo);
        }
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
    public function getObjConfirmacao()
    {
        $obj = new \stdClass();
        $obj->id = $this->confirmacao;
        $obj->descricao = 'Aguardando confirmação';
        $obj->icone = 'fa fa-clock-o';
        $obj->css = 'label label-info';
        $obj->cssAlert = 'alert alert-info';
        if (!\is_null($this->confirmacao)) {
            switch ($this->confirmacao) {
                case 0:
                    $obj->descricao = 'Não confirmado';
                    $obj->css = 'label label-warning';
                    $obj->icone = 'fa fa-times';
                    $obj->cssAlert = 'alert alert-warning';
                    break;
                case 1:
                    $obj->descricao = 'Confirmado';
                    $obj->css = 'label label-success';
                    $obj->icone = 'fa fa-check-circle-o';
                    $obj->cssAlert = 'alert alert-success';
                    break;
            }
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
