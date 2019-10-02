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
 * @MongoDB\EmbeddedDocument
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
     * @MongoDB\Field(type="string")
     */
    private $name;

    /**
     * @OA\Property(
     *     description="Hostname type",
     *     title="Type"
     * )
     * @MongoDB\Field(type="string")
     */
    private $type;

    public function __construct()
    {
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
}
