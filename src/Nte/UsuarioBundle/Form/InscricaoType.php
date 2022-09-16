<?php

namespace Nte\UsuarioBundle\Form;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Nte\Aplicacao\FrigaBundle\Entity\CAPESAreaConhecimento;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEdital;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalCargo;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalCota;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalPontuacaoCategoria;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricao;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalPontuacao;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InscricaoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        /** @var EntityManager|null $em */
        $em = $options['entityManager'];


        /** @var ArrayCollection $categorias */
        $categorias = new ArrayCollection($em->getRepository(FrigaEdital::class)
            ->getCategoriaPontuacaoInscricao($options['data']->getIdEdital()));

        /** @var FrigaInscricao $data */
        $data = $options['data'];

        /** @var FrigaEdital $edital */
        $edital = $data->getIdEdital();

        $builder
            ->add('nome', TextType::class, [
                'label' => 'Nome Completo',
                'attr' => [
                    'placeholder' => "Nome completo, sem abreviações",
                    'minlength' => 5,
                    'data-label' => 'Nome Completo',
                    'data-msg-minlength' => "Por favor, digite seu nome completo evitando abreviações.",
                ]
            ])
            ->add('dataNascimento', DateType::class, [
                    'widget' => 'single_text',
                    'label' => 'Data de Nascimento',
                    'html5' => false,
                    'format' => 'dd/MM/yyyy',
                    'attr' => [
                        'data-plugin-datepicker' => true,
                        'data-label' => 'Data de Nascimento',
                        'class' => 'datepicker',
                        'data-plugin-options' => json_encode(["language" => "pt-BR"]),
                        'data-date-format' => 'dd/mm/yyyy',
                        'placeholder' => date('d/m/Y')
                    ]
                ]
            )
            ->add('cpf', TextType::class, [
                'label' => 'CPF',
                'attr' => [
                    'data-label' => 'CPF',
                    'placeholder' => '000.0000.000-00',
                    'minlength' => 10,
                    'data-rule-cpf' => 'true',
                    'data-msg-minlength' => "Por favor, digite um número de CPF válido",
                ]
            ])
            ->add('rgNro', TextType::class, [
                'label' => 'RG',
                'attr' => [
                    'data-label' => 'Número do RG',
                    'placeholder' => '0000000',
                    'minlength' => 5,
                    'data-msg-minlength' => "Por favor, digite um número da carteira de identidade válido",
                ]
            ])
            ->add('rgOrgaoExpedidor', ChoiceType::class, [
                'label' => 'Órgão Expedidor',
                'choices' => $this->getSiglasOrgaoExpedidor(),
                'attr' => [
                    'data-label' => 'Orgão Expedidor do RG',
                    'data-placeholder' => 'Selecione'
                ]
            ])
            ->add('rgDataExpedicao', DateType::class, [
                    'widget' => 'single_text',
                    'label' => 'Data de Expedição',
                    'html5' => false,
                    'format' => 'dd/MM/yyyy',
                    'attr' => [
                        'data-plugin-datepicker' => true,
                        'data-label' => 'Data de Expedição do RG',
                        'class' => 'datepicker',
                        'data-plugin-options' => json_encode(["language" => "pt-BR"]),
                        'data-date-format' => 'dd/mm/yyyy',
                        'placeholder' => date('d/m/Y')
                    ]
                ]
            )
            ->add('rgUF', ChoiceType::class, [
                'label' => 'UF',
                'choices' => $this->getUF(),
                'attr' => [
                    'data-label' => 'UF do Orgão Expedidor',
                ]
            ])
            ->add('contatoTelefone1', TextType::class, [
                'label' => 'Celular',
                'attr' => [
                    'data-label' => 'Celular',
                    'placeholder' => '(00) 00000-0000',
                    'minlength' => 15,
                    'data-msg-minlength' => "Por favor, digite um número de celular válido",
                ]
            ])
            ->add('contatoTelefone2', TextType::class, [
                'label' => 'Telefone',
                'required' => false,
                'attr' => [
                    'data-label' => 'Telefone',
                    'placeholder' => '(00) 0000-0000',
                    'minlength' => 14,
                    'data-msg-minlength' => "Por favor, digite um número de telefone válido",
                ]
            ])
            ->add('contatoEmail', EmailType::class, [
                'label' => 'E-Mail',
                'attr' => [
                    'data-label' => 'e-Mail',
                    'placeholder' => 'Informe seu e-mail',
                    'data-email' => true,
                ]
            ]);
        if ($data->getIdEdital()->getModeloInscricao() == 1) {
            $capes = $em->getRepository(CAPESAreaConhecimento::class)->findAll();
            $tmp = [];
            $mestre = [];
            foreach ($capes as $c){
                $cod = $c->getId() . ' '.$c->getArea();
                $tmp[$c->getAreaMestre()][$cod] =$cod;
            }
            foreach ($capes as $c){
                $cod = $c->getId() . ' '.$c->getArea();
                if(array_key_exists($c->getId(), $tmp)){
                    $mestre[$cod] = $tmp[$c->getId()];
                }
            }

            $builder->add('projetoTitulo', TextType::class, [
                'label' => 'Título',
                'attr' => [
                    'data-label' => 'Título',
                    'placeholder' => 'Título do projeto',
                    'minlength' => 5,
                    'data-msg-minlength' => "Por favor, digite um título para o projeto",
                ]
            ])->add('projetoResumo', TextAreaType::class, [
                    'label' => 'Resumo',
                    'attr' => [
                        'data-label' => 'Resumo',
                        'class'=>'form-control',
                        'rows'=>'6',
                        'placeholder' => is_null($data->getIdEdital()->getInfo11())?'Escreva o resumo do projeto nesta área.' : $data->getIdEdital()->getInfo11(),
                        'data-msg-minlength' => "Por favor, digite um resumo para o projeto",
                    ]
                ])
                ->add('projetoAreaConhecimento', ChoiceType::class, [
                    'label' => 'Área do conhecimento',
                    'choices'=> $mestre,
                    'attr' => [
                        'data-label' => 'Área do conhecimento',
                    ],
                    'multiple'=>true,
                ])
            ;
            for($i=1; $i<= $data->getIdEdital()->getProjetoParticipanteMax(); $i++){
                $builder->add('projetoParticipanteNome'.$i, TextType::class, [
                    'label' => 'Nome completo',
                    'mapped'=>false,
                    'required' => false,
                    'attr' => [
                        'data-label' => 'Participante '.$i.' nome',
                        'placeholder' => 'Fulano de tal',
                        'minlength' => '3',
                    ]
                ])
                    ->add('projetoParticipanteEmail'.$i, EmailType::class, [
                        'label' => 'e-Mail',
                        'mapped'=>false,
                        'required' => false,
                        'attr' => [
                            'data-label' => 'Participante '.$i.' e-Mail',
                            'placeholder' => 'participantex@ufsm.br',
                        ]
                    ]);
            }
        }
        if ($data->getIdEdital()->getDoc13()) {
            $builder->add('enderecoCep', TextType::class, [
                'label' => 'CEP',
                'attr' => [
                    'data-label' => 'CEP',
                    'placeholder' => '000000-000',
                    'minlength' => 9,
                    'data-msg-minlength' => "Por favor, digite um número de CEP válido",
                ]
            ])
                ->add('enderecoLogradouro', TextType::class, [
                    'label' => 'Logradouro',
                    'attr' => [
                        'data-label' => 'Logradouro',
                        'placeholder' => 'Informe o nome da Rua/Av'
                    ]
                ])
                ->add('enderecoNumero', TextType::class, [
                    'label' => 'Número',
                    'attr' => [
                        'data-label' => 'Número',
                        'placeholder' => 'Informe o numero'
                    ]
                ])
                ->add('enderecoComplemento', TextType::class, [
                    'label' => 'Complemento',
                    'required' => false,
                    'attr' => [
                        'data-label' => 'Complemento',
                        'placeholder' => 'Apto'
                    ]
                ])
                ->add('enderecoBairro', TextType::class, [
                    'label' => 'Bairro',
                    'attr' => [
                        'data-label' => 'Bairro',
                        'placeholder' => 'Informe o nome do bairro'
                    ]
                ])
                ->add('enderecoMunicipio', TextType::class, [
                    'label' => 'Município',
                    'attr' => [
                        'data-label' => 'Município',
                        'placeholder' => 'Informe o nome da cidade'
                    ]
                ])
                ->add('enderecoUf', ChoiceType::class, [
                    'label' => 'UF',
                    'choices' => $this->getUF(),
                    'attr' => [
                        'data-label' => 'UF',
                    ]
                ]);
        }
        if ($data->getIdEdital()->getCargo()->count()) {
            $builder->add('idCargo', EntityType::class, [
                'class' => FrigaEditalCargo::class,
                'choice_label' => 'descricao',
                'query_builder' => function (EntityRepository $er) use ($data) {
                    return $er->createQueryBuilder('e')
                        ->where('e.idEdital = :edital')
                        ->setParameter('edital', $data->getIdEdital()->getId())
                        ->orderBy('e.descricao','asc')
                        ;
                },
                'label' => ($data->getIdEdital()->getCampoCargoTitulo() ?? "Em qual cargo você está interessado?"),
                'attr' => [
                    'data-plugin-select2',
                    'data-label' => ($data->getIdEdital()->getCampoCargoTitulo() ?? "Em qual cargo você está interessado?"),
                ],
            ]);
        }
        if ($data->getIdEdital()->getCota()->count()) {
            $builder->add('idCota', EntityType::class, [
                'class' => FrigaEditalCota::class,
                'choice_label' => 'descricao',
                'query_builder' => function (EntityRepository $er) use ($data) {
                    return $er->createQueryBuilder('e')
                        ->where('e.idEdital = :edital')
                        ->setParameter('edital', $data->getIdEdital()->getId())
                        ->orderBy('e.descricao','asc');
                },
                'label' => ($data->getIdEdital()->getCampoListaTitulo() ?? "Em qual situação você se encaixa?"),
                'attr' => [
                    'data-plugin-select2',
                    'data-label' => ($data->getIdEdital()->getCampoListaTitulo() ?? "Em qual situação você se encaixa?"),
                ],
            ]);
        }


        if ($categorias->count()) {
            /** @var FrigaEditalPontuacaoCategoria $categoria */
            foreach ($categorias as $categoria) {
                if ($categoria->isAgruparPontuacao()) {
                    $opt = [];
                    $upload = 0;
                    /** @var FrigaEditalPontuacao $pontuacao */
                    foreach ($categoria->getPontuacaoInscricao() as $pontuacao) {
                        if ($pontuacao->getUpload() > $upload) {
                            $upload = $pontuacao->getUpload();
                        }
                        $opt = array_merge($opt, $pontuacao->getFormChoiceOptions());
                    }
                    $builder->add("cat__" . $categoria->getId(), ChoiceType::class, [
                        'mapped' => false,
                        'label' => $categoria->getDescricao(),
                        'attr' => [
                            'data-label' => $categoria->getDescricao(),
                            'data-id' => $categoria->getId(),
                            'data-tipo' => "CATEGORIA",
                            'data-texto' => "tcat__" . $categoria->getId(),
                            'data-observacao' => $categoria->getExplicacao(),
                            'data-observacaoTexto' => $categoria->getExplicacaoTexto(),
                            'data-anexo' => $categoria->getPontuacaoTipo(),
                            'data-obrigatorio' => $categoria->getRequisito(),
                        ],
                        'choices' => $opt,
                    ]);
                } else {
                    /** @var FrigaEditalPontuacao $pontuacao */
                    foreach ($categoria->getPontuacaoInscricao() as $pontuacao) {
                        $builder->add("pt__" . $pontuacao->getId(), ChoiceType::class, [
                            'mapped' => false,
                            'label' => $pontuacao->getTitulo(),
                            'attr' => [
                                'data-label' => $pontuacao->getTitulo(),
                                'data-id' => $pontuacao->getId(),
                                'data-tipo' => "PONTUACAO",
                                'data-texto' => "tpt__" . $pontuacao->getId(),
                                'data-observacao' => $pontuacao->getExplicacao(),
                                'data-observacaoTexto' => $pontuacao->getExplicacaoTexto(),
                                'data-anexo' => $pontuacao->getPontuacaoTipo(),
                                'data-obrigatorio' => $pontuacao->getRequisito(),
                            ],
                            'choices' => $pontuacao->getFormChoiceOptions(),
                        ]);
                    }
                }
            }
        }

        if (strlen($options['data']->getIdEdital()->getInfo4())) {
            $info4 = explode("\n", $options['data']->getIdEdital()->getInfo4());
            foreach ($info4 as $i => $r) {
                if (strlen($r) > 1) {
                    $builder->add("requisito_" . $i, CheckboxType::class, [
                        'label' => $r,
                        'mapped' => false,
                        'required' => true,
                    ]);
                }
            }
        }
        if ($edital->getPermitirEstrangeiro()) {
            $builder->add('nacionalidade', TextType::class, [
                'label' => 'Nacionalidade',
                //'disabled'=>true,
                'required' => false,
                'attr' => [
                    'data-label' => 'Nacionalidade',
                    'placeholder' => 'Brasil',
                    'data-msg-minlength' => "Por favor, digite a sua nascionalidade",
                ]
            ])->add('estrangeiro', ChoiceType::class, [
                'label' => 'Estrangeiro',
                'required' => true,
                //'expanded'=>true,
                'choices' => [
                    "Não" => 0,
                    "Sim" => 1,
                ],
                'attr' => [
                    'data-label' => 'Estrangeiro',
                    'placeholder' => 'Brazil',
                    'data-msg-minlength' => "Por favor, digite a sua nascionalidade",
                ]
            ]);
        }

        if ($edital->getDoc0()) {
            $builder->add('teNro', TextType::class, [
                'label' => 'Número',
                'required' => (!$edital->getPermitirEstrangeiro() and $edital->getDoc0() == 2),
                'attr' => [
                    'data-label' => 'Número do Título de Eleitor',
                    'placeholder' => '0000000000',
                    'minlength' => 5,
                    'data-msg-minlength' => "Por favor, digite o número do título válido",
                ]
            ])->add('teDataExpedicao', DateType::class, [
                    'widget' => 'single_text',
                    'label' => 'Data de Expedição',
                    'required' => (!$edital->getPermitirEstrangeiro() and $edital->getDoc0() == 2),
                    'html5' => false,
                    'format' => 'dd/MM/yyyy',
                    'attr' => [
                        'data-plugin-datepicker' => true,
                        'data-label' => 'Data de Expedição do Título de Eleitor',
                        'class' => 'datepicker',
                        'data-plugin-options' => json_encode(["language" => "pt-BR"]),
                        'data-date-format' => 'dd/mm/yyyy',
                        'placeholder' => date('d/m/Y')
                    ]
                ]
            );
        }
        if ($edital->getDoc1()) {
            $builder->add('crNro', TextType::class, [
                'label' => 'Número',
                'required' => false,
                'attr' => [
                    'data-label' => 'Número do Certificado de Reservista',
                    'placeholder' => '0000000000',
                ]
            ])
                ->add('crDataExpedicao', DateType::class, [
                        'widget' => 'single_text',
                        'label' => 'Data de Expedição',
                        'required' => false,
                        'html5' => false,
                        'format' => 'dd/MM/yyyy',
                        'attr' => [
                            'data-plugin-datepicker' => true,
                            'data-label' => 'Data de Expedição do Certificado de Reservista',
                            'class' => 'datepicker',
                            'data-plugin-options' => json_encode(["language" => "pt-BR"]),
                            'data-date-format' => 'dd/mm/yyyy',
                            'placeholder' => date('d/m/Y')
                        ]
                    ]
                );;
        }
        if ($edital->getDoc2()) {
            $builder->add('passaporteNro', TextType::class, [
                'label' => 'Passaporte',
                'required' => ($edital->getDoc2() == 2),
                'attr' => [
                    'data-label' => 'Número do Passaporte',
                    'placeholder' => '0000000000',
                    'minlength' => 5,
                    'data-msg-minlength' => "Por favor, digite o número do passaporte válido",
                ]
            ]);
        }
        if ($edital->getDoc3()) {
            $builder->add('rneNro', TextType::class, [
                'label' => 'RNE',
                'required' => ($edital->getDoc3() == 2),
                'attr' => [
                    'data-label' => 'Número do Registro Nacional de Estrangeiro',
                    'placeholder' => '0000000000',
                    'minlength' => 5,
                    'data-msg-minlength' => "Por favor, digite o número do Registro Nacional de Estrangeiro válido",
                ]
            ])
                ->add('rneDataExpedicao', DateType::class, [
                        'widget' => 'single_text',
                        'label' => 'Data de Expedição',
                        'required' => ($edital->getDoc3() == 2),
                        'html5' => false,
                        'format' => 'dd/MM/yyyy',
                        'attr' => [
                            'data-plugin-datepicker' => true,
                            'data-label' => 'Data de Expedição do Registro Nacional de Estrangeiro',
                            'class' => 'datepicker',
                            'data-plugin-options' => json_encode(["language" => "pt-BR"]),
                            'data-date-format' => 'dd/mm/yyyy',
                            'placeholder' => date('d/m/Y')
                        ]
                    ]
                )
                ->add('rneDataValidade', DateType::class, [
                        'widget' => 'single_text',
                        'label' => 'Data de Validade',
                        'required' => ($edital->getDoc3() == 2),
                        'html5' => false,
                        'format' => 'dd/MM/yyyy',
                        'attr' => [
                            'data-plugin-datepicker' => true,
                            'data-label' => 'Data de Validade do Registro Nacional de Estrangeiro',
                            'class' => 'datepicker',
                            'data-plugin-options' => json_encode(["language" => "pt-BR"]),
                            'data-date-format' => 'dd/mm/yyyy',
                            'placeholder' => date('d/m/Y')
                        ]
                    ]
                );
        }
        if ($edital->getDoc4()) {
            $builder->add('matriculaNro', TextType::class, [
                'label' => 'Matrícula',
                'required' => ($edital->getDoc4() == 2),
                'attr' => [
                    'data-label' => 'Número da Matrícula',
                    'placeholder' => '00000000',
                    'minlength' => 3,
                    'data-msg-minlength' => "Por favor, digite o número da matrícula",
                ]
            ]);
        }
        if ($edital->getDoc5()) {
            $builder->add('certidaoNascimentoNro', TextType::class, [
                'label' => 'Número',
                'required' => ($edital->getDoc5() == 2),
                'attr' => [
                    'data-label' => 'Número da Certidão de Nascimento',
                    'placeholder' => '00000000',
                    'minlength' => 3,
                    'data-msg-minlength' => "Por favor, digite o número da Certidão de Nascimento",
                ]
            ]);
        }
        if ($edital->getDoc6()) {
            $builder->add('url0', UrlType::class, [
                'label' => 'URL',
                'required' => ($edital->getDoc6() == 2),
                'attr' => [
                    'data-label' => 'URL do Currículo Lattes',
                    'placeholder' => 'https://lattes.cnpq.br/fulano.de.tal',
                    'data-msg-minlength' => "Por favor, digite a url do lattes",
                ]
            ]);
        }
        if ($edital->getDoc10()) {
            $builder->add('url1', UrlType::class, [
                'label' => 'URL do vídeo de apresentação',
                'required' => ($edital->getDoc10() == 2),
                'attr' => [
                    'data-label' => 'URL do vídeo de apresentação',
                    'placeholder' => 'https://youtube.com/...',
                    'data-msg-minlength' => "Por favor, digite a URL válida",
                ]
            ]);
        }
        if ($edital->getDoc12()) {
            $builder->add('recebimentoBolsa', TextType::class, [
                'label' => 'Recebe algum tipo de bolsa?',
                'required' => ($edital->getDoc12() == 2),
                'attr' => [
                    'data-label' => 'Recebimento de Bolsa',
                    'placeholder' => 'CNPq, CAPES, FAPES, BCE, Bolsa PRAE, etc.. ',
                ]
            ]);
        }
        if ($edital->getDoc11()) {
            $builder->add('bancoInstituicao', ChoiceType::class, [
                'label' => 'Banco',
                'required' => true,
                //'expanded' => true,
                'choices' => [
                    " " => " ",
                    "1 - BANCO DO BRASIL" => "1 - BANCO DO BRASIL",
                    "341 - ITAÚ" => "341 - ITAÚ",
                    "104 - CAIXA ECONÔMICA FEDERAL" => "104 - CAIXA ECONÔMICA FEDERAL",
                    "33 - SANTANDER" => "33 - SANTANDER",
                    "70 - BRB - BANCO DE BRASÍLIA" => "70 - BRB - BANCO DE BRASÍLIA",
                    "77 - BANCO INTER" => "77 - BANCO INTER",
                    "237 - BRADESCO" => "237 - BRADESCO",
                    "745 - CITIBANK" => "745 - CITIBANK",
                    "422 - BANCO SAFRA" => "422 - BANCO SAFRA",
                    "399 - BANCO HSBC" => "399 - BANCO HSBC",
                    "756 - BANCOOB" => "756 - BANCOOB",
                    "212 - BANCO ORIGINAL" => "212 - BANCO ORIGINAL",
                    "2 - BANCO CENTRAL DO BRASIL" => "2 - BANCO CENTRAL DO BRASIL",
                    "3 - BANCO DA AMAZONIA S.A" => "3 - BANCO DA AMAZONIA S.A",
                    "4 - BANCO DO NORDESTE DO BRASIL S.A" => "4 - BANCO DO NORDESTE DO BRASIL S.A",
                    "7 - BANCO NAC DESENV. ECO. SOCIAL S.A" => "7 - BANCO NAC DESENV. ECO. SOCIAL S.A",
                    "8 - BANCO MERIDIONAL DO BRASIL" => "8 - BANCO MERIDIONAL DO BRASIL",
                    "20 - BANCO DO ESTADO DE ALAGOAS S.A" => "20 - BANCO DO ESTADO DE ALAGOAS S.A",
                    "21 - BANCO DO ESTADO DO ESPIRITO SANTO S.A" => "21 - BANCO DO ESTADO DO ESPIRITO SANTO S.A",
                    "22 - BANCO DE CREDITO REAL DE MINAS GERAIS SA" => "22 - BANCO DE CREDITO REAL DE MINAS GERAIS SA",
                    "24 - BANCO DO ESTADO DE PERNAMBUCO" => "24 - BANCO DO ESTADO DE PERNAMBUCO",
                    "25 - BANCO ALFA S/A" => "25 - BANCO ALFA S/A",
                    "26 - BANCO DO ESTADO DO ACRE S.A" => "26 - BANCO DO ESTADO DO ACRE S.A",
                    "27 - BANCO DO ESTADO DE SANTA CATARINA S.A" => "27 - BANCO DO ESTADO DE SANTA CATARINA S.A",
                    "28 - BANCO DO ESTADO DA BAHIA S.A" => "28 - BANCO DO ESTADO DA BAHIA S.A",
                    "29 - BANCO DO ESTADO DO RIO DE JANEIRO S.A" => "29 - BANCO DO ESTADO DO RIO DE JANEIRO S.A",
                    "30 - BANCO DO ESTADO DA PARAIBA S.A" => "30 - BANCO DO ESTADO DA PARAIBA S.A",
                    "31 - BANCO DO ESTADO DE GOIAS S.A" => "31 - BANCO DO ESTADO DE GOIAS S.A",
                    "32 - BANCO DO ESTADO DO MATO GROSSO S.A." => "32 - BANCO DO ESTADO DO MATO GROSSO S.A.",
                    "34 - BANCO DO ESADO DO AMAZONAS S.A" => "34 - BANCO DO ESADO DO AMAZONAS S.A",
                    "35 - BANCO DO ESTADO DO CEARA S.A" => "35 - BANCO DO ESTADO DO CEARA S.A",
                    "36 - BANCO DO ESTADO DO MARANHAO S.A" => "36 - BANCO DO ESTADO DO MARANHAO S.A",
                    "37 - BANCO DO ESTADO DO PARA S.A" => "37 - BANCO DO ESTADO DO PARA S.A",
                    "38 - BANCO DO ESTADO DO PARANA S.A" => "38 - BANCO DO ESTADO DO PARANA S.A",
                    "39 - BANCO DO ESTADO DO PIAUI S.A" => "39 - BANCO DO ESTADO DO PIAUI S.A",
                    "41 - BANCO DO ESTADO DO RIO GRANDE DO SUL S.A" => "41 - BANCO DO ESTADO DO RIO GRANDE DO SUL S.A",
                    "47 - BANCO DO ESTADO DE SERGIPE S.A" => "47 - BANCO DO ESTADO DE SERGIPE S.A",
                    "48 - BANCO DO ESTADO DE MINAS GERAIS S.A" => "48 - BANCO DO ESTADO DE MINAS GERAIS S.A",
                    "59 - BANCO DO ESTADO DE RONDONIA S.A" => "59 - BANCO DO ESTADO DE RONDONIA S.A",
                    "106 - BANCO ITABANCO S.A." => "106 - BANCO ITABANCO S.A.",
                    "107 - BANCO BBM S.A" => "107 - BANCO BBM S.A",
                    "109 - BANCO CREDIBANCO S.A" => "109 - BANCO CREDIBANCO S.A",
                    "116 - BANCO B.N.L DO BRASIL S.A" => "116 - BANCO B.N.L DO BRASIL S.A",
                    "148 - MULTI BANCO S.A" => "148 - MULTI BANCO S.A",
                    "151 - CAIXA ECONOMICA DO ESTADO DE SAO PAULO" => "151 - CAIXA ECONOMICA DO ESTADO DE SAO PAULO",
                    "153 - CAIXA ECONOMICA DO ESTADO DO R.G.SUL" => "153 - CAIXA ECONOMICA DO ESTADO DO R.G.SUL",
                    "165 - BANCO NORCHEM S.A" => "165 - BANCO NORCHEM S.A",
                    "166 - BANCO INTER-ATLANTICO S.A" => "166 - BANCO INTER-ATLANTICO S.A",
                    "168 - BANCO C.C.F. BRASIL S.A" => "168 - BANCO C.C.F. BRASIL S.A",
                    "175 - CONTINENTAL BANCO S.A" => "175 - CONTINENTAL BANCO S.A",
                    "184 - BBA - CREDITANSTALT S.A" => "184 - BBA - CREDITANSTALT S.A",
                    "197 - STONE PAGAMENTOS S.A." => "197 - STONE PAGAMENTOS S.A.",
                    "199 - BANCO FINANCIAL PORTUGUES" => "199 - BANCO FINANCIAL PORTUGUES",
                    "200 - BANCO FRICRISA AXELRUD S.A" => "200 - BANCO FRICRISA AXELRUD S.A",
                    "201 - BANCO AUGUSTA INDUSTRIA E COMERCIAL S.A" => "201 - BANCO AUGUSTA INDUSTRIA E COMERCIAL S.A",
                    "204 - BANCO S.R.L S.A" => "204 - BANCO S.R.L S.A",
                    "205 - BANCO SUL AMERICA S.A" => "205 - BANCO SUL AMERICA S.A",
                    "206 - BANCO MARTINELLI S.A" => "206 - BANCO MARTINELLI S.A",
                    "208 - BANCO PACTUAL S.A" => "208 - BANCO PACTUAL S.A",
                    "210 - DEUTSCH SUDAMERIKANICHE BANK AG" => "210 - DEUTSCH SUDAMERIKANICHE BANK AG",
                    "211 - BANCO SISTEMA S.A" => "211 - BANCO SISTEMA S.A",
                    "213 - BANCO ARBI S.A" => "213 - BANCO ARBI S.A",
                    "214 - BANCO DIBENS S.A" => "214 - BANCO DIBENS S.A",
                    "215 - BANCO AMERICA DO SUL S.A" => "215 - BANCO AMERICA DO SUL S.A",
                    "216 - BANCO REGIONAL MALCON S.A" => "216 - BANCO REGIONAL MALCON S.A",
                    "217 - BANCO AGROINVEST S.A" => "217 - BANCO AGROINVEST S.A",
                    "218 - BS2" => "218 - BS2",
                    "219 - BANCO DE CREDITO DE SAO PAULO S.A" => "219 - BANCO DE CREDITO DE SAO PAULO S.A",
                    "220 - BANCO CREFISUL" => "220 - BANCO CREFISUL",
                    "221 - BANCO GRAPHUS S.A" => "221 - BANCO GRAPHUS S.A",
                    "222 - BANCO AGF BRASIL S. A." => "222 - BANCO AGF BRASIL S. A.",
                    "223 - BANCO INTERUNION S.A" => "223 - BANCO INTERUNION S.A",
                    "224 - BANCO FIBRA S.A" => "224 - BANCO FIBRA S.A",
                    "225 - BANCO BRASCAN S.A" => "225 - BANCO BRASCAN S.A",
                    "228 - BANCO ICATU S.A" => "228 - BANCO ICATU S.A",
                    "229 - BANCO CRUZEIRO S.A" => "229 - BANCO CRUZEIRO S.A",
                    "230 - BANCO BANDEIRANTES S.A" => "230 - BANCO BANDEIRANTES S.A",
                    "231 - BANCO BOAVISTA S.A" => "231 - BANCO BOAVISTA S.A",
                    "232 - BANCO INTERPART S.A" => "232 - BANCO INTERPART S.A",
                    "233 - BANCO MAPPIN S.A" => "233 - BANCO MAPPIN S.A",
                    "234 - BANCO LAVRA S.A." => "234 - BANCO LAVRA S.A.",
                    "235 - BANCO LIBERAL S.A" => "235 - BANCO LIBERAL S.A",
                    "236 - BANCO CAMBIAL S.A" => "236 - BANCO CAMBIAL S.A",
                    "239 - BANCO BANCRED S.A" => "239 - BANCO BANCRED S.A",
                    "240 - BANCO DE CREDITO REAL DE MINAS GERAIS S." => "240 - BANCO DE CREDITO REAL DE MINAS GERAIS S.",
                    "241 - BANCO CLASSICO S.A" => "241 - BANCO CLASSICO S.A",
                    "242 - BANCO EUROINVEST S.A" => "242 - BANCO EUROINVEST S.A",
                    "243 - BANCO MÁXIMA S.A" => "243 - BANCO MÁXIMA S.A",
                    "244 - BANCO CIDADE S.A" => "244 - BANCO CIDADE S.A",
                    "245 - BANCO EMPRESARIAL S.A" => "245 - BANCO EMPRESARIAL S.A",
                    "246 - BANCO ABC ROMA S.A" => "246 - BANCO ABC ROMA S.A",
                    "247 - BANCO OMEGA S.A" => "247 - BANCO OMEGA S.A",
                    "249 - BANCO INVESTCRED S.A" => "249 - BANCO INVESTCRED S.A",
                    "250 - BANCO SCHAHIN CURY S.A" => "250 - BANCO SCHAHIN CURY S.A",
                    "251 - BANCO SAO JORGE S.A." => "251 - BANCO SAO JORGE S.A.",
                    "252 - BANCO FININVEST S.A" => "252 - BANCO FININVEST S.A",
                    "254 - BANCO PARANA BANCO S.A" => "254 - BANCO PARANA BANCO S.A",
                    "255 - MILBANCO S.A." => "255 - MILBANCO S.A.",
                    "256 - BANCO GULVINVEST S.A" => "256 - BANCO GULVINVEST S.A",
                    "258 - BANCO INDUSCRED S.A" => "258 - BANCO INDUSCRED S.A",
                    "261 - BANCO VARIG S.A" => "261 - BANCO VARIG S.A",
                    "262 - BANCO BOREAL S.A" => "262 - BANCO BOREAL S.A",
                    "263 - BANCO CACIQUE" => "263 - BANCO CACIQUE",
                    "264 - BANCO PERFORMANCE S.A" => "264 - BANCO PERFORMANCE S.A",
                    "265 - BANCO FATOR S.A" => "265 - BANCO FATOR S.A",
                    "266 - BANCO CEDULA S.A" => "266 - BANCO CEDULA S.A",
                    "267 - BANCO BBM-COM.C.IMOB.CFI S.A." => "267 - BANCO BBM-COM.C.IMOB.CFI S.A.",
                    "275 - BANCO REAL S.A" => "275 - BANCO REAL S.A",
                    "277 - BANCO PLANIBANC S.A" => "277 - BANCO PLANIBANC S.A",
                    "282 - BANCO BRASILEIRO COMERCIAL" => "282 - BANCO BRASILEIRO COMERCIAL",
                    "291 - BANCO DE CREDITO NACIONAL S.A" => "291 - BANCO DE CREDITO NACIONAL S.A",
                    "294 - BCR - BANCO DE CREDITO REAL S.A" => "294 - BCR - BANCO DE CREDITO REAL S.A",
                    "295 - BANCO CREDIPLAN S.A" => "295 - BANCO CREDIPLAN S.A",
                    "300 - BANCO DE LA NACION ARGENTINA S.A" => "300 - BANCO DE LA NACION ARGENTINA S.A",
                    "302 - BANCO DO PROGRESSO S.A" => "302 - BANCO DO PROGRESSO S.A",
                    "303 - BANCO HNF S.A." => "303 - BANCO HNF S.A.",
                    "304 - BANCO PONTUAL S.A" => "304 - BANCO PONTUAL S.A",
                    "308 - BANCO COMERCIAL BANCESA S.A." => "308 - BANCO COMERCIAL BANCESA S.A.",
                    "318 - BANCO B.M.G. S.A" => "318 - BANCO B.M.G. S.A",
                    "320 - BANCO INDUSTRIAL E COMERCIAL" => "320 - BANCO INDUSTRIAL E COMERCIAL",
                    "346 - BANCO FRANCES E BRASILEIRO S.A" => "346 - BANCO FRANCES E BRASILEIRO S.A",
                    "347 - BANCO SUDAMERIS BRASIL S.A" => "347 - BANCO SUDAMERIS BRASIL S.A",
                    "351 - BANCO BOZANO SIMONSEN S.A" => "351 - BANCO BOZANO SIMONSEN S.A",
                    "353 - BANCO GERAL DO COMERCIO S.A" => "353 - BANCO GERAL DO COMERCIO S.A",
                    "356 - ABN AMRO S.A" => "356 - ABN AMRO S.A",
                    "366 - BANCO SOGERAL S.A" => "366 - BANCO SOGERAL S.A",
                    "369 - PONTUAL" => "369 - PONTUAL",
                    "370 - BEAL - BANCO EUROPEU PARA AMERICA LATINA" => "370 - BEAL - BANCO EUROPEU PARA AMERICA LATINA",
                    "372 - BANCO ITAMARATI S.A" => "372 - BANCO ITAMARATI S.A",
                    "375 - BANCO FENICIA S.A" => "375 - BANCO FENICIA S.A",
                    "376 - CHASE MANHATTAN BANK S.A" => "376 - CHASE MANHATTAN BANK S.A",
                    "388 - BANCO MERCANTIL DE DESCONTOS S/A" => "388 - BANCO MERCANTIL DE DESCONTOS S/A",
                    "389 - BANCO MERCANTIL DO BRASIL S.A" => "389 - BANCO MERCANTIL DO BRASIL S.A",
                    "392 - BANCO MERCANTIL DE SAO PAULO S.A" => "392 - BANCO MERCANTIL DE SAO PAULO S.A",
                    "394 - BANCO B.M.C. S.A" => "394 - BANCO B.M.C. S.A",
                    "409 - UNIBANCO - UNIAO DOS BANCOS BRASILEIROS" => "409 - UNIBANCO - UNIAO DOS BANCOS BRASILEIROS",
                    "412 - BANCO NACIONAL DA BAHIA S.A" => "412 - BANCO NACIONAL DA BAHIA S.A",
                    "415 - BANCO NACIONAL S.A" => "415 - BANCO NACIONAL S.A",
                    "420 - BANCO NACIONAL DO NORTE S.A" => "420 - BANCO NACIONAL DO NORTE S.A",
                    "424 - BANCO NOROESTE S.A" => "424 - BANCO NOROESTE S.A",
                    "434 - BANCO FORTALEZA S.A" => "434 - BANCO FORTALEZA S.A",
                    "453 - BANCO RURAL S.A" => "453 - BANCO RURAL S.A",
                    "456 - BANCO TOKIO S.A" => "456 - BANCO TOKIO S.A",
                    "464 - BANCO SUMITOMO BRASILEIRO S.A" => "464 - BANCO SUMITOMO BRASILEIRO S.A",
                    "466 - BANCO MITSUBISHI BRASILEIRO S.A" => "466 - BANCO MITSUBISHI BRASILEIRO S.A",
                    "472 - LLOYDS BANK PLC" => "472 - LLOYDS BANK PLC",
                    "473 - BANCO FINANCIAL PORTUGUES S.A" => "473 - BANCO FINANCIAL PORTUGUES S.A",
                    "477 - CITIBANK N.A" => "477 - CITIBANK N.A",
                    "479 - BANCO DE BOSTON S.A" => "479 - BANCO DE BOSTON S.A",
                    "480 - BANCO PORTUGUES DO ATLANTICO-BRASIL S.A" => "480 - BANCO PORTUGUES DO ATLANTICO-BRASIL S.A",
                    "483 - BANCO AGRIMISA S.A." => "483 - BANCO AGRIMISA S.A.",
                    "487 - DEUTSCHE BANK S.A - BANCO ALEMAO" => "487 - DEUTSCHE BANK S.A - BANCO ALEMAO",
                    "488 - BANCO J. P. MORGAN S.A" => "488 - BANCO J. P. MORGAN S.A",
                    "489 - BANESTO BANCO URUGAUAY S.A" => "489 - BANESTO BANCO URUGAUAY S.A",
                    "492 - INTERNATIONALE NEDERLANDEN BANK N.V." => "492 - INTERNATIONALE NEDERLANDEN BANK N.V.",
                    "493 - BANCO UNION S.A.C.A" => "493 - BANCO UNION S.A.C.A",
                    "494 - BANCO LA REP. ORIENTAL DEL URUGUAY" => "494 - BANCO LA REP. ORIENTAL DEL URUGUAY",
                    "495 - BANCO LA PROVINCIA DE BUENOS AIRES" => "495 - BANCO LA PROVINCIA DE BUENOS AIRES",
                    "496 - BANCO EXTERIOR DE ESPANA S.A" => "496 - BANCO EXTERIOR DE ESPANA S.A",
                    "498 - CENTRO HISPANO BANCO" => "498 - CENTRO HISPANO BANCO",
                    "499 - BANCO IOCHPE S.A" => "499 - BANCO IOCHPE S.A",
                    "501 - BANCO BRASILEIRO IRAQUIANO S.A." => "501 - BANCO BRASILEIRO IRAQUIANO S.A.",
                    "502 - BANCO SANTANDER DE NEGOCIOS S.A" => "502 - BANCO SANTANDER DE NEGOCIOS S.A",
                    "504 - BANCO MULTIPLIC S.A" => "504 - BANCO MULTIPLIC S.A",
                    "505 - BANCO GARANTIA S.A" => "505 - BANCO GARANTIA S.A",
                    "600 - BANCO LUSO BRASILEIRO S.A" => "600 - BANCO LUSO BRASILEIRO S.A",
                    "601 - BFC BANCO S.A." => "601 - BFC BANCO S.A.",
                    "602 - BANCO PATENTE S.A" => "602 - BANCO PATENTE S.A",
                    "604 - BANCO INDUSTRIAL DO BRASIL S.A" => "604 - BANCO INDUSTRIAL DO BRASIL S.A",
                    "607 - BANCO SANTOS NEVES S.A" => "607 - BANCO SANTOS NEVES S.A",
                    "608 - BANCO OPEN S.A" => "608 - BANCO OPEN S.A",
                    "610 - BANCO V.R. S.A" => "610 - BANCO V.R. S.A",
                    "611 - BANCO PAULISTA S.A" => "611 - BANCO PAULISTA S.A",
                    "612 - BANCO GUANABARA S.A" => "612 - BANCO GUANABARA S.A",
                    "613 - BANCO PECUNIA S.A" => "613 - BANCO PECUNIA S.A",
                    "616 - BANCO INTERPACIFICO S.A" => "616 - BANCO INTERPACIFICO S.A",
                    "617 - BANCO INVESTOR S.A." => "617 - BANCO INVESTOR S.A.",
                    "618 - BANCO TENDENCIA S.A" => "618 - BANCO TENDENCIA S.A",
                    "621 - BANCO APLICAP S.A." => "621 - BANCO APLICAP S.A.",
                    "622 - BANCO DRACMA S.A" => "622 - BANCO DRACMA S.A",
                    "623 - BANCO PANAMERICANO S.A" => "623 - BANCO PANAMERICANO S.A",
                    "624 - BANCO GENERAL MOTORS S.A" => "624 - BANCO GENERAL MOTORS S.A",
                    "625 - BANCO ARAUCARIA S.A" => "625 - BANCO ARAUCARIA S.A",
                    "626 - BANCO FICSA S.A" => "626 - BANCO FICSA S.A",
                    "627 - BANCO DESTAK S.A" => "627 - BANCO DESTAK S.A",
                    "628 - BANCO CRITERIUM S.A" => "628 - BANCO CRITERIUM S.A",
                    "629 - BANCORP BANCO COML. E. DE INVESTMENTO" => "629 - BANCORP BANCO COML. E. DE INVESTMENTO",
                    "630 - BANCO INTERCAP S.A" => "630 - BANCO INTERCAP S.A",
                    "633 - BANCO REDIMENTO S.A" => "633 - BANCO REDIMENTO S.A",
                    "634 - BANCO TRIANGULO S.A" => "634 - BANCO TRIANGULO S.A",
                    "635 - BANCO DO ESTADO DO AMAPA S.A" => "635 - BANCO DO ESTADO DO AMAPA S.A",
                    "637 - BANCO SOFISA S.A" => "637 - BANCO SOFISA S.A",
                    "638 - BANCO PROSPER S.A" => "638 - BANCO PROSPER S.A",
                    "639 - BIG S.A. - BANCO IRMAOS GUIMARAES" => "639 - BIG S.A. - BANCO IRMAOS GUIMARAES",
                    "640 - BANCO DE CREDITO METROPOLITANO S.A" => "640 - BANCO DE CREDITO METROPOLITANO S.A",
                    "641 - BANCO EXCEL ECONOMICO S/A" => "641 - BANCO EXCEL ECONOMICO S/A",
                    "643 - BANCO SEGMENTO S.A" => "643 - BANCO SEGMENTO S.A",
                    "645 - BANCO DO ESTADO DE RORAIMA S.A" => "645 - BANCO DO ESTADO DE RORAIMA S.A",
                    "647 - BANCO MARKA S.A" => "647 - BANCO MARKA S.A",
                    "648 - BANCO ATLANTIS S.A" => "648 - BANCO ATLANTIS S.A",
                    "649 - BANCO DIMENSAO S.A" => "649 - BANCO DIMENSAO S.A",
                    "650 - BANCO PEBB S.A" => "650 - BANCO PEBB S.A",
                    "652 - BANCO FRANCES E BRASILEIRO SA" => "652 - BANCO FRANCES E BRASILEIRO SA",
                    "653 - BANCO INDUSVAL S.A" => "653 - BANCO INDUSVAL S.A",
                    "654 - BANCO A. J. RENNER S.A" => "654 - BANCO A. J. RENNER S.A",
                    "655 - BANCO VOTORANTIM S.A." => "655 - BANCO VOTORANTIM S.A.",
                    "656 - BANCO MATRIX S.A" => "656 - BANCO MATRIX S.A",
                    "657 - BANCO TECNICORP S.A" => "657 - BANCO TECNICORP S.A",
                    "658 - BANCO PORTO REAL S.A" => "658 - BANCO PORTO REAL S.A",
                    "702 - BANCO SANTOS S.A" => "702 - BANCO SANTOS S.A",
                    "705 - BANCO INVESTCORP S.A." => "705 - BANCO INVESTCORP S.A.",
                    "707 - BANCO DAYCOVAL S.A" => "707 - BANCO DAYCOVAL S.A",
                    "711 - BANCO VETOR S.A." => "711 - BANCO VETOR S.A.",
                    "713 - BANCO CINDAM S.A" => "713 - BANCO CINDAM S.A",
                    "715 - BANCO VEGA S.A" => "715 - BANCO VEGA S.A",
                    "718 - BANCO OPERADOR S.A" => "718 - BANCO OPERADOR S.A",
                    "719 - BANCO PRIMUS S.A" => "719 - BANCO PRIMUS S.A",
                    "720 - BANCO MAXINVEST S.A" => "720 - BANCO MAXINVEST S.A",
                    "721 - BANCO CREDIBEL S.A" => "721 - BANCO CREDIBEL S.A",
                    "722 - BANCO INTERIOR DE SAO PAULO S.A" => "722 - BANCO INTERIOR DE SAO PAULO S.A",
                    "724 - BANCO PORTO SEGURO S.A" => "724 - BANCO PORTO SEGURO S.A",
                    "725 - BANCO FINABANCO S.A" => "725 - BANCO FINABANCO S.A",
                    "726 - BANCO UNIVERSAL S.A" => "726 - BANCO UNIVERSAL S.A",
                    "728 - BANCO FITAL S.A" => "728 - BANCO FITAL S.A",
                    "729 - BANCO FONTE S.A" => "729 - BANCO FONTE S.A",
                    "730 - BANCO COMERCIAL PARAGUAYO S.A" => "730 - BANCO COMERCIAL PARAGUAYO S.A",
                    "731 - BANCO GNPP S.A." => "731 - BANCO GNPP S.A.",
                    "732 - BANCO PREMIER S.A." => "732 - BANCO PREMIER S.A.",
                    "733 - BANCO NACOES S.A." => "733 - BANCO NACOES S.A.",
                    "734 - BANCO GERDAU S.A" => "734 - BANCO GERDAU S.A",
                    "735 - BANCO NEON" => "735 - BANCO NEON",
                    "736 - BANCO UNITED S.A" => "736 - BANCO UNITED S.A",
                    "737 - THECA" => "737 - THECA",
                    "738 - MARADA" => "738 - MARADA",
                    "739 - BGN" => "739 - BGN",
                    "740 - BCN BARCLAYS" => "740 - BCN BARCLAYS",
                    "741 - BRP" => "741 - BRP",
                    "742 - EQUATORIAL" => "742 - EQUATORIAL",
                    "743 - BANCO EMBLEMA S.A" => "743 - BANCO EMBLEMA S.A",
                    "744 - THE FIRST NATIONAL BANK OF BOSTON" => "744 - THE FIRST NATIONAL BANK OF BOSTON",
                    "746 - MODAL S\A" => "746 - MODAL S\A",
                    "747 - RABOBANK DO BRASIL" => "747 - RABOBANK DO BRASIL",
                    "748 - SICREDI" => "748 - SICREDI",
                    "749 - BRMSANTIL SA" => "749 - BRMSANTIL SA",
                    "750 - BANCO REPUBLIC NATIONAL OF NEW YORK (BRA" => "750 - BANCO REPUBLIC NATIONAL OF NEW YORK (BRA",
                    "751 - DRESDNER BANK LATEINAMERIKA-BRASIL S/A" => "751 - DRESDNER BANK LATEINAMERIKA-BRASIL S/A",
                    "752 - BANCO BANQUE NATIONALE DE PARIS BRASIL S" => "752 - BANCO BANQUE NATIONALE DE PARIS BRASIL S",
                    "753 - BANCO COMERCIAL URUGUAI S.A." => "753 - BANCO COMERCIAL URUGUAI S.A.",
                    "755 - BANCO MERRILL LYNCH S.A" => "755 - BANCO MERRILL LYNCH S.A",
                    "757 - BANCO KEB DO BRASIL S.A." => "757 - BANCO KEB DO BRASIL S.A.",
                    "260 - Nu Pagamentos S.A" => "260 - Nu Pagamentos S.A",
                    "102 - XP INVESTIMENTOS" => "102 - XP INVESTIMENTOS",
                    "336 - BANCO C6 S.A." => "336 - BANCO C6 S.A.",
                    "290 - PagSeguro Internet S.A." => "290 - PagSeguro Internet S.A.",
                    "323 - MercadoPago.com Representações Ltda." => "323 - MercadoPago.com Representações Ltda.",
                    "332 - Acesso Soluções de Pagamento S.A." => "332 - Acesso Soluções de Pagamento S.A.",
                    "325 - Órama DTVM S.A." => "325 - Órama DTVM S.A.",
                    "85 - COOPERATIVA CENTRAL DE CREDITO - AILOS" => "85 - COOPERATIVA CENTRAL DE CREDITO - AILOS",
                    "125 - PLURAL S.A. BANCO MULTIPLO" => "125 - PLURAL S.A. BANCO MULTIPLO",
                ],
                'attr' => [
                    'data-label' => 'Banco',
                    'placeholder' => 'Banco',
                ]
            ])->add('bancoAgencia', TextType::class, [
                'label' => 'Agência',
                'required' => ($edital->getDoc11() == 2),
                'attr' => [
                    'data-label' => 'Agência',
                    'placeholder' => '0000',
                    'data-msg-minlength' => "Por favor, digite uma agência válida",
                ]
            ])->add('bancoConta', TextType::class, [
                'label' => 'Conta Corrente',
                'required' => ($edital->getDoc11() == 2),
                'attr' => [
                    'data-label' => 'Conta Corrente',
                    'placeholder' => '0000000-0',
                    'data-msg-minlength' => "Por favor, digite uma agência válida",
                ]
            ]);
        }
        if ($edital->getDoc7()) {
            $in = $edital->getInfo13()?:'Faça um  resumo ou texto de apresentação ';
            $builder->add('apresentacao', TextareaType::class, [
                'label' => 'Texto',
                'required' => ($edital->getDoc7() == 2),
                'attr' => [
                    'data-label' => 'Texto',
                    'class' => 'form-control',
                    'placeholder' => $in,
                    'rows' => 10,
                    'data-msg-minlength' => "Por favor, escreva um texto neste campo",
                ]
            ]);
        }
        if ($edital->getDoc8()) {
            $builder->add('matriculaCurso', TextType::class, [
                'label' => 'Curso',
                'required' => ($edital->getDoc8() == 2),
                'attr' => [
                    'data-label' => 'Nome do Curso',
                    'class' => 'form-control',
                    'placeholder' => 'Administração',
                    'data-msg-minlength' => "Por favor, escreva o nome do curso",
                ]
            ]);

        }
        if ($edital->getDoc14()) {
            $builder->add('matriculaIndiceDesempenho', TextType::class, [
                'label' => 'Indíce de Desempenho Acadêmico',
                'required' => ($edital->getDoc14() == 2),
                'attr' => [
                    'data-label' => 'Indíce de Desempenho Acadêmico',
                    'class' => 'form-control',
                    'placeholder' => '10.345',
                ]
            ]);

        }
        if ($edital->getDoc15()) {
            $builder->add('matriculaBeneficio', ChoiceType::class, [
                'label' => 'Tenho benefício (BSE) ?',
                'required' => ($edital->getDoc15() == 2),
               // 'expanded'=>true,
                'choices'=>[
                  ' Não'=>0,
                  ' Sim'=>1,
                ],
                'attr' => [
                    'data-label' => 'Benefício Socioeconômico (BSE)',
                   // 'class' => 'form-control',
                ]
            ]);

        }
        if ($edital->getDoc9()) {
            $builder->add('sexo', ChoiceType::class, [
                'label' => 'Sexo',
                'required' => ($edital->getDoc9() == 2),
                'choices' => [
                    "Feminino" => 0,
                    "Masculino" => 1,
                ],
                'attr' => [
                    'data-label' => 'Sexo',
                ]
            ]);

        }
        if ($edital->getDoc10()) {


        }


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
            'data_class' => FrigaInscricao::class,
            'allow_extra_fields' => true,
            'entityManager' => null,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'nte_inscricao';
    }

}
