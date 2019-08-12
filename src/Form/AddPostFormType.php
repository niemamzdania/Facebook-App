<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class AddPostFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setAction($this->generateUrl('add_post'))
            ->setMethod('POST')
            ->add('Title', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('Content', TextareaType::class, array('required' => false, 'attr' => array('class' => 'form-control')))
            ->add('Submit', SubmitType::class, array('label' => 'Add Post', 'attr' => array('class' => 'btn btn-primary mt-3')))
            ->getForm();
    }
}