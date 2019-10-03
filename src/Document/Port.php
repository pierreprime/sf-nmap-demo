<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     description="Port description",
 *     title="Port"
 * )
 * @MongoDB\EmbeddedDocument
 */
class Port implements \JsonSerializable
{
    /**
     * @MongoDB\Id
     */
    private $id;

    /**
     * @OA\Property(
     *      description="Port number",
     *      title=""
     * )
     * @MongoDB\Field(type="integer")
     */
    private $number;

    /**
     * @MongoDB\Field(type="string")
     */
    private $protocol;

    /**
     * @MongoDB\Field(type="string")
     */
    private $state;

    /**
     * @MongoDB\EmbedOne(targetDocument="Service")
     */
    private $service;

    public function __construct($number, $protocol, $state, $service = [])
    {
        $this->number = $number;
        $this->protocol = $protocol;
        $this->state = $state;
        $this->service = $service;
    }

    public function jsonSerialize()
    {
        $vars = get_object_vars($this);
        return $vars;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getProtocol(): ?string
    {
        return $this->protocol;
    }

    public function setProtocol(?string $protocol): self
    {
        $this->protocol = $protocol;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): self
    {
        $this->service = $service;

        return $this;
    }
}
