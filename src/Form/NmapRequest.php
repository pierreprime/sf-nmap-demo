<?php

namespace App\Form;

use Symfony\Component\Validator\Constraints as Assert;

class NmapRequest
{
    // list here what we need for showcase

    /**
     * -O option
     */
    public $osDetection;

    /**
     * -sV option
     */
    public $serviceInfo;

    /**
     * -v option
     */
    public $verbose;

    /**
     * -sn option
     */
    public $disPortScan;

    /**
     * -n option
     */
    public $disReverseDNS;

    /**
     * -Pn
     */
    public $hostsAsOnline;

    /**
     * @Assert\Ip()
     */
    public $fromIp;

    /**
     * @Assert\Ip()
     */
    public $toIp;

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
     * @Assert\Url(
     *  message="The url '{{value}}' is not a valid URL"
     * )
     */
    public $hostname;

    /**
     * in seconds
     * @Assert\Type(
     *  type="integer",
     *  message="Timeout must be an integer (in seconds)"
     * )
     */
    public $timeout;

    /**
     * -sP
     */
    public $onlyCheckOnline;

    /**
     * -sL
     */
    public $listScan;

    /**
     * -sS
     */
    public $tcpSynScan;

    /**
     * -sU
     */
    public $udpScan;

    /**
     * -sT
     */
    public $tcpConnectScan;

    /**
     * -T[0-5] : lower is slower and stealthier
     */
    public $stealthLevel;

    /**
     * -A
     */
    public $quickEnableOsVersions;

    /**
     * -F
     */
    public $fastScan;

    /**
     *
     */
    public $scannedAt;

    public function setCreatedAt()
    {
        // auto fill
    }
}

