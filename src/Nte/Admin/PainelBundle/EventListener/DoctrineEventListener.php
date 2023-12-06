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

namespace Nte\Admin\PainelBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DoctrineEventListener implements EventSubscriber
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return array|string[]
     */
    public function getSubscribedEvents()
    {
        return [
            ORM\Events::prePersist,
            ORM\Events::postPersist,
        ];
    }

    /**
     * @throws \Exception
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entidade = $args->getObject();
        $em = $args->getObjectManager();

        // dump($entidade);
        if (\method_exists($args->getEntity(), 'setRegistroDataCriacao')) {
            //$uow  = $em->getUnitOfWork();
            //$meta = $em->getClassMetadata(get_class($entidade));
            //dump($meta,$uow);
            //    $uow->computeChangeSet($meta, $entidade);
        }
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entidade = $args->getObject();
        $em = $args->getObjectManager();

        if (\method_exists($args->getEntity(), 'setRegistroDataCriacao')) {
            //   $dt = clone $entidade->getRegistroDataCriacao();
            // $entidade->setRegistroDataCriacao($dt);
        }
    }
}
