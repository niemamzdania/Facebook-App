<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;

class PostFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Title', TextType::class, ['attr' => ['class' => 'form-control mb-3'],
                'constraints' => [
                    new Length([
                        'min' => 3,
                        'max' => 4,
                        'minMessage' => "Title of post must be at least {{ limit }} characters long",
                        'maxMessage' => "Title of post cannot be longer than {{ limit }} characters"
                    ])
                ],
            ])
            ->add('Content', TextareaType::class, ['required' => false, 'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new Length([
                        'min' => 3,
                        'max' => 300,
                        'minMessage' => "Content of post must be at least {{ limit }} characters long",
                        'maxMessage' => "Content of post cannot be longer than {{ limit }} characters"
                    ])
                ],
                ])
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
                ],
            ])
            ;
    }
}