<?php

namespace Nte\Aplicacao\FrigaBundle\Form;

use Doctrine\ORM\EntityRepository;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalEtapa;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalPontuacao;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalPontuacaoCategoria;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FrigaEditalPontuacaoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        /** @var FrigaEditalPontuacao $data */
        $data = $options['data'];
        $builder
            ->add('titulo', TextType::class, [
                'label' => 'Título',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Publicação em eventos da area'
                ]
            ])
            ->add('descricao', TextType::class, [
                'label' => 'Descrição do item',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Publicação em eventos da area'
                ]
            ])
            ->add('explicacao', TextareaType::class, [
                'label' => 'Explicação do item para o candidato no campo anexo',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Texto explicativo sobre o item para o candidato'
                ]
            ])
            ->add('explicacaoTexto', TextareaType::class, [
                'label' => 'Explicação do item para o candidato no campo texto',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Texto explicativo sobre o item para o candidato'
                ]
            ])
            ->add('pontuacaoAuto', ChoiceType::class, [
                'label' => 'Pontuação informada pelo candidato',
                'expanded' => true,
                'choices' => [
                    'Nenhuma pontuação poderá ser informada pelo candidato' => 0,
                    'A pontuação poderá ser informada pelo candidato' => 1,
                ],
                'attr' => [
                    //   'class' => 'form-control',
                ]
            ])

            ->add('pontuacaoTipo', ChoiceType::class, [
                'label' => 'Comprovante de pontuação',
                'expanded' => true,
                'choices' => [
                    'Nenhum - Não é necessário comprovar pontuação'=>0,
                    'Anexo - O candidato poderá  anexar seus arquivos para comprovar a pontuação'=>1,
                    'Texto - O candidato poderá enviar um texto para comprovar a pontuação'=>2,
                    'Texto e Anexo - O candidato poderá enviar um texto e anexar seus arquivos para comprovar a pontuação'=>3,
                ],
                'attr' => []
            ])
            ->add('requisito', ChoiceType::class, [
                    'label' => 'Preenchimento Obrigatório',
                    'expanded' => true,
                    'choices' => [
                        'Não Obrigatóŕio' => 0,
                        'Anexo Obrigatório' => 1,
                        'Texto Obrigatório' => 2,
                        'Anexo e Texto Obrigatório' => 3,
                    ]
                ]
            )
            ->add('valorMinimo', NumberType::class, [
                'label' => 'Valor mínimo',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => '1'
                ]
            ])
            ->add('valorMaximo', NumberType::class, [
                'label' => 'Valor Máximo',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => '0'
                ]
            ])
            ->add('valorIntervalo', NumberType::class, [
                'label' => 'Intervalo de Pontuação',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => '0'
                ]
            ])
            ->add('valorTexto', TextType::class, [
                'label' => 'Valor Texto',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Pontos'
                ]
            ])
            ->add('idEtapa', EntityType::class, [
                'class' => FrigaEditalEtapa::class,
                'label' => "Etapa",
                'choice_label' => 'descricao',
                'query_builder' => function (EntityRepository $er) use ($data) {
                    return $er->createQueryBuilder('e')
                        ->where('e.idEdital = :edital and (e.tipo =1 or e.tipo =2 or e.tipo = 3)')
                        ->setParameter('edital', $data->getIdEdital()->getId());
                },
                // 'by_reference' => false,
                'multiple' => true,
                'expanded' => true,
                'required' => false,

            ])
            ->add('idCategoria', EntityType::class, [
                'class' => FrigaEditalPontuacaoCategoria::class,
                'label' => "Categoria",
                'choice_label' => 'descricao',
                'query_builder' => function (EntityRepository $er) use ($data) {
                    return $er->createQueryBuilder('e')
                        ->where('e.idEdital = :edital and e.idCategoria is not null')
                        ->setParameter('edital', $data->getIdEdital()->getId());
                },
                // 'by_reference' => false,
                'multiple' => false,
                'expanded' => true,
                'required' => true,

            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => FrigaEditalPontuacao::class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'edital_pontuacao';
    }


}
