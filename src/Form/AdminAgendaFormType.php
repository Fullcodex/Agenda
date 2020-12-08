<?php

namespace App\Form;

use App\Entity\Agenda;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;


class AdminAgendaFormType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('id', IntegerType::class,
                        [
                            'required' => false,
                            'attr'       => ['hidden' => 'true'],
                            'label_attr' => ['hidden' => 'true']])
                ->add('Nom', TextType::class,
                        [
                            'attr'       => ['class' => 'form-control'],
                            'label_attr' => ['class' => 'input-group-text']])
                ->add('Cle', TextType::class,
                        [
                            'attr'       => ['class' => 'form-control', 'disabled' => true],
                            'label_attr' => ['class' => 'input-group-text']])
                ->add('Dt_Cle', DateType::class,
                        [
                            'widget'     => 'single_text',
                            'attr'       => ['class' => 'form-control', 'disabled' => true],
                            'label_attr' => ['class' => 'input-group-text']])
//            ->add('Img')
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => Agenda::class,
        ]);
    }

}
