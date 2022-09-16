<?php

namespace Nte\UsuarioBundle\Form;

use Nte\Aplicacao\FrigaBundle\Entity\FrigaInscricaoProjetoParticipante;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InscricaoProjetoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('confirmacao', ChoiceType::class, [
            'label' => 'Confirmação',
            'choices' => [
                "Sim, confirmar minha inscrição neste projeto." => 1,
                "Não" => 0,
            ],
            'expanded'=>true,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => FrigaInscricaoProjetoParticipante::class,
            'allow_extra_fields' => true,
            'entityManager' => null,
        ));
    }

    public function getBlockPrefix()
    {
        return 'nte_inscricao';
    }
}
