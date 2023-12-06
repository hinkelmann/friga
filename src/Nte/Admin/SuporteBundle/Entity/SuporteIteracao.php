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

namespace Nte\Admin\SuporteBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaArquivo;
use Nte\UsuarioBundle\Entity\Usuario;

/**
 * SuporteIteracao.
 *
 * @ORM\Table(name="suporte_iteracao")
 *
 * @ORM\Entity
 *
 * @ORM\HasLifecycleCallbacks()
 */
class SuporteIteracao
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
     * @var string
     *
     * @ORM\Column(name="resposta", type="text", length=65535, nullable=true)
     */
    private $resposta;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="registro_data_atualizacao", type="datetime", nullable=true)
     */
    private $registroDataAtualizacao;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="registro_data_criacao", type="datetime", nullable=true)
     */
    private $registroDataCriacao;

    /**
     * @var Suporte
     *
     * @ORM\ManyToOne(targetEntity="Suporte", inversedBy="iteracao")
     *
     * @ORM\JoinColumns({
     *
     *   @ORM\JoinColumn(name="id_suporte", referencedColumnName="id")
     * })
     */
    private $idSuporte;

    /**
     * @var Usuario
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
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Nte\Aplicacao\FrigaBundle\Entity\FrigaArquivo", inversedBy="idSuporteIteracao")
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
     * @ORM\PreUpdate
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        if ($args->hasChangedField('registroDataCriacao')) {
            $this->registroDataCriacao = $args->getOldValue('registroDataCriacao');
            $this->registroDataAtualizacao = new \DateTime();
        }
    }

    /**
     * @ORM\PrePersist
     */
    public function PrePersist()
    {
        $this->registroDataCriacao = new \DateTime();
        $this->registroDataAtualizacao = new \DateTime();
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
     * Set resposta.
     *
     * @param string $resposta
     *
     * @return SuporteIteracao
     */
    public function setResposta($resposta)
    {
        $this->resposta = $resposta;

        return $this;
    }

    /**
     * Get resposta.
     *
     * @return string
     */
    public function getResposta()
    {
        return $this->resposta;
    }

    /**
     * Set registroDataAtualizacao.
     *
     * @param \DateTime $registroDataAtualizacao
     *
     * @return SuporteIteracao
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
     * Set registroDataCriacao.
     *
     * @param \DateTime $registroDataCriacao
     *
     * @return SuporteIteracao
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
     * Set idSuporte.
     *
     * @return SuporteIteracao
     */
    public function setIdSuporte(Suporte $idSuporte = null)
    {
        $this->idSuporte = $idSuporte;

        return $this;
    }

    /**
     * Get idSuporte.
     *
     * @return Suporte
     */
    public function getIdSuporte()
    {
        return $this->idSuporte;
    }

    /**
     * Set idUsuario.
     *
     * @return SuporteIteracao
     */
    public function setIdUsuario(Usuario $idUsuario = null)
    {
        $this->idUsuario = $idUsuario;

        return $this;
    }

    /**
     * Get idUsuario.
     *
     * @return Usuario
     */
    public function getIdUsuario()
    {
        return $this->idUsuario;
    }

    /**
     * Add idArquivo.
     *
     * @return SuporteIteracao
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
}
