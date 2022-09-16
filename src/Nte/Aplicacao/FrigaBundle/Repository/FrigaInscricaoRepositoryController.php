<?php

namespace Nte\Aplicacao\FrigaBundle\Controller;

use Doctrine\ORM\EntityRepository;

/**
 * Class FrigaInscricaoRepositoryController
 * @package Nte\Aplicacao\FrigaBundle\Controller
 */
class FrigaInscricaoRepositoryController extends EntityRepository
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }
}
