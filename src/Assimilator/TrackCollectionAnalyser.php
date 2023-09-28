<?php

namespace App\Assimilator;

use App\Entity\Track;
use Doctrine\Common\Collections\Collection;

class TrackCollectionAnalyser
{

    protected $years = array();
    protected $totalDuration = 0;

    public function __construct(Collection $tracks = null)
    {
        if ($tracks) $this->analyseTracks($tracks);
    }


    /**
     * @param Collection $tracks
     * @return $this
     */
    public function analyseTracks(Collection $tracks): self
    {
        foreach ($tracks as $track) {
            $this->addTrack($track);
        }
        return $this;
    }

    /**
     * @param Track $track
     * @return $this
     */
    public function addTrack(Track $track): self
    {
        $trackYear = $track->getYear();
        if (!empty($trackYear)) {
            $this->years[$track->getId()] = $trackYear;
        }
        $trackDuration = $track->getDuration();
        if (!empty($trackDuration)) {
            $this->totalDuration += $trackDuration;
        }

        return $this;
    }

    /**
     * @param Track $track
     * @return $this
     */
    public function removeTrack(Track $track): self
    {
        unset($this->years[$track->getId()]);

        $trackDuration = $track->getDuration();
        if (!empty($trackDuration)) {
            $this->totalDuration -= $trackDuration;
        }

        return $this;
    }

    /**
     * @return float
     */
    public function getTotalDuration(): float
    {
        return $this->totalDuration;
    }

    /**
     * @return int|null
     */
    public function getMinYear(): ?int
    {
        if (empty($this->years)) return null;
        return min(array_values($this->years));
    }

    /**
     * @return int|null
     */
    public function getMaxYear(): ?int
    {
        if (empty($this->years)) return null;
        return max(array_values($this->years));
    }

    /**
     * @return string
     */
    public function getYearExpression(): string
    {
        $min = $this->getMinYear();
        $max = $this->getMaxYear();
        if (empty($min) && empty($max)) return '';
        if ($min == $max) return ''.$max;
        return $min . '-' . $max;
    }

}
