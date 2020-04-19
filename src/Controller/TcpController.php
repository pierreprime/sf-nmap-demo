<?php

namespace App\Controller;

use App\Form\VanillaRequest;
use App\Form\VanillaType;
use App\Service\TcpService;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;

class TcpController extends AbstractController
{
    private $request;

    private $tcpService;

    private $om;

    private $dm;

    public function __construct(ObjectManager $om, RequestStack $requestStack, TcpService $tcpService, DocumentManager $dm)
    {
        $this->om = $om;
        $this->request = $requestStack->getCurrentRequest();
        $this->tcpService = $tcpService;
        $this->dm = $dm;
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
    public function vanillaReport()
    {
        error_reporting(-E_ALL);
        // ip port range and ip
        $host = $this->request->get('ip'); // beware of default socket timeout
        $from = $this->request->get('from');
        $to = $this->request->get('to');

        $data = $this->tcpService->scanOpenPorts($host, $from, $to);

        return $this->render('vanilla/report.html.twig', array(
            'host' => $host,
            'from' => $from,
            'to' => $to,
            'openPorts' => $data['openPorts'],
            'error' => $data['error']
        ));
    }
}
