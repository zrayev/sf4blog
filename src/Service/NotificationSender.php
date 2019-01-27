<?php

namespace App\Service;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class NotificationSender
{
    private $em;
    private $templating;
    private $router;
    private $mailer;

    public function __construct(EntityManagerInterface $em, EngineInterface $templating, RouterInterface $router, \Swift_Mailer $mailer)
    {
        $this->em = $em;
        $this->templating = $templating;
        $this->router = $router;
        $this->mailer = $mailer;
    }

    /**
     * @param Post $post
     * @param User $currentUser
     */
    public function sendNotification(Post $post, User $currentUser): void
    {
        $users = $this->em->getRepository(User::class)->findAll();
        foreach ($users as $user) {
            if ($currentUser->getId() !== $user->getId()) {
                $this->sendMail($user, $post);
            }
        }
    }

    /**
     * @param User $user
     * @param Post $post
     */
    public function sendMail(User $user, Post $post): void
    {
        if ($post->getSlug() !== null) {
            $url = $this->router->generate('post_show', ['slug' => $post->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL);
        } else {
            $url = $this->router->generate('blog', [], UrlGeneratorInterface::ABSOLUTE_URL);
        }
        $message = (new \Swift_Message('New notice from Blog'))
            ->setFrom('blog@example.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->templating->render(
                    'emails/notification.html.twig',
                    [
                        'name' => $user->getUsername(),
                        'post' => $post,
                        'url' => $url,
                    ]
                ),
                'text/html'
            )
        ;

        $this->mailer->send($message);
    }
}
