<?php

declare(strict_types=1);

namespace App\Controller;

use App\Examples;
use App\Page;
use Pentiminax\UX\SweetAlert\AlertManagerInterface;
use Pentiminax\UX\SweetAlert\Enum\Position;
use Pentiminax\UX\SweetAlert\Http\PreConfirmResponse;
use Pentiminax\UX\SweetAlert\Model\Result;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Requirement\EnumRequirement;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class DemoController extends AbstractController
{
    private const CONFIG_FILE = '/config/packages/sweet_alert.yaml';

    #[Route('/', name: 'home', methods: ['GET'])]
    public function home(): Response
    {
        return $this->render('home.html.twig');
    }

    #[Route('/{page}', name: 'page', requirements: ['page' => new EnumRequirement(Page::class)], methods: ['GET'])]
    public function page(Page $page, #[Autowire('%kernel.project_dir%')] string $projectDir): Response
    {
        return $this->render(\sprintf('pages/%s.html.twig', $page->value), [
            'page'   => $page,
            'config' => Page::Configuration === $page ? file_get_contents($projectDir.self::CONFIG_FILE) : null,
        ]);
    }

    /**
     * Runs the example closure, then redirects so the queued alert shows on the next page render.
     */
    #[Route('/fire/{page}/{example}', name: 'fire', methods: ['POST'])]
    public function fire(string $page, string $example, Request $request, AlertManagerInterface $alertManager, UrlGeneratorInterface $urls): Response
    {
        $closure = Examples::for($page)[$example] ?? throw new NotFoundHttpException();
        $closure($alertManager, $urls, $request);

        return Examples::HOME === $page
            ? $this->redirectToRoute('home', status: Response::HTTP_SEE_OTHER)
            : $this->redirectToRoute('page', ['page' => $page], Response::HTTP_SEE_OTHER);
    }

    #[Route('/result', name: 'result', methods: ['POST'])]
    public function result(Result $result, AlertManagerInterface $alertManager): JsonResponse
    {
        $alertManager->toast(
            title: $result->isConfirmed ? 'Symfony received' : 'Dismissed',
            text: json_encode($result->value, \JSON_THROW_ON_ERROR | \JSON_UNESCAPED_UNICODE | \JSON_INVALID_UTF8_SUBSTITUTE),
            position: Position::TOP_END,
            timer: 4000,
        );

        // The bundle adds the queued toast to this JSON under "ux_sweet_alert".
        return $this->json(['received' => true]);
    }

    #[Route('/username/check', name: 'username_check', methods: ['POST'])]
    public function checkUsername(Request $request, ValidatorInterface $validator): Response
    {
        $name = $request->getPayload()->getString('value');

        $violations = $validator->validate($name, [
            new Assert\NotBlank(),
            new Assert\Length(min: 3, max: 20),
        ]);

        return PreConfirmResponse::fromViolations($violations, ['username' => $name]);
    }
}
