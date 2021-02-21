<?php

namespace App\Security;

use App\Entity\Category;
use App\Entity\Post;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PostAdminVoter extends Voter
{
    const CREATE = 'create';
    const EDIT = 'edit';
    const DELETE = 'delete';
    const REMOVE = 'remove';
    const RESTORE = 'restore';
    const VIEW = 'view';
    const ACTIVE = 'active';

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute An attribute
     * @param mixed $subject The subject to secure, e.g. an object the user wants to access or any other PHP type
     *
     * @return bool True if the attribute and subject are supported, false otherwise
     */
    protected function supports(string $attribute, $subject): bool
    {
        if (!in_array($attribute, [self::CREATE, self::EDIT, self::REMOVE, self::DELETE, self::RESTORE, self::VIEW, self::ACTIVE])) {
            return false;
        }

        if (!$subject instanceof Post) {
            return false;
        }

        return true;
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     * It is safe to assume that $attribute and $subject already passed the "supports()" method check.
     *
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();

        if (!$user) return false;

        /** @var Post $post */
        $post = $subject;

        if ($post->isDeleted()) {
            return in_array('ROLE_SUPER_ADMIN', $user->getRoles());
        }

        return in_array('ROLE_ADMIN', $user->getRoles());
    }
}