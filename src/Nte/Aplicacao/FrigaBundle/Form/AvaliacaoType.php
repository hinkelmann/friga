<?php

namespace Nte\Aplicacao\FrigaBundle\Form;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEdital;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalCargo;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalCota;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalEtapa;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalPontuacaoCategoria;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricao;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalPontuacao;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricaoPontuacao;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricaoPontuacaoAvaliacao;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricaoRecurso;
use Nte\UsuarioBundle\Entity\Usuario;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\OptionsResolver\OptionsResolver;

class  AvaliacaoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        /** @var EntityManager|null $em */
        $em = $options['em'];


        /** @var FrigaInscricao $inscricao */
        $inscricao = $options['inscricao'];

        /** @var FrigaEditalEtapa $etapa */
        $etapa = $options['etapa'];

        /** @var ArrayCollection $feedback */
        $feedback = $options['feedback'];

        /** @var Usuario $avaliador */
        $avaliador = $options['usuario'];

        /** @var ArrayCollection $avaliacao */
        $avaliacao = $options['avaliacao'];


        /** @var ArrayCollection $recurso */
        $recursos = $options['recurso'];


        /** @var ArrayCollection $categorias */
        $categorias = new ArrayCollection(
            $em->getRepository(FrigaEdital::class)
                ->getCategoriaPontuacaoEtapa($etapa)
        );

        //$pontuacaoAvaliador = $inscricao->getAvalicaoAvaliador($avaliador, $etapa);
        $avaliacao = $em->getRepository(FrigaInscricaoPontuacaoAvaliacao::class)
            ->createQueryBuilder('a')
            ->Where('a.idEtapa = :etapa')
            ->andWhere('a.idInscricao   = :inscricao')
            ->setParameter('etapa', $etapa)
            ->setParameter('inscricao', $inscricao)
            ->getQuery()->getResult();
        $avaliacao = new ArrayCollection($avaliacao);


        if ($categorias->count()) {
            /** @var FrigaEditalPontuacaoCategoria $categoria */
            foreach ($categorias as $categoria) {
                if ($categoria->isAgruparPontuacao()) {
                    if ($etapa->getPontuacaoRelativaUsuario() and $categoria->getPontuacaoAuto()) {
                        $ptc = $inscricao->getPontuacaoCategoriaItem($categoria->getId());
                        /*if (!$ptc) {
                            $pontuacaoAC = new \stdClass();
                            $pontuacaoAC->pontos = 0;
                            $pontuacaoAC->anexo = false;
                        } else {

                        }*/
                        $pontuacaoAC = $this->avaliacaoAnterior($etapa, $inscricao, $avaliador, $ptc, $avaliacao, null, $categoria);
                    } else {
                        $pontuacaoAC = $this->avaliacaoAnterior($etapa, $inscricao, $avaliador, null, $avaliacao, null, $categoria);
                    }


                    $opt = [];

                    /** @var FrigaEditalPontuacao $pontuacao */
                    foreach ($categoria->getPontuacao() as $pontuacao) {
                        $opt = array_merge($opt, $pontuacao->getFormChoiceOptions());
                    }

                    $builder
                        ->add("cat__" . $categoria->getId(), ChoiceType::class, [
                            'mapped' => false,
                            'required' => false,
                            'label' => $categoria->getDescricao(),
                            'attr' => [
                                'data-label' => $categoria->getDescricao(),
                                'data-id' => $categoria->getId(),
                                'data-tipo' => "CATEGORIA",
                                'data-observacao' => $categoria->getExplicacao(),
                                'data-anexo' => $categoria->getPontuacaoTipo(),
                                'data-pontuacaoauto'=>$categoria->getPontuacaoAuto(),
                            ],
                            'choices' => $opt,
                            'data' => $pontuacaoAC->pontos,
                        ]);
                    if ($categoria->getPontuacaoTipo()) {
                        $builder->add('catanexo__' . $categoria->getId(), ChoiceType::class, [
                            'label' => 'null',
                            'mapped' => false,
                            'expanded' => true,
                            'required' => true,
                            'data' => $pontuacaoAC->anexo,
                            'choices' => [
                                "Não aceitar pontuação" => 0,
                                "Aceitar pontuação" => 1,
                            ],
                            'attr' => [
                                'checked' => $pontuacaoAC->anexo,
                                'data-tipo' => 'ANEXO',
                                'data-tipo2' => "CATEGORIA",
                                'data-anexo' => true,
                                'data-id' => $categoria->getId(),
                                "data-toggle" => "toggle",
                                "data-onstyle" => "success",
                                "data-offstyle" => "default",
                                "data-on" => "Aceito",
                                "data-off" => "Não Aceito",
                                "data-width" => "200",
                            ]
                        ]);
                    }

                } else {

                    /** @var FrigaEditalPontuacao $pontuacao */
                    foreach ($categoria->getPontuacao() as $pontuacao) {


                        if ($etapa->getPontuacaoRelativaUsuario() && $pontuacao->getPontuacaoAuto()) {
                            $ptc = $inscricao->getPontuacaoItem($pontuacao->getId());
                            if (!$ptc) {
                                $pontuacaoAC = new \stdClass();
                                $pontuacaoAC->pontos = 0;
                                $pontuacaoAC->anexo = false;
                            } else {

                                $pontuacaoAC = $this->avaliacaoAnterior($etapa, $inscricao, $avaliador, $ptc, $avaliacao, $pontuacao);
                            }
                        } else {
                            $pontuacaoAC = $this->avaliacaoAnterior($etapa, $inscricao, $avaliador, null, $avaliacao, $pontuacao);
                        }


                        $builder->add("pt__" . $pontuacao->getId(), ChoiceType::class, [
                            'mapped' => false,
                            'required' => false,
                            'label' => $pontuacao->getTitulo(),
                            'attr' => [
                                'data-label' => $pontuacao->getTitulo(),
                                'data-id' => $pontuacao->getId(),
                                'data-tipo' => "PONTUACAO",
                                'data-observacao' => $pontuacao->getExplicacao(),
                                'data-anexo' => $pontuacao->getUpload(),
                                'data-pontuacaoauto'=>$pontuacao->getPontuacaoAuto(),

                            ],
                            'choices' => $pontuacao->getFormChoiceOptions(),
                            'data' => $pontuacaoAC->pontos
                        ]);
                        if ($pontuacao->getPontuacaoTipo()) {
                            $builder->add('ptanexo__' . $pontuacao->getId(), ChoiceType::class, [
                                'label' => 'null',
                                'mapped' => false,
                                'expanded' => true,
                                'required' => true,
                                'choices' => [
                                    "Não aceitar pontuação" => false,
                                    "Aceitar pontuação" => true,
                                ],
                                'data' => $pontuacaoAC->anexo,
                                'attr' => [
                                    'checked' => $pontuacaoAC->anexo,
                                    'data-tipo' => 'ANEXO',
                                    'data-tipo2' => "PONTUACAO",
                                    'data-anexo' => true,
                                    'data-id' => $pontuacao->getId(),
                                    "data-toggle" => "toggle",
                                    "data-onstyle" => "success",
                                    "data-offstyle" => "default",
                                    "data-on" => "Aceito",
                                    "data-off" => "Não Aceito",
                                    "data-width" => "200",
                                ]
                            ]);
                        }
                    }
                }
            }
        }
        if ($recursos->count()) {
            /** @var FrigaInscricaoRecurso $recurso */
            foreach ($recursos as $recurso) {
                $builder->add("recurso__" . $recurso->getId(), TextareaType::class, [
                    'mapped' => false,
                    'required' => true,
                    'label' => "Parecer descritivo",
                    'attr' => [
                        'rows' => 5,
                        'class' => 'form-control',
                        'data-id-recurso' => $recurso->getId(),
                        'data-tipo' => "RECURSO",
                        'data-observacao' => $recurso->getJustificativa(),
                        'placeholder' => ""
                    ],
                    'data' => $recurso->getDesfecho(),
                ]);
                $builder->add("recursosituacao__" . $recurso->getId(), ChoiceType::class, [
                    'mapped' => false,
                    'expanded' => true,
                    'required' => true,
                    'label' => 'Decisão',
                    'data' => $recurso->getIdSituacao(),
                    'choices' => [
                        'Indeferir Recurso' => -1,
                        'Deferir Recurso' => 1,
                    ],
                    'attr' => [
                        'data-id-recurso' => $recurso->getId(),
                        'data-tipo' => "RECURSOSITUACAO",

                    ]
                ]);
            }
        } else {
            $builder->add('feedback', TextareaType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Observações',
                'data' => $feedback,
                'attr' => [
                    'rows' => 5,
                    'class' => 'form-control',
                    'placeholder' =>
                        'Utilize este campo para escrever um feedback ao candidato. Este feedback estará disponível apenas para o candidato '
                ]
            ]);
        }

        $situacao = null;
        if ($recursos->count()) {
            if ($inscricao->getIdSituacaoAnterior() == 5) {
                $situacao = $inscricao->getIdSituacao();
            } else {
                $situacao = $inscricao->getIdSituacaoAnterior();
            }
        } else {
            $situacao = $inscricao->getIdSituacao();
        }
        $builder->add('situacao', ChoiceType::class, [
            'mapped' => false,
            'expanded' => true,
            'label' => 'Situação da Inscrição',
            'data' => $situacao,
            'choices' => $this->situacaoInscricao($etapa)
        ]);
    }

    /**
     * @param FrigaEditalEtapa $etapa
     * @return array
     */
    private function situacaoInscricao(FrigaEditalEtapa $etapa)
    {
        if ($etapa->getPontuacaoRelativa()->count()) {
            $tmp = [
                'Não Homologar Inscrição' => 1,
                'Homologar Inscrição' => 2,
            ];
        } else {
            $tmp = [
                'Desclassificar' => 3,
                'Classificar' => 2,
            ];
        }
        return $tmp;
    }


    /**
     * Recupera a pontuação anterior
     *
     * @param FrigaEditalEtapa $etapa
     * @param FrigaInscricao $inscricao
     * @param Usuario $avaliador
     * @param FrigaInscricaoPontuacao|null $ptc
     * @param ArrayCollection $avaliacao
     * @param FrigaEditalPontuacao|null $item
     * @param \Nte\Aplicacao\FrigaBundle\Model\FrigaEditalPontuacaoCategoria $categoria
     * @return \stdClass
     */
    private function avaliacaoAnterior($etapa, $inscricao, $avaliador, $ptc, $avaliacao, $item = null, $categoria = null)
    {
        $obj = new \stdClass();
        $obj->pontos = 0;
        $obj->anexo = false;
        $pontuacao = $avaliacao->filter(function (FrigaInscricaoPontuacaoAvaliacao $a)
        use ($inscricao, $etapa, $item, $categoria, $avaliador) {
            $ua = $etapa->isPontuacaoMultipla() ? ($a->getIdAvaliador() == $avaliador->getId()) : true;
            if ($categoria == null) {
                return $a->getIdInscricao()->getId() == $inscricao->getId()
                    and $a->getIdEtapa()->getId() == $etapa->getId()
                    and $a->getIdEditalPontuacao() != null
                    and $a->getIdEditalPontuacao()->getId() == $item->getId()
                    and $ua;
            } else {
                return $a->getIdInscricao()->getId() == $inscricao->getId()
                    and $a->getIdEtapa()->getId() == $etapa->getId()
                    and $a->getIdEditalPontuacaoCategoria() != null
                    and $a->getIdEditalPontuacaoCategoria()->getId() == $categoria->getId()
                    and $ua;
            }
        });
        //dump($pontuacao);

        if ($pontuacao->count()) {
            if ($categoria != null) {
                if ($pontuacao->first()->getIdEditalPontuacao()) {
                    $obj->pontos = $pontuacao->first()->getIdEditalPontuacao()->getId();
                }
            } else {
                $obj->pontos = $pontuacao->first()->getValorAvaliador() + 0;
            }
            $obj->anexo = $pontuacao->first()->isConsiderado();
        } else {
            if ($ptc) {

                $obj->pontos = $categoria != null ? $ptc->getIdEditalPontuacao()->getId() : $ptc->getValorInformado() + 0;
            }
        }
        return $obj;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => FrigaInscricao::class,
            'allow_extra_fields' => true,
            'em' => null,
            'etapa' => null,
            'inscricao' => null,
            'usuario' => null,
            'avaliacao' => null,
            'feedback' => null,
            'recurso' => false,
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
