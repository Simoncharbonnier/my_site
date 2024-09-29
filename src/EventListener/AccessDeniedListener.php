<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;

class AccessDeniedListener implements EventSubscriberInterface
{
    /**
     * @var $urlGenerator url generator
     */
    private $urlGenerator;

    /**
     * @var $security security handler
     */
    private $security;

    /**
     * @var $session session
     */
    private $session;

    /**
     * Construct
     * @param UrlGeneratorInterface $urlGenerator url generator
     * @param Security $security security handler
     * @param RequestStack $request request to get session
     */
    public function __construct(UrlGeneratorInterface $urlGenerator, Security $security, RequestStack $request)
    {
        $this->urlGenerator = $urlGenerator;
        $this->security = $security;
        $this->session = $request->getSession();
    }

    /**
     * Get subscribed events
     *
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 2],
        ];
    }

    /**
     * On Kernel Exception
     * @param ExceptionEvent $event event exception
     *
     * @return void;
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if (!$exception instanceof AccessDeniedException) {
            return;
        }

        if ($this->security->isGranted('ROLE_USER')) {
            $this->session->getFlashBag()->add('error', "Vous n'avez pas les permissions.");

            $event->setResponse(new RedirectResponse($this->urlGenerator->generate('app_home')));
        } else {
            $this->session->getFlashBag()->add('error', "Veuillez vous connecter.");

            $event->setResponse(new RedirectResponse($this->urlGenerator->generate('app_login')));
        }
    }
}
