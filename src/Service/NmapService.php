<?php

namespace App\Service;

use App\Document\Address;
use App\Document\Hostname;
use App\Document\Port;
use App\Document\Scan;
use App\Document\Service;
use Nmap\Host;
use Nmap\Nmap;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

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
     * @return array
     */
    public function discoverIpsSubnet($ipRange, $portsList = [])
    {
        /*
         * -sP option, -sn for more recent releases of nmap (actual here)
         * convert to pure array
         */
        $hostArray =  $this->nmap
            ->disablePortScan()
            ->scan($ipRange);
        // copyScanContent
        return json_decode(json_encode($hostArray), true);
    }

    /**
     * @param $ipRange
     * @param array $portsList
     * @return Scan
     */
    public function scanOpenPorts($ipRange, $portsList = [])
    {
        /*
         * No flags at all, default behaviour
         * convert to pure array
         */
        $hostArray = $this->nmap->scan($ipRange, $portsList);
        $purgedScanArray = $this->copyToChildScanClasses($hostArray);

        $scanDocument = $this->buildScanDocument($purgedScanArray);
        // replace this with JsonSerialize strategy from Scan or Host
        return $scanDocument;
    }

    private function buildScanDocument($scan){
        // Adapt to hosts array
        $scanDocument = new Scan();
        $hostsDocuments = [];
        $hosts = $scan['hosts'];

        foreach ($hosts as $host){

            $addressesDocument = [];
            foreach ($host['addresses'] as $address){
                $addressesDocument[] = new Address(
                    $address['address'],
                    $address['type'],
                    $address['vendor']
                );
            }

            $hostnamesDocument = [];
            foreach ($host['hostnames'] as $hostname){
                $hostnamesDocument[] = new Hostname(
                    $hostname['name'],
                    $hostname['type']
                );
            }

            $portsDocument = [];
            foreach ($host['ports'] as $port){
                $service = new Service(
                    $port['service']['name'],
                    $port['service']['product'],
                    $port['service']['version']
                );
                $portsDocument[] = new Port(
                    $port['number'],
                    $port['protocol'],
                    $port['state'],
                    $service
                );
            }

            $hostsDocuments[] = new \App\Document\Host($host['state'], $addressesDocument, $hostnamesDocument, $portsDocument);
        }
        $scanDocument->setHosts($hostsDocuments);
        return $scanDocument;
    }

    /**
     * @param Host[] $hosts
     * @return Scan
     */
    private function copyToChildScanClasses($hosts = []){
        // Adapt to Nmap getters/setters
        // foreach host, add to hosts collection (MOVE)
        // INSTANTIATE AND PERSIST
        $scanDocument = new Scan();
        $hostsDocuments = [];

        foreach ($hosts as $host){

            $addressesDocument = [];
            foreach ($host->getAddresses() as $address){
                $addressesDocument[] = new Address($address->getAddress(), $address->getType(), $address->getVendor());
            }

            $hostnamesDocument = [];
            foreach ($host->getHostnames() as $hostname){
                $hostnamesDocument[] = new Hostname($hostname->getName(), $hostname->getType());
            }

            $portsDocument = [];
            foreach ($host->getPorts() as $port){
                $service = new Service(
                    $port->getService()->getName(),
                    $port->getService()->getProduct(),
                    $port->getService()->getVersion()
                );
                $portsDocument[] = new Port($port->getNumber(), $port->getProtocol(), $port->getState(), $service);
            }

            $hostsDocuments[] = new \App\Document\Host($host->getState(), $addressesDocument, $hostnamesDocument, $portsDocument);
        }
        $scanDocument->setHosts($hostsDocuments);
        return json_decode(json_encode($scanDocument), true);
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
