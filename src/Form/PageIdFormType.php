<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;

class PageIdFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pageId', TextType::class, ['attr' => ['class' => 'form-control mb-3'], 'required' => false,
                'constraints' => [
                    new Length([
                        'max' => 255,
                    ])
                ],
            ])
            ->getForm();
    }
}