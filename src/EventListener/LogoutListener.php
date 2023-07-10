<?php

namespace App\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutListener{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    public function onSymfonyComponentSecurityHttpEventLogoutEvent(LogoutEvent $logoutEvent): void
    {
        $logoutEvent->getToken()->getUser()->setOnline(false);
        $this->entityManager->flush();
    }
}