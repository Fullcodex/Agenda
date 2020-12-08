<?php

namespace App\Form;

use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class AdminCategorieFormType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('id', IntegerType::class,
                        [
                            'required'   => false,
                            'attr'       => ['hidden' => 'true'],
                            'label_attr' => ['hidden' => 'true']])
                ->add('Libelle', TextType::class,
                        [
                            'attr'       => ['class' => 'form-control'],
                            'label_attr' => ['class' => 'input-group-text']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => Categorie::class,
        ]);
    }

}
