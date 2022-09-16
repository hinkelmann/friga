<?php

namespace Nte\Aplicacao\FrigaBundle\Form;

use Doctrine\ORM\EntityRepository;
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

class FrigaEditalCargoType extends AbstractType
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
            ->add('idEditalUsuario', EntityType::class, [
                'class' => FrigaEditalUsuario::class,
                'label' => "Avaliador",
                'choice_label' => 'idUsuario.nome',
                'query_builder' => function (EntityRepository $er) use ($data) {
                    return $er->createQueryBuilder('e')
                        ->where('e.idEdital = :edital')
                        ->setParameter('edital', $data->getIdEdital()->getId());
                },
                'by_reference' => false,
                'multiple' => true,
                'required' => false,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => FrigaEditalCargo::class
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
