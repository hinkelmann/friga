<?php

namespace Nte\UsuarioBundle\Form;

use Nte\Aplicacao\CadastrosBundle\Entity\FrigaPolo;
use Nte\UsuarioBundle\Entity\Usuario;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UsuarioComumType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', null, array('label' => 'form.username', 'translation_domain' => 'FOSUserBundle'))
            ->add('email', EmailType::class, array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle'))
            ->add('nome', TextType::class, ['label' => 'Nome Completo', 'attr' => ['placeholder' => 'Informe o nome completo']])
            ->add('cpf', TextType::class, [
                'label' => 'CPF',
                'attr' => [
                    'placeholder' => '000.000.000-00',
                    'data-rule-cpf' => 'true',
                ]
            ])
            ->add('img', HiddenType::class)
            ->add('cpf', TextType::class, [
                'label' => 'CPF',
                'attr' => [
                    'placeholder' => '000.0000.000-00',
                    'minlength' => 10,
                    'data-rule-cpf' => 'true',
                    'data-msg-minlength' => "Por favor, digite um número de CPF válido",
                ]
            ])
            ->add('nome', TextType::class, [
                'label' => 'Nome Completo',
                'attr' => [
                    'placeholder' => "Nome completo, sem abreviações",
                    'minlength' => 5,
                    'data-msg-minlength' => "Por favor, digite seu nome completo evitando abreviações.",
                ]
            ])
            ->add('nomeSocial', TextType::class, [
                'label' => 'Nome Social',
                'required' => false,
                'attr' => [
                    'placeholder' => "Nome Social",
                    'minlength' => 5,
                    'data-msg-minlength' => "",
                ]
            ])
            ->add('dataNascimento', DateType::class, [
                    'widget' => 'single_text',
                    'label' => 'Data de Nascimento',
                    'html5' => false,
                    'format' => 'dd/MM/yyyy',
                    'attr' => [
                        'data-plugin-datepicker' => true,
                        'data-plugin-options' => json_encode(["language" => "pt-BR"]),
                        'data-date-format' => 'dd/mm/yyyy',
                        'placeholder' => date('d/m/Y')
                    ]
                ]
            )
            ->add('cpf', TextType::class, [
                'label' => 'CPF',
                'attr' => [
                    'placeholder' => '000.0000.000-00',
                    'minlength' => 10,
                    'data-rule-cpf' => 'true',
                    'data-msg-minlength' => "Por favor, digite um número de CPF válido",
                ]
            ])
            ->add('rgNro', TextType::class, [
                'label' => 'RG',
                'required' => false,
                'attr' => [
                    'placeholder' => '0000000',
                    'minlength' => 5,
                    'data-msg-minlength' => "Por favor, digite um número de RG válido",
                ]
            ])
            ->add('rgOrgaoExpedidor', ChoiceType::class, [
                'label' => 'Órgão Expedidor do RG',
                'choices' => $this->getSiglasOrgaoExpedidor(),
                'attr' => ['data-placeholder' => 'Selecione ']
            ])
            ->add('contatoTelefone1', TextType::class, [
                'label' => 'Celular',
                'attr' => [
                    'placeholder' => '(00) 00000-0000',
                    'minlength' => 15,
                    'data-msg-minlength' => "Por favor, digite um número de celular válido",
                ]
            ])
            ->add('contatoTelefone2', TextType::class, [
                'label' => 'Telefone',
                'required' => false,
                'attr' => [
                    'placeholder' => '(00) 0000-0000',
                    'minlength' => 14,
                    'data-msg-minlength' => "Por favor, digite um número de telefone válido",
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'E-Mail',
                'attr' => [
                    'placeholder' => 'Informe seu e-mail',
                    'data-email' => true,
                ]
            ])
            ->add('enderecoCep', TextType::class, [
                'label' => 'CEP',
                'attr' => [
                    'placeholder' => '000000-000',
                    'minlength' => 9,
                    'data-msg-minlength' => "Por favor, digite um número de CEP válido",
                ]
            ])
            ->add('enderecoLogradouro', TextType::class, [
                'label' => 'Endereço residencial',
                'attr' => [
                    'placeholder' => 'Informe o nome da Rua/Av'
                ]
            ])
            ->add('enderecoNumero', TextType::class, [
                'label' => 'Número',
                'attr' => [
                    'placeholder' => 'Informe o numero'
                ]
            ])
            ->add('enderecoComplemento', TextType::class, [
                'label' => 'Complemento',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Apto'
                ]
            ])
            ->add('enderecoBairro', TextType::class, [
                'label' => 'Bairro',
                'attr' => [
                    'placeholder' => 'Informe o nome do bairro'
                ]
            ])
            ->add('enderecoMunicipio', TextType::class, [
                'label' => 'Município',
                'attr' => [
                    'placeholder' => 'Informe o nome da cidade'
                ]
            ])
            ->add('enderecoUf', ChoiceType::class, [
                'label' => 'UF',
                'choices' => $this->getUF(),
            ]);;
    }

    private function getSiglasOrgaoExpedidor()
    {
        return [
            "SSP - Secretaria de Segurança Pública" => 'SSP',
            "CRA - Conselho Regional de Administração" => "CRA",
            "CRC - Conselho Regional de Contabilidade" => "CRC",
            "CREA - Conselho Regional de Engenharia Arquitetura e Agronomia" => "CREA",
            "CRM - Conselho Regional de Medicina" => "CRM",
            "CRMV - Conselho Regional de Medicina Veterinária" => "CRMV",
            "DETRAN - Departamento de Trânsito" => "DETRAN",
            "DPT - Departamento de Polícia Técnica Geral" => "DPT",
            "POF ou DPF - Polícia Federal" => "POF",
            "POM - Polícia Militar" => "POM",
            "OAB - Ordem dos Advogados do Brasil" => "OAB",
            "SECC - Secretaria de Estado da Casa Civil" => "SECC",
            "SDS - Secretaria de Defesa Social" => "SDS",
            "SESP - Secretaria de Estado da Segurança Pública" => "SESP",
            "SEJUSP - Secretaria de Estado de Justiça e Segurança Pública" => "SEJUSP",
            "SJS - Secretaria da Justiça e Segurança" => "SJS",
            "SJTC - Secretaria da Justiça do Trabalho e Cidadania" => "SJTC",
            "SJTS - Secretaria da Justiça do Trabalho e Segurança" => "SJTS",
            "SES ou EST - Carteira de Estrangeiro" => "SES",
            "Outro" => "OUTRO"
        ];
    }

    private function getUF()
    {
        return [
            "Rio Grande do Sul" => "RS",
            "Acre" => "AC",
            "Alagoas" => "AL",
            "Amapá" => "AP",
            "Amazonas" => "AM",
            "Bahia" => "BA",
            "Ceará" => "CE",
            "Distrito Federal" => "DF",
            "Espírito Santo" => "ES",
            "Goiás" => "GO",
            "Maranhão" => "MA",
            "Mato Grosso" => "MT",
            "Mato Grosso do Sul" => "MS",
            "Minas Gerais" => "MG",
            "Pará" => "A",
            "Paraíba" => "PB",
            "Paraná" => "PR",
            "Pernambuco" => "PE",
            "Piauí" => "PI",
            "Rio de Janeiro" => "RJ",
            "Rio Grande do Norte" => "RN",
            "Rondônia" => "RO",
            "Roraima" => "RR",
            "Santa Catarina" => "SC",
            "São Paulo" => "SP",
            "Sergipe" => "SE",
            "Tocantins" => "TO",
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Usuario::class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'nte_usuario';
    }
}
