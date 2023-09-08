<?php

namespace App\Entity;

use App\Repository\TrackRepository;
use Doctrine\ORM\Mapping as ORM;
use Keiwen\Cacofony\EntitiesManagement\ExportableEntityTrait;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=TrackRepository::class)
 */
class Track
{

    use ExportableEntityTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"track"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"track"})
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=Artist::class, inversedBy="tracks")
     * @Groups({"trackAndArtist"})
     */
    private $artist;

    /**
     * @ORM\ManyToOne(targetEntity=Album::class, inversedBy="tracks")
     * @Groups({"trackAndAlbum"})
     */
    private $album;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"track"})
     */
    private $year;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"track"})
     */
    private $trackNumber;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"track"})
     */
    private $filepath;

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

    public function getArtist(): ?Artist
    {
        return $this->artist;
    }

    public function setArtist(?Artist $artist): self
    {
        $this->artist = $artist;

        return $this;
    }

    public function getAlbum(): ?Album
    {
        return $this->album;
    }

    public function setAlbum(?Album $album): self
    {
        $this->album = $album;

        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(?int $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getTrackNumber(): ?int
    {
        return $this->trackNumber;
    }

    public function setTrackNumber(?int $trackNumber): self
    {
        $this->trackNumber = $trackNumber;

        return $this;
    }

    public function getFilepath(): ?string
    {
        return $this->filepath;
    }

    public function setFilepath(string $filepath): self
    {
        $this->filepath = $filepath;

        return $this;
    }

    /**
     * @return string|null folder containing file
     */
    public function getFolder(): ?string
    {
        $path = $this->getFilepath();
        if (empty($path)) return null;
        $pathParts = explode('/', $path);
        array_pop($pathParts);
        return '/' . implode('/', $pathParts);
    }

    /**
     * @return string|null filename, without path, including extension
     */
    public function getFilename(): ?string
    {
        $path = $this->getFilepath();
        if (empty($path)) return null;
        $pathParts = explode('/', $path);
        return array_pop($pathParts);
    }

    /**
     * @return string|null extension of file
     */
    public function getExtension(): ?string
    {
        $filename = $this->getFilename();
        if (empty($filename)) return null;
        $filenameParts = explode('.', $filename);
        return array_pop($filenameParts);
    }

}
