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

use Nte\UsuarioBundle\Entity\Usuario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nome', TextType::class, [
                'attr' => [
                    'placeholder' => 'Nome Completo',
                    'class' => 'form-control m-input',
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'form.email',
                'translation_domain' => 'FOSUserBundle',
                'attr' => [
                    'placeholder' => 'e-Mail',
                    'class' => 'form-control m-input',
                ],
            ])
            ->add('username', null, [
                'label' => 'form.username',
                'translation_domain' => 'FOSUserBundle',
                'attr' => [
                    'placeholder' => 'usuário',
                    'class' => 'form-control m-input d-none',
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'options' => [
                    'translation_domain' => 'FOSUserBundle',
                    'attr' => [
                        'autocomplete' => 'new-password',
                    ],
                ],
                'first_options' => [
                    'label' => 'form.password',
                    'attr' => [
                        'placeholder' => 'Senha',
                        'class' => 'form-control m-input',
                    ],
                ],
                'second_options' => [
                    'label' => 'form.password_confirmation',
                    'attr' => [
                        'placeholder' => 'Confirme a senha',
                        'class' => 'form-control m-input m-login__form-input--last',
                    ],
                ],
                'invalid_message' => 'fos_user.password.mismatch',
            ])
            ->add('cpf', TextType::class, [
                'attr' => [
                    'placeholder' => '000.0000.000-00',
                    'minlength' => 10,
                    'data-rule-cpf' => 'true',
                    'data-msg-minlength' => 'Por favor, digite um número de CPF válido',
                    'class' => 'form-control m-input',
                ],
            ])
            ->add('contatoTelefone1', TextType::class, [
                'attr' => [
                    'placeholder' => '(55) 99999-9999',
                    'class' => 'form-control m-input',
                ],
            ]);
    }

    private function getSiglasOrgaoExpedidor()
    {
        return [
            'SSP - Secretaria de Segurança Pública' => 'SSP',
            'CRA - Conselho Regional de Administração' => 'CRA',
            'CRC - Conselho Regional de Contabilidade' => 'CRC',
            'CREA - Conselho Regional de Engenharia Arquitetura e Agronomia' => 'CREA',
            'CRM - Conselho Regional de Medicina' => 'CRM',
            'CRMV - Conselho Regional de Medicina Veterinária' => 'CRMV',
            'DETRAN - Departamento de Trânsito' => 'DETRAN',
            'DPT - Departamento de Polícia Técnica Geral' => 'DPT',
            'POF ou DPF - Polícia Federal' => 'POF',
            'POM - Polícia Militar' => 'POM',
            'OAB - Ordem dos Advogados do Brasil' => 'OAB',
            'SECC - Secretaria de Estado da Casa Civil' => 'SECC',
            'SDS - Secretaria de Defesa Social' => 'SDS',
            'SESP - Secretaria de Estado da Segurança Pública' => 'SESP',
            'SEJUSP - Secretaria de Estado de Justiça e Segurança Pública' => 'SEJUSP',
            'SJS - Secretaria da Justiça e Segurança' => 'SJS',
            'SJTC - Secretaria da Justiça do Trabalho e Cidadania' => 'SJTC',
            'SJTS - Secretaria da Justiça do Trabalho e Segurança' => 'SJTS',
            'SES ou EST - Carteira de Estrangeiro' => 'SES',
            'Outro' => 'OUTRO',
        ];
    }

    private function getUF()
    {
        return [
            'Rio Grande do Sul' => 'RS',
            'Acre' => 'AC',
            'Alagoas' => 'AL',
            'Amapá' => 'AP',
            'Amazonas' => 'AM',
            'Bahia' => 'BA',
            'Ceará' => 'CE',
            'Distrito Federal' => 'DF',
            'Espírito Santo' => 'ES',
            'Goiás' => 'GO',
            'Maranhão' => 'MA',
            'Mato Grosso' => 'MT',
            'Mato Grosso do Sul' => 'MS',
            'Minas Gerais' => 'MG',
            'Pará' => 'A',
            'Paraíba' => 'PB',
            'Paraná' => 'PR',
            'Pernambuco' => 'PE',
            'Piauí' => 'PI',
            'Rio de Janeiro' => 'RJ',
            'Rio Grande do Norte' => 'RN',
            'Rondônia' => 'RO',
            'Roraima' => 'RR',
            'Santa Catarina' => 'SC',
            'São Paulo' => 'SP',
            'Sergipe' => 'SE',
            'Tocantins' => 'TO',
        ];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Usuario::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'fos_user_registration';
    }
}
