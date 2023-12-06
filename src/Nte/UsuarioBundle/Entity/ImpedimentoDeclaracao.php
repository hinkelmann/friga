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

namespace Nte\UsuarioBundle\Entity;

use DateTime;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEdital;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricao;

/**
 * Impedimento de usuários.
 *
 * @ORM\Table(name="fos_usuario_impedimento_declaracao")
 *
 * @ORM\Entity
 *
 * @ORM\HasLifecycleCallbacks()
 */
class ImpedimentoDeclaracao
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
     * @var Usuario|null
     *
     * @ORM\ManyToOne(targetEntity="Nte\UsuarioBundle\Entity\Usuario")
     *
     * @ORM\JoinColumns({
     *
     *   @ORM\JoinColumn(name="id_usuario", referencedColumnName="id")
     * })
     */
    private $idUsuario;

    /**
     * @var FrigaInscricao|null
     *
     * @ORM\ManyToOne(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricao", inversedBy="idImpedimentoDeclaracao")
     *
     * @ORM\JoinColumns({
     *
     *   @ORM\JoinColumn(name="id_inscricao", referencedColumnName="id")
     * })
     */
    private $idInscricao;

    /**
     * @var FrigaEdital|null
     *
     * @ORM\ManyToOne(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaEdital", inversedBy="idImpedimentoDeclaracao")
     *
     * @ORM\JoinColumns({
     *
     *   @ORM\JoinColumn(name="id_edital", referencedColumnName="id")
     * })
     */
    private $idEdital;

    /**
     * @var string|null
     *
     * @ORM\Column(name="justificativa", type="text", length=65535, nullable=true)
     */
    private $justificativa;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="registro_data_criacao", type="datetime", nullable=true)
     */
    private $registroDataCriacao;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="registro_data_atualizacao", type="datetime", nullable=true)
     */
    private $registroDataAtualizacao;

    public function __construct(Usuario $u, FrigaInscricao $i, string $j)
    {
        $this->idUsuario = $u;
        $this->idInscricao = $i;
        $this->idEdital = $i->getIdEdital();
        $this->justificativa = $j;
        $this->registroDataCriacao = new \DateTime();
        $this->registroDataAtualizacao = new \DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): ImpedimentoDeclaracao
    {
        $this->id = $id;

        return $this;
    }

    public function getIdUsuario(): Usuario
    {
        return $this->idUsuario;
    }

    public function setIdUsuario(Usuario $idUsuario): ImpedimentoDeclaracao
    {
        $this->idUsuario = $idUsuario;

        return $this;
    }

    public function getIdInscricao(): FrigaInscricao
    {
        return $this->idInscricao;
    }

    public function setIdInscricao(FrigaInscricao $idInscricao): ImpedimentoDeclaracao
    {
        $this->idInscricao = $idInscricao;

        return $this;
    }

    public function getIdEdital(): FrigaEdital
    {
        return $this->idEdital;
    }

    public function setIdEdital(FrigaEdital $idEdital): ImpedimentoDeclaracao
    {
        $this->idEdital = $idEdital;

        return $this;
    }

    public function getJustificativa(): string
    {
        return $this->justificativa;
    }

    public function setJustificativa(string $justificativa): ImpedimentoDeclaracao
    {
        $this->justificativa = $justificativa;

        return $this;
    }

    public function getRegistroDataCriacao(): \DateTime
    {
        return $this->registroDataCriacao;
    }

    public function setRegistroDataCriacao(\DateTime $registroDataCriacao): ImpedimentoDeclaracao
    {
        $this->registroDataCriacao = $registroDataCriacao;

        return $this;
    }

    public function getRegistroDataAtualizacao(): \DateTime
    {
        return $this->registroDataAtualizacao;
    }

    public function setRegistroDataAtualizacao(\DateTime $registroDataAtualizacao): ImpedimentoDeclaracao
    {
        $this->registroDataAtualizacao = $registroDataAtualizacao;

        return $this;
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $this->registroDataAtualizacao = new \DateTime();
    }

    /**
     * @ORM\PrePersist
     */
    public function PrePersist()
    {
        $this->registroDataCriacao = new \DateTime();
        $this->registroDataAtualizacao = new \DateTime();
    }
}
