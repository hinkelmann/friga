<?php

namespace Nte\Aplicacao\FrigaBundle\Form;

use Doctrine\ORM\EntityRepository;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalCargo;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalPontuacao;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalPontuacaoCategoria;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FrigaEditalPontuacaoCategoriaPesoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var FrigaEditalPontuacao $data */
        $data = $options['data'];
        $builder
            ->add('descricao', TextType::class)
            ->add('valorMaximo', NumberType::class, [
                'label' => 'Peso',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => '0'
                ]
            ])
            ->add('valorTexto', TextType::class, [
                'label' => 'Valor texto',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Pontos'
                ]
            ])
        ;

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => FrigaEditalPontuacaoCategoria::class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'edital_cargo';
    }


}
