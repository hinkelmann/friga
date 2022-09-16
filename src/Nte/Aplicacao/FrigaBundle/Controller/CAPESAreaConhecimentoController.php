<?php

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
        $em= $this->getDoctrine()->getManager();
        $areas = $em->getRepository(CAPESAreaConhecimento::class)->findAll();
        return $this->render('NteAplicacaoFrigaBundle:Capes:index.html.twig', [
            'areas'=>$areas,
        ]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response|null
     */
    public function criarAction(Request $request, CAPESAreaConhecimento $entity = null)
    {

        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('nte_admin_painel_homepage');
        }
        if(is_null($entity)){
            $entity  = new CAPESAreaConhecimento();
        }
        $form = $this->createForm(CAPESAreaConhecimentoType::class, $entity);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirectToRoute('capes_area_conhecimento_index');
        }

        return $this->render('NteAplicacaoFrigaBundle:Capes:form.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    /**
     * @param Request $request
     * @param CAPESAreaConhecimento $entity
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
