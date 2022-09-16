<?php

namespace Nte\Aplicacao\FrigaBundle\Form;

use Doctrine\ORM\EntityRepository;
use Nte\Aplicacao\FrigaBundle\Entity\CAPESAreaConhecimento;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalCargo;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalEtapa;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalPontuacao;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalUsuario;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class CAPESAreaConhecimentoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('area', TextType::class, [
            'label' => 'Area',
            'attr' => [
                'class' => 'form-control',
                'placeholder' => 'EX: LÓGICA MATEMÁTICA'
            ]
        ])->add('areaMestre', TextType::class, [
                'label' => 'Sub area',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => '0000001'
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
            'data_class' => CAPESAreaConhecimento::class
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
