<?php

namespace Nte\Admin\SuporteBundle\Controller;

use Nte\Admin\SuporteBundle\Entity\Suporte;
use Nte\Admin\SuporteBundle\Entity\SuporteFaq;
use Nte\Admin\SuporteBundle\Form\SuporteFaqType;
use Nte\Admin\SuporteBundle\Form\SuporteType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FaqController extends Controller
{
    public function indexAction()
    {
        $suporte = $this->getDoctrine()
            ->getManager()
            ->getRepository(SuporteFaq::class)->findAll();
        return $this->render('NteSuporteBundle:Default:index-faq.html.twig', [
            'suporte' => $suporte,
        ]);
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
        if ($this->isGranted('ROLE_ADMIN')) {
            $em = $this->getDoctrine()->getManager();

            if (!$id) {
                $suporte = new SuporteFaq();
            } else {
                $suporte = $em->find(SuporteFaq::class, $id);
            }

            $form = $this->createForm(SuporteFaqType::class, $suporte);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $suporte->setIdUsuario($this->getUser());
                $em->persist($suporte);
                $em->flush();
                return $this->redirectToRoute('nte_suporte_faq');
            }
            return $this->render('NteSuporteBundle:Default:form-faq.html.twig', array(
                'suporte' => $suporte,
                'form' => $form->createView(),

            ));
        } else {
            return $this->redirectToRoute('nte_suporte_homepage');
        }
    }

    /**
     * Deletes a SuporteFaq entity.
     */
    public function deleteAction(Request $request, SuporteFaq $faq)
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($faq);
            $em->flush();
            return $this->redirectToRoute('nte_suporte_faq');
        } else {
            return $this->redirectToRoute('nte_suporte_homepage');
        }

    }

}
