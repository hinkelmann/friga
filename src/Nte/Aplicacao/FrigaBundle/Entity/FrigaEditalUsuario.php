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
 * FrigaEditalUsuario.
 *
 * @ORM\Table(name="friga_edital_usuario")
 *
 * @ORM\Entity
 *
 * @ORM\HasLifecycleCallbacks()
 */
class FrigaEditalUsuario
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
     * @ORM\ManyToOne(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaEdital", inversedBy="idEditalUsuario")
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
     * @ORM\ManyToOne(targetEntity="Nte\UsuarioBundle\Entity\Usuario", inversedBy="idEditalUsuario")
     *
     * @ORM\JoinColumns({
     *
     *   @ORM\JoinColumn(name="id_usuario", referencedColumnName="id")
     * })
     */
    private $idUsuario;

    /**
     * @var bool
     *
     * @ORM\Column(name="administrador", type="boolean", nullable=true)
     */
    private $administrador;

    /**
     * @var bool
     *
     * @ORM\Column(name="avaliador", type="boolean", nullable=true)
     */
    private $avaliador;

    /**
     * @var bool
     *
     * @ORM\Column(name="resultado", type="boolean", nullable=true)
     */
    private $resultado;

    /**
     * @var bool
     *
     * @ORM\Column(name="convocacao", type="boolean", nullable=true)
     */
    private $convocacao;

    /**
     * @var bool
     *
     * @ORM\Column(name="termo_compromisso", type="boolean", nullable=true)
     */
    private $termoCompromisso;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="termo_compromisso_data", type="datetime", nullable=true)
     */
    private $termoCompromissoData;

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
     * @ORM\ManyToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalCargo", inversedBy="idEditalUsuario")
     *
     * @ORM\JoinTable(name="friga_edital_usuario_tem_cargo",
     *   joinColumns={
     *
     *     @ORM\JoinColumn(name="id_edital_usuario", referencedColumnName="id")
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
     * @ORM\ManyToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaArquivo", inversedBy="idEditalUsuario")
     *
     * @ORM\JoinTable(name="friga_edital_usuario_tem_arquivo",
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

    /**
     * FrigaEditalUsuario constructor.
     */
    public function __construct()
    {
        $this->idEditalCargo = new ArrayCollection();
        $this->idArquivo = new ArrayCollection();
        $this->avaliador = false;
        $this->convocacao = false;
        $this->resultado = false;
        $this->administrador = false;
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
     * @return FrigaEditalUsuario
     */
    public function setId($id)
    {
        $this->id = $id;

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
     * @return FrigaEditalUsuario
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
     * @return FrigaEditalUsuario
     */
    public function setIdUsuario($idUsuario)
    {
        $this->idUsuario = $idUsuario;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAdministrador()
    {
        return $this->administrador;
    }

    /**
     * @param bool $administrador
     *
     * @return FrigaEditalUsuario
     */
    public function setAdministrador($administrador)
    {
        $this->administrador = $administrador;

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
     * @return FrigaEditalUsuario
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
     * @return FrigaEditalUsuario
     */
    public function setRegistroDataAtualizacao($registroDataAtualizacao)
    {
        $this->registroDataAtualizacao = $registroDataAtualizacao;

        return $this;
    }

    /**
     * Add idEditalCargo.
     *
     * @return FrigaEditalUsuario
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
     * @return bool
     */
    public function isTermoCompromisso()
    {
        return $this->termoCompromisso;
    }

    /**
     * Add idArquivo.
     *
     * @return FrigaEditalUsuario
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
     * @param bool $termoCompromisso
     *
     * @return FrigaEditalUsuario
     */
    public function setTermoCompromisso($termoCompromisso)
    {
        $this->termoCompromisso = $termoCompromisso;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getTermoCompromissoData()
    {
        return $this->termoCompromissoData;
    }

    /**
     * @param \DateTime $termoCompromissoData
     *
     * @return FrigaEditalUsuario
     */
    public function setTermoCompromissoData($termoCompromissoData)
    {
        $this->termoCompromissoData = $termoCompromissoData;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAvaliador()
    {
        return $this->avaliador;
    }

    /**
     * @param bool $avaliador
     *
     * @return FrigaEditalUsuario
     */
    public function setAvaliador($avaliador)
    {
        $this->avaliador = $avaliador;

        return $this;
    }

    /**
     * @return bool
     */
    public function isResultado()
    {
        return $this->resultado;
    }

    /**
     * @param bool $resultado
     *
     * @return FrigaEditalUsuario
     */
    public function setResultado($resultado)
    {
        $this->resultado = $resultado;

        return $this;
    }

    /**
     * @return bool
     */
    public function isConvocacao()
    {
        return $this->convocacao;
    }

    /**
     * @param bool $convocacao
     *
     * @return FrigaEditalUsuario
     */
    public function setConvocacao($convocacao)
    {
        $this->convocacao = $convocacao;

        return $this;
    }

    /**
     * Get idEditalCargo.
     *
     * @return ArrayCollection
     */
    public function getIdEditalCargo()
    {
        return $this->idEditalCargo;
    }

    /**
     * @ORM\PreUpdate
     *
     * @throws \Exception
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $this->setRegistroDataAtualizacao(new \DateTime());
    }

    /**
     * @ORM\PrePersist
     *
     * @throws \Exception
     */
    public function PrePersist()
    {
        $this->setRegistroDataCriacao(new \DateTime());
        $this->setRegistroDataAtualizacao(new \DateTime());
    }
}
