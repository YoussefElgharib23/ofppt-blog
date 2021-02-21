<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Post;
use App\Repository\CategoryRepository;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class PostFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'This is field is required !'
                    ]),
                    new Length([
                        'min' => '10'
                    ])
                ]
            ])
            ->add('minDescription', null, [
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => '40'
                    ])
                ]
            ])
            ->add('description', CKEditorType::class, array(
                'config' => array('toolbar' => 'full'),
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'You can not have a post without a descrption'
                    ]),
                    new Length([
                        'min' => '100'
                    ])
                ]
            ))
            ->add('imageFile', FileType::class, [
                'required' => true,
            ])
            ->add('status', ChoiceType::class, [
                'choices' => $this->status(),
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'query_builder' => function (CategoryRepository $categoryRepository) {
                    return $categoryRepository->createQueryBuilder('c')
                        ->where('c.status = 0');
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }

    public static function status(): array
    {
        $returnArray = [];
        foreach (Post::STATUS as $k => $value ) {
            $returnArray[$value] = $k;
        }
        return $returnArray;
    }
}
