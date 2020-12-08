<?php

namespace App\Form;

use App\Entity\Acceder;
use App\Entity\Personne;
use App\Entity\Agenda;
use App\Entity\Droit;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminAccesFormType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('id', IntegerType::class,
                        [
                            'required' => false,
                            'attr'       => ['hidden' => 'true'],
                            'label_attr' => ['hidden' => 'true']])
                ->add('Ref_Personne', EntityType::class, [
                    'class'        => Personne::class,
                    'choice_label' => 'email',
                    'attr'         => ['class' => 'custom-select'],
                    'label_attr'   => ['class' => 'input-group-text']
                        // used to render a select box, check boxes or radios
                        // 'multiple' => true,
                        // 'expanded' => true,
                ])
                ->add('Agendas', EntityType::class, [
                    'class'        => Agenda::class,
                    'choice_label' => 'nom',
                    'attr'         => ['class' => 'custom-select'],
                    'label_attr'   => ['class' => 'input-group-text']
                        // used to render a select box, check boxes or radios
                        // 'multiple' => true,
                        // 'expanded' => true,
                ])
                ->add('Id_Droit', EntityType::class, [
                    'class'        => Droit::class,
                    'choice_label' => 'Libelle',
                    'attr'         => ['class' => 'custom-select'],
                    'label_attr'   => ['class' => 'input-group-text']
                        // used to render a select box, check boxes or radios
                        // 'multiple' => true,
                        // 'expanded' => true,
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => Acceder::class,
        ]);
    }

}
