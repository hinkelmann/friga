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

use Nte\Aplicacao\FrigaBundle\Entity\FrigaEdital;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FrigaEditalConfigInscricaoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('modeloInscricao', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'Pessoal' => 0,
                    'Projeto' => 1,
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('tipoInscricao', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'ti.op0' => 0,
                    'ti.op1' => 1,
                    'ti.op2' => 2,
                    'ti.op4' => 4,
                    'ti.op5' => 5,
                    'ti.op3' => 3,
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('tipoInscricaoLimite', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => '0',
                    'class' => 'form-control',
                ],
            ])
            ->add('projetoParticipanteMax', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => '0',
                    'class' => 'form-control',
                ],
            ])
            ->add('projetoParticipanteMin', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => '0',
                    'class' => 'form-control',
                ],
            ])

            ->add('anexo4', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'anexo.op0' => 0,
                    'anexo.op1' => 1,
                    'anexo.op2' => 2,
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('permitirEstrangeiro', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'permitir.op0' => 0,
                    'permitir.op1' => 1,
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('anexo0', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'anexo.op0' => 0,
                    'anexo.op1' => 1,
                    'anexo.op2' => 2,
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('anexo1', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'anexo.op0' => 0,
                    'anexo.op1' => 1,
                    'anexo.op2' => 2,
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('anexo2', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'anexo.op0' => 0,
                    'anexo.op1' => 1,
                    'anexo.op2' => 2,
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('anexo3', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'anexo.op0' => 0,
                    'anexo.op1' => 1,
                    'anexo.op2' => 2,
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('info4', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Insira os requisitos do edital linha por linha',
                    'class' => 'form-control',
                    'rows' => 10,
                ],
            ])
            ->add('info5', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Observações sobre os requisitos, listas ou cargos',
                    'class' => 'form-control',
                    'rows' => 5,
                ],
            ])

            ->add('info6', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Observações sobre os dados de identificação',
                    'class' => 'form-control',
                    'rows' => 5,
                ],
            ])
            ->add('info7', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Observações sobre os dados de endereço',
                    'class' => 'form-control',
                    'rows' => 5,
                ],
            ])
            ->add('info8', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Observações sobre a pontuação',
                    'class' => 'form-control',
                    'rows' => 5,
                ],
            ])
            ->add('info9', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Observações sobre a confirmação da inscrição',
                    'class' => 'form-control',
                    'rows' => 5,
                ],
            ])
            ->add('info10', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Informações sobre outros anexos e comprovantes.',
                    'class' => 'form-control',
                    'rows' => 5,
                ],
            ])
            ->add('info11', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Observações sobre o resumo do projeto.',
                    'class' => 'form-control',
                    'rows' => 5,
                ],
            ])
            ->add('info12', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Observações sobre o anexo  do projeto.',
                    'class' => 'form-control',
                    'rows' => 5,
                ],
            ])
            ->add('info13', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Observações sobre a apresentação individual.',
                    'class' => 'form-control',
                    'rows' => 5,
                ],
            ])
            ->add('doc0', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'campoDocumento.op0' => 0,
                    'campoDocumento.op1' => 1,
                    'campoDocumento.op2' => 2,
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('doc1', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'campoDocumento.op0' => 0,
                    'campoDocumento.op1' => 1,
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('doc2', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'campoDocumento.op0' => 0,
                    'campoDocumento.op1' => 1,
                    'campoDocumento.op2' => 2,
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('doc3', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'campoDocumento.op0' => 0,
                    'campoDocumento.op1' => 1,
                    'campoDocumento.op2' => 2,
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('doc4', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'campoDocumento.op0' => 0,
                    'campoDocumento.op1' => 1,
                    'campoDocumento.op2' => 2,
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('doc9', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'campoDocumento.op0' => 0,
                    'campoDocumento.op1' => 1,
                    'campoDocumento.op2' => 2,
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('doc5', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'campoDocumento.op0' => 0,
                    'campoDocumento.op1' => 1,
                    'campoDocumento.op2' => 2,
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('doc6', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'campoDocumento.op0' => 0,
                    'campoDocumento.op1' => 1,
                    'campoDocumento.op2' => 2,
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('doc7', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'campoDocumento.op0' => 0,
                    'campoDocumento.op1' => 1,
                    'campoDocumento.op2' => 2,
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('doc8', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'campoDocumento.op0' => 0,
                    'campoDocumento.op1' => 1,
                    'campoDocumento.op2' => 2,
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('doc10', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'campoDocumento.op0' => 0,
                    'campoDocumento.op1' => 1,
                    'campoDocumento.op2' => 2,
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('doc11', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'campoDocumento.op0' => 0,
                    'campoDocumento.op1' => 1,
                    'campoDocumento.op2' => 2,
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('doc12', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'campoDocumento.op0' => 0,
                    'campoDocumento.op1' => 1,
                    'campoDocumento.op2' => 2,
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('doc13', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'campoDocumento.op0' => 0,
                    'campoDocumento.op1' => 1,
                    'campoDocumento.op2' => 2,
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('doc14', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'campoDocumento.op0' => 0,
                    'campoDocumento.op1' => 1,
                    'campoDocumento.op2' => 2,
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('doc15', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'campoDocumento.op0' => 0,
                 //   "campoDocumento.op1" => 1,
                    'campoDocumento.op2' => 2,
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('campoCargoTitulo', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Em qual cargo voce está interessado?',
                ],
            ])
            ->add('campoListaTitulo', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Em qual situação você se encaixa?',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FrigaEdital::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'friga_edital';
    }
}
