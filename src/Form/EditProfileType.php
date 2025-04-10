<?php

namespace App\Form;

use App\DTO\Users\UserUpdateDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class EditProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('profilePicture', FileType::class, [
                'mapped' => false,
                'help' => ' Formats acceptés : .jpg, .png; Taille maximum : 3Mo',
                'label' => 'Photo de profil',
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '3072k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (formats acceptés : .jpg, .png, .webp).',
                    ])
                ],
            ])
            ->add('name', TextType::class, [
                'label' => 'Nom',
            ])
            ->add('surname', TextType::class, [
                'label' => 'Prénom',
            ])
            ->add('country', CountryType::class, [
                'preferred_choices' => ['FR'],
                'label' => 'Nationalité',
            ])
            ->add('email', TextType::class, [
                'label' => 'Email',
            ])
            ->add('website', UrlType::class, [
                'label' => 'Site web',
                'required' => false,
                'default_protocol' => null,
                'attr' => [
                    'placeholder' => 'https://nom-de-domaine',
                ],
            ])
            ->add('github', UrlType::class, [
                'label' => 'GitHub',
                'required' => false,
                'default_protocol' => null,
                'attr' => [
                    'placeholder' => 'https://github.com/utilisateur/',
                ],
            ]) 
            ->add('linkedin', UrlType::class, [
                'label' => 'LinkedIn',
                'required' => false,
                'default_protocol' => null,
                'attr' => [
                    'placeholder' => 'https://www.linkedin.com/in/utilisateur/',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserUpdateDTO::class,
        ]);
    }
}
