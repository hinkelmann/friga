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

namespace Nte\Admin\PainelBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEdital;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalEtapa;
use Nte\UsuarioBundle\Entity\Usuario;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $dados = [];
        $obj = new \stdClass();
        $obj->candidatos = 0;
        $obj->avaliados = 0;
        $obj->entrevistados = 0;
        $obj->recursos = 0;
        $em = $this->getDoctrine()->getManager();

        return $this->render('NteAdminPainelBundle:Default:index.html.twig', [
            'dados' => $dados,
            'graficoAlteracoes' => '',
            'avaliador' => $obj,
            'editais' => $this->editais(),
            'editaisselect' => $this->editais(true),
            'editalEtapa' => $this->etapa(),
        ]);
    }

    /**
     * Traaz todos os editais que usuário pode ter acesso;
     * Se administrador, traz todos os editais.
     *
     * @param int $situacao
     *
     * @return array|ArrayCollection|object[]
     */
    public function editais($situacao = false)
    {
        $em = $this->getDoctrine()->getManager();
        $editais = $em->getRepository(FrigaEdital::class)
            ->createQueryBuilder('e');
        if ($this->isGranted('ROLE_ADMIN')) {
            //            $editais->where('e.situacao in (0,1)');
        } elseif ($this->isGranted('ROLE_GERENCIAL') or $this->isGranted('ROLE_ADMIN_EDITAL')) {
            $editais
                ->innerJoin('e.idEditalUsuario', 'u')
  //              ->where('e.situacao in (1,2)')
                ->andWhere('u.idUsuario = :id')
                ->setParameter('id', $this->getUser()->getId());
        } else {
            $editais->where('e.situacao in (999999)');
        }
        if ($situacao) {
            $editais->andWhere('e.situacao != 0');
        }
        $editais->orderBy('e.dataPublicacaoOficial', 'desc');

        return $editais->getQuery()->getResult();
    }

    public function etapa()
    {
        if (!$this->isGranted('ROLE_AVALIADOR')) {
            return [];
        }
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $etapas = $em->createQueryBuilder()
            ->select('e')
            ->from(FrigaEditalEtapa::class, 'e')
            ->innerJoin('e.idEdital', 'ed')
            ->where('e.tipo in (3,4,5,7)')
            ->andWhere('ed.situacao >0')
            ->orderBy('e.dataFinal', 'ASC')
         //   ->andWhere(':dt0 > e.dataInicial and :dt0 < e.dataFinal')
        //    ->setParameter('dt0', new \DateTime())
        ;
        if ($this->isGranted('ROLE_ADMIN')) {
        } elseif (!$this->isGranted('ROLE_ADMIN')
            and ($this->isGranted('ROLE_AVALIADOR')
                and $this->isGranted('ROLE_AVALIADOR'))
        ) {
            $etapas->innerJoin('ed.idEditalUsuario', 'u')
                ->andWhere('u.idUsuario = :usuario')
                ->setParameter('usuario', $this->getUser()->getId());
        }

        $etapas = $etapas->getQuery()->getResult();

        $tmp = [];
        /** @var FrigaEditalEtapa $e */
        foreach ($etapas as $e) {
            if (\key_exists($e->getIdEdital()->getId(), $tmp)) {
                $tmp[$e->getIdEdital()->getId()]->etapa[] = $e;
            } else {
                $obj = new \stdClass();
                $obj->edital = $e->getIdEdital();
                $obj->etapa = [$e];
                $tmp[$e->getIdEdital()->getId()] = $obj;
            }
        }

        return $etapas;
    }

    public function enviarMensagemAction(Request $request)
    {
        if ($request->request->get('token')) {
            if ($this->isCsrfTokenValid('mensagem-para-todos', $request->request->get('token'))) {
                $i = 0;
                $lista = [];
                if ('SEM_REGRA' == $request->request->get('destinatario')) {
                    $pessoas = $this->getDoctrine()
                        ->getManager()
                        ->createQueryBuilder()
                        ->select('fp')
                        ->from(FrigaPessoa::class, 'fp')
                        ->where('fp.idSituacao <>  -99 or fp.idSituacao is null')
                        ->getQuery()->getResult();
                    foreach ($pessoas as $p) {
                        $this->enviarEmail($p->getContatoEmail(), $request->request->get('assunto'), $request->request->get('msg'));
                        ++$i;
                        $lista[$p->getNome()] = $p->getContatoEmail();
                    }
                } else {
                    $pessoas = $this->getDoctrine()
                        ->getManager()
                        ->createQueryBuilder()
                        ->select('u')
                        ->from(Usuario::class, 'u')
                        ->where('u.roles LIKE :role')
                        ->setParameter('role', '%' . $request->request->get('destinatario') . '%')
                        ->getQuery()->getResult();
                    foreach ($pessoas as $p) {
                        $this->enviarEmail($p->getEmail(), $request->request->get('assunto'), $request->request->get('msg'));
                        ++$i;
                        $lista[$p->getNome()] = $p->getEmail();
                    }
                }

                return $this->render('NteAdminPainelBundle:Default:mensagem.html.twig', [
                    'msgTotal' => $i,
                    'lista' => $lista,
                ]);
            }
        }

        return $this->render('NteAdminPainelBundle:Default:mensagem.html.twig', []);
    }

    public function enviarEmail($destinatario, $assunto, $msg)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject($assunto)
            ->setFrom('assistentepolo@cead.ufsm.br', 'Não Responder')
            ->setTo($destinatario)
            //->setBcc(['alexandre@nte.ufsm.br', 'luizguilherme@cead.ufsm.br'])
            // ->setBcc(['luizguilherme@cead.ufsm.br'])
            ->setBody($msg, 'text/html');
        $this->get('mailer')->send($message);
    }
}
