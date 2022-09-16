<?php

namespace Nte\Admin\SuporteBundle\Entity;
use Nte\UsuarioBundle\Entity\Usuario;


use Doctrine\ORM\Mapping as ORM;

/**
 * SuporteFaq
 *
  @ORM\Table(name="suporte_faq")
 * @ORM\Entity
 */
class SuporteFaq
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
     * @var string
     *
     * @ORM\Column(name="resposta", type="text", length=65535, nullable=true)
     */
    private $resposta;

    /**
     * @var string
     *
     * @ORM\Column(name="pergunta", type="text", length=65535, nullable=true)
     */
    private $pergunta;
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
     * Set resposta
     *
     * @param string $resposta
     *
     * @return SuporteFaq
     */
    public function setResposta($resposta)
    {
        $this->resposta = $resposta;

        return $this;
    }

    /**
     * Get resposta
     *
     * @return string
     */
    public function getResposta()
    {
        return $this->resposta;
    }

    /**
     * @return string
     */
    public function getPergunta()
    {
        return $this->pergunta;
    }

    /**
     * @param string $pergunta
     * @return SuporteFaq
     */
    public function setPergunta($pergunta)
    {
        $this->pergunta = $pergunta;
        return $this;
    }



    /**
     * Set registroDataAtualizacao
     *
     * @param \DateTime $registroDataAtualizacao
     *
     * @return SuporteFaq
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
     * Set registroDataCriacao
     *
     * @param \DateTime $registroDataCriacao
     *
     * @return SuporteFaq
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
     * Set idUsuario
     *
     * @param Usuario $idUsuario
     *
     * @return SuporteFaq
     */
    public function setIdUsuario(Usuario $idUsuario = null)
    {
        $this->idUsuario = $idUsuario;

        return $this;
    }

    /**
     * Get idUsuario
     *
     * @return Usuario
     */
    public function getIdUsuario()
    {
        return $this->idUsuario;
    }
}
