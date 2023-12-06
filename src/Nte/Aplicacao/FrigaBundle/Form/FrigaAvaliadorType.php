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
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalUsuario;
use Nte\UsuarioBundle\Entity\Usuario;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FrigaAvaliadorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var FrigaEditalUsuario $feu */
        $feu = $options['data'];
        if (\is_null($feu->getIdUsuario())) {
            $builder
                ->add('idUsuario', EntityType::class, [
                    'class' => Usuario::class,
                    'label' => 'Usuário',
                    'choice_label' => function(Usuario $u) {
                        return \strtoupper($u->getNome());
                    },
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('u')
                            ->leftJoin('u.idEditalUsuario', 'eu')
                            ->where("u.roles like '%ROLE_AVALIADOR%' or u.roles like '%ROLE_GERENCIAL%'")
                            ->orderBy('u.nome', 'asc');
                    },
                    'by_reference' => false,
                    'multiple' => false,
                    'required' => false,
                ]);
        }
        $builder
            ->add('administrador')
            ->add('avaliador')
            ->add('resultado')
            ->add('convocacao')
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
            'data_class' => FrigaEditalUsuario::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'friga_usuario';
    }
}
