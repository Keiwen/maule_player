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

}
