<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
// use Nmap class for mapping from lib
use Nmap\Nmap;

class VanillaType extends AbstractType
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
            ->add('fromPort', IntegerType::class, [
                'label' => 'From port :',
                'required' => false
            ])
            ->add('toPort', IntegerType::class, [
                'label' => 'To port :',
                'required' => false
            ])
            ->add('scan', SubmitType::class, [
                'label' => 'Launch scan'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => VanillaRequest::class
        ]);
    }
}

