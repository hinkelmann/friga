<?php

namespace Nte\Admin\PainelBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Expr;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEdital;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalEtapa;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalUsuario;
use Nte\UsuarioBundle\Entity\Usuario;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Role\SwitchUserRole;

class MenuController extends Controller
{
    public function topoItemUsuarioAction()
    {
        $authChecker = $this->get('security.authorization_checker');
        $tokenStorage = $this->get('security.token_storage');
        $outro = null;
        if ($authChecker->isGranted('ROLE_PREVIOUS_ADMIN')) {
            foreach ($tokenStorage->getToken()->getRoles() as $role) {
                if ($role instanceof SwitchUserRole) {
                    $em = $this->getDoctrine()->getManager();
                    $outro = $em->find(Usuario::class,$role->getSource()->getUser()->getId());
                    break;
                }
            }
        }

        return $this->render('@NteAdminPainel/Menu/topo-usuario.html.twig', [
            'outro' => $outro
        ]);
    }
}