<?php

namespace Nte\Aplicacao\FrigaBundle\Form;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalCargo;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalEtapa;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalPontuacao;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FrigaEditalEtapaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var FrigaEditalEtapa $data */
        $data = $options['data'];
        $builder
            ->add('descricao', TextType::class, [
                'label' => 'Descrição da Etapa',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Inscrições - primeiro etapa'
                ]
            ]);
        if ($data->getTipo() == 6 or $data->getTipo() == 7) {
            $builder->add('idEtapa', EntityType::class, [
                'class' => FrigaEditalEtapa::class,
                'label' => 'Etapa complementar',
                'empty_data' => null,
                'expanded' => true,
                'required' => false,
                'placeholder' => 'Nenhuma Etapa',
                'choice_label' => 'descricao',
                'query_builder' => function (EntityRepository $er) use ($data) {
                    return $er->createQueryBuilder('e')
                        ->where('e.idEdital = :edital and (e.tipo = 3)')
                        ->setParameter('edital', $data->getIdEdital()->getId());
                },
            ]);
        }
        if ($data->getTipo() == 5 or $data->getTipo() == 4) {
            $builder
                ->add('observacao', TextareaType::class, [
                    'label' => 'Observações',
                    'required' => false,
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'Informações complementares - Utilize este campo para adicionar informações na página do edital relativa a esta etapa'
                    ]
                ])
                ->add('final', ChoiceType::class, [
                    'label' => 'Situação das inscrições ao final da etapa',
                    'empty_data' => null,
                    'expanded' => true,
                    'choices' => [
                        'Manter a situação atual' => 0,
                        'Alterar a situação para Classificado/Convocado' => 1,
                    ]
                ])
                ->add('desconsiderarInscricao', ChoiceType::class, [
                    'label' => 'Desconsiderar Inscrições',
                    'empty_data' => null,
                    'expanded' => true,
                    'choices' => [
                        'Mostrar todas  inscrições ' => 0,
                        'Não mostrar inscrições com a situação "não homologada" ou "desclassificado"' => 1,
                    ]
                ]);
            if ($data->getTipo() == 4) {

                $builder->add('cron',ChoiceType::class,[
                    'label'=>"Publicar resultados automaticamente",
                    'expanded' => true,
                    'choices' => [
                        'Não' => 0,
                        'Sim' => 1,
                    ]
                ])
                    ->add('qtdClassificado', TextType::class, [
                    'label' => 'Número máximo de classificados',
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'Ex: 10'
                    ]
                ]);
            }
        }
        if ($data->getTipo() == 3) {
            $builder->add('pontuacaoMultipla', ChoiceType::class, [
                'label' => 'Pontuação Individual',
                'expanded' => true,
                'choices' => [
                    'Não' => 0,
                    'Sim' => 1,
                ]
            ]);
        }
        if($data->getTipo() == 8 or $data->getTipo()==9){
            $builder
                ->add('observacao', TextareaType::class, [
                    'label' => 'Observações',
                    'required' => false,
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'Informações complementares - Utilize este campo para adicionar informações na página do edital relativa a esta etapa'
                    ]
                ]);
        }
        if ($data->getTipo() > 0 and $data->getTipo() < 8) {
            $builder->add('dataInicial', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Data Inicial',
                //'format'=>'dd/mm/YYY'
            ]);
            $builder->add('dataFinal', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Data Final',
                //'format'=>'dd/mm/YYY'
            ]);
            if ($data->getTipo() > 1) {
                $builder->add('dataDivulgacao', DateType::class, [
                    'widget' => 'single_text',
                    'label' => 'Data de Divulgação de Resultados',
                    //'format'=>'dd/mm/YYY'
                    'required' => false,
                ]);
            }
        } else {
            $builder->add('dataInicial', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Data ',
                //'format'=>'dd/mm/YYY'
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => FrigaEditalEtapa::class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'edital_etapa';
    }


}
