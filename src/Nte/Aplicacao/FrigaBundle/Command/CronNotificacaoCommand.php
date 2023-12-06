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
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalEtapa;
use Nte\UsuarioBundle\Entity\Usuario;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CronNotificacaoCommand extends ContainerAwareCommand
{
    /** @var EntityManager */
    protected $em;

    protected $mail;

    protected $template;

    protected function configure()
    {
        $this
            ->setName('friga:cron:notificacao')
            ->setDescription('Envia notificações para os usuários');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var EntityManager em */
        $this->em = $this->getContainer()->get('doctrine')->getManager();
        $this->template = $this->getContainer()->get('templating');
        $this->mail = $this->getContainer()->get('mailer');
        // $this->em->getConnection()->getConfiguration()->setSQLLogger(null);

        $dt0 = new \DateTime();
        $dt1 = new \DateTime();
        $dt0->setTime(0, 0, 0);
        $dt1->setTime(23, 59, 59);
        $etapas = $this->em->createQueryBuilder()
            ->select('fee')
            ->from(FrigaEditalEtapa::class, 'fee')
            ->orderBy('fee.id', 'asc')
            ->setParameter('dt0', $dt0)
            ->setParameter('dt1', $dt1);

        $etapas0 = $etapas
            ->where('fee.dataInicial between :dt0  and :dt1 and fee.tipo in (3,4,5,7)')
            ->getQuery()
            ->getResult();

        $etapas1 = $etapas
            ->where('fee.dataFinal between :dt0  and :dt1 and fee.tipo in (3,4,5,7)')
            ->getQuery()
            ->getResult();

        /** @var FrigaEditalEtapa $etapa */
        foreach ($etapas0 as $etapa) {
            /** @var Usuario $usuario */
            foreach ($etapa->getIdEdital()->getBanca() as $usuario) {
                $this->enviarNotificacao($usuario, $etapa, 0);
            }
        }

        /** @var FrigaEditalEtapa $etapa */
        foreach ($etapas1 as $etapa) {
            /** @var Usuario $usuario */
            foreach ($etapa->getIdEdital()->getBanca() as $usuario) {
                $this->enviarNotificacao($usuario, $etapa, 1);
            }
        }
    }

    /**
     * @throws \Exception
     */
    public function enviarNotificacao(Usuario $usuario, FrigaEditalEtapa $etapa, $tipo = 0)
    {
        $situacao = 'Aberta';
        $template = 'NteAplicacaoFrigaBundle:Notificacao:msg-etapa-aberta.html.twig';
        if (1 == $tipo) {
            $situacao = 'finalizando';
            $template = 'NteAplicacaoFrigaBundle:Notificacao:msg-etapa-fechamento.html.twig';
        }
        $str = [
            3 => [
                0 => "Etapa $situacao para Avaliação",
                1 => 'avaliação',
                2 => 'candidatos para serem avaliados',
            ],
            4 => [
                0 => "Etapa  $situacao para Publicação de Resultados",
                1 => 'resultados',
                2 => 'candidatos para serem classificados',
            ],
            5 => [
                0 => "Etapa  $situacao para Convocação",
                1 => 'convocação',
                2 => 'candidatos para serem convocados',
            ],
            7 => [
                0 => "Etapa  $situacao para Avaliação de Recursos",
                1 => 'avaliação de recursos',
                2 => 'recursos para serem avaliados',
            ],
        ];
        $str = $str[$etapa->getTipo()];
        $qtd = 0;
        if (7 == $etapa->getTipo()) {
            $qtd = $etapa->getIdEdital()
                ->getInscricaoRecurso($etapa->getIdEtapaCategoria())->count();
        } else {
            $qtd = $etapa->getIdEdital()
                ->getInscricaoValida($usuario, $etapa->getIdEtapaCategoria())->count();
        }
        try {
            $html = $this->template->render($template, [
                'usuario' => $usuario,
                'etapa' => $etapa,
                'str' => $str,
                'qtd' => $qtd,
            ]);

            $message = \Swift_Message::newInstance()
                ->setFrom('processoseletivo@cead.ufsm.br', 'Processo Seletivo')
                ->setSubject("{$str[0]}: {$etapa->getDescricao()}")
                ->setTo([$usuario->getEmail()])
                ->setBody($html, 'text/html');
            $this->mail->send($message);
        } catch (\Exception $exception) {
            dump($exception->getMessage());
        }
    }
}
