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

namespace Nte\Aplicacao\FrigaBundle\Command;

use Doctrine\ORM\EntityManager;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaArquivo;
use Nte\Aplicacao\FrigaBundle\Entity\Log;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CronTempInvalidoCommand extends ContainerAwareCommand
{
    /** @var EntityManager */
    protected $em;

    protected function configure()
    {
        $this
            ->setName('friga:arquivo:limpar')
            ->setDescription('Remove arquivos temporários e  inválidos com data de criação superior a 90 dias');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dt0 = new \DateTime();
        $dt0->modify('-90 days');

        $this->em = $this->getContainer()->get('doctrine')->getManager();
        //$this->em->getConnection()->getConfiguration()->setSQLLogger(null);

        $d = '/media/frigadata/';
        $arquivos = $this->em->createQueryBuilder()
            ->select('a')
            ->from(FrigaArquivo::class, 'a')
            ->orderBy('a.id', 'asc')
            ->where('a.registroDataCriacao <= :dt0')
            ->setParameter('dt0', $dt0)
            ->getQuery()->getResult();
        $i = 0;
        /** @var FrigaArquivo $item */
        foreach ($arquivos as $item) {
            $f = $d . $item->getNome();
            if (!\is_file($f)) {
                //$this->escreverLog("Arquivo não encontrado",$f,$item);
                $this->em->remove($item);
                ++$i;
            }
            if ('INVALIDADO' == $item->getMetaContexto() or 'TEMP' == $item->getMetaContexto()) {
                //$this->escreverLog("Arquivo ignorando:".$item->getMetaContexto(),$f,$item);
                dump([$item->getMetaContexto() => $f]);
                if (\is_file($f)) {
                    \unlink($f);
                    $this->em->remove($item);
                }
                ++$i;
            }
            if ($i > 50) {
                $this->em->flush();
                $i = 0;
            }
        }
        $this->em->flush();
    }

    private function escreverLog($msg, $arquivo, $item)
    {
        try {
            $log = new Log();
            $log->setMsg($msg)
                ->setContexto(FrigaArquivo::class)
                ->setIdContexto($item->getId())
                ->setInterface(0);
            $this->em = $this->getContainer()->get('doctrine')->getManager();
            $this->em->persist($log);
            $this->em->flush();
            unset($log);
        } catch (\Exception $e) {
            $string = \date('Y-m-d H:i:s');
            $string .= "\t ARQUIVO:" . $arquivo;
            $string .= "\t LOGDB-ERRO" . $e->getMessage();
            $string .= "\r\n";
            \file_put_contents('/var/log/friga.log', $string, \FILE_APPEND);
        }
        $string = \date('Y-m-d H:i:s');
        $string .= "\t ARQUIVO:" . $arquivo;
        $string .= "\t $msg";
        $string .= "\r\n";
        \file_put_contents('/var/log/friga.log', $string, \FILE_APPEND);
    }
}
