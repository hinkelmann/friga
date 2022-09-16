<?php

namespace Nte\Admin\PainelBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BackupRestoreController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }
}
