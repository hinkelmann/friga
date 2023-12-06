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

namespace Nte\Aplicacao\FrigaBundle\EventListener;

use Oneup\UploaderBundle\Uploader\File\FileInterface;
use Oneup\UploaderBundle\Uploader\Naming\NamerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * Class UploadNamer.
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
     */
    public function __construct(TokenStorage $tokenStorage, RequestStack $requestStack)
    {
        $this->tokenStorage = $tokenStorage;
        $this->requestStack = $requestStack;
    }

    /**
     * Creates a user directory name for the file being uploaded.
     *
     * @return string the directory name
     */
    public function name(FileInterface $file)
    {
        $contexto = $this->requestStack->getCurrentRequest()->headers->get('x-contexto');
        $userId = $this->tokenStorage->getToken()->getUser()->getCpf();
        $xId = $this->requestStack->getCurrentRequest()->headers->get('x-id');

        switch ($contexto) {
            case 'PERFIL':
                return \sprintf('usuario/%s/perfil/%s.%s', $userId, \uniqid(), $file->getExtension());
            case 'DOCUMENTO':
                return \sprintf('usuario/%s/documentos/%s.%s', $userId, \uniqid(), $file->getExtension());
            case 'RECURSO':
                return \sprintf('usuario/%s/recursos/%s.%s', $userId, \uniqid(), $file->getExtension());
            case 'PONTUACAO':
            case 'CATEGORIA':
                return \sprintf('usuario/%s/pontuacao/%s.%s', $userId, \uniqid(), $file->getExtension());
            case 'INSCRICAOPROJETO':
                return \sprintf('usuario/%s/projeto/%s.%s', $userId, \uniqid(), $file->getExtension());
            case 'ARQUIVO':
                return \sprintf('usuario/%s/arquivo/%s.%s', $userId, \uniqid(), $file->getExtension());
            case 'CONVITE':
                return \sprintf('usuario/%s/termo/%s.%s', $userId, \uniqid(), $file->getExtension());
            case 'EDITALUSUARIO':
                return \sprintf('usuario/%s/declaracao/%s.%s', $userId, \uniqid(), $file->getExtension());
            case 'EDITAL':
                return \sprintf('edital/%s/%s.%s', $xId, \uniqid(), $file->getExtension());
            default:
                return \sprintf('tmp/%s/%s.%s', $userId, \uniqid(), $file->getExtension());
        }
    }
}
