<?php

namespace App\Form;

use App\Entity\ContactUs;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class ContactUsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', null, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Your first name'
                ],
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('lastName', null, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Your last name'
                ],
                'constraints' => [
                    new NotBlank(),
                ]
            ])
            ->add('email', null, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Your email here'
                ]
            ])
            ->add('type', ChoiceType::class, [
                'choices' => ContactUs::TYPES,
                'constraints' => [
                    new NotBlank(),
                ]
            ])
            ->add('category', ChoiceType::class, [
                'choices' => ContactUs::CATEGORY,
                'constraints' => [
                    new NotBlank(),
                ]
            ])
            ->add('priority', ChoiceType::class, [
                'choices' => ContactUs::PRIORITIES,
                'constraints' => [
                    new NotBlank(),
                ]
            ])
            ->add('imageFile', FileType::class, [
                'required' => false
            ])
            ->add('description', null, [
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                ]
            ])
            ->add('details', null, [
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ContactUs::class
        ]);
    }

    /**
     * @return ArrayCollection
     */
    private function getTypes(): ArrayCollection
    {
        $arr_return = new ArrayCollection();
        foreach (ContactUs::TYPES as $k => $v) $arr_return->add([$v => $k]);
        return $arr_return;
    }

    /**
     * @return array
     */
    private function getCategories(): array
    {
        $arr_return = [];
        foreach (ContactUs::CATEGORY as $k => $v) $arr_return[] = [$v => $k];
        return $arr_return;
    }

    /**
     * @return array
     */
    private function getPriorities(): array
    {
        $arr_return = [];
        foreach (ContactUs::PRIORITIES as $k => $v) $arr_return[] = [$v => $k];
        return $arr_return;
    }
}
