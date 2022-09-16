<?php

namespace Nte\Aplicacao\FrigaBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;

class FrigaEditalRepository  extends EntityRepository
{
    /**
     * @param FrigaEdital $edital
     * @return mixed
     */
    public function getCategoriaPontuacaoInscricao(FrigaEdital $edital)
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('c')
            ->from(FrigaEditalPontuacaoCategoria::class,'c')
            ->innerJoin('c.pontuacao', 'p')
            ->innerJoin('p.idEtapa', 'e')
            ->where('e.tipo = 1')
            ->andWhere('c.idEdital = :edital')
            ->setParameter('edital',$edital)
            ->orderBy('c.ordem','asc')
            ->addOrderBy('p.ordem','asc')
            ->getQuery()->getResult();
    }

    /**
     * @param  FrigaEditalEtapa $etapa
     * @return mixed
     */
    public function getCategoriaPontuacaoEtapa(FrigaEditalEtapa $etapa )
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('c')
            ->from(FrigaEditalPontuacaoCategoria::class,'c')
            ->innerJoin('c.pontuacao', 'p')
            ->innerJoin('p.idEtapa', 'e')
            ->where('e = :etapa')
            ->andWhere('c.idEdital = :edital')
            ->setParameter('edital', $etapa->getIdEdital())
            ->setParameter('etapa', $etapa)
            ->orderBy('c.ordem','asc')
            ->addOrderBy('p.ordem','asc')
            ->getQuery()->getResult();
    }


}