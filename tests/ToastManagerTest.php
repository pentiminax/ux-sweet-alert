<?php

namespace Pentiminax\UX\SweetAlert\Tests;

use Pentiminax\UX\SweetAlert\Model\Toast;
use Pentiminax\UX\SweetAlert\ToastManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class ToastManagerTest extends KernelTestCase
{
    private ToastManager $toastManager;

    protected function setUp(): void
    {
        $session = new Session(new MockArraySessionStorage());

        $request = new Request();
        $request->setSession($session);

        $requestStack = new RequestStack();
        $requestStack->push($request);

        $this->toastManager = new ToastManager($requestStack);
    }

    public function testAddToast(): void
    {
        $toast = Toast::new(
            id: 'toast',
            title: 'title'
        );

        $this->toastManager->addToast($toast);

        $toasts = $this->toastManager->getToasts();

        $this->assertContains($toast, $toasts);
    }
}