<?php 
namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Security;

class AccessDeniedSubscriber implements EventSubscriberInterface
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        if ($exception instanceof AccessDeniedHttpException) {
            $user = $this->security->getUser();

            if ($this->security->isGranted('ROLE_ADMIN')) {
                $event->setResponse(new RedirectResponse('/admin/'));
            } elseif ($this->security->isGranted('ROLE_TEACHER') || $this->security->isGranted('ROLE_STUDENT')) {
                $event->setResponse(new RedirectResponse('/'));
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            ExceptionEvent::class => 'onKernelException',
        ];
    }
}
