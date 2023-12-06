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

use Doctrine\ORM\EntityManager;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalDesempate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FrigaEditalDesempateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var FrigaEditalDesempate $data */
        $data = $options['data'];

        /** @var EntityManager $em */
        $em = $options['em'];
        if ($data->getTipo()) {
        } else {
            $builder->add('idContexto', ChoiceType::class, [
                'label' => 'Contexto para Comparação',
                'expanded' => true,
                'choices' => $this->choices($em, $data),
            ]);
        }
        if (1 == $data->getTipo()) {
            $builder->add('sentido', ChoiceType::class, [
                'label' => 'Regra de Comparação ',
                'expanded' => true,
                'choices' => [
                    'A > B = 1° 20/06/1990  -  2° 10/05/1961' => 1,
                    'A < B = 1° 10/05/1961  -  2° 20/06/1990' => -1,
                ],
            ]);
        } else {
            $builder->add('sentido', ChoiceType::class, [
                'label' => 'Regra de Comparação ',
                'expanded' => true,
                'choices' => [
                    'A > B ' => 1,
                    'A < B ' => -1,
                ],
            ]);
        }
    }

    /**
     * @return array
     */
    private function choices(EntityManager $em, FrigaEditalDesempate $entidade)
    {
        // $x = new $entidade->getContexto();

        $itens = $em->getRepository($entidade->getContexto())
            ->findBy(['idEdital' => $entidade->getIdEdital()]);
        $tmp = [];

        /** @var $item */
        foreach ($itens as $item) {
            $tmp[$item->getTitulo()] = $item->getId();
        }

        return $tmp;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FrigaEditalDesempate::class,
            'em' => null,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'edital_desempate';
    }
}
