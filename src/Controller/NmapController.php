<?php

namespace App\Controller;

use App\Document\Address;
use App\Document\Host;
use App\Document\Hostname;
use App\Document\Port;
use App\Document\Scan;
use App\Document\Service;
use App\Service\NmapService;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\NmapRequest;
use App\Form\NmapType;
use App\Form\VanillaRequest;
use App\Form\VanillaType;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use OpenApi\Annotations as OA;
use Doctrine\ODM\MongoDB\DocumentManager as DocumentManager;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class NmapController
 * @package App\Controller
 * @OA\Info(
 *     title="Symfony Nmap demo",
 *     version="0.1"
 * )
 */
class NmapController extends AbstractController
{
    /**
     * @var Doctrine\Common\Persistence\ObjectManager
     */
    private $om;

    /**
     * @var DocumentManager
     */
    private $dm;

    /**
     * @var Request|null
     */
    private $request;

    /**
     * @var NmapService;
     */
    private $nmapService;

    /**
     * @var array
     */
    private $encoder;

    /**
     * @var array
     */
    private $normalizer;

    /**
     * @Route("/mongo", methods={"GET"})
     */
    public function mongoTest(){

        $address = new Address();
        $address->setAddress('localhost');
        $address->setType('type1');
        $address->setVendor('tigers');
        // make host nullable
        $this->dm->persist($address);
        $this->dm->flush();

        return new Response('Created address id '.$address->getId());
    }

    /**
     * @Route("/mongo/get", methods={"GET"})
     */
    public function mongoGetTest(DocumentManager $dm, $id = '5d94647ccb36f117de4ed6d3'){

        // get $id query parameter
        $address = $this->dm->getRepository(Address::class)->find($id);
        if(!$address){
            throw $this->createNotFoundException('No address found for id '.$id);
        }
        // JSON serialize address
        $serializer = new Serializer($this->normalizer, $this->encoder);

        $json = $serializer->serialize($address, 'json');
        return new Response($json);
    }

    // TEMP
    public function __construct(ObjectManager $om, RequestStack $requestStack, NmapService $nmapService, DocumentManager $dm, EncoderInterface $encoder, ObjectNormalizer $normalizer)
    {
        $this->om = $om;
        $this->dm = $dm;
        $this->request = $requestStack->getCurrentRequest();
        $this->nmapService = $nmapService;
        $this->encoder = [$encoder];
        $this->normalizer = [$normalizer];
    }

    /**
     * @OA\Post(
     *     path="/nmap/scan",
     *     description="Unified nmap scan for commands",
     *     @OA\Response(
     *          response=200,
     *          description="Multiple hosts report",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schema/Host")
     *          )
     *     )
     * )
     * @Route(
     *     path="/nmap/scan",
     *     name="nmap_scan",
     *     methods={"POST"}
     * )
     */
    public function nmapScanValid()
    {
        // determine ip range and ports list
        $data = json_decode($this->request->getContent(), true);
        $serializer = new Serializer($this->normalizer, $this->encoder);
        $ipRange = $data['ipRange'];
        $command = $data['command'];
        $ports = $data['ports'];

        if(!empty($data['id'])){
            // get object of id $id in scan collection
            $uuid = $data['id'];
            $scan = $this->dm->getRepository(Scan::class)->find($uuid);
            if(!$scan)
                throw $this->createNotFoundException('No scan found for uuid '.$uuid);
            // instance of serializer
            $json = $serializer->serialize($scan, 'json');
            $response = new Response($json);
        }
        else {
            // raw output
            $hosts = [];
            $saveFlag = (!isset($data['save']) || $data['save'] === true) ? true : false;
            switch ($command) {
                case "discover_ips":
                    $hosts = $this->nmapService->discoverIpsSubnet($ipRange);
                    var_dump($hosts);
                    break;
                case "open_ports":
                    $scanDocument = $this->nmapService->scanOpenPorts($ipRange, $ports);
                    break;
                default:
                    $hosts = [
                        'no hosts, invalid command'
                    ];
                    break;
            }
            // save to mongo
            if($saveFlag){

//                var_dump($scanDocument);
//                die();

                $this->dm->persist($scanDocument);
                $this->dm->flush();
                $message = 'Stored scan result';
                $body = $serializer->serialize($scanDocument, 'json');

                $response = new Response($body);
                return $response;
            }

            $response = $this->json(array(
//                'message' => empty($message) ? $message : null,
                'hosts' => $hosts
            ));
        }
        return $response;
    }

    // make a scan GET route

    // delete from here, put in in NmapService

    /**
     * @Route(
     *     path="/nmap/os",
     *     name="identify_os",
     *     methods={"POST"}
     * )
     */
    public function identfiyOs()
    {
        $hosts = $this->nmapService->identifyOs($ipRange, $portsList);
        return $this->json($hosts);
    }

    /**
     * @Route(
     *     path="/nmap/hostnames",
     *     name="identify_hostnames",
     *     methods={"POST"}
     * )
     */
    public function identifyHostnames($ipRange, $portsList)
    {
        $hosts = $this->nmapService->identifyHostnames($ipRange, $portsList);
        return $this->json($hosts);
    }

    /**
     * @Route(
     *     path="/nmap/synudp",
     *     name="syncudp_scan",
     *     methods={"POST"}
     * )
     */
    public function synAndUdpScan()
    {
    }

    /**
     * @Route(
     *     path="/nmap/tcpconnect",
     *     name="tcpconnect_scan",
     *     methods={"POST"}
     * )
     */
    public function tcpConnectScan()
    {

    }

    /**
     * @Route(
     *     path="/nmap/aggressive",
     *     name="aggressive_scan",
     *     methods={"POST"}
     * )
     */
    public function aggressiveScan()
    {

    }

    /**
     * @Route(
     *     path="/nmap/fast",
     *     name="fast_scan",
     *     methods={"POST"}
     * )
     */
    public function fastScan()
    {

    }

    // KEEP

    /**
     * @Route(
     *     path="/nmap/report",
     *     name="nmap_report",
     *     methods={"POST", "GET"}
     * )
     */
    public function nmapReport(array $hosts = [])
    {
        // process data for template
        //$formParameters = $this->request->request->parameters->nmap;

        // render view
        return $this->render('nmap/report.html.twig', [
            'hosts' => $hosts
        ]);
    }

    // WEB INTERFACE DEMO

    /**
     * @Route(
     *     path="/nmap",
     *     name="nmap"
     * )
     */
    public function nmapIndex()
    {
        $nmapRequest = new NmapRequest();
        $form = $this->createForm(NmapType::class, $nmapRequest);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $ipRange = [
                $data->getFromIp()
            ];
            if (!is_null($data->getToIp())) {
                array_push($ipRange, $data->getToIp());
            }

            $fromPort = $data->getFromPort();
            $toPort = $data->getToPort();

            $portsList = [];
            for ($i = $fromPort; $i <= $toPort; $i++) {
                array_push($portsList, $i);
            }

            // service class with nmap treatment
            if (!empty($data->getOnlyCheckOnline())) {
                $hosts = $this->nmapService->discoverIpsSubnet($ipRange, $portsList);
                $macro = 'ipSubnet';
            } else if (!empty($data->getVerbose())) {
                $hosts = $this->nmapService->verboseScan($ipRange, $portsList);
                $macro = 'verbose';
            } else if (!empty($data->getOsDetection())) {
                // requires root privileges
                $hosts = $this->nmapService->identifyOs($ipRange, $portsList);
                $macro = 'osDetection';
            } else {
                $hosts = $this->nmapService->scanOpenPorts($ipRange, $portsList);
                $macro = 'openPorts';
            }

            return $this->forward('App\Controller\NmapController::nmapReport', [
                'hosts' => $hosts,
                'macro' => $macro
            ]);
        }

        // manage output

        return $this->render('nmap/index.html.twig', array(
            'form' => $form->createView()
        ));
    }

    // LEGACY VANILLA PART

    /**
     * @Route(
     *     path="/vanilla",
     *     name="vanilla"
     * )
     */
    public function vanillaIndex()
    {
        // form with redirect to route report
        //$nmapRequest = new NmapRequest();
        //$form = $this->createForm(NmapType::class, $nmapRequest);

        $vanillaRequest = new VanillaRequest();
        $form = $this->createForm(VanillaType::class, $vanillaRequest);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            // launch nmap scan
            return $this->redirectToRoute('vanilla_report', array(
                'ip' => $data->getFromIp(),
                'from' => $data->getFromPort(),
                'to' => $data->getToPort()
            ));
        }

        return $this->render('vanilla/index.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route(
     *     path="/vanilla/report",
     *     name="vanilla_report"
     * )
     */
    public function vanillaScan()
    {
        // script
        error_reporting(-E_ALL);
        // ip port range and ip
        $host = $this->request->get('ip');
        // nb of connection varies following default_socket_timeout
        $from = $this->request->get('from');
        $to = $this->request->get('to');

        $openPorts = array();

        // validation
        if (empty($host) || empty($from) || empty($to)) {
            echo "<b>Incomplete data, go back choose IP address and port range</b>";
        } else if (!(filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))) {
            echo "<b>This IP address is not valid !</b>";
        } else if (!(is_numeric($from)) || !(is_numeric($to))) {
            echo "<b>Entered data is not port number</b>";
        } else if ($from > $to || $from == $to) {
            echo "<b>Please enter lower value in the FROM field</b>";
        } else // everything OK
        {
            echo "<br>
            <b>
                <u>Scanned IP/Host : $host</u>
                <br>
                <u>
                    <i>List of open ports</i>
                </u>
            </b>
        <br>";
            // create socket
            $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

            for ($port = $from; $port <= $to; $port++) {
                // connect to host and port
                $connection = socket_connect($socket, $host, $port);

                // make list of open ports in the loop

                if ($connection) {
                    // add to open ports
                    array_push($openPorts, $port);

                    // port open warning on connect
                    echo "port $port Open (Warning !) <img src='warning.png' height=30px width=30px alt='open port warning'><br>";
                    // close socket connection
                    socket_close($socket);
                    // create new when earlier socket was closed, recreate when connection made
                    // otherwise same socket used
                    $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
                }
            }
        }

        return $this->render('vanilla/report.html.twig', array(
            'host' => $host,
            'from' => $from,
            'to' => $to,
            'openPorts' => $openPorts
        ));
    }

    /**
     * @Route(
     *     path="/",
     *     name="report"
     * )
     */
    public function report()
    {
        return $this->render('nmap/report.html.twig');
    }
}

