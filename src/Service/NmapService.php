<?php

namespace App\Service;

use Nmap\Nmap;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class NmapService
 * @package App\Service
 *
 * Move pre-made methods of this class in unit tests
 */
class NmapService
{
    /**
     * @var Nmap
     */
    private $nmap;

    /**
     * NmapService constructor.
     * @param Nmap $nmap
     */
    public function __construct(Nmap $nmap)
    {
        $this->nmap = $nmap;
    }

    /**
     * @param $ipRange
     * @param array $portsList
     * @return \Nmap\Host[]
     */
    public function discoverIpsSubnet($ipRange, $portsList = [])
    {
        /*
         * -sP option, -sn for more recent releases of nmap (actual here)
         */
        return $this->nmap
            ->disablePortScan()
            ->scan($ipRange);
    }

    /**
     * @param $ipRange
     * @param array $portsList
     * @return \Nmap\Host[]
     */
    public function scanOpenPorts($ipRange, $portsList = [])
    {
        /**
         * No flags at all, default behaviour
         */
        return $this->nmap->scan($ipRange, $portsList);
    }

    /**
     * @param $ipRange
     * @param array $portsList
     * @return \Nmap\Host[]
     */
    public function verboseScan($ipRange, $portsList = [])
    {
        /*
         * -v flag
         */
        return $this->nmap->enableVerbose()
            ->scan($ipRange, $portsList);
    }

    /**
     * requires root
     * @param $ipRange
     * @param array $portsList
     * @return \Nmap\Host[]
     */
    public function identifyOs($ipRange, $portsList = [])
    {
        /*
         * -O flag
         */
        return $this->nmap->enableOsDetection()
            ->scan($ipRange, $portsList);
    }

    /**
     * no root required
     * @param $ipRange
     * @param array $portsList
     * @return
     */
    public function identifyHostnames($ipRange, $portsList = []){
        /*
         * -sL flag
         * not existing yet in library
         */
        return $this->nmap->scan($ipRange, $portsList);
    }

    public function synAndUdpScan($ipRange, $portsList = []){
        /*
         * -sS, -sU and -PN flags
         * option not existing yet in library
         */
        return $this->nmap->scan($ipRange, $portsList);
    }

    public function tcpConnectScan($ipRange, $portsList = []){
        /*
         * -sT flag
         * option not existing yet in library
         */
        return $this->nmap->scan($ipRange, $portsList);
    }

    public function aggressiveScan($ipRange, $portsList = []){
        /*
         * -T4 and -A flags
         */
        return $this->nmap->scan($ipRange, $portsList);
    }
}
