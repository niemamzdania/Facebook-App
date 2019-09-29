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
                        'max' => 50,
                        'minMessage' => "Tytuł postu musi mieć przynajmniej {{ limit }} znaki.",
                        'maxMessage' => "Tytuł postu nie może mieć więcej niż {{ limit }} znaków."
                    ])
                ],
            ])
            ->add('Content', TextareaType::class, ['required' => false, 'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new Length([
                        'min' => 3,
                        'max' => 300,
                        'minMessage' => "Treść postu musi mieć przynajmniej {{ limit }} znaki.",
                        'maxMessage' => "Treść postu nie może być dłuższa niż {{ limit }} znaków."
                    ])
                ],
                ])
            ->add('name', FileType::class, ['label' => 'Choose an image',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                new File([
                    'mimeTypes' => [
                        'image/jpg',
                        'image/jpeg',
                        'image/png',
                    ],
                ])
                ],
            ])
            ;
    }
}