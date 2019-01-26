<?php

namespace App\Admin;

use App\Entity\Category;
use App\Entity\Tag;
use App\Form\Type\PostWorkflowType;
use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PostAdmin extends AbstractAdmin
{
    /**
     * @param $object
     */
    public function prePersist($object)
    {
        $user = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();
        $object->setAuthor($user);
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Content')
                ->add('title', TextType::class)
                ->add('description', TextareaType::class)
                ->add('body', TextareaType::class)
                ->add('status', PostWorkflowType::class, [
                    'placeholder' => 'Choose a article status option', ])
            ->end()
            ->with('Meta data')
                ->add('category', EntityType::class, [
                    'class' => Category::class,
                    'choice_label' => 'title',
                    'required' => true,
                ])
                ->add('tags', EntityType::class, [
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
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('title')
            ->add('status')
            ->add('category')
            ->add('tags')
            ->add('createdAt')
            ->add('modifiedAt');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->addIdentifier('title')
            ->addIdentifier('status')
            ->addIdentifier('category')
            ->addIdentifier('tags')
            ->addIdentifier('createdAt')
            ->addIdentifier('modifiedAt');
    }
}
