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

namespace Nte\UsuarioBundle\EventListener;

use Nte\UsuarioBundle\Entity\Usuario;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LoginListener
{
    private $router;
    private $dispatcher;
    private $securityChecker;

    public function __construct(Router $router, EventDispatcherInterface $dispatcher, $securityChecker)
    {
        $this->router = $router;
        $this->dispatcher = $dispatcher;
        $this->securityChecker = $securityChecker;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        if ($this->securityChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            /** @var Usuario $user */
            $user = $event->getAuthenticationToken()->getUser();
            if (null == $user->getLastLogin() or null === $user->getCpf() or 'assets/img/default_user.png' === $user->getImg()) {
                $this->dispatcher->addListener(KernelEvents::RESPONSE, [
                    $this,
                    'onKernelResponse',
                ]);
            }
        }
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        $response = new RedirectResponse($this->router->generate('nte_usuario_redefinir'));
        $event->setResponse($response);
    }
}
