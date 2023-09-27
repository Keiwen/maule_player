<?php

namespace App\Entity;

use App\Repository\AlbumRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Keiwen\Cacofony\EntitiesManagement\ExportableEntityTrait;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=AlbumRepository::class)
 */
class Album
{
    use ExportableEntityTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"album"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"album"})
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Track::class, mappedBy="album")
     * @Groups({"albumAndTracks"})
     */
    private $tracks;

    /**
     * @ORM\Column(type="datetime")
     */
    private $importDate;

    public function __construct(string $name = '')
    {
        $this->setName($name);
        $this->tracks = new ArrayCollection();
        $this->setImportDate(new \DateTime());
    }

    public static function retrieveExportFields()
    {
        return ['id', 'name', 'tracksCount'];
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Track>
     */
    public function getTracks(): Collection
    {
        return $this->tracks;
    }

    public function addTrack(Track $track): self
    {
        if (!$this->tracks->contains($track)) {
            $this->tracks[] = $track;
            $track->setAlbum($this);
        }

        return $this;
    }

    public function removeTrack(Track $track): self
    {
        if ($this->tracks->removeElement($track)) {
            // set the owning side to null (unless already changed)
            if ($track->getAlbum() === $this) {
                $track->setAlbum(null);
            }
        }

        return $this;
    }

    public function getTracksCount(): int
    {
        return $this->tracks->count();
    }

    public function getImportDate(): ?\DateTimeInterface
    {
        return $this->importDate;
    }

    public function setImportDate(\DateTimeInterface $importDate): self
    {
        $this->importDate = $importDate;

        return $this;
    }
}
