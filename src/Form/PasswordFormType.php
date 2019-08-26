<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class PasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => array('label' => 'Password', 'attr' => array('class' => 'form-control')),
                'second_options' => array('label' => 'Repeat Password', 'attr' => array('class' => 'form-control')),
            ])
            ->add('Edit', SubmitType::class, array('label' => 'Edit data', 'attr' => array('class' => 'btn btn-primary mt-3')))
            ->getForm();
    }
}