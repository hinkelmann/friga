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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

class FrigaEditalExportadorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('configuracao', CheckboxType::class, [
                'label' => 'Configurações do edital',
                'mapped' => false,
                'required' => false,
            ])
            ->add('arquivo', CheckboxType::class, [
                'label' => 'Arquivos',
                'mapped' => false,
                'required' => false,
            ])
            ->add('termo', CheckboxType::class, [
                'label' => 'Termo de banca',
                'mapped' => false,
                'required' => false,
            ])
            ->add('vaga', CheckboxType::class, [
                'label' => 'Cadastro de vagas',
                'mapped' => false,
                'required' => false,
            ])
            ->add('lista', CheckboxType::class, [
                'label' => 'Listas de classificação',
                'mapped' => false,
                'required' => false,
            ])
            ->add('etapa', CheckboxType::class, [
                'label' => 'Etapas',
                'mapped' => false,
                'required' => false,
            ])
            ->add('pontuacao', CheckboxType::class, [
                'label' => 'Tabela de pontuação',
                'mapped' => false,
                'required' => false,
            ])
            ->add('resultado', CheckboxType::class, [
                'label' => 'Configuração de resultados',
                'mapped' => false,
                'required' => false,
            ])
            ->add('desempate', CheckboxType::class, [
                'label' => 'Critério de desempate',
                'mapped' => false,
                'required' => false,
            ]);
    }

    public function getBlockPrefix()
    {
        return 'friga_edital_exportador';
    }
}
