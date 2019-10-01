<?php

namespace App\Form;

use Symfony\Component\Validator\Constraints as Assert;

class NmapRequest
{
    // list here what we need for showcase

    /**
     * -O option
     */
    protected $osDetection;

    /**
     * -sV option
     */
    protected $serviceInfo;

    /**
     * -v option
     */
    protected $verbose;

    /**
     * -sn option
     */
    protected $disPortScan;

    /**
     * -n option
     */
    protected $disReverseDNS;

    /**
     * -Pn
     */
    protected $hostsAsOnline;

    /**
     * @Assert\Ip()
     */
    protected $fromIp;

    /**
     * @Assert\Ip()
     */
    protected $toIp;

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
    protected $fromPort;

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
    protected $toPort;

    /**
     * @Assert\Url(
     *  message="The url '{{value}}' is not a valid URL"
     * )
     */
    protected $hostname;

    /**
     * in seconds
     * @Assert\Type(
     *  type="integer",
     *  message="Timeout must be an integer (in seconds)"
     * )
     */
    protected $timeout;

    /**
     * -sP
     */
    protected $onlyCheckOnline;

    /**
     * -sL
     */
    protected $listScan;

    /**
     * -sS
     */
    protected $tcpSynScan;

    /**
     * -sU
     */
    protected $udpScan;

    /**
     * -sT
     */
    protected $tcpConnectScan;

    /**
     * -T[0-5] : lower is slower and stealthier
     */
    protected $stealthLevel;

    /**
     * -A
     */
    protected $quickEnableOsVersions;

    /**
     * -F
     */
    protected $fastScan;

    /**
     *
     */
    protected $scannedAt;

    public function setCreatedAt()
    {
        // auto fill
    }

    /**
     * @return mixed
     */
    public function getOsDetection()
    {
        return $this->osDetection;
    }

    /**
     * @param mixed $osDetection
     * @return NmapRequest
     */
    public function setOsDetection($osDetection)
    {
        $this->osDetection = $osDetection;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getServiceInfo()
    {
        return $this->serviceInfo;
    }

    /**
     * @param mixed $serviceInfo
     * @return NmapRequest
     */
    public function setServiceInfo($serviceInfo)
    {
        $this->serviceInfo = $serviceInfo;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getVerbose()
    {
        return $this->verbose;
    }

    /**
     * @param mixed $verbose
     * @return NmapRequest
     */
    public function setVerbose($verbose)
    {
        $this->verbose = $verbose;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDisPortScan()
    {
        return $this->disPortScan;
    }

    /**
     * @param mixed $disPortScan
     * @return NmapRequest
     */
    public function setDisPortScan($disPortScan)
    {
        $this->disPortScan = $disPortScan;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDisReverseDNS()
    {
        return $this->disReverseDNS;
    }

    /**
     * @param mixed $disReverseDNS
     * @return NmapRequest
     */
    public function setDisReverseDNS($disReverseDNS)
    {
        $this->disReverseDNS = $disReverseDNS;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHostsAsOnline()
    {
        return $this->hostsAsOnline;
    }

    /**
     * @param mixed $hostsAsOnline
     * @return NmapRequest
     */
    public function setHostsAsOnline($hostsAsOnline)
    {
        $this->hostsAsOnline = $hostsAsOnline;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFromIp()
    {
        return $this->fromIp;
    }

    /**
     * @param mixed $fromIp
     * @return NmapRequest
     */
    public function setFromIp($fromIp)
    {
        $this->fromIp = $fromIp;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getToIp()
    {
        return $this->toIp;
    }

    /**
     * @param mixed $toIp
     * @return NmapRequest
     */
    public function setToIp($toIp)
    {
        $this->toIp = $toIp;
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
     * @return NmapRequest
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
     * @return NmapRequest
     */
    public function setToPort($toPort)
    {
        $this->toPort = $toPort;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * @param mixed $hostname
     * @return NmapRequest
     */
    public function setHostname($hostname)
    {
        $this->hostname = $hostname;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * @param mixed $timeout
     * @return NmapRequest
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOnlyCheckOnline()
    {
        return $this->onlyCheckOnline;
    }

    /**
     * @param mixed $onlyCheckOnline
     * @return NmapRequest
     */
    public function setOnlyCheckOnline($onlyCheckOnline)
    {
        $this->onlyCheckOnline = $onlyCheckOnline;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getListScan()
    {
        return $this->listScan;
    }

    /**
     * @param mixed $listScan
     * @return NmapRequest
     */
    public function setListScan($listScan)
    {
        $this->listScan = $listScan;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTcpSynScan()
    {
        return $this->tcpSynScan;
    }

    /**
     * @param mixed $tcpSynScan
     * @return NmapRequest
     */
    public function setTcpSynScan($tcpSynScan)
    {
        $this->tcpSynScan = $tcpSynScan;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUdpScan()
    {
        return $this->udpScan;
    }

    /**
     * @param mixed $udpScan
     * @return NmapRequest
     */
    public function setUdpScan($udpScan)
    {
        $this->udpScan = $udpScan;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTcpConnectScan()
    {
        return $this->tcpConnectScan;
    }

    /**
     * @param mixed $tcpConnectScan
     * @return NmapRequest
     */
    public function setTcpConnectScan($tcpConnectScan)
    {
        $this->tcpConnectScan = $tcpConnectScan;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStealthLevel()
    {
        return $this->stealthLevel;
    }

    /**
     * @param mixed $stealthLevel
     * @return NmapRequest
     */
    public function setStealthLevel($stealthLevel)
    {
        $this->stealthLevel = $stealthLevel;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getQuickEnableOsVersions()
    {
        return $this->quickEnableOsVersions;
    }

    /**
     * @param mixed $quickEnableOsVersions
     * @return NmapRequest
     */
    public function setQuickEnableOsVersions($quickEnableOsVersions)
    {
        $this->quickEnableOsVersions = $quickEnableOsVersions;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFastScan()
    {
        return $this->fastScan;
    }

    /**
     * @param mixed $fastScan
     * @return NmapRequest
     */
    public function setFastScan($fastScan)
    {
        $this->fastScan = $fastScan;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getScannedAt()
    {
        return $this->scannedAt;
    }

    /**
     * @param mixed $scannedAt
     * @return NmapRequest
     */
    public function setScannedAt($scannedAt)
    {
        $this->scannedAt = $scannedAt;
        return $this;
    }
}

