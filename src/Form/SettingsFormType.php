<?php

namespace App\Form;

use App\Entity\Setting;
use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SettingsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('imageFileLogo', FileType::class, [
                'required' => false
            ])
            ->add('imageFileHome', FileType::class, [
                'required' => false
            ])
            ->add('favIconImageFile', FileType::class, [
                'required' => false
            ])
            ->add('applicationName')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Setting::class,
        ]);
    }
}
