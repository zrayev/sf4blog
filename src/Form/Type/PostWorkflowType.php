<?php

namespace App\Form\Type;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostWorkflowType extends AbstractType
{
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => [
                'Draft' => Post::STATUS_DRAFT,
                'Review' => Post::STATUS_REVIEW,
                'Publish' => Post::STATUS_PUBLISH,
            ],
        ]);
    }

    /**
     * @return string|null
     */
    public function getParent()
    {
        return ChoiceType::class;
    }
}
