<?php

namespace App\Repository;

use App\Entity\Album;
use App\Entity\Artist;
use App\Entity\Track;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Album>
 *
 * @method Album|null find($id, $lockMode = null, $lockVersion = null)
 * @method Album|null findOneBy(array $criteria, array $orderBy = null)
 * @method Album[]    findAll()
 * @method Album[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AlbumRepository extends ServiceEntityRepository
{
    const ORDER_RECENT = '';
    const ORDER_NAME = 'name';
    const ORDER_OLDEST = 'oldest';
    const ORDER_IMPORTDATE = 'importDate';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Album::class);
    }

    public function add(Album $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Album $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    /**
     * @param string $search
     * @return Album[]
     */
    public function searchForName(string $search): array
    {
        return $this->createQueryBuilder('al')
            ->where('al.name LIKE ?', '%' . $search . '%')
            ->orderBy('name', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param string[] $searchedNames
     * @return Album[]
     */
    public function findNames(array $searchedNames): array
    {
        return $this->createQueryBuilder('al')
            ->where('al.name in (:names)')
            ->setParameter('names', $searchedNames)
            ->getQuery()
            ->getResult()
            ;
    }


    /**
     * @param array $names
     * @return Album[] name => entity|null
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


    /**
     * @param Artist $artist
     * @param string $order use class constants
     * @param int $limit
     * @param int $offset
     * @return Album[]
     */
    public function searchByArtist(Artist $artist, string $order = self::ORDER_RECENT, int $limit = 0, int $offset = 0): array
    {
        $qb = $this->createQueryBuilder('al')
            ->from(Track::class, 't')
            ->where('t.artist = :artist')
            ->setParameter('artist', $artist)
            ->groupBy('al.id')
            ;

        switch ($order) {
            case self::ORDER_NAME:
                $qb->addOrderBy('al.name', 'DESC');
                break;
            case self::ORDER_OLDEST:
                $qb->addOrderBy('t.year', 'ASC');
                break;
            case self::ORDER_IMPORTDATE:
                $qb->addOrderBy('al.importDate', 'DESC');
                break;
            default:
                $qb->addOrderBy('t.year', 'DESC');
        }

        if (!empty($limit)) {
            $qb->setMaxResults($limit);
        }
        if (!empty($offset)) {
            $qb->setFirstResult($offset);
        }
        return $qb->getQuery()
            ->getResult();

    }

}
