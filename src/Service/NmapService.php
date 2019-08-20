<?php

namespace App\Service;

use Nmap\Nmap;

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

    public function __construct()
    {
    }

    /**
     * @param $ipRange
     * @param array $portsList
     * @return \Nmap\Host[]
     */
    public function discoverIpsSubnet($ipRange, $portsList = [])
    {
        $this->nmap = new Nmap();
        return $this->nmap->scan($ipRange, $portsList);
    }

    /**
     * @param $ipRange
     * @param array $portsList
     * @return \Nmap\Host[]
     */
    public function scanOpenPorts($ipRange, $portsList = [])
    {
        $this->nmap = new Nmap();
        return $this->nmap->scan($ipRange, $portsList);
    }

    /**
     * requires root
     * @param $ipRange
     * @param array $portsList
     * @return \Nmap\Host[]
     */
    public function identifyOs($ipRange, $portsList = [])
    {
        $this->nmap = new Nmap();
        return $this->nmap->enableOsDetection()
            ->scan($ipRange, $portsList);
    }

    /**
     * @param $ipRange
     * @param array $portsList
     * @return \Nmap\Host[]
     */
    public function identityHostnames($ipRange, $portsList = [])
    {
        $this->nmap = new Nmap();
        return $this->nmap->scan($ipRange, $portsList = []);
    }

    /**
     * requires root
     * @param $ipRange
     * @param array $portsList
     * @return \Nmap\Host[]
     */
    public function tcpSynUdpScan($ipRange, $portsList = [])
    {
        $this->nmap = new Nmap();
        return $this->nmap->scan($ipRange, $portsList = []);
    }

    /**
     * requires root
     * @param $ipRange
     * @param array $portsList
     * @return \Nmap\Host[]
     */
    public function tcpSynUdpAllPortsScan($ipRange, $portsList = [])
    {
        $this->nmap = new Nmap();
        return $this->nmap->scan($ipRange, $portsList = []);
    }

    /**
     * @param $ipRange
     * @param array $portsList
     * @return \Nmap\Host[]
     */
    public function tcpConnectScan($ipRange, $portsList = [])
    {
        $this->nmap = new Nmap();
        return $this->nmap->scan($ipRange, $portsList = []);
    }

    /**
     * @param $ipRange
     * @param array $portsList
     * @return \Nmap\Host[]
     */
    public function aggressiveScan($ipRange, $portsList = [])
    {
        $this->nmap = new Nmap();
        return $this->nmap->scan($ipRange, $portsList = []);
    }

    /**
     * @param $ipRange
     * @param array $portsList
     * @return \Nmap\Host[]
     */
    public function fastScan($ipRange, $portsList = [])
    {
        $this->nmap = new Nmap();
        return $this->nmap->scan($ipRange, $portsList = []);
    }

    /**
     * @param $ipRange
     * @param array $portsList
     * @return \Nmap\Host[]
     */
    public function verboseScan($ipRange, $portsList = [])
    {
        $this->nmap = new Nmap();
        return $this->nmap->enableVerbose()
            ->scan($ipRange, $portsList);
    }
}
