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

namespace Nte\UsuarioBundle\Command;

use Doctrine\ORM\EntityManager;
use Nte\UsuarioBundle\Entity\Usuario;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UserCommand extends ContainerAwareCommand
{
    /** @var EntityManager */
    protected $em;

    protected function configure()
    {
        $this
            ->setName('fos:user:sie')
            ->setDescription('Busca informações do usuário no webservice xxx');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine')->getManager();
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
        $usuarios = $this->em->createQueryBuilder()
            ->select('u')
            ->from(Usuario::class, 'u')
            ->where('u.extra is null')
            //->andWhere('u.id > 4082 ')
            ->setMaxResults(800)
            ->getQuery()->getResult()
        ;

        /** @var Usuario $user */
        foreach ($usuarios as $user) {
            ///
        }
    }

    /**
     * Persiste objetos no banco de dados e descarrega da memoria.
     *
     * @return mixed
     */
    public function persistir($entity)
    {
        $m = $this->em->getClassMetaData(\get_class($entity));
        $m->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
        $m->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $this->em->merge($entity);
        $this->em->flush();
        $this->em->detach($entity);
        $this->em->clear();

        return $entity;
    }
}
