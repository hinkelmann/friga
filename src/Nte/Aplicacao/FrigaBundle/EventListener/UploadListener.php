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

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\QueryBuilder;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaArquivo;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEdital;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalUsuario;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalUsuarioConvite;
use Nte\UsuarioBundle\Entity\Usuario;
use Oneup\UploaderBundle\Event\PostPersistEvent;
use Oneup\UploaderBundle\Event\ValidationEvent;
use Oneup\UploaderBundle\Uploader\Response\ResponseInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UploadListener
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorageInterface;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * UploadListener constructor.
     */
    public function __construct(ObjectManager $om, TokenStorageInterface $tokenStorageInterface, RequestStack $requestStack)
    {
        $this->om = $om;
        $this->tokenStorageInterface = $tokenStorageInterface;
        $this->requestStack = $requestStack;
    }

    /**
     * @return ResponseInterface
     *
     * @throws \Exception
     */
    public function onUpload(PostPersistEvent $event)
    {
        /** @var Usuario $usuario */
        $usuario = $this->tokenStorageInterface->getToken()->getUser();
        $contexto = $this->requestStack->getCurrentRequest()->headers->get('x-contexto');
        $id = $this->requestStack->getCurrentRequest()->headers->get('x-id');

        /** @var QueryBuilder $qb */
        $qb = $this->om->createQueryBuilder();

        $arquivo = new FrigaArquivo();
        $arquivo->setIdUsuario($usuario);
        $arquivo->setContexto($contexto);
        $arquivo->setNome($event->getFile()->getPath());
        $arquivo->setMimeType($event->getFile()->getMimeType());

        switch ($contexto) {
            case 'ARQUIVO':
            case 'CATEGORIA':
            case 'DOCUMENTO':
            case 'RECURSO':
            case 'PONTUACAO':
            case 'INSCRICAOPROJETO':
                $arquivo->setIdContexto($id);
                $this->om->persist($arquivo);
                $this->om->flush();
                break;

            case 'PERFIL':
                $qb->update(FrigaArquivo::class, 'a')
                    ->set('a.atributo', 0)
                    ->where('a.contexto = :c')
                    ->andwhere('a.idUsuario = :u')
                    ->setParameter('c', $contexto)
                    ->setParameter('u', $usuario)
                    ->getQuery()->execute();
                $arquivo->setAtributo(100);
                $arquivo->setDataPublicacao(new \DateTime());
                $this->om->persist($arquivo);
                $this->om->flush();
                break;
            case 'EDITAL':
                $edital = $this->om->find(FrigaEdital::class, $id);
                $edital->addIdArquivo($arquivo);
                $arquivo->addIdEdital($edital);
                $arquivo->setTitulo('Sem Título');
                $arquivo->setDataPublicacao(new \DateTime('2100-10-10 00:00:00'));
                $this->om->persist($arquivo);
                $this->om->persist($edital);
                $this->om->flush();
                break;
            case 'CONVITE':
                $convite = $this->om->find(FrigaEditalUsuarioConvite::class, $id);
                $convite->addIdArquivo($arquivo);
                $arquivo->addIdEditalUsuarioConvite($convite);
                $arquivo->setIdContexto($id);
                $this->om->persist($arquivo);
                $this->om->persist($convite);
                $this->om->flush();
                break;
            case 'EDITALUSUARIO':
                $editalusuario = $this->om->find(FrigaEditalUsuario::class, $id);
                $editalusuario->addIdArquivo($arquivo);
                $arquivo->addIdEditalUsuario($editalusuario);
                $arquivo->setIdContexto($id);
                $this->om->persist($arquivo);
                $this->om->persist($editalusuario);
                $this->om->flush();
                break;
            default:
                //		return sprintf('%s/%s.%s', $userId, uniqid(), $file->getExtension());
        }
        $response = $event->getResponse();
        $response['success'] = true;
        $response['msg'] = 'Arquivo enviado com sucesso';
        $response['tipo'] = 'success';
        $response['id'] = $arquivo->getId();
        $response['idContexto'] = $arquivo->getIdContexto();
        $response['contexto'] = $arquivo->getContexto();

        return $response;
    }

    /**
     * Validação extra.
     *
     * @return bool
     */
    public function onValidate(ValidationEvent $event)
    {
        $config = $event->getConfig();
        $file = $event->getFile();
        $type = $event->getType();
        $request = $event->getRequest();

        // do some validations
        //

        //	throw new ValidationException('Sorry! Always false.');

        return true;
    }
}
