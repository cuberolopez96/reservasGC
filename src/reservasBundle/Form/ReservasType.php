<?php

namespace reservasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ReservasType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nombre')
        ->add('apellidos')
        ->add('correo')
        ->add('telefono')
        ->add('observaciones')
        ->add('codreserva')
        ->add('npersonas')
        ->add('horallegada')
        ->add('estadoreservaestadoreserva',EntityType::class,array(
          'class'=>'reservasBundle:Estadoreserva',
          'choice_label'=>'nombre'
        ))
        ->add('serviciosservicios',EntityType::class,array(
          'class'=>'reservasBundle:Servicios',
          'choice_label'=>'idservicios'
        ));

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'reservasBundle\Entity\Reservas'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'reservasbundle_reservas';
    }


}
