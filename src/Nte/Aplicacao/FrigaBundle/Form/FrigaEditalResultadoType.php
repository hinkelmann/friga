<?php

namespace Nte\Aplicacao\FrigaBundle\Form;

use Nte\Aplicacao\FrigaBundle\Entity\FrigaEdital;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FrigaEditalResultadoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('resultado0', CheckboxType::class, [
                'required' => false,
                'label'=>'Mostrar Cargo x Lista',
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('resultado1', CheckboxType::class, [
                'required' => false,
                'label'=>'Mostrar  Geral x Cargo',
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('resultado2', CheckboxType::class, [
                'required' => false,
                'label'=>'Mostrar  Geral x Lista',
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('resultado3', CheckboxType::class, [
                'required' => false,
                'label'=>'Mostrar  Geral ',
                'attr' => [
                    'class' => 'form-control',
                ]
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => FrigaEdital::class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'friga_edital';
    }


}
