<?php

namespace App\Document;

use OpenApi\Annotations as OA;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @OA\Schema(
 *     description="Scan description, array of Hosts",
 *     title="Scan"
 * )
 * @MongoDB\Document
 */
class Scan implements \JsonSerializable
{
    /**
     * @MongoDB\Id
     */
    private $id;

    /**
     * @MongoDB\EmbedMany(targetDocument="Host")
     */
    private $hosts;

    public function __construct()
    {
    }

    public function jsonSerialize()
    {
        $vars = get_object_vars($this);
        return $vars;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Scan
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHosts()
    {
        return $this->hosts;
    }

    /**
     * @param mixed $hosts
     * @return Scan
     */
    public function setHosts($hosts)
    {
        $this->hosts = $hosts;
        return $this;
    }
}
