<?php

namespace Nte\UsuarioBundle\Form;

use Nte\Aplicacao\CadastrosBundle\Entity\FrigaPolo;
use Nte\UsuarioBundle\Entity\Usuario;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class UsuarioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', null, array('label' => 'form.username', 'translation_domain' => 'FOSUserBundle'))
            ->add('email', EmailType::class, array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle'))
            ->add('nome', TextType::class, ['label' => 'Nome Completo', 'attr' => ['placeholder' => 'Informe o nome completo']])
            ->add('cpf', TextType::class, [
                'label' => 'CPF',
                'attr' => [
                    'placeholder' => '000.000.000-00',
                    'data-rule-cpf'=>'true',
                ]
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => Usuario::getRolesNames(),
                'multiple' => true,
                'attr' => ['style' => 'width:100%;', 'rows' => 10,],
                'label' => 'Permissões'
            ])
            /*->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'options' => array('translation_domain' => 'FOSUserBundle'),
                'first_options' => array('label' => 'form.password'),
                'second_options' => array('label' => 'form.password_confirmation'),
                'invalid_message' => 'fos_user.password.mismatch'
            ])*/
            ->add('img', HiddenType::class);
    }
}