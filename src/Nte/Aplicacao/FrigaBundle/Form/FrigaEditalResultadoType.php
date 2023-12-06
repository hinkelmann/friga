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
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FrigaEditalResultadoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('resultado0', CheckboxType::class, [
                'required' => false,
                'label' => 'Mostrar Cargo x Lista',
                'attr' => [
                ],
            ])
            ->add('resultado1', CheckboxType::class, [
                'required' => false,
                'label' => 'Mostrar  Geral x Cargo',
                'attr' => [
                ],
            ])
            ->add('resultado2', CheckboxType::class, [
                'required' => false,
                'label' => 'Mostrar  Geral x Lista',
                'attr' => [
                ],
            ])
            ->add('resultado3', CheckboxType::class, [
                'required' => false,
                'label' => 'Mostrar  Geral ',
                'attr' => [
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
