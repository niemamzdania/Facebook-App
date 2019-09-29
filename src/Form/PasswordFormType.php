<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class PasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_name' => 'first',
                'second_name' => 'second',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options' => ['label' => 'Password', 'attr' => ['class' => 'form-control']],
                'second_options' => ['label' => 'Repeat Password', 'attr' => ['class' => 'form-control']],
                'constraints' => [
                    new NotBlank([
                    ]),
                    new Length([
                        'min' => 6,
                        'max' => 4096,
                    ]),
                ],
            ])
            ->getForm();
    }
}