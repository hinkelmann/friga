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

namespace Nte\Admin\PainelBundle\Controller;

use Nte\UsuarioBundle\Entity\Usuario;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
                    $outro = $em->find(Usuario::class, $role->getSource()->getUser()->getId());
                    break;
                }
            }
        }

        return $this->render('@NteAdminPainel/Menu/topo-usuario.html.twig', [
            'outro' => $outro,
        ]);
    }
}
