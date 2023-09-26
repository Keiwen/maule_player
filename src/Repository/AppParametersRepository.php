<?php

namespace App\Repository;

use App\Entity\AppParameters;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AppParameters>
 *
 * @method AppParameters|null findOneBy(array $criteria, array $orderBy = null)
 * @method AppParameters[]    findAll()
 * @method AppParameters[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AppParametersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AppParameters::class);
    }

    public function add(AppParameters $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AppParameters $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function find($id, $lockMode = null, $lockVersion = null)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.paramKey = :key')
            ->setParameter('key', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
