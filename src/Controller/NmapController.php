<?php

namespace App\Controller;

use App\Service\NmapService;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\NmapRequest;
use App\Form\NmapType;
use App\Form\VanillaRequest;
use App\Form\VanillaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;

class NmapController extends AbstractController
{
    /**
     * @var Doctrine\Common\Persistence\ObjectManager
     */
    private $om;

    /**
     * @var Request|null
     */
    private $request;

    /**
     * @var NmapService;
     */
    private $nmapService;

    public function __construct(ObjectManager $om, RequestStack $requestStack, NmapService $nmapService)
    {
        $this->om = $om;
        $this->request = $requestStack->getCurrentRequest();
        $this->nmapService = $nmapService;
    }

    /**
     * @Route("/nmap", name="nmap")
     */
    public function nmapIndex()
    {
        $nmapRequest = new NmapRequest();
        $form = $this->createForm(NmapType::class, $nmapRequest, [
            //'action' => $this->generateUrl('nmap_report'),
            //'method' => 'POST'
        ]);
        $form->handleRequest($this->request);

        if($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();

            $ipRange = [
                '192.168.1.1'
            ];
            $portsList = [
                '21', '22', '80'
            ];

            // service class with nmap treatment
            $hosts = $this->nmapService->discoverIpsSubnet($ipRange, $portsList);
            //$hosts = $this->nmapService->scanOpenPorts($ipRange, $portsList);
            //$hosts = $this->nmapService->identityHostnames($ipRange, $portsList);
            //$hosts = $this->nmapService->tcpConnectScan($ipRange, $portsList);
            //$hosts = $this->nmapService->aggressiveScan($ipRange, $portsList);
            //$hosts = $this->nmapService->fastScan($ipRange, $portsList);
            //$hosts = $this->nmapService->verboseScan($ipRange, $portsList);

            // requires root privileges
            //$hosts = $this->nmapService->identifyOs($ipRange, $portsList);
            //$hosts = $this->nmapService->tcpSynUdpScan($ipRange, $portsList);
            //$hosts = $this->nmapService->tcpSynUdpAllPortsScan($ipRange, $portsList);

            return $this->forward('App\Controller\NmapController::nmapReport', [
                'hosts' => $hosts
            ]);
        }

        // manage output

        return $this->render('nmap/index.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/nmap/report", name="nmap_report", methods={"POST", "GET"})
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

    /**
     * @Route("/vanilla", name="vanilla")
     */
    public function vanillaIndex()
    {
        // form with redirect to route report
        //$nmapRequest = new NmapRequest();
        //$form = $this->createForm(NmapType::class, $nmapRequest);

        $vanillaRequest = new VanillaRequest();
        $form = $this->createForm(VanillaType::class, $vanillaRequest);
        $form->handleRequest($this->request);

        if($form->isSubmitted() && $form->isValid()){
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
     * @Route("/vanilla/report", name="vanilla_report")
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
        if(empty($host) || empty($from) || empty($to))
        {
            echo "<b>Incomplete data, go back choose IP address and port range</b>";
        }
        else if(!(filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)))
        {
            echo "<b>This IP address is not valid !</b>";
        }
        else if(!(is_numeric($from)) || !(is_numeric($to)))
        {
            echo "<b>Entered data is not port number</b>";
        }
        else if($from > $to || $from == $to)
        {
            echo "<b>Please enter lower value in the FROM field</b>";
        }
        else // everything OK
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

            for($port = $from; $port <= $to; $port++)
            {
                // connect to host and port
                $connection = socket_connect($socket, $host, $port);

                // make list of open ports in the loop

                if($connection)
                {
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
     * @Route("/", name="report")
     */
    public function report()
    {
       return $this->render('nmap/report.html.twig'); 
    }
}

