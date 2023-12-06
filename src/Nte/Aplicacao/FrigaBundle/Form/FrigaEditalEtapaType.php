<?php

/*
 * This file is part of  Friga - https://nte.ufsm.br/friga.
 * (c) Friga
 * Friga is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Friga is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Friga.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Nte\Aplicacao\FrigaBundle\Form;

use Doctrine\ORM\EntityRepository;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalEtapa;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalEtapaCategoria;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FrigaEditalEtapaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var FrigaEditalEtapa $data */
        $data = $options['data'];
        $builder
            ->add('descricao', TextType::class, [
                'label' => 'Descrição da Etapa',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Inscrições - primeiro etapa',
                ],
            ]);
        if ($data->getIdEdital()->getIdEtapaCategoria()->count()) {
            $builder->add('idEtapaCategoria', EntityType::class, [
                'class' => FrigaEditalEtapaCategoria::class,
                'label' => 'Categoria',
                'empty_data' => null,
                'expanded' => true,
                'required' => false,
                'placeholder' => 'Nenhuma categoria',
                'choice_label' => 'descricao',
                'query_builder' => function(EntityRepository $er) use ($data) {
                    return $er->createQueryBuilder('e')
                        ->where('e.idEdital = :edital')
                        ->setParameter('edital', $data->getIdEdital()->getId());
                },
            ]);
        }
        if (6 == $data->getTipo() or 7 == $data->getTipo()) {
            $builder->add('idEtapa', EntityType::class, [
                'class' => FrigaEditalEtapa::class,
                'label' => 'Etapa complementar',
                'empty_data' => null,
                'expanded' => true,
                'required' => false,
                'placeholder' => 'Nenhuma Etapa',
                'choice_label' => 'descricao',
                'query_builder' => function(EntityRepository $er) use ($data) {
                    return $er->createQueryBuilder('e')
                        ->where('e.idEdital = :edital and (e.tipo = 3)')
                        ->setParameter('edital', $data->getIdEdital()->getId());
                },
            ]);
        }
        if (5 == $data->getTipo() or 4 == $data->getTipo()) {
            if (5 == $data->getTipo()) {
                $builder->add('idEtapa', EntityType::class, [
                    'class' => FrigaEditalEtapa::class,
                    'label' => 'Etapa complementar',
                    'empty_data' => null,
                    'expanded' => true,
                    'required' => true,
                    'placeholder' => 'Nenhuma Etapa',
                    'choice_label' => 'descricao',
                    'query_builder' => function(EntityRepository $er) use ($data) {
                        return $er->createQueryBuilder('e')
                            ->where('e.idEdital = :edital and (e.tipo = 4)')
                            ->setParameter('edital', $data->getIdEdital()->getId());
                    },
                ]);
            }
            $builder
                ->add('observacao', TextareaType::class, [
                    'label' => 'Observações',
                    'required' => false,
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'Informações complementares - Utilize este campo para adicionar informações na página do edital relativa a esta etapa',
                    ],
                ])
                ->add('final', ChoiceType::class, [
                    'label' => 'Situação das inscrições ao final da etapa',
                    'empty_data' => null,
                    'expanded' => true,
                    'choices' => [
                        'Manter a situação atual' => 0,
                        'Alterar a situação para Classificado/Convocado' => 1,
                    ],
                ])
                ->add('desconsiderarInscricao', ChoiceType::class, [
                    'label' => 'Desconsiderar Inscrições',
                    'empty_data' => null,
                    'expanded' => true,
                    'choices' => [
                        'Mostrar todas  inscrições ' => 0,
                        'Não mostrar inscrições com a situação "não homologada" ou "desclassificado"' => 1,
                    ],
                ]);
            if (4 == $data->getTipo()) {
                $builder->add('cron', ChoiceType::class, [
                    'label' => 'Publicar resultados automaticamente',
                    'expanded' => true,
                    'choices' => [
                        'Não' => 0,
                        'Sim' => 1,
                    ],
                ])
                    ->add('qtdClassificado', TextType::class, [
                        'label' => 'Número máximo de classificados',
                        'required' => false,
                        'attr' => [
                            'placeholder' => 'Ex: 10',
                        ],
                    ]);
            }
        }
        if (3 == $data->getTipo()) {
            $builder->add('pontuacaoMultipla', ChoiceType::class, [
                'label' => 'Pontuação Individual',
                'expanded' => true,
                'choices' => [
                    'Não' => 0,
                    'Sim' => 1,
                ],
            ]);
        }
        if (8 == $data->getTipo() or 9 == $data->getTipo()) {
            $builder
                ->add('observacao', TextareaType::class, [
                    'label' => 'Observações',
                    'required' => false,
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'Informações complementares - Utilize este campo para adicionar informações na página do edital relativa a esta etapa',
                    ],
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

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FrigaEditalEtapa::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'edital_etapa';
    }
}
