<?php

namespace App\Repository;

use App\Entity\Artist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Artist>
 *
 * @method Artist|null find($id, $lockMode = null, $lockVersion = null)
 * @method Artist|null findOneBy(array $criteria, array $orderBy = null)
 * @method Artist[]    findAll()
 * @method Artist[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArtistRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Artist::class);
    }

    public function add(Artist $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Artist $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    /**
     * @param string $search
     * @return Artist[]
     */
    public function searchForName(string $search): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.name LIKE :search')
            ->setParameter('search', '%'.$search.'%')
            ->orderBy('a.name', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param string[] $searchedNames
     * @return Artist[]
     */
    public function findNames(array $searchedNames): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.name in (:names)')
            ->setParameter('names', $searchedNames)
            ->getQuery()
            ->getResult()
            ;
    }


    /**
     * @param array $names
     * @return Artist[] name => entity|null
     */
    public function loadPersistedEntities(array $names): array
    {
        $names = array_unique($names);
        $allNames = array_combine($names, array_fill(0, count($names), null));
        // allNames key is entity name, value is null. Now fill with entities found
        $entities = $this->findNames($names);
        foreach ($entities as $entity) {
            $allNames[$entity->getName()] = $entity;
        }
        return $allNames;
    }



//    /**
//     * @return Artist[] Returns an array of Artist objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Artist
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
