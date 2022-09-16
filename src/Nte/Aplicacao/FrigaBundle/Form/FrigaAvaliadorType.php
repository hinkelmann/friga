<?php

namespace Nte\Aplicacao\FrigaBundle\Form;

use Doctrine\ORM\EntityRepository;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEdital;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalPontuacao;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalUsuario;
use Nte\UsuarioBundle\Entity\Usuario;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class  FrigaAvaliadorType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        /** @var FrigaEditalUsuario $data */
        $data = $options['data'];
        $builder
            ->add('idUsuario', EntityType::class, [
                'class' => Usuario::class,
                'label' => "Usuário",
                'choice_label' => 'nome',
                'query_builder' => function (EntityRepository $er) use ($data) {
                    return $er->createQueryBuilder('u')
                        ->leftJoin('u.idEditalUsuario', 'eu')
                        ->where("u.roles like '%ROLE_AVALIADOR%' or u.roles like '%ROLE_GERENCIAL%'")
                        ;
                },
                'by_reference' => false,
                'multiple' => false,
                'required' => false,

            ])
            ->add('administrador');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => FrigaEditalUsuario::class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'friga_usuario';
    }


}
