<?php
// src/Acme/ApiBundle/Entity/AccessToken.php

namespace Nte\UsuarioBundle\Entity;

use FOS\OAuthServerBundle\Entity\AccessToken as BaseAccessToken;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table("oauth2_token_acesso")
 * @ORM\Entity
 */
class TokenAcesso extends BaseAccessToken
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