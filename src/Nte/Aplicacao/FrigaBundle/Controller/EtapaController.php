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

namespace Nte\Aplicacao\FrigaBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEdital;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalEtapa;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class EtapaController extends Controller
{
    public function indexAction(Request $request, $situacao = 0)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $ordem = $request->query->get('o') ?? 0;
        $edital = $request->query->get('edital') ?? 0;
        $tipo = $request->query->get('tipo') ?? 0;
        $dt0 = new \DateTime();
        $editais = $em->createQueryBuilder()
            ->select('e.id')
            ->addSelect('e.titulo')
            ->from(FrigaEdital::class, 'e')
            ->where('e.situacao > 0')
            ->orderBy('e.id', 'DESC');

        $etapas = $em->createQueryBuilder()
            ->select('e')
            ->from(FrigaEditalEtapa::class, 'e')
            ->innerJoin('e.idEdital', 'ed')
            ->where('e.tipo in (3,4,5,7)')
            ->andWhere('ed.situacao >0')
        ;
        if ($edital) {
            $etapas->andWhere('ed.id = :edital')
                ->setParameter('edital', $edital)
            ;
        }
        if ($tipo) {
            $etapas->andWhere('e.tipo = :tipo')
                ->setParameter('tipo', $tipo);
        }
        if ($ordem) {
            $etapas->orderBy('e.dataFinal', 1 == $ordem ? 'ASC' : 'DESC');
        } else {
            $etapas->orderBy('e.dataFinal', 'DESC');
        }

        if ($situacao > 0) {
            switch ($situacao) {
                case 1:
                    $etapas->andWhere(':dt0 > e.dataInicial and :dt0 < e.dataFinal');
                    break;
                case 2:
                    $etapas->andWhere('e.dataInicial > :dt0');
                    break;
                case 3:
                    $etapas->andWhere('e.dataFinal < :dt0');
                    break;
            }
            $etapas->setParameter('dt0', $dt0);
        }

        if (!$this->isGranted('ROLE_ADMIN')
            and ($this->isGranted('ROLE_AVALIADOR')
            and $this->isGranted('ROLE_AVALIADOR'))
        ) {
            $editais->innerJoin('e.idEditalUsuario', 'u')
                ->andWhere('u.idUsuario = :usuario')
                ->setParameter('usuario', $this->getUser()->getId())
            ;
            $etapas->innerJoin('ed.idEditalUsuario', 'u')
                ->andWhere('u.idUsuario = :usuario')
                ->setParameter('usuario', $this->getUser()->getId())
            ;
        }

        $etapas = new ArrayCollection($etapas->getQuery()->getResult());
        $editais = new ArrayCollection($editais->getQuery()->getResult());

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

        return $this->render('NteAplicacaoFrigaBundle:Etapa:index.html.twig', [
            'etapas' => $etapas,
            'editais' => $editais,
            'ordem' => $ordem,
            'edital' => $edital,
            'tipo' => $tipo,
            'editalEtapa' => $tmp,
        ]);
    }
}
