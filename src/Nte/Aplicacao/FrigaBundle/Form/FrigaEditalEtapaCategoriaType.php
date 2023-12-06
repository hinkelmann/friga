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

use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalEtapaCategoria;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FrigaEditalEtapaCategoriaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var FrigaEditalEtapaCategoria $data */
        $data = $options['data'];
        $builder
            ->add('descricao', TextType::class, [
                'label' => 'Descrição da categoria',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Etapa 1',
                ],
            ])
            ->add('ordem', TextType::class, [
                'label' => 'Posição/Ordenação',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => '1',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FrigaEditalEtapaCategoria::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'edital_etapa_categoria';
    }
}
