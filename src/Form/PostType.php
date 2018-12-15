<?php

namespace App\Form;

use App\Entity\Author;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\Tag;
use App\Form\Type\WorkflowType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
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
            ->add('title', TextType::class)
            ->add('description', TextareaType::class)
            ->add('body', TextareaType::class)
            ->add('image', FileType::class)
            ->add('status', WorkflowType::class, [
                'placeholder' => 'Choose a article status option', ])
            ->add('author', EntityType::class, [
                'class' => Author::class,
                'choice_label' => 'name',
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'title',
            ])
            ->add('tags', EntityType::class, [
                'class' => Tag::class,
                'choice_label' => 'title',
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('comments', EntityType::class, [
                'class' => Comment::class,
                'choice_label' => 'title',
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('save', SubmitType::class, ['label' => 'Create Post', 'attr' => ['class' => 'btn btn-default pull-right']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
