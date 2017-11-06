<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * ProductRepository
 */
class ProductRepository extends EntityRepository
{
    /**
     * @param $text
     * @param $offset
     * @param $limit
     * @return Paginator
     */
    public function findByText($text, $offset, $limit)
    {
        $qb = $this->createQueryBuilder('p')
            ->select('p')
            ->innerJoin('p.category', 'c')
            ->where('p.active = :active')
            ->andWhere('c.active = :active')
            ->orderBy('p.price', 'asc')
            ->setParameter('active', true)
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        $words = $text !== '' ? preg_split('/\s+/isu', $text) : array();
        foreach ($words as $wordIndex => $word) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('lower(p.title)', 'lower(:word' . $wordIndex . ')'),
                    $qb->expr()->like('lower(p.description)', 'lower(:word' . $wordIndex . ')')
                )
            );
            $qb->setParameter('word' . $wordIndex, '%' . $word . '%');
        }

        return new Paginator($qb, false);
    }
}