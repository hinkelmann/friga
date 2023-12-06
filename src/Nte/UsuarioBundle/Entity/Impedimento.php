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

/**
 * Impedimento de usuários.
 *
 * @ORM\Table(name="fos_usuario_impedimento")
 *
 * @ORM\Entity
 *
 * @ORM\HasLifecycleCallbacks()
 */
class Impedimento
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
     *   @ORM\JoinColumn(name="id_usuario0", referencedColumnName="id")
     * })
     */
    private $idUsuario0;

    /**
     * @var Usuario|null
     *
     * @ORM\ManyToOne(targetEntity="Nte\UsuarioBundle\Entity\Usuario")
     *
     * @ORM\JoinColumns({
     *
     *   @ORM\JoinColumn(name="id_usuario1", referencedColumnName="id")
     * })
     */
    private $idUsuario1;

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

    public function __construct($u0, $u1, $txt)
    {
        $this->idUsuario0 = $u0;
        $this->idUsuario1 = $u1;
        $this->justificativa = $txt;
        $this->registroDataCriacao = new \DateTime();
        $this->registroDataAtualizacao = new \DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Impedimento
    {
        $this->id = $id;

        return $this;
    }

    public function getIdUsuario0(): ?Usuario
    {
        return $this->idUsuario0;
    }

    public function setIdUsuario0(?Usuario $idUsuario0): Impedimento
    {
        $this->idUsuario0 = $idUsuario0;

        return $this;
    }

    public function getIdUsuario1(): ?Usuario
    {
        return $this->idUsuario1;
    }

    public function setIdUsuario1(?Usuario $idUsuario1): Impedimento
    {
        $this->idUsuario1 = $idUsuario1;

        return $this;
    }

    public function getJustificativa(): ?string
    {
        return $this->justificativa;
    }

    public function setJustificativa(?string $justificativa): Impedimento
    {
        $this->justificativa = $justificativa;

        return $this;
    }

    public function getRegistroDataCriacao(): ?\DateTime
    {
        return $this->registroDataCriacao;
    }

    public function setRegistroDataCriacao(?\DateTime $registroDataCriacao): Impedimento
    {
        $this->registroDataCriacao = $registroDataCriacao;

        return $this;
    }

    public function getRegistroDataAtualizacao(): ?\DateTime
    {
        return $this->registroDataAtualizacao;
    }

    public function setRegistroDataAtualizacao(?\DateTime $registroDataAtualizacao): Impedimento
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
    }
}
