<?php

namespace App\Security;

use App\Entity\Comment;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CommentAdminVoter extends Voter
{
    const DELETE = 'delete';
    const REMOVE = 'remove';
    const RESTORE = 'restore';

    /**
     * @param string $attribute
     * @param mixed $subject
     * @return bool
     */
    protected function supports(string $attribute, $subject): bool
    {
        if (!in_array($attribute, [self::REMOVE, self::DELETE, self::RESTORE])) return false;

        if (!$subject instanceof Comment) return false;

        return true;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        /** @var User|null $user */
        $user = $token->getUser();

        if (!$user || !$user instanceof User) return false;

        return $this->hasAdminRole($user);
    }

    private function hasAdminRole(?User $user): bool
    {
        return in_array('ROLE_ADMIN', $user->getRoles());
    }
}