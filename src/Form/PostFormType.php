<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

class PostFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setMethod('POST')
            ->add('id', HiddenType::class)
            ->add('Title', TextType::class, array('attr' => array('class' => 'form-control mb-3')))
            ->add('Content', TextareaType::class, array('required' => false, 'attr' => array('class' => 'form-control')))
            ->getForm();
    }
}