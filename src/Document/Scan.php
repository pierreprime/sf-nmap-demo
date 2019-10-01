<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @OA\Schema(
 *     description="Scan description, array of Hosts",
 *     title="Scan"
 * )
 * @MongoDB\Document
 */
class Scan
{
    /**
     * @MongoDB\Id
     */
    private $id;
}
