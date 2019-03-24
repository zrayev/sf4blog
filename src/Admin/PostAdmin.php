<?php

namespace App\Admin;

use App\Entity\Category;
use App\Entity\Post;
use App\Entity\Tag;
use App\Form\Type\PostWorkflowType;
use App\Service\NotificationSender;
use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PostAdmin extends AbstractAdmin
{
    private $tokenStorage;
    private $notificationSender;

    /**
     * PostAdmin constructor.
     * @param string $code
     * @param string $class
     * @param string $baseControllerName
     * @param TokenStorageInterface $tokenStorage
     * @param NotificationSender $notificationSender
     */
    public function __construct($code, $class, $baseControllerName, TokenStorageInterface $tokenStorage, NotificationSender $notificationSender)
    {
        parent::__construct($code, $class, $baseControllerName);
        $this->tokenStorage = $tokenStorage;
        $this->notificationSender = $notificationSender;
    }

    /**
     * @param Post $post
     */
    public function prePersist($post)
    {
        $user = $this->tokenStorage->getToken()->getUser();
        $post->setAuthor($user);
        if ($post->getStatus() === Post::STATUS_PUBLISH) {
            $this->notificationSender->sendNotification($post, $user);
        }
    }

    /**
     * @param Post $post
     */
    public function postUpdate($post)
    {
        $user = $this->tokenStorage->getToken()->getUser();
        if ($post->getStatus() === Post::STATUS_PUBLISH) {
            $this->notificationSender->sendNotification($post, $user);
        }
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
            ->add('category');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('title')
            ->add('status')
            ->add('category')
            ->add('tags')
            ->add('createdAt')
            ->add('_action', 'actions', [
                'actions' => [
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }
}
