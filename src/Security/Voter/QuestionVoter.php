<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class QuestionVoter extends Voter
{
    public const VIEW = 'view';

    /**
     * Supports
     * @param string $attribute attribute
     * @param mixed $subject subject
     *
     * @return bool
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW]) && $subject instanceof \App\Entity\Question;
    }

    /**
     * Vote on attribute
     * @param string $attribute attribute
     * @param mixed $subject subject
     * @param TokenInterface $token token
     *
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        return $user instanceof UserInterface && $attribute === self::VIEW;
    }
}
