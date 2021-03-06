<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;

class UserProfileAdminFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', null, [
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => '2'
                    ])
                ]
            ])
            ->add('lastName', null, [
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => '2'
                    ])
                ]
            ])
            ->add('username', null, [
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => '2'
                    ])
                ]
            ])
            ->add('email', EmailType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => '2'
                    ])
                ]
            ])
            ->add('displayName',null, [
                'required' => false,
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => '2'
                    ])
                ]
            ])
            ->add('isChangedToDisplay', CheckboxType::class, [
                'required' => false
            ])
            ->add('phoneNumber', null, [
                'required' => false,
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => '10'
                    ])
                ]
            ])
            ->add('birthDate', TextType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank()
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
