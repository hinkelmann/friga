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

namespace Nte\Aplicacao\FrigaBundle\Controller;

use Doctrine\ORM\EntityManager;
use Nte\Aplicacao\FrigaBundle\Entity\CAPESAreaConhecimento;
use Nte\Aplicacao\FrigaBundle\Form\CAPESAreaConhecimentoType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CAPESAreaConhecimentoController extends Controller
{
    public function indexAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $areas = $em->getRepository(CAPESAreaConhecimento::class)->findAll();

        return $this->render('NteAplicacaoFrigaBundle:Capes:index.html.twig', [
            'areas' => $areas,
        ]);
    }

    /**
     * @return RedirectResponse|Response|null
     */
    public function criarAction(Request $request, CAPESAreaConhecimento $entity = null)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('nte_admin_painel_homepage');
        }
        if (\is_null($entity)) {
            $entity = new CAPESAreaConhecimento();
        }
        $form = $this->createForm(CAPESAreaConhecimentoType::class, $entity);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirectToRoute('capes_area_conhecimento_index');
        }

        return $this->render('NteAplicacaoFrigaBundle:Capes:form.html.twig', [
            'entity' => $entity,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return RedirectResponse
     */
    public function removerAction(Request $request, CAPESAreaConhecimento $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();

        return $this->redirectToRoute('capes_area_conhecimento_index');
    }
}
