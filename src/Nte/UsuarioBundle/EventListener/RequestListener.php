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

use Doctrine\Common\Persistence\ObjectManager;
use Nte\Aplicacao\FrigaBundle\Entity\Log;
use Nte\UsuarioBundle\Entity\Usuario;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 *  Obriga o usuário definir suas informações pessoais;
 * Class RequestListener.
 */
class RequestListener
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorageInterface;

    /**
     * @var RouterInterface
     */
    private $routerInterface;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $securityChecker;

    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * RequestListener constructor.
     */
    public function __construct(TokenStorageInterface $tokenStorageInterface, RouterInterface $routerInterface, AuthorizationCheckerInterface $securityChecker, ObjectManager $om
    ) {
        $this->tokenStorageInterface = $tokenStorageInterface;
        $this->routerInterface = $routerInterface;
        $this->securityChecker = $securityChecker;
        $this->om = $om;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $urls = ['nte_usuario_redefinir', '_uploader_upload_frigadata', 'arquivo_download', 'get_logistica_cep',
            '_wdt', '_profiler_home', '_profiler_search', '_profiler_search_bar', '_profiler_phpinfo', '_profiler_search_results',
            '_profiler_open_file', '_profiler', '_profiler_router', '_profiler_exception', '_profiler_exception_css',
            '_twig_error_test', 'fos_user_registration_confirmed'];

        $request = $event->getRequest()->attributes;
        if ($this->securityChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            $uri = \str_replace('/app.php/', '', $event->getRequest()->server->get('REQUEST_URI'));
            $log = new Log();
            $log->setMetodo($event->getRequest()->getMethod())
                ->setDominio($event->getRequest()->server->get('HTTP_HOST'))
                ->setUri($uri)
                ->setIdUsuario($this->tokenStorageInterface->getToken()->getUser())
                ->setInterface(1);

            $this->om->persist($log);
            $this->om->flush();

            if (!\in_array($request->get('_route'), $urls) and null != $request->get('_route')) {
                /** @var Usuario $user */
                $user = $this->tokenStorageInterface->getToken()->getUser();
                if ($user->getUpdate() or \is_null($user->getLastLogin()) or \is_null($user->getCpf())) {
                    return $event->setResponse(
                        new RedirectResponse($this->routerInterface->generate('nte_usuario_redefinir'))
                    );
                }
            }
        }
    }
}
