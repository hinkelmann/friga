<?php

namespace Nte\Aplicacao\FrigaBundle\EventListener;

use Oneup\UploaderBundle\Uploader\File\FileInterface;
use Oneup\UploaderBundle\Uploader\Naming\NamerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * Class UploadNamer
 * @package Nte\Aplicacao\FrigaBundle\EventListener
 */
class UploadNamer implements NamerInterface
{
    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * UploadNamer constructor.
     *
     * @param TokenStorage $tokenStorage
     * @param RequestStack $requestStack
     */
    public function __construct(TokenStorage $tokenStorage, RequestStack $requestStack)
    {
        $this->tokenStorage = $tokenStorage;
        $this->requestStack = $requestStack;
    }

    /**
     * Creates a user directory name for the file being uploaded.
     *
     * @param FileInterface $file
     *
     * @return string The directory name.
     */
    public function name(FileInterface $file)
    {
        $contexto = $this->requestStack->getCurrentRequest()->headers->get('x-contexto');
        $userId = $this->tokenStorage->getToken()->getUser()->getCpf();
        $xId = $this->requestStack->getCurrentRequest()->headers->get('x-id');

        switch ($contexto) {
            case 'PERFIL':
                return sprintf('usuario/%s/perfil/%s.%s', $userId, uniqid(), $file->getExtension());
            case 'DOCUMENTO':
                return sprintf('usuario/%s/documentos/%s.%s', $userId, uniqid(), $file->getExtension());
            case 'RECURSO':
                return sprintf('usuario/%s/recursos/%s.%s', $userId, uniqid(), $file->getExtension());
            case 'PONTUACAO':
            case 'CATEGORIA':
                return sprintf('usuario/%s/pontuacao/%s.%s', $userId, uniqid(), $file->getExtension());
            case 'INSCRICAOPROJETO':
                return sprintf('usuario/%s/projeto/%s.%s', $userId, uniqid(), $file->getExtension());
            case 'ARQUIVO':
                return sprintf('usuario/%s/arquivo/%s.%s', $userId, uniqid(), $file->getExtension());
            case 'EDITAL':
                return sprintf('edital/%s/%s.%s', $xId, uniqid(), $file->getExtension());
            default:
                return sprintf('tmp/%s/%s.%s', $userId, uniqid(), $file->getExtension());
        }

    }
}