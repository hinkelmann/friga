<?php

namespace Nte\Aplicacao\FrigaBundle\Form;

use Doctrine\ORM\EntityRepository;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEdital;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalCategoria;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FrigaEditalExportadorType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('configuracao', CheckboxType::class, [
                'label'=>"Configurações do edital",
                'mapped'=>false,
                'required'=>false,
            ])
            ->add('arquivo', CheckboxType::class, [
                'label'=>"Arquivos",
                'mapped'=>false,
                'required'=>false,
            ])
            ->add('termo', CheckboxType::class, [
                'label'=>"Termo de banca",
                'mapped'=>false,
                'required'=>false,
            ])
            ->add('vaga', CheckboxType::class, [
                'label'=>"Cadastro de vagas",
                'mapped'=>false,
                'required'=>false,
            ])
            ->add('lista', CheckboxType::class, [
                'label'=>"Listas de classificação",
                'mapped'=>false,
                'required'=>false,
            ])
            ->add('etapa', CheckboxType::class, [
                'label'=>"Etapas",
                'mapped'=>false,
                'required'=>false,
            ])
            ->add('pontuacao', CheckboxType::class, [
                'label'=>"Tabela de pontuação",
                'mapped'=>false,
                'required'=>false,
            ])
            ->add('resultado', CheckboxType::class, [
                'label'=>"Configuração de resultados",
                'mapped'=>false,
                'required'=>false,
            ])
            ->add('desempate', CheckboxType::class, [
                'label'=>"Critério de desempate",
                'mapped'=>false,
                'required'=>false,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'friga_edital_exportador';
    }


}
