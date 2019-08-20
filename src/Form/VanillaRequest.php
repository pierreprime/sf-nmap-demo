<?php

namespace App\Form;

use Symfony\Component\Validator\Constraints as Assert;

class VanillaRequest
{
    /**
     * @Assert\Ip()
     */
    public $fromIp;

    /**
     * @Assert\Type(
     *  type="integer",
     *  message="First port must be an integer"
     * )
     * @Assert\Range(
     *  min=1,
     *  max=65535,
     *  minMessage="No port below 1",
     *  maxMessage="No port beyond 65535"
     * )
     */
    public $fromPort;

    /**
     * @Assert\Type(
     *  type="integer",
     *  message="Second port must be an integer"
     * )
     * @Assert\Range(
     *  min=1,
     *  max=65535,
     *  minMessage="No port below 1",
     *  maxMessage="No port beyond 65535"
     * )
     */
    public $toPort;

    /**
     * @return mixed
     */
    public function getFromIp()
    {
        return $this->fromIp;
    }

    /**
     * @param mixed $fromIp
     * @return VanillaRequest
     */
    public function setFromIp($fromIp)
    {
        $this->fromIp = $fromIp;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFromPort()
    {
        return $this->fromPort;
    }

    /**
     * @param mixed $fromPort
     * @return VanillaRequest
     */
    public function setFromPort($fromPort)
    {
        $this->fromPort = $fromPort;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getToPort()
    {
        return $this->toPort;
    }

    /**
     * @param mixed $toPort
     * @return VanillaRequest
     */
    public function setToPort($toPort)
    {
        $this->toPort = $toPort;
        return $this;
    }
}

