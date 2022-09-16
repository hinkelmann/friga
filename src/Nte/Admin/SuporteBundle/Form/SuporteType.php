<?php

namespace Nte\Admin\SuporteBundle\Form;

use Nte\Admin\SuporteBundle\Entity\Suporte;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SuporteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('assunto', TextType::class,[
            'attr'=>[
                'placeholder'=>'Assunto'
            ]
        ])
            ->add('descricao', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Escreva aqui a sua solicitação.',
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
            'data_class' => Suporte::class
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
