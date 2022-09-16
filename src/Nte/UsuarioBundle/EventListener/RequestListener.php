<?php

namespace Nte\UsuarioBundle\EventListener;

use Nte\UsuarioBundle\Entity\Usuario;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 *  Obriga o usuário definir suas informações pessoais;
 * Class RequestListener
 * @package Nte\UsuarioBundle\EventListener
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
     * RequestListener constructor.
     *
     * @param TokenStorageInterface $tokenStorageInterface
     * @param RouterInterface $routerInterface
     * @param AuthorizationCheckerInterface $securityChecker
     */
    public function __construct(TokenStorageInterface $tokenStorageInterface, RouterInterface $routerInterface, AuthorizationCheckerInterface $securityChecker)
    {
        $this->tokenStorageInterface = $tokenStorageInterface;
        $this->routerInterface = $routerInterface;
        $this->securityChecker = $securityChecker;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $urls = ['nte_usuario_redefinir', '_uploader_upload_frigadata', 'arquivo_download','get_logistica_cep',
            '_wdt','_profiler_home', '_profiler_search', '_profiler_search_bar', '_profiler_phpinfo', '_profiler_search_results',
            '_profiler_open_file', '_profiler', '_profiler_router', '_profiler_exception', '_profiler_exception_css',
            '_twig_error_test','fos_user_registration_confirmed'];

        $request = $event->getRequest()->attributes;
        if ($this->securityChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            if (!in_array($request->get('_route'), $urls) and $request->get('_route') != null) {
                /** @var Usuario $user */
                $user = $this->tokenStorageInterface->getToken()->getUser();
                if (is_null($user->getLastLogin()) or is_null($user->getCpf())) {
                   return $event->setResponse(
                        new RedirectResponse($this->routerInterface->generate('nte_usuario_redefinir'))
                    );
                }
            }
        }
    }
}