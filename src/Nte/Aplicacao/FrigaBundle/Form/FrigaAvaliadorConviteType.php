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
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalUsuarioConvite;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FrigaAvaliadorConviteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var FrigaEditalUsuarioConvite $feu */
        $feu = $options['data'];
        $builder
            ->add('nome', TextType::class, [
                'label' => 'Nome',
                'attr' => [
                    'placeholder' => 'Fulano de tal',
                ],
            ])
            ->add('cpf', TextType::class, [
                'label' => 'CPF',
                'attr' => [
                    'data-rule-cpf' => true,
                    'placeholder' => '000.000.000-00',
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'e-Mail',
                'attr' => [
                    'placeholder' => 'fulano.tal@ufsm.br',
                ],
            ])
            ->add('descricao', TextareaType::class, [
                'label' => 'Observações',
                'required' => false,
                'attr' => [
                    'rows' => 6,
                    'placeholder' => 'Escreva uma mensagem no convite a ser enviado por e-mail',
                ],
            ])
            ->add('funcaoAdministracao')
            ->add('funcaoAvaliacao')
            ->add('funcaoConvocacao')
            ->add('funcaoResultado')
            ->add('idEditalCargo', EntityType::class, [
                'class' => FrigaEditalCargo::class,
                'label' => 'Cargos',
                'choice_label' => function(FrigaEditalCargo $entidade) {
                    return $entidade->getDescricao();
                },
                'query_builder' => function(EntityRepository $er) use ($feu) {
                    return $er->createQueryBuilder('c')
                        ->where('c.idEdital = :edital')
                        ->setParameter('edital', $feu->getIdEdital())
                        ->orderBy('c.descricao', 'ASC');
                },
                'expanded' => true,
                'by_reference' => true,
                'multiple' => true,
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FrigaEditalUsuarioConvite::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'friga_usuario';
    }
}
