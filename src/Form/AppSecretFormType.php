<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;

class AppSecretFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('appSecret', PasswordType::class, ['attr' => ['class' => 'form-control mb-3'], 'required' => false,
                'constraints' => [
                    new Length([
                        'max' => 40,
                        'maxMessage' => "App secret token cannot be longer than {{ limit }} characters"
                    ])
                ],
            ])
            ->getForm();
    }
}