<?php

namespace App\Security;

use App\Entity\Category;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CategoryVoter extends Voter
{
    const ACTIVE = 'active';
    const EDIT = 'edit';
    const DELETE = 'delete';
    const REMOVE = 'remove';
    const RESTORE = 'restore';
    const VIEW_POSTS = 'view_category_posts';

    public function supports(string $attribute, $subject): bool
    {
        if (!in_array($attribute, [self::EDIT, self::REMOVE, self::RESTORE, self::DELETE, self::ACTIVE, self::VIEW_POSTS])) {
            return false;
        }

        if ( !$subject instanceof Category ) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        /** @var User|null $user */
        $user = $token->getUser();

        if ( !$user ) return false;

        /** @var Category $category */
        $category = $subject;

        return $this->hasAccess($user, $category);
    }

    /**
     * @param User $user
     * @param Category $category
     * @return bool
     */
    public function hasAccess(User $user, Category $category): bool
    {
        return $category->isDeleted() ? in_array('ROLE_SUPER_ADMIN', $user->getRoles()) : in_array('ROLE_ADMIN', $user->getRoles());
    }
}