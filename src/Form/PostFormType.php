<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;

class PostFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', HiddenType::class)
            ->add('Title', TextType::class, array('attr' => array('class' => 'form-control mb-3')))
            ->add('Content', TextareaType::class, array('required' => false, 'attr' => array('class' => 'form-control')))
            ->add('name', FileType::class, ['label' => 'Choose an image',
                'mapped' => false,
                'constraints' => [
                new File([
                    'maxSize' => '2048k',
                    'mimeTypes' => [
                        'image/jpg',
                        'image/png',
                    ],
                    'mimeTypesMessage' => 'Please upload a valid image file',
                ])
            ]])
            ;
    }
}