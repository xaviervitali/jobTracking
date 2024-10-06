<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;

final class ExceptionListener
{

    public function __construct(private RouterInterface $router)
    {
    }
    #[AsEventListener(event: ExceptionEvent::class)]
    public function onExceptionEvent(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        // Vérifier si l'exception est une erreur 404
        if ($exception instanceof NotFoundHttpException) {
            // Générer une réponse de redirection vers la route 'home'
            // $response = new RedirectResponse($this->router->generate('app_synthese'));
            // $event->setResponse($response);
        }
    }
}
