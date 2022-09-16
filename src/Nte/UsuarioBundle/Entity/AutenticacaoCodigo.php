<?php
// src/Acme/ApiBundle/Entity/AuthCode.php

namespace Nte\UsuarioBundle\Entity;

use FOS\OAuthServerBundle\Entity\AuthCode as BaseAuthCode;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table("oauth2_autenticacao_codigo")
 * @ORM\Entity
 */
class AutenticacaoCodigo extends BaseAuthCode
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Nte\UsuarioBundle\Entity\Cliente")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $client;

    /**
     * @ORM\ManyToOne(targetEntity="Nte\UsuarioBundle\Entity\Usuario")
     */
    protected $user;
}