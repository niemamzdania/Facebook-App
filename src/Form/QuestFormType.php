<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;

class QuestFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Content', TextareaType::class, ['required' => true, 'attr' => ['class' => 'form-control']])
            ->add('Status', ChoiceType::class, ['choices' => [
                '0%' => 0,
                '20%' => 20,
                '40%' => 40,
                '60%' => 60,
                '80%' => 80,
                '100%' => 100,
            ]])
            ->add('EndDate', DateTimeType::class, ['required' => true])
            ->getForm();
    }
}