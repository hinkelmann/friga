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

use Nte\Aplicacao\FrigaBundle\Entity\FrigaConvocacao;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FrigaEditalConvocacaoType extends AbstractType
{
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
                    'placeholder' => 'Informe o texto para convocação do candidato.',
                ],
            ]);
        } else {
            $builder
                ->add('data', DateTimeType::class, [
                    'widget' => 'single_text',
                    'html5' => false,
                    'attr' => ['style' => 'display:none'],
                ])
                ->add('observacao', TextareaType::class, [
                    'label' => 'Informe o local que o candidato deverá comparecer e se for o caso, alguma consideração',
                    'attr' => [
                        'class' => 'form-control',
                        'rows' => 5,
                        'placeholder' => 'Rua Senhora das Lagrimas, N° 1000 - Sala 103, CEP 97000-666, Bairro Perpétua Tormenta, Porto Alegre.',
                    ],
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FrigaConvocacao::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'friga_convocacao';
    }
}
