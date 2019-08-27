<?php
namespace App\Form;

use App\Entity\Posts;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Valid;

class PostFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Title', TextType::class, array('attr' => array('class' => 'form-control mb-3')))
            ->add('Content', TextareaType::class, array('required' => false, 'attr' => array('class' => 'form-control')))
            ->add('name', FileType::class, ['label' => 'Choose an image',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                new File([
                    'maxSize' => '2048k',
                    'mimeTypes' => [
                        'image/jpg',
                        'image/jpeg',
                        'image/png',
                    ],
                    'mimeTypesMessage' => 'Please upload a valid image file',
                    'maxSizeMessage' => 'File size is too large. The maximum file size is 2048 kB.'
                ])
            ],])
            ;
    }
}