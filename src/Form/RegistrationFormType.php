<?php

namespace App\Form;

use App\DTO\Users\UserCreateDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, [
                'row_attr' => ['class' => 'form-group'],
            ])
            ->add('name', TextType::class, [
                'row_attr' => ['class' => 'form-group'],
            ])
            ->add('surname', TextType::class, [
                'row_attr' => ['class' => 'form-group'],
            ])
            ->add('country', CountryType::class, [
                'preferred_choices' => ['FR'],
                'row_attr' => ['class' => 'form-group'],
            ])
            ->add('username', TextType::class, [
                'row_attr' => ['class' => 'form-group'],
            ])
            ->add('plainPassword', PasswordType::class, [
                'attr' => ['autocomplete' => 'new-password'],
                'row_attr' => ['class' => 'form-group'],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'row_attr' => ['class' => 'form-check'],
                'constraints' => [
                    new IsTrue([
                        'message' => 'Veuillez accepter la déclaration de confidentialité.',
                    ]),
                ],
            ])
        ;

        return;
    }



    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserCreateDTO::class,
        ]);

        return;
    }
}
