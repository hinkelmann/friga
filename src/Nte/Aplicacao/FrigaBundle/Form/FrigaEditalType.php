<?php

namespace Nte\Aplicacao\FrigaBundle\Form;

use Doctrine\ORM\EntityRepository;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEdital;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalCategoria;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FrigaEditalType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titulo', TextType::class)
            ->add('subtitulo', TextType::class, ['required' => false,])
            ->add('url', TextType::class)
            ->add('idCategoria', EntityType::class, [
                'class' => FrigaEditalCategoria::class,
                'required'=>false,
                'label' => "Categoria",
                'choice_label' => 'descricao',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.descricao', 'asc');
                },
            ])
            ->add('sobre', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Insira as informações básicas sobre o edital',
                    'class' => 'form-control',
                    'rows' => 10
                ]
            ])
            ->add('info1', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Insira as informações sobre os cargos',
                    'class' => 'form-control',
                    'rows' => 10
                ]
            ])
            ->add('info2', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Insira as informações sobre a remuneração ',
                    'class' => 'form-control',
                    'rows' => 10
                ]
            ])
            ->add('info3', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Insira as informações sobre onde o candidato poderá encontrar ajuda',
                    'class' => 'form-control',
                    'rows' => 10
                ]
            ])
            ->add('publico', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    "publico.op0" => 0,
                    "publico.op1" => 1,
                    "publico.op2" => 2,
                    "publico.op3" => 3,
                ],
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('situacao', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    "Rascunho" => 0,
                    "Aberto" => 1,
                    "Encerrado" => 2,
                ],
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('dataPublicacaoOficial', DateType::class, [
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy',
                    'html5' => false,
                    'attr' => [
                        'class' => 'datepicker',
                        'data-plugin-options' => json_encode(["language" => "pt-BR"]),
                        'data-plugin-datepicker' => null,
                        'data-date-format' => 'dd/mm/yyyy',
                        'placeholder' => date('d/m/Y')
                    ]
                ]
            );
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
