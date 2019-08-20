<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
// use Nmap class for mapping from lib
use Nmap\Nmap;

class NmapType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // instance of enums if custom enums wanted
        //
        // determine wanted options following 10 nmap commands and library input types
        
        $builder
            ->add('fromIp', TextType::class, [
                'label' => 'From IP',
                'required' => false
            ])
            ->add('toIp', TextType::class, [
                'label' => 'To IP',
                'required' => false
            ])
            ->add('fromPort', IntegerType::class, [
                'label' => 'From port :',
                'required' => false
            ])
            ->add('toPort', IntegerType::class, [
                'label' => 'To port :',
                'required' => false
            ])
            ->add('hostname', TextType::class, [
                'label' => 'Hostname to scan :',
                'required' => false
            ])
            ->add('timeout', IntegerType::class, [
                'label' => 'Scan timeout in seconds',
                'required' => false
            ])
            ->add('stealthLevel')
            ->add('listScan', CheckboxType::class, [
                'label' => 'Only check if hosts are online',
                'required' => false
            ])
            ->add('tcpSynScan', CheckboxType::class, [
                'label' => '',
                'required' => false
            ])
            ->add('udpScan', CheckboxType::class, [
                'label' => '',
                'required' => false
            ])
            ->add('tcpConnectScan', CheckboxType::class, [
                'label' => '',
                'required' => false
            ])
            ->add('quickEnableOsVersions', CheckboxType::class, [
                'label' => '',
                'required' => false
            ])
            ->add('fastScan', CheckboxType::class, [
                'label' => '',
                'required' => false
            ])
            ->add('osDetection', CheckboxType::class, [
                'label' => 'Enable OS detection',
                'required' => false
            ])
            ->add('serviceInfo', CheckboxType::class, [
                'label' => 'Enable service info',
                'required' => false
            ])
            ->add('verbose', CheckboxType::class, [
                'label' => 'Enable verbose',
                'required' => false
            ])
            ->add('disPortScan', CheckboxType::class, [
                'label' => 'Disable port scan',
                'required' => false
            ])
            ->add('disReverseDNS', CheckboxType::class, [
                'label' => 'Disable reverse DNS',
                'required' => false
            ])
            ->add('hostsAsOnline', CheckboxType::class, [
                'label' => 'Treat hosts as online',
                'required' => false
            ])
            ->add('scanButton', SubmitType::class, [
                'label' => 'Launch scan'
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => NmapRequest::class
        ]);
    }
}

