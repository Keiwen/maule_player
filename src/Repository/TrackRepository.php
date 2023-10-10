<?php

namespace App\Repository;

use App\Entity\Album;
use App\Entity\Artist;
use App\Entity\Track;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Track>
 *
 * @method Track|null find($id, $lockMode = null, $lockVersion = null)
 * @method Track|null findOneBy(array $criteria, array $orderBy = null)
 * @method Track[]    findAll()
 * @method Track[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrackRepository extends ServiceEntityRepository
{

    const ORDER_RECENT = '';
    const ORDER_NAME = 'name';
    const ORDER_OLDEST = 'oldest';
    const ORDER_IMPORTDATE = 'importDate';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Track::class);
    }

    /**
     * @param $id
     * @return Track|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findFullTrack($id)
    {
        return $this->createQueryBuilder('t')
            ->addSelect('a', 'al')
            ->innerJoin('t.artist', 'a')
            ->innerJoin('t.album', 'al')
            ->andWhere('t.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    /**
     * @param int $limit
     * @return Track[]
     */
    public function findAllFullTrack(int $limit = 0)
    {
        return $this->findFullTracksBy(array(), self::ORDER_NAME, $limit);
    }

    public function add(Track $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Track $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return array
     */
    public function getBasicOrderBy(): array
    {
        return array(
            'year' => 'DESC',
            'trackNumber' => 'ASC',
        );
    }

    /**
     * @param array $criteria field => value. Prefix field with '%' to switch 'where field like %value%'
     * @param string $order use class constants
     * @param int $limit
     * @param int $offset
     * @return Track[]
     */
    public function findFullTracksBy(array $criteria, string $order = self::ORDER_RECENT, int $limit = 0, int $offset = 0): array
    {
        $orderBy = $this->getBasicOrderBy();
        switch ($order) {
            case self::ORDER_NAME:
                $orderBy = ['name' => 'ASC'];
                break;
            case self::ORDER_OLDEST:
                $orderBy['year'] = 'ASC';
                break;
            case self::ORDER_IMPORTDATE:
                $orderBy = ['importDate' => 'DESC'];
                break;
        }

        $qb = $this->createQueryBuilder('t')
            ->addSelect('a', 'al')
            ->innerJoin('t.artist', 'a')
            ->innerJoin('t.album', 'al')
        ;
        foreach ($criteria as $field => $value) {
            if (strpos($field, '%') !== false) {
                $field = str_replace('%', '', $field);
                $qb->andWhere('t.'.$field.' LIKE :value_'.$field);
                $qb->setParameter('value_'.$field, '%'.$value.'%');

            } else {
                $qb->andWhere('t.'.$field.' = :value_'.$field);
                $qb->setParameter(':value_'.$field, $value);
            }
        }
        foreach ($orderBy as $field => $order) {
            $qb->addOrderBy('t.'.$field, $order);
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


    /**
     * @param Artist $artist
     * @param string $order use class constants
     * @param int $limit
     * @param int $offset
     * @return Track[]
     */
    public function findByArtist(Artist $artist, string $order = self::ORDER_RECENT, int $limit = 0, int $offset = 0): array
    {
        return $this->findFullTracksBy(array('artist' => $artist), $order, $limit, $offset);
    }


    /**
     * @param Album $album
     * @param string $order use class constants
     * @param int $limit
     * @param int $offset
     * @return Track[]
     */
    public function findByAlbum(Album $album, string $order = self::ORDER_RECENT, int $limit = 0, int $offset = 0): array
    {
        return $this->findFullTracksBy(array('album' => $album), $order, $limit, $offset);
    }

    /**
     * @param int|null $year
     * @param string $order use class constants
     * @param int $limit
     * @param int $offset
     * @return Track[]
     */
    public function findByYear(?int $year, string $order = self::ORDER_RECENT, int $limit = 0, int $offset = 0): array
    {
        return $this->findFullTracksBy(array('year' => $year), $order, $limit, $offset);
    }

    /**
     * @param string $search
     * @param string $order use class constants
     * @param int $limit
     * @param int $offset
     * @return Track[]
     */
    public function searchForName(string $search, string $order = self::ORDER_RECENT, int $limit = 0, int $offset = 0): array
    {
        return $this->findFullTracksBy(array('%name' => $search), $order, $limit, $offset);
    }

    /**
     * @param string[] $searchedNames
     * @return Track[]
     */
    public function findNames(array $searchedNames): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.name in (:names)')
            ->setParameter('names', $searchedNames)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param string[] $searchedPaths
     * @return Track[]
     */
    public function findPaths(array $searchedPaths): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.filepath in (:paths)')
            ->setParameter('paths', $searchedPaths)
            ->getQuery()
            ->getResult()
            ;
    }


    /**
     * @param array $names
     * @param bool $searchByPath set to true if using names of paths instead of track title
     * @return Track[] name => entity|null
     */
    public function loadPersistedEntities(array $names, bool $searchByPath = false): array
    {
        $names = array_unique($names);
        $allNames = array_combine($names, array_fill(0, count($names), null));
        // allNames key is entity name, value is null. Now fill with entities found
        if ($searchByPath) {
            $entities = $this->findPaths($names);
        } else {
            $entities = $this->findNames($names);
        }
        foreach ($entities as $entity) {
            if ($searchByPath) {
                $allNames[$entity->getFilepath()] = $entity;
            } else {
                $allNames[$entity->getName()] = $entity;
            }
        }
        return $allNames;
    }



}
