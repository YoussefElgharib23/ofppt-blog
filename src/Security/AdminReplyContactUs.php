<?php

namespace App\Security;

use App\Entity\ContactUs;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AdminReplyContactUs extends Voter
{
    const REPLY = 'reply';

    /**
     * @param string $attribute
     * @param mixed $subject
     * @return bool
     */
    protected function supports(string $attribute, $subject): bool
    {
        if (!in_array($attribute, [self::REPLY])) {
            return false;
        }

        if (!$subject instanceof ContactUs) {
            return false;
        }

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
        /** @var User | null $user */
        $user = $token->getUser();

        if (!$user) {
            return false;
        }

        return $this->hasAdminRole($user);
    }

    private function hasAdminRole(User $user): bool
    {
        return in_array('ROLE_ADMIN', $user->getRoles());
    }
}