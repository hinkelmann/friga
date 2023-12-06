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
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEdital;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalCargo;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalCota;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExportarMoodleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var FrigaEdital $edital */
        $edital = $options['edital'];
        $builder
            ->add('idCargo', EntityType::class, [
                'class' => FrigaEditalCargo::class,
                'label' => 'Cargo',
                'choice_label' => function(FrigaEditalCargo $entidade) {
                    return $entidade->getDescricao();
                },
                'query_builder' => function(EntityRepository $er) use ($edital) {
                    return $er->createQueryBuilder('e')
                        ->where('e.idEdital = :id')->setParameter('id', $edital->getId());
                }, //'expanded'  => true,
                'by_reference' => false,
                'multiple' => true,
                'required' => true,
                'mapped' => false,
            ])->add('idCota', EntityType::class, [
                'class' => FrigaEditalCota::class,
                'label' => 'Listas',
                'choice_label' => function(FrigaEditalCota $entidade) {
                    return $entidade->getDescricao();
                },
                'query_builder' => function(EntityRepository $er) use ($edital) {
                    return $er->createQueryBuilder('e')
                        ->where('e.idEdital = :id')->setParameter('id', $edital->getId());
                },
                'by_reference' => false,
                'multiple' => true,
                'mapped' => false,
            ])
            ->add('auth', ChoiceType::class, [
                'label' => 'Autenticação',
                'expanded' => true,
                'mapped' => false,
                'choices' => [
                    'Manual' => 'manual',
                    'SIE' => 'sie',
                ],
            ])
            ->add('papel', ChoiceType::class, [
                'label' => 'Papel',
                'expanded' => true,
                'mapped' => false,
                'choices' => [
                    'Tutor a Distância' => 9,
                    'Tutor Presencial' => 10,
                    'Coordenador de Tutoria' => 12,
                    'Coordenador de Curso' => 11,
                    'Professor Externo' => 14,
                    'Coordenador e Assistente de Polo' => 18,
                ],
            ])

            ->add('curso', ChoiceType::class, [
                'label' => 'Curso',
                'mapped' => false, 'multiple' => true,
                'choices' => \array_flip($this->get_courses()),
            ])
            ->add('dataInicial', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Data Inicial', 'mapped' => false,
                //'format'=>'dd/mm/YYY'
            ])
            ->add('dataFinal', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Data Final', 'mapped' => false,
                //'format'=>'dd/mm/YYY'
            ])
            ->add('ambiente', ChoiceType::class, [
                'label' => 'Ambiente',
                'expanded' => true,
                'mapped' => false,
                'choices' => [
                    'LSM 001' => 'https://moodle.xxx.edu.br',
                    // Nome  => url
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['edital' => null]);
    }

    public function getBlockPrefix()
    {
        return 'edital_convocacao_exportar_moodle';
    }

    public function get_courses()
    {
        return [
            'AA001' => 'Bacharelado em Administração ',
            // codigo do curso => nome do curso
        ];
    }
}
