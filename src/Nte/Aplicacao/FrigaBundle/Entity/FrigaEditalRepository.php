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

namespace Nte\Aplicacao\FrigaBundle\Entity;

use Doctrine\ORM\EntityRepository;

class FrigaEditalRepository  extends EntityRepository
{
    /**
     * @return mixed
     */
    public function getCategoriaPontuacaoInscricao(FrigaEdital $edital)
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('c')
            ->from(FrigaEditalPontuacaoCategoria::class, 'c')
            ->innerJoin('c.pontuacao', 'p')
            ->innerJoin('p.idEtapa', 'e')
            ->where('e.tipo = 1')
            ->andWhere('c.idEdital = :edital')
            ->setParameter('edital', $edital)
            ->orderBy('c.ordem', 'asc')
            ->addOrderBy('p.ordem', 'asc')
            ->getQuery()->getResult();
    }

    /**
     * @return mixed
     */
    public function getCategoriaPontuacaoEtapa(FrigaEditalEtapa $etapa)
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('c')
            ->from(FrigaEditalPontuacaoCategoria::class, 'c')
            ->innerJoin('c.pontuacao', 'p')
            ->innerJoin('p.idEtapa', 'e')
            ->where('e = :etapa')
            ->andWhere('c.idEdital = :edital')
            ->setParameter('edital', $etapa->getIdEdital())
            ->setParameter('etapa', $etapa)
            ->orderBy('c.ordem', 'asc')
            ->addOrderBy('p.ordem', 'asc')
            ->getQuery()->getResult();
    }
}
