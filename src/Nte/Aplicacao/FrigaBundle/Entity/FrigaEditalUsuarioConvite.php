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

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Nte\UsuarioBundle\Entity\Usuario;

/**
 * FrigaEditalUsuarioConvite.
 *
 * @ORM\Table(name="friga_edital_usuario_convite")
 *
 * @ORM\Entity
 *
 * @ORM\HasLifecycleCallbacks()
 */
class FrigaEditalUsuarioConvite
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
     * @var FrigaEdital
     *
     * @ORM\ManyToOne(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaEdital", inversedBy="idEditalUsuarioConvite")
     *
     * @ORM\JoinColumns({
     *
     *   @ORM\JoinColumn(name="id_edital", referencedColumnName="id")
     * })$idPontuacao
     */
    private $idEdital;

    /**
     * @var Usuario
     *
     * @ORM\ManyToOne(targetEntity="Nte\UsuarioBundle\Entity\Usuario", inversedBy="idEditalUsuarioConvite")
     *
     * @ORM\JoinColumns({
     *
     *   @ORM\JoinColumn(name="id_usuario", referencedColumnName="id", nullable=true)
     * })
     */
    private $idUsuario;

    /**
     * @var string
     *
     * @ORM\Column(name="uuid", type="string", length=255, nullable=true)
     */
    private $uuid;

    /**
     * @var string
     *
     * @ORM\Column(name="cpf", type="string", length=15, nullable=true)
     */
    private $cpf;

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
     * @ORM\Column(name="descricao", type="text", nullable=true)
     */
    private $descricao;

    /**
     * @var bool
     *
     * @ORM\Column(name="funcao_administracao", type="boolean",nullable=true)
     */
    private $funcaoAdministracao;

    /**
     * @var bool
     *
     * @ORM\Column(name="funcao_avaliacao", type="boolean",nullable=true)
     */
    private $funcaoAvaliacao;

    /**
     * @var bool
     *
     * @ORM\Column(name="funcao_convocacao", type="boolean",nullable=true)
     */
    private $funcaoConvocacao;

    /**
     * @var bool
     *
     * @ORM\Column(name="funcao_resultado", type="boolean",nullable=true)
     */
    private $funcaoResultado;

    /**
     * @var bool
     *
     * @ORM\Column(name="envio", type="boolean",nullable=true)
     */
    private $envio;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="envio_data", type="datetime", nullable=true)
     */
    private $envioData;

    /**
     * @var bool
     *
     * @ORM\Column(name="aceite", type="boolean",nullable=true)
     */
    private $aceite;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="aceite_data", type="datetime", nullable=true)
     */
    private $aceiteData;

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
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalCargo", inversedBy="idEditalUsuarioConvite")
     *
     * @ORM\JoinTable(name="friga_edital_usuario_convite_tem_cargo",
     *   joinColumns={
     *
     *     @ORM\JoinColumn(name="id_edital_usuario_convite", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="id_cargo", referencedColumnName="id")
     *   }
     * )
     */
    protected $idEditalCargo;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaArquivo", inversedBy="idEditalUsuarioConvite")
     *
     * @ORM\JoinTable(name="friga_edital_usuario_convite_tem_arquivo",
     *   joinColumns={
     *
     *     @ORM\JoinColumn(name="id_edital_usuario", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="id_arquivo", referencedColumnName="id")
     *   }
     * )
     */
    private $idArquivo;

    public function __construct()
    {
        $this->envio = 0;
        $this->idArquivo = new ArrayCollection();
        $this->idEditalCargo = new ArrayCollection();
        $this->registroDataCriacao = new \DateTime();
        $this->registroDataAtualizacao = new \DateTime();
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
     * @return FrigaEditalUsuarioConvite
     */
    public function setId($id)
    {
        $this->id = $id;

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
     * @return FrigaEditalUsuarioConvite
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * @return FrigaEdital
     */
    public function getIdEdital()
    {
        return $this->idEdital;
    }

    /**
     * @param FrigaEdital $idEdital
     *
     * @return FrigaEditalUsuarioConvite
     */
    public function setIdEdital($idEdital)
    {
        $this->idEdital = $idEdital;

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
     * @return FrigaEditalUsuarioConvite
     */
    public function setIdUsuario($idUsuario)
    {
        $this->idUsuario = $idUsuario;

        return $this;
    }

    /**
     * @return string
     */
    public function getCpf()
    {
        return $this->cpf;
    }

    /**
     * @param string $cpf
     *
     * @return FrigaEditalUsuarioConvite
     */
    public function setCpf($cpf)
    {
        $this->cpf = $cpf;

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
     * @return FrigaEditalUsuarioConvite
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
     * @return FrigaEditalUsuarioConvite
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * @param string $descricao
     *
     * @return FrigaEditalUsuarioConvite
     */
    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;

        return $this;
    }

    /**
     * @return bool
     */
    public function isFuncaoAdministracao()
    {
        return $this->funcaoAdministracao;
    }

    /**
     * @param bool $funcaoAdministracao
     *
     * @return FrigaEditalUsuarioConvite
     */
    public function setFuncaoAdministracao($funcaoAdministracao)
    {
        $this->funcaoAdministracao = $funcaoAdministracao;

        return $this;
    }

    /**
     * @return bool
     */
    public function isFuncaoAvaliacao()
    {
        return $this->funcaoAvaliacao;
    }

    /**
     * @param bool $funcaoAvaliacao
     *
     * @return FrigaEditalUsuarioConvite
     */
    public function setFuncaoAvaliacao($funcaoAvaliacao)
    {
        $this->funcaoAvaliacao = $funcaoAvaliacao;

        return $this;
    }

    /**
     * @return bool
     */
    public function isFuncaoConvocacao()
    {
        return $this->funcaoConvocacao;
    }

    /**
     * @param bool $funcaoConvocacao
     *
     * @return FrigaEditalUsuarioConvite
     */
    public function setFuncaoConvocacao($funcaoConvocacao)
    {
        $this->funcaoConvocacao = $funcaoConvocacao;

        return $this;
    }

    /**
     * @return bool
     */
    public function isFuncaoResultado()
    {
        return $this->funcaoResultado;
    }

    /**
     * @param bool $funcaoResultado
     *
     * @return FrigaEditalUsuarioConvite
     */
    public function setFuncaoResultado($funcaoResultado)
    {
        $this->funcaoResultado = $funcaoResultado;

        return $this;
    }

    /**
     * @return bool
     */
    public function isEnvio()
    {
        return $this->envio;
    }

    /**
     * @param bool $envio
     *
     * @return FrigaEditalUsuarioConvite
     */
    public function setEnvio($envio)
    {
        $this->envio = $envio;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEnvioData()
    {
        return $this->envioData;
    }

    /**
     * @param \DateTime $envioData
     *
     * @return FrigaEditalUsuarioConvite
     */
    public function setEnvioData($envioData)
    {
        $this->envioData = $envioData;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAceite()
    {
        return $this->aceite;
    }

    /**
     * @param bool $aceite
     *
     * @return FrigaEditalUsuarioConvite
     */
    public function setAceite($aceite)
    {
        $this->aceite = $aceite;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getAceiteData()
    {
        return $this->aceiteData;
    }

    /**
     * @param \DateTime $aceiteData
     *
     * @return FrigaEditalUsuarioConvite
     */
    public function setAceiteData($aceiteData)
    {
        $this->aceiteData = $aceiteData;

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
     * @return FrigaEditalUsuarioConvite
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
     * @return FrigaEditalUsuarioConvite
     */
    public function setRegistroDataAtualizacao($registroDataAtualizacao)
    {
        $this->registroDataAtualizacao = $registroDataAtualizacao;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getIdEditalCargo()
    {
        return $this->idEditalCargo;
    }

    /**
     * Add idEditalCargo.
     *
     * @return FrigaEditalUsuarioConvite
     */
    public function addIdEditalCargo(FrigaEditalCargo $idEditalCargo)
    {
        $this->idEditalCargo->add($idEditalCargo);

        return $this;
    }

    /**
     * Remove idEditalCargo.
     */
    public function removeIdEditalCargo(FrigaEditalCargo $idEditalCargo)
    {
        $this->idEditalCargo->removeElement($idEditalCargo);
    }

    /**
     * Add idArquivo.
     *
     * @return FrigaEditalUsuarioConvite
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
     * @return ArrayCollection
     */
    public function getIdArquivo()
    {
        return $this->idArquivo;
    }

    /**
     * @ORM\PreUpdate
     *
     * @throws \Exception
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        if ($args->hasChangedField('aceite')) {
            $this->setAceiteData(new \DateTime());
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
