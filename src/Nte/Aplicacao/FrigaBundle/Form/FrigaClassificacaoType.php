<?php

namespace Nte\Aplicacao\FrigaBundle\Form;

use Nte\Aplicacao\FrigaBundle\Entity\FrigaEdital;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FrigaClassificacaoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

    }
}
