<?php

namespace App\Form;

use App\Entity\Personne;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use \Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;


class AdminUserFormType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('id', IntegerType::class,
                        [
                            'required' => false,
                            'attr'       => ['hidden' => 'true'],
                            'label_attr' => ['hidden' => 'true']])
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
                ->add('password', PasswordType::class,
                        [
                            'required' => false,
                            'constraints' => [
                                new Length([
                                    'min'        => 6,
                                    'minMessage' => 'Votre mot de passe doit comporter au moin {{ limit }} characters',
                                    // max length allowed by Symfony for security reasons
                                    'max'        => 4096,
                                        ]),
                            ],
                            'attr'        => ['class' => 'form-control'],
                            'label_attr'  => ['class' => 'input-group-text']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => Personne::class,
        ]);
    }

}
