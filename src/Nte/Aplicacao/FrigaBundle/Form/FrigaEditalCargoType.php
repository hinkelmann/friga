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
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalCargo;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalPontuacao;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalUsuario;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FrigaEditalCargoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var FrigaEditalPontuacao $data */
        $data = $options['data'];

        $builder->add('descricao', TextType::class, [
            'label' => 'Descrição do Cargo',
            'attr' => [
                'class' => 'form-control',
                'placeholder' => 'Campi Santa Maria/Analista pedagogico senior',
            ],
        ])
            ->add('idEditalUsuario', EntityType::class, [
                'class' => FrigaEditalUsuario::class,
                'label' => 'Avaliador',
                'choice_label' => 'idUsuario.nome',
                'query_builder' => function(EntityRepository $er) use ($data) {
                    return $er->createQueryBuilder('e')
                        ->where('e.idEdital = :edital')
                        ->setParameter('edital', $data->getIdEdital()->getId());
                },
                'by_reference' => false,
                'multiple' => true,
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FrigaEditalCargo::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'edital_cargo';
    }
}
