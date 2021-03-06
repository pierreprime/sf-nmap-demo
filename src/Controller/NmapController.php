<?php

namespace App\Controller;

use App\Document\Address;
use App\Document\Scan;
use App\Service\NmapService;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @var DocumentRepository
     */
    private $scanRepository;

    // TEMP
    public function __construct(ObjectManager $om, RequestStack $requestStack, NmapService $nmapService, DocumentManager $dm, EncoderInterface $encoder, ObjectNormalizer $normalizer)
    {
        $this->om = $om;
        $this->dm = $dm;
        $this->request = $requestStack->getCurrentRequest();
        $this->nmapService = $nmapService;
        $this->encoder = [$encoder];
        $this->normalizer = [$normalizer];
        $this->scanRepository = $this->dm->getRepository(Scan::class);
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
    public function nmapScan()
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
                    $scanDocument = $this->nmapService->discoverIpsSubnet($ipRange);
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
                $this->dm->persist($scanDocument);
                $this->dm->flush();
                $message = 'Stored scan result';
                $body = $serializer->serialize($scanDocument, 'json');
                $response = new Response($body);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }

            $response = $this->json(array(
                'message' => empty($message) ? $message : null,
                'hosts' => $hosts
            ));
        }
        return $response;
    }

    /**
     * @OA\Get(
     *     path="/nmap/scan",
     *     description="Get nmap scan by UUID",
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
     *     path="/nmap/scan/{uuid}",
     *     name="nmap_scan_get",
     *     methods={"GET"}
     * )
     */
    public function nmapGet($uuid){
        // Scan Document instance
        $scanDocument = $this->scanRepository->find($uuid);
        $serializer = new Serializer($this->normalizer, $this->encoder);

        if(!$scanDocument){
            throw $this->createNotFoundException('Scan of uuid '.$uuid.' was not found.');
        }

//        var_dump($scanDocument);

        $body = $serializer->serialize($scanDocument, 'json');
        $response = new Response($body);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

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

    /**
     * @Route(
     *     path="/shooter",
     *     name="shooter"
     * )
     */
    public function shooter()
    {
        return $this->render('shooter/shooter.html.twig');
    }

    /**
     * @Route(
     *     path="/admin",
     *     name="admin"
     * )
     * PHPMoAdmin route, not working, remove later if no clue, use MongoExpress instead
     */
    public function mongoAdmin()
    {
//        return $this->render('admin/moadmin.php');
        require_once(__DIR__.'/../../templates/admin/moadmin.php');
    }
}

