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

namespace Nte\UsuarioBundle\Form;

use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalUsuario;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricao;
use Nte\UsuarioBundle\Entity\ImpedimentoDeclaracao;
use Nte\UsuarioBundle\Entity\Usuario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeclaracaoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var FrigaEditalUsuario $data */
        $data = $options['data'];

        /** @var Usuario $u */
        $u = $options['user'];

        $builder
            ->add('termoCompromisso', ChoiceType::class, [
                'expanded' => true,
                //'data'=>$data->isTermoCompromisso(),
                // 'empty_data'=>1,
                'label' => 'Declaração de Impedimento ou não Impedimento',
                'choices' => [
                    'Declaro que não me enquadro em nenhuma das condições de impedimento ou suspeição para atuar na banca' => true,
                    'Declaro que estou impedido(a) de participar da banca' => false,
                ],
            ]);

        /** @var FrigaInscricao $item */
        foreach ($data->getIdEdital()->getInscricaoValida() as $item) {
            /** @var false|ImpedimentoDeclaracao $impedimento */
            $impedimento = $item->getIdImpedimentoDeclaracao($u)->first();
            $valor = 0;
            if ($impedimento and \array_key_exists($impedimento->getJustificativa(), $this->justificativa())) {
                $valor = $this->justificativa()[$impedimento->getJustificativa()];
            }
            $builder
                ->add('inscricao__' . $item->getUuid(), ChoiceType::class, [
                    // 'expanded' => false,
                    'data' => $valor,
                    'mapped' => false,
                    'required' => false,
                    'label' => $item->getNome(),
                    'choices' => $this->justificativa(),
                    'attr' => [
                        'data-id' => $item->getId(),
                    ],
                ]);
        }
    }

    public function justificativa()
    {
        return [
            'Não impedido' => 0, 'Cônjuge de candidato(a) ou companheiro(a), mesmo que divorciado ou separado judicialmente' => 1, 'Ascendente ou descendente de candidato(a), até segundo grau, ou colateral até o quarto grau, seja o parentesco por consanguinidade, afinidade ou adoção' => 2, 'Autoridade ou servidor que tenha amizade íntima ou inimizade notória com algum dos interessados ou com os respectivos cônjuges, companheiros, parentes e afins até o terceiro grau' => 3, 'Outras situações de impedimento ou suspeição previstas na legislação vigente' => 4,
        ];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FrigaEditalUsuario::class,
            'allow_extra_fields' => true,
            'user' => null,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'friga_usuario';
    }
}
