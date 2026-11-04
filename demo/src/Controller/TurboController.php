<?php

declare(strict_types=1);

namespace App\Controller;

use Pentiminax\UX\SweetAlert\AlertManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Turbo\TurboBundle;

#[Route('/turbo', methods: ['POST'])]
final class TurboController extends AbstractController
{
    #[Route('/stream', name: 'turbo_stream')]
    public function turboStream(AlertManagerInterface $alertManager): Response
    {
        $alertManager->success(title: 'Saved over a Turbo Stream', text: 'No page reload happened.');

        // The bundle appends <turbo-stream action="alert"> to this response.
        return $this->render('turbo/saved.stream.html.twig', [
            'savedAt' => new \DateTimeImmutable(),
        ], new Response(headers: ['Content-Type' => TurboBundle::STREAM_MEDIA_TYPE]));
    }

    #[Route('/frame', name: 'turbo_frame')]
    public function turboFrame(AlertManagerInterface $alertManager): Response
    {
        $alertManager->info(title: 'Answered from a Turbo Frame', text: 'Only the frame was replaced.');

        // The bundle inserts the alert stream inside the requested <turbo-frame>.
        return $this->render('turbo/frame.html.twig', ['savedAt' => new \DateTimeImmutable()]);
    }
}
