<?php

namespace App\Admin;

use App\Entity\User;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\Form\Type\BooleanType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class PersonAdmin extends AbstractAdmin
{
    private $passwordEncoder;

    /**
     * PersonAdmin constructor.
     * @param string $code
     * @param string $class
     * @param string $baseControllerName
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(string $code, string $class, string $baseControllerName, UserPasswordEncoderInterface $passwordEncoder)
    {
        parent::__construct($code, $class, $baseControllerName);
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param User $object
     */
    public function prePersist($object): void
    {
        $this->encodePassword($object);
    }

    /**
     * @param User $object
     */
    public function preUpdate($object): void
    {
        $this->encodePassword($object);
    }

    protected function configureRoutes(RouteCollection $collection): void
    {
//        $securityContext = $this->getConfigurationPool()->getContainer()->get('security.authorization_checker');
//
//        if (!$securityContext->isGranted('ROLE_SUPER_ADMIN')) {
//            $collection->remove('create');
//            $collection->remove('edit');
//        }
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with('General')
            ->add('username', TextType::class)
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('email', EmailType::class)
            ->add('roles', ChoiceType::class, [
                'multiple' => true,
                'choices' => User::$availableUserRoles,
            ])
            ->add('enabled', BooleanType::class)
            ->end()
            ->with('Credentials')
            ->add('password', PasswordType::class)
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('username')
            ->add('email')
            ->add('createdAt');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('username')
            ->add('email')
            ->add('roles', 'choice', [
                'multiple' => true,
                'choices' => self::$availableUserRoles,
            ])
            ->add('_action', 'actions', [
                    'actions' => [
                        'edit' => [],
                        'delete' => [],
                    ],
                ]
            );
    }

    /**
     * @param User $user
     */
    private function encodePassword(User $user): void
    {
        $plainPassword = $this
            ->getForm()
            ->get('password')
            ->getData()
        ;
        if ($plainPassword !== null) {
            $user->setPassword($this->passwordEncoder->encodePassword($user, $plainPassword));
        }
    }
}
