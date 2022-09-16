<?php

namespace Nte\Admin\SuporteBundle\Controller;

use Nte\Admin\SuporteBundle\Entity\Suporte;
use Nte\Admin\SuporteBundle\Entity\SuporteIteracao;
use Nte\Admin\SuporteBundle\Form\SuporteIteracaoType;
use Nte\Admin\SuporteBundle\Form\SuporteType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        if($this->isGranted('ROLE_ADMIN')){
            $suporte = $this->getDoctrine()
                ->getManager()
                ->getRepository(Suporte::class)->findAll();
        }else{
            $suporte = $this->getDoctrine()
                ->getManager()
                ->getRepository(Suporte::class)
                ->findBy(['idUsuarioSolicitante'=>$this->getUser()]);
        }

        return $this->render('NteSuporteBundle:Default:index.html.twig',[
            'suporte'=>$suporte,
        ]);
    }

    /**
     * @return Response
     */
    public function manualAction()
    {
        return $this->render('NteSuporteBundle:Default:manual.html.twig');
    }
    /**
     * @return Response
     */
    public function sobreAction()
    {
        return $this->render('NteSuporteBundle:Default:sobre.html.twig');
    }

    /**
     * Visualiza o chamado em aberto
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function verAction(Request $request, Suporte $suporte)
    {
        if(!$suporte){
            return $this->redirectToRoute('nte_suporte_homepage');
        }
        $em = $this->getDoctrine()->getManager();
        $suporteIteracao = new SuporteIteracao();
        $suporteIteracao->setIdUsuario($this->getUser());
        $suporteIteracao->setIdSuporte($suporte);
        $form = $this->createForm(SuporteIteracaoType::class, $suporteIteracao);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($suporteIteracao);
            $em->flush();
            $suporte->setIdSituacao(1);
            $em->persist($suporte);
            $em->flush();
            return $this->redirectToRoute('nte_suporte_visualizar',['id'=>$suporte->getId()]);
        }
        return $this->render('NteSuporteBundle:Default:chamado.html.twig', array(
            'suporteIteracao' => $suporteIteracao,
            'suporte' => $suporte,
            'form' => $form->createView(),
        ));
    }
    /**
     * Abre ou edita uma solicitação de suporte
     *
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function formAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $suporte = $em->find(Suporte::class, $id);
        if (!$suporte) {
            $suporte = new Suporte();
        }
        $form = $this->createForm(SuporteType::class, $suporte);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $suporte->setIdSituacao(0);
            $suporte->setIdUsuarioSolicitante($this->getUser());
            $em->persist($suporte);
            $em->flush();
            return $this->redirectToRoute('nte_suporte_homepage');
        }
        return $this->render('NteSuporteBundle:Default:form.html.twig', array(
            'suporte' => $suporte,
            'form' => $form->createView(),
        ));
    }

}
