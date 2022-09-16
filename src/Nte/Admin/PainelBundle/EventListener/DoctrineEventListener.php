<?php

namespace Nte\Admin\PainelBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM;
use Symfony\Component\DependencyInjection\ContainerInterface;
use DateTime;
use Exception
    ;
class  DoctrineEventListener implements EventSubscriber
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
     * @param LifecycleEventArgs $args
     * @throws Exception
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entidade = $args->getObject();
        $em = $args->getObjectManager();

       // dump($entidade);
        if (method_exists($args->getEntity(), 'setRegistroDataCriacao')) {


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
        //
        if (method_exists($args->getEntity(), 'setRegistroDataCriacao')) {
         //   $dt = clone $entidade->getRegistroDataCriacao();
           // $entidade->setRegistroDataCriacao($dt);

        }
    }
}