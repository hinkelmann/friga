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

class CronOcrCommand extends ContainerAwareCommand
{
    /** @var EntityManager */
    protected $em;

    protected function configure()
    {
        $this
            ->setName('friga:arquivo:ocr')
            ->setDescription('Tranforma arquivos em PDF em texto');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine')->getManager();

        $d = '/media/frigadata/';
        $tmp = '/media/frigadata/tmp/ocr/';
        $tmpIn = '/media/frigadata/tmp/ocr/input.pdf';
        $tmpOut = '/media/frigadata/tmp/ocr/output.pdf';
        $tmpOutTxt = '/media/frigadata/tmp/ocr/output.txt';
        if (!\is_dir($tmp)) {
            \mkdir($tmp);
        }
        $arquivos = $this->em->createQueryBuilder()
            ->select('a')
            ->from(FrigaArquivo::class, 'a')
            ->orderBy('a.id', 'desc')
            ->getQuery()->getResult();

        /** @var FrigaArquivo $item */
        foreach ($arquivos as $item) {
            $f = $d . $item->getNome();
            if (!\is_file($f)) {
                $this->escreverLog('Arquivo não encontrado', $f, $item);
                continue;
            }
            if ('INVALIDADO' == $item->getMetaContexto() or 'TEMP' == $item->getMetaContexto()) {
                $this->escreverLog('Arquivo ignorando:' . $item->getMetaContexto(), $f, $item);
                continue;
            }
            if (\is_file($tmpIn)) {
                \unlink($tmpIn);
            }
            if (\is_file($tmpOut)) {
                \unlink($tmpOut);
            }
            if (\is_file($tmpOutTxt)) {
                \unlink($tmpOutTxt);
            }
            \copy($f, $tmpIn);
            $cmd = "env TMPDIR=$tmp /usr/bin/ocrmypdf --deskew --oversample 600 --clean --rotate-pages --sidecar $tmpOutTxt $tmpIn $tmpOut > /dev/null";
            \exec($cmd, $cod, $err);
            if (6 == $err) {
                $cmd = "/usr/bin/pdftotext $tmpIn  $tmpOutTxt";
                \exec($cmd, $cod, $err);
            }
            if (\is_file($tmpOutTxt)) {
                try {
                    $item->setConteudo(\file_get_contents($tmpOutTxt));
                    $this->em = $this->getContainer()->get('doctrine')->getManager();
                    $this->em->persist($item);
                    $this->em->flush();
                    $this->escreverLog('Arquivo processado pelo OCR', $f, $item);
                    unset($item);
                } catch (\Exception $e) {
                    $this->escreverLog('ERRO:' . $e->getMessage(), $f, $item);
                }
            }
        }
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
            $string .= "\t OCR:" . $arquivo;
            $string .= "\t LOGDB-ERRO" . $e->getMessage();
            $string .= "\r\n";
            \file_put_contents('/var/log/friga.log', $string, \FILE_APPEND);
        }
        $string = \date('Y-m-d H:i:s');
        $string .= "\t OCR:" . $arquivo;
        $string .= "\t $msg";
        $string .= "\r\n";
        \file_put_contents('/var/log/friga.log', $string, \FILE_APPEND);
    }
}
