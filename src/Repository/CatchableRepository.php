<?php

/*
 * This file is part of the ForciCatchableBundle package.
 *
 * Copyright (c) Forci Web Consulting Ltd.
 *
 * Author Tatyana Mincheva <tatjana@forci.com>
 * Author Martin Kirilov <martin@forci.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Forci\Bundle\Catchable\Repository;

use Doctrine\ORM\EntityRepository;
use Forci\Bundle\Catchable\Entity\Catchable;
use Forci\Bundle\Catchable\Filter\CatchableFilter;

class CatchableRepository extends EntityRepository {

    public function findOneById(int $id): ?Catchable {
        $builder = $this->createQueryBuilder('c')
            ->addSelect('c')
            ->andWhere('c.id = :id')
            ->setParameter('id', $id);
        $query = $builder->getQuery();

        return $query->getOneOrNullResult();
    }

    /**
     * @param string $class
     *
     * @return Catchable[]
     */
    public function findByClass(string $class) {
        $builder = $this->createQueryBuilder('c')
            ->addSelect('c')
            ->andWhere('c.class = :class')
            ->setParameter('class', $class);

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function filter(CatchableFilter $filter): array {
        $subBuilder = $this->createQueryBuilder('sc');
        $builder = $this->createQueryBuilder('c');

        $sub = $subBuilder->select('p.id')
            ->join('sc.previous', 'p')
            ->where('sc.previous IS NOT NULL');

        $builder = $builder->select('c')
            ->where($builder->expr()->notIn('c.id', $sub->getDQL()));

        if ($file = $filter->getFile()) {
            $builder->andWhere('c.file LIKE :file')
                ->setParameter('file', '%'.$file.'%');
        }
        if ($class = $filter->getClass()) {
            $builder->andWhere('c.class LIKE :class')
                ->setParameter('class', '%'.$class.'%');
        }
        if ($message = $filter->getMessage()) {
            $builder->andWhere('c.message LIKE :message')
                ->setParameter('message', '%'.$message.'%');
        }

        $limit = $filter->getLimit();
        $page = $filter->getPage();

        $builder->setMaxResults($limit)
            ->setFirstResult($limit * ($page - 1))
            ->orderBy('c.createdAt', 'DESC')
            ->addOrderBy('c.id', 'DESC');

        $query = $builder->getQuery();

        return $query->getResult();
    }

    /**
     * @param CatchableFilter $filter
     *
     * @return int
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countForFilter(CatchableFilter $filter): int {
        $subBuilder = $this->createQueryBuilder('sc');
        $builder = $this->createQueryBuilder('c');

        $sub = $subBuilder->select('p.id')
            ->join('sc.previous', 'p')
            ->where('sc.previous IS NOT NULL');

        $builder = $builder->select('COUNT(c.id)')
            ->where($builder->expr()->notIn('c.id', $sub->getDQL()));

        if ($file = $filter->getFile()) {
            $builder->andWhere('c.file LIKE :file')
                ->setParameter('file', '%'.$file.'%');
        }
        if ($class = $filter->getClass()) {
            $builder->andWhere('c.class LIKE :class')
                ->setParameter('class', '%'.$class.'%');
        }
        if ($message = $filter->getMessage()) {
            $builder->andWhere('c.message LIKE :message')
                ->setParameter('message', '%'.$message.'%');
        }

        $query = $builder->getQuery();

        return intval($query->getSingleScalarResult());
    }

    /**
     * @param Catchable $current
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(Catchable $current) {
        $em = $this->getEntityManager();

        $em->persist($current);

        while ($previous = $current->getPrevious()) {
            $previous->setNext($current);
            $em->persist($previous);
            $current = $previous;
        }

        $em->flush();
    }

    /**
     * @param int $id
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function removeById(int $id) {
        $em = $this->getEntityManager();

        $dql = sprintf('DELETE FROM %s c WHERE c.id = :id', Catchable::class);

        $query = $em->createQuery($dql);

        $query->execute([
            'id' => $id
        ]);
    }

    public function removeByClass(string $class) {
        $em = $this->getEntityManager();

        $dql = sprintf('DELETE FROM %s c WHERE c.class = :class', Catchable::class);

        $query = $em->createQuery($dql);

        $query->execute([
            'class' => $class
        ]);
    }

    public function removeAll() {
        $this->getEntityManager()->createQueryBuilder()->delete(Catchable::class, 'c')->getQuery()->execute();
    }
}
