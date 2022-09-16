<?php

namespace Nte\UsuarioBundle\Form\Type;

use Nte\Aplicacao\CadastrosBundle\Entity\FrigaPolo;
use Nte\UsuarioBundle\Entity\Usuario;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

class RegistrationFormType extends BaseType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('nome',TextType::class,['label'=>'Nome Completo','attr'=>['placeholder'=>'Informe o nome completo']])
            ->add('cpf', TextType::class,['label'=>'CPF','attr'=>['placeholder'=>'000.000.000-00']])
            ->add('telefone', TextType::class,['label'=>'Telefone','attr'=>['placeholder'=>'(00) 00000-0000']])
            ->add('socialWhatsapp', TextType::class,['label'=>'WhatsApp','attr'=>['placeholder'=>'(00) 00000-0000']])
            ->add('plainPassword',  RepeatedType::class, array(
                'type' => PasswordType::class,
                'options' => array('translation_domain' => 'FOSUserBundle'),
                'first_options' => array('label' => 'form.password'),
                'second_options' => array('label' => 'form.password_confirmation'),
                'invalid_message' => 'fos_user.password.mismatch'
            ))
            ->add('roles', ChoiceType::class, [
                //    'expanded'=>true,
                    'choices' => Usuario::getRolesNames(),
                    'multiple' => true,
                    'attr' => ['style' => 'width:100%;','rows'=> 10,],
                    'label' => 'Permissões'
                ]
            )
            ->add('polos', EntityType::class, [
                    'class' => FrigaPolo::class,
                    'choice_label' => 'descricao',
                    'label' => "Cidade",
                    'attr' => ['data-plugin-select2'],
                ]
            )
            ->add('img', HiddenType::class);
    }

    public function getName()
    {
        return 'nte_usuario_registration';
    }


}