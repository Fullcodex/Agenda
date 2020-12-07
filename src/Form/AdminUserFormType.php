<?php

namespace App\Form;

use App\Entity\Personne;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class AdminUserFormType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('Nom_Personnes', TextType::class,
                        [
                            'attr'       => ['class' => 'form-control'],
                            'label_attr' => ['class' => 'input-group-text']])
                ->add('Date_Naissance', DateType::class,
                        [
                            'widget'     => 'single_text',
                            'attr'       => ['class' => 'form-control'],
                            'label_attr' => ['class' => 'input-group-text']])
                ->add('email', EmailType::class,
                        [
                            'attr'       => ['class' => 'form-control'],
                            'label_attr' => ['class' => 'input-group-text']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => Personne::class,
        ]);
    }

}
