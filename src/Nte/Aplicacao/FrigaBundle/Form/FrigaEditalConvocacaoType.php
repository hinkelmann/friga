<?php

namespace Nte\Aplicacao\FrigaBundle\Form;

use Nte\Aplicacao\FrigaBundle\Entity\FrigaConvocacao;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalEtapa;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FrigaEditalConvocacaoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        /** @var FrigaConvocacao $convocacao */
        $convocacao = $options['data'];
        if ($convocacao->getIdEtapa()->getFinal()) {
            $builder->add('observacao', TextareaType::class, [
                'label' => 'Informações ',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 15,
                    'placeholder' => 'Informe o texto para convocação do candidato.'
                ]
            ]);
        } else {
            $builder
                ->add('data', DateTimeType::class, [
                    'widget' => 'single_text',
                    'html5' => false,
                    'attr' => ['style' => 'display:none']
                ])
                ->add('observacao', TextareaType::class, [
                    'label' => 'Informe o local que o candidato deverá comparecer e se for o caso, alguma consideração',
                    'attr' => [
                        'class' => 'form-control',
                        'rows' => 5,
                        'placeholder' => 'Rua Senhora das Lagrimas, N° 1000 - Sala 103, CEP 97000-666, Bairro Perpétua Tormenta, Porto Alegre.'
                    ]
                ]);;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => FrigaConvocacao::class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'friga_convocacao';
    }


}
