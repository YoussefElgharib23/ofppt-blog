<?php

namespace App\Security\Voter;

use App\Entity\Category;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class CategoryVisitVoter extends Voter
{
    const CATEGORY_VIEW = 'category_view';
    /**
     * @var Security
     */
    private $security;

    public function __construct(
        Security $security
    )
    {
        $this->security = $security;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @return bool
     */
    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::CATEGORY_VIEW]) && $subject instanceof Category;
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
        /** @var Category $category */
        $category = $subject;

        switch ($attribute) {
            case self::CATEGORY_VIEW:
                    if ($category->getActivePosts()->count() > 0 && (!$user || $user)) {
                        return true;
                    }
                    else if ($category->getActivePosts()->count() === 0 && $user && $this->security->isGranted('ROLE_ADMIN', $user)) {
                        return true;
                    }
                break;
        }
        return false;
    }
}
