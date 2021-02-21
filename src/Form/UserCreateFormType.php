<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserCreateFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', null, [
                'constraints' => [
                    new NotBlank([
                        'message' => "U can't have a user without first name"
                    ])
                ]
            ])
            ->add('lastName', null, [
                'constraints' => [
                    new NotBlank([
                        'message' => "U can't have a user without last name"
                    ])
                ]
            ])
            ->add('username', null, [
                'constraints' => [
                    new NotBlank([
                        'message' => "U can't have a user without first name"
                    ])
                ]
            ])
            ->add('gender', ChoiceType::class, [
                'choices' => User::GENDERS,
                'label' => 'Gender'
            ])
            ->add('email', null, [
                'constraints' => [
                    new NotBlank([
                        'message' => "U can't have a user without first name"
                    ])
                ]
            ])
            ->add('status', null, [
                'label' => 'Is active',
                'required' => false
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => User::ROLES,
                'label' => 'User role'
            ])
            ->add('password', PasswordType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => '{{ label }} can not be null'
                    ]),
                    new Length([
                        'min' => 8,
                        'max' => 255
                    ])
                ]
            ])
            ->add('isVerified', null, [
                'required' => false
            ])
            ->add('displayName', null, [
                'required' => false
            ])
            ->add('isChangedToDisplay', null, [
                'required' => false
            ])
            ->add('phoneNumber', null, [
                'required' => false
            ])
            ->add('birthDate', null, [
                'required' => false
            ])
            ->add('imageFile', FileType::class, [
                'required' => false
            ])
        ;

        $builder->get('roles')->addModelTransformer(new CallbackTransformer(
            function ($rolesArray) {
                // transform the array to a string
                return count($rolesArray) ? $rolesArray[0]: null;
            },
            function ($rolesString) {
                // transform the string back to an array
                return [$rolesString];
            }
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
