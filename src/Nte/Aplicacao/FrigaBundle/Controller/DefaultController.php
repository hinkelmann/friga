<?php

namespace Nte\Aplicacao\FrigaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('NteAplicacaoFrigaBundle:Default:index.html.twig');
    }

	public function testePdfAction()
	{
		return $this->render('NteAplicacaoFrigaBundle:Default:pdf.html.twig');
	}
}
