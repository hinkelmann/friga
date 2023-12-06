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

namespace Nte\Admin\SuporteBundle\Controller;

use Nte\Admin\SuporteBundle\Entity\Suporte;
use Nte\Admin\SuporteBundle\Entity\SuporteFaq;
use Nte\Admin\SuporteBundle\Form\SuporteFaqType;
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
     * Abre ou edita uma solicitação de suporte.
     *
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

            return $this->render('NteSuporteBundle:Default:form-faq.html.twig', [
                'suporte' => $suporte,
                'form' => $form->createView(),
            ]);
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
