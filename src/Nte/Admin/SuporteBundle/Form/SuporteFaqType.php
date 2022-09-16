<?php

namespace Nte\Admin\SuporteBundle\Form;

use Nte\Admin\SuporteBundle\Entity\Suporte;
use Nte\Admin\SuporteBundle\Entity\SuporteFaq;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SuporteFaqType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('pergunta', TextareaType::class, [
            'required' => false,
            'attr' => [
                'placeholder' => 'Escreva aqui a pergunta frequente.',
                'class' => 'form-control',
                'rows' => 3
            ]
        ])
            ->add('resposta', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Escreva aqui a resposta para sua pergunta',
                    'class' => 'form-control',
                    'rows' => 4
                ]
            ]);
    }
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => SuporteFaq::class
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
