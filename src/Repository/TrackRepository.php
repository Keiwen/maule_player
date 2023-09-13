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
     * @return Track[]
     */
    public function findAllFullTrack()
    {
        $qb = $this->createQueryBuilder('t')
            ->addSelect('a', 'al')
            ->innerJoin('t.artist', 'a')
            ->innerJoin('t.album', 'al')
            ;
        foreach ($this->getBasicOrderBy() as $field => $order) {
            $qb->addOrderBy($field, $order);
        }
        return $qb->getQuery()
            ->getResult();

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
            'year' => 'ASC',
            'trackNumber' => 'ASC',
        );
    }

    /**
     * @param Artist $artist
     * @return Track[]
     */
    public function findByArtist(Artist $artist): array
    {
        return $this->findBy(['artist' => $artist], $this->getBasicOrderBy());
    }


    /**
     * @param Album $album
     * @return Track[]
     */
    public function findByAlbum(Album $album): array
    {
        return $this->findBy(['album' => $album], $this->getBasicOrderBy());
    }

    /**
     * @param int|null $year
     * @return Track[]
     */
    public function findByYear(?int $year): array
    {
        return $this->findBy(['year' => $year], $this->getBasicOrderBy());
    }

    /**
     * @param string $search
     * @return Track[]
     */
    public function searchForName(string $search): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.name LIKE ?', '%' . $search . '%')
            ->orderBy('trackNumber', 'ASC')
            ->orderBy('year', 'ASC')
            ->getQuery()
            ->getResult()
        ;
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
