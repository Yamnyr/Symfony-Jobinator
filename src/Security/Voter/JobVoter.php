<?php

namespace App\Security\Voter;

use App\Entity\Job;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class JobVoter extends Voter
{
    public const EDIT = 'JOB_EDIT';
    public const DELETE = 'JOB_DELETE';
    public const SHOW = 'JOB_VIEW';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::DELETE, self::SHOW])
            && $subject instanceof Job;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        /*if (!$user instanceof UserInterface) {
            return false;
        }*/

        // ... (check conditions and return true to grant permission) ...
        $job = $subject;
        switch ($attribute) {
            case self::EDIT:
            case self::DELETE:
                if ($user instanceof UserInterface) {
                    return $job->getOwner() === $user;
                }
                // no break
            case self::SHOW:
                return $job->isPublished();
        }

        return false;
    }
}
