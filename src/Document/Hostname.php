<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     description="Hostname description",
 *     title="Hostname"
 * )
 * @MongoDB\Document
 */
class Hostname
{
    /**
     * @MongoDB\Id
     */
    private $id;

    /**
     * @OA\Property(
     *     description="Hostname value",
     *     title="Hostname"
     * )
     */
    private $name;

    /**
     * @OA\Property(
     *     description="Hostname type",
     *     title="Type"
     * )
     */
    private $type;

    private $hosts;

    public function __construct()
    {
        $this->hosts = new ArrayCollection();
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection|Host[]
     */
    public function getHosts(): Collection
    {
        return $this->hosts;
    }

    public function addHost(Host $host): self
    {
        if (!$this->hosts->contains($host)) {
            $this->hosts[] = $host;
            $host->addHostname($this);
        }

        return $this;
    }

    public function removeHost(Host $host): self
    {
        if ($this->hosts->contains($host)) {
            $this->hosts->removeElement($host);
            $host->removeHostname($this);
        }

        return $this;
    }
}
