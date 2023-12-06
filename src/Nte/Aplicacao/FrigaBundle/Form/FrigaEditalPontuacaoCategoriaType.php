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
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalPontuacao;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalPontuacaoCategoria;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FrigaEditalPontuacaoCategoriaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var FrigaEditalPontuacao $data */
        $data = $options['data'];
        $builder
            ->add('descricao', TextType::class, [
                'label' => 'Descrição da Categoria',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'I - Titulação Acadêmica',
                ],
            ])
            ->add('explicacao', TextareaType::class, [
                'label' => 'Explicação do item para o candidato',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Texto explicativo sobre o item para o candidato',
                ],
            ])
            ->add('explicacaoTexto', TextareaType::class, [
                'label' => 'Explicação do item para o candidato no campo texto',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Texto explicativo sobre o item para o candidato',
                ],
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
                ],
            ])

            ->add('pontuacaoTipo', ChoiceType::class, [
                'label' => 'Comprovante de pontuação',
                'expanded' => true,
                'choices' => [
                    'Nenhum - Não é necessário comprovar pontuação' => 0,
                    'Anexo - O candidato poderá  anexar seus arquivos para comprovar a pontuação' => 1,
                    'Texto - O candidato poderá enviar um texto para comprovar a pontuação' => 2,
                    'Texto  e Anexo  - O candidato poderá enviar um texto e anexar seus arquivos para comprovar a pontuação' => 3,
                ],
                'attr' => [
                    //   'class' => 'form-control',
                ],
            ])
            ->add('requisito', ChoiceType::class, [
                    'label' => 'Preenchimento Obrigatório',
                    'expanded' => true,
                    'choices' => [
                        'Não Obrigatóŕio' => 0,
                        'Anexo Obrigatório' => 1,
                        'Texto Obrigatório' => 2,
                        'Anexo e Texto Obrigatório' => 3,
                    ],
                ]
            )
            ->add('valorMinimo', NumberType::class, [
                'label' => 'Valor mínimo maior do que zero ',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => '1',
                ],
            ])
            ->add('valorMaximo', NumberType::class, [
                'label' => 'Valor Máximo',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => '0',
                ],
            ])
            ->add('valorTexto', TextType::class, [
                'label' => 'Valor texto',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Pontos',
                ],
            ])
            ->add('agruparPontuacao', ChoiceType::class, [
                    'label' => 'Agrupar pontuação',
                    'expanded' => true,
                    'choices' => [
                        'Não - item de pontuação separado' => 0,
                        'Sim - item de pontuação como intervalos de pontos da categoria' => 1,
                    ],
                ]
            )
            ->add('idCategoria', EntityType::class, [
                'class' => FrigaEditalPontuacaoCategoria::class,
                'label' => 'Peso',
                'empty_data' => null,
                'choice_label' => 'descricao',
                'query_builder' => function(EntityRepository $er) use ($data) {
                    return $er->createQueryBuilder('e')
                        ->where('e.idEdital = :edital and e.idCategoria is null')
                        ->setParameter('edital', $data->getIdEdital()->getId())
                    ;
                },
                'multiple' => false,
                //'required' => false,
                'expanded' => true,
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FrigaEditalPontuacaoCategoria::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'edital_cargo';
    }
}
