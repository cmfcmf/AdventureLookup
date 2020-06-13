<?php

namespace AppBundle\Listener;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class OnLoginListener
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function onSecurityInteractivelogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();
        if (!($user instanceof User)) {
            return;
        }

        $user->setLastLoginAt(new \DateTime('now'));
        $this->em->persist($user);
        $this->em->flush();
        $event->getAuthenticationToken()->setUser($user);
    }
}
