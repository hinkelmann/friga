<?php

namespace Nte\Aplicacao\FrigaBundle\Form;

use Doctrine\ORM\EntityManager;
use Nte\Aplicacao\FrigaBundle\Entity\FrigaEditalDesempate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FrigaEditalDesempateType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var FrigaEditalDesempate $data */
        $data = $options['data'];

        /** @var EntityManager $em */
        $em = $options['em'];
        if ($data->getTipo()) {

        } else {
            $builder->add('idContexto', ChoiceType::class, [
                'label' => 'Contexto para Comparação',
                'expanded' => true,
                'choices' => $this->choices($em, $data),
            ]);
        }
        if($data->getTipo() == 1){
            $builder->add('sentido', ChoiceType::class, [
                'label' => 'Regra de Comparação ',
                'expanded' => true,
                'choices' => [
                    "A > B = 1° 20/06/1990  -  2° 10/05/1961" => 1,
                    "A < B = 1° 10/05/1961  -  2° 20/06/1990" => -1
                ],
            ]);
        }else{
            $builder->add('sentido', ChoiceType::class, [
                'label' => 'Regra de Comparação ',
                'expanded' => true,
                'choices' => [
                    "A > B " => 1,
                    "A < B " => -1
                ],
            ]);
        }

    }

    /**
     * @param EntityManager $em
     * @param FrigaEditalDesempate $entidade
     * @return array
     */
    private function choices(EntityManager $em, FrigaEditalDesempate $entidade)
    {

        // $x = new $entidade->getContexto();

        $itens = $em->getRepository($entidade->getContexto())
            ->findBy(['idEdital' => $entidade->getIdEdital()]);
        $tmp = [];

        /** @var  $item */
        foreach ($itens as $item) {
            $tmp[$item->getTitulo()] = $item->getId();
        }
        return $tmp;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => FrigaEditalDesempate::class,
            'em' => null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'edital_desempate';
    }


}
