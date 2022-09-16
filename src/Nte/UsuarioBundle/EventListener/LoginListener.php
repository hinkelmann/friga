<?php

namespace Nte\UsuarioBundle\EventListener;

use Nte\UsuarioBundle\Entity\Usuario;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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
            if ($user->getLastLogin() == null or $user->getCpf() === null or $user->getImg() === 'assets/img/default_user.png') {
                $this->dispatcher->addListener(KernelEvents::RESPONSE, array(
                    $this,
                    'onKernelResponse'
                ));
            }
        }
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        $response = new RedirectResponse ($this->router->generate('nte_usuario_redefinir'));
        $event->setResponse($response);
    }
}