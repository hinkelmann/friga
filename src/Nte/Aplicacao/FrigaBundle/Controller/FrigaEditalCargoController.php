<?php

namespace Nte\Aplicacao\FrigaBundle\Controller;


use Nte\Aplicacao\FrigaBundle\Form\FrigaEditalCargoType;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEdital;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalCargo;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * frigaeditalcargo controller.
 *
 */
class FrigaEditalCargoController extends Controller
{


	/**
	 * Creates a new frigaeditalcargo entity.
	 */
	public function criarAction(Request $request, FrigaEdital $frigaEdital)


	{

		$this->getUser();
		if (!$this->isGranted('ROLE_ADMIN_EDITAL')) {
			return $this->redirectToRoute('nte_admin_painel_homepage');
		}
		$frigaeditalcargo = new FrigaEditalCargo();
		$form = $this->createForm(FrigaEditalCargoType::class, $frigaeditalcargo);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$frigaeditalcargo->setIdEdital($frigaEdital);
			$em = $this->getDoctrine()->getManager();
			$em->persist($frigaeditalcargo);
			$em->flush();

			return $this->redirectToRoute('edital_cargo',['uuid'=>$frigaEdital->getUuid()]);
		}

		return $this->render('NteAplicacaoFrigaBundle:cargo:form.html.twig', array(
			'frigaeditalcargo' => $frigaeditalcargo,
			'form' => $form->createView(),
		));
	}

    /**
     * Displays a form to edit an existing frigaeditalcargo entity.
     *
     */
    public function editarAction(Request $request, FrigaEditalCargo $frigaeditalcargo)
    {

        if (!$this->isGranted('ROLE_ADMIN_EDITAL')) {
            return $this->redirectToRoute('nte_admin_painel_homepage');
        }
        
        $deleteForm = $this->createDeleteForm($frigaeditalcargo);
        $configForm = $this->createForm(FrigaEditalCargoType::class, $frigaeditalcargo);
	    $configForm->handleRequest($request);



        if ($configForm->isSubmitted() && $configForm->isValid()) {

            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('edital_cargo',['uuid'=>$frigaeditalcargo->getIdEdital()->getUuid()]);
        }

        return $this->render('NteAplicacaoFrigaBundle:cargo:form.html.twig', array(
            'frigaeditalcargo' => $frigaeditalcargo,
            'form' => $configForm->createView(),
        //    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a frigaedital entity.
     *
     */
    public function removerAction(Request $request, FrigaEditalCargo $frigaeditalcargo)
    {
        if (!$this->isGranted('ROLE_ADMIN_EDITAL')) {
            return $this->redirectToRoute('nte_admin_painel_homepage');
        }
        $form = $this->createDeleteForm($frigaeditalcargo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($frigaeditalcargo);
            $em->flush();
        }

        return $this->redirectToRoute('edital_index');
    }

    /**
     * Creates a form to delete a frigaedital entity.
     *
     * @param FrigaEditalCargo $frigaeditalcargo The frigaedital entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(FrigaEditalCargo $frigaeditalcargo)
    {
        return 0;
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('edital_cargo_editar', array('id' => $frigaeditalcargo->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
