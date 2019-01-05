<?php

namespace App\Security;

use App\Entity\Post;
use App\Entity\User;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class PostVoter extends Voter
{
    public const EDIT = 'edit';
    public const DELETE = 'delete';
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        if (!\in_array($attribute, [self::EDIT, self::DELETE], true)) {
            return false;
        }

        // only vote on Post objects inside this voter
        if (!$subject instanceof Post) {
            return false;
        }

        return true;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        // ROLE_SUPER_ADMIN can edit all posts
        if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
            return true;
        }

        $post = $subject;

        if ($attribute === self::EDIT) {
            return $this->canEdit($post, $user);
        }

        if ($attribute === self::DELETE) {
            return $this->canDelete($post, $user);
        }

        throw new LogicException('This code should not be reached!');
    }

    private function canEdit(Post $post, User $user): bool
    {
        return $user === $post->getAuthor();
    }

    private function canDelete(Post $post, User $user): bool
    {
        return $user === $post->getAuthor();
    }
}
