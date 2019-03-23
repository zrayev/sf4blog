<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Post;
use App\Entity\Tag;
use App\Form\Type\PostWorkflowType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'label.title',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'label.description',
            ])
            ->add('body', TextareaType::class, [
                'label' => 'label.body',
            ])
            ->add('status', PostWorkflowType::class, [
                'label' => 'label.status',
                'placeholder' => 'placeholder.status', ])
            ->add('category', EntityType::class, [
                'label' => 'label.category',
                'class' => Category::class,
                'choice_label' => 'title',
                'required' => true,
            ])
            ->add('tags', EntityType::class, [
                'label' => 'label.tags',
                'class' => Tag::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.title', 'ASC');
                },
                'choice_label' => 'title',
                'multiple' => true,
                'expanded' => true,
                'required' => true,
            ])
            ->add('save', SubmitType::class, ['label' => 'label.save', 'attr' => ['class' => 'btn btn-default pull-right']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
            'translation_domain' => 'forms',
        ]);
    }
}
