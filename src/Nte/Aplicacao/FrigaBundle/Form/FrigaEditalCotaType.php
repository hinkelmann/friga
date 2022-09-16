<?php

namespace Nte\Aplicacao\FrigaBundle\Form;

use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalCota;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalPontuacao;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FrigaEditalCotaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var FrigaEditalPontuacao $data */
        $data = $options['data'];

        $builder->add('descricao', TextType::class, [
            'label' => 'Descrição do Cargo',
            'attr' => [
                'class' => 'form-control',
                'placeholder' => 'Campi Santa Maria/Analista pedagogico senior'
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
            'data_class' => FrigaEditalCota::class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'edital_cota';
    }


}
