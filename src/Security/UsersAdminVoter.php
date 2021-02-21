<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UsersAdminVoter extends Voter
{
    const SUSPEND = 'suspend';
    const ACTIVE = 'active';
    const REMOVE = 'remove';
    const VIEW = 'view_user';

    /**
     * @param string $attribute
     * @param mixed $subject
     *
     * @return bool
     */
    protected function supports(string $attribute, $subject): bool
    {
        if(!in_array($attribute, [self::ACTIVE, self::REMOVE, self::SUSPEND, self::VIEW])) {
            return false;
        }

        if (!$subject instanceof User) {
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

        /** @var User $user__ */
        $user__ = $subject;

        if ($attribute === self::VIEW && $user__->isDeleted()) {
            dd('here');
            return in_array('ROLE_SUPER_ADMIN', $user->getRoles());
        }

        return in_array('ROLE_ADMIN', $user->getRoles());
    }
}