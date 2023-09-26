<?php

namespace App\Entity;

use App\Repository\AppParametersRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AppParametersRepository::class)
 */
class AppParameters
{

    const IMPORT_TRACK_LAST_EXECUTION = 'IMPORT_TRACK_LAST_EXECUTION';

    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=255)
     */
    private $paramKey;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $paramValue;

    public function __construct(string $key, string $value)
    {
        $this->setParamKey($key);
        $this->setParamValue($value);
    }


    public function getParamKey(): ?string
    {
        return $this->paramKey;
    }

    public function setParamKey(string $paramKey): self
    {
        $this->paramKey = $paramKey;

        return $this;
    }

    public function getParamValue(): ?string
    {
        return $this->paramValue;
    }

    public function setParamValue(string $paramValue): self
    {
        $this->paramValue = $paramValue;

        return $this;
    }
}
