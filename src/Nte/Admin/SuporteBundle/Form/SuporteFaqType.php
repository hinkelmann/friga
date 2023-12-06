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

namespace Nte\Admin\SuporteBundle\Form;

use Nte\Admin\SuporteBundle\Entity\SuporteFaq;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SuporteFaqType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('pergunta', TextareaType::class, [
            'required' => false,
            'attr' => [
                'placeholder' => 'Escreva aqui a pergunta frequente.',
                'class' => 'form-control',
                'rows' => 3,
            ],
        ])
            ->add('resposta', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Escreva aqui a resposta para sua pergunta',
                    'class' => 'form-control',
                    'rows' => 4,
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SuporteFaq::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'suporte';
    }
}
