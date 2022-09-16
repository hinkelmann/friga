<?php

namespace Nte\Admin\SuporteBundle\Form;
use Nte\Admin\SuporteBundle\Entity\SuporteIteracao;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SuporteIteracaoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('resposta', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Escreva aqui a sua resposta.',
                    'class' => 'form-control',
                    'rows' => 3
                ]
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => SuporteIteracao::class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'suporte';
    }


}
