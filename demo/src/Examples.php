<?php

declare(strict_types=1);

namespace App;

use Pentiminax\UX\SweetAlert\AlertManagerInterface;
use Pentiminax\UX\SweetAlert\Enum\Icon;
use Pentiminax\UX\SweetAlert\Enum\InputType;
use Pentiminax\UX\SweetAlert\Enum\Position;
use Pentiminax\UX\SweetAlert\Enum\Theme;
use Pentiminax\UX\SweetAlert\InputType\Checkbox;
use Pentiminax\UX\SweetAlert\InputType\File;
use Pentiminax\UX\SweetAlert\InputType\HtmlInputType;
use Pentiminax\UX\SweetAlert\InputType\Radio;
use Pentiminax\UX\SweetAlert\InputType\Range;
use Pentiminax\UX\SweetAlert\InputType\Select;
use Pentiminax\UX\SweetAlert\InputType\Text;
use Pentiminax\UX\SweetAlert\InputType\Textarea;
use Pentiminax\UX\SweetAlert\Model\Alert;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Every demo button runs one of these closures; the page shows the closure body as its code sample,
 * so the sample can never drift from what actually runs. Keep each signature and closing brace on their own line.
 *
 * Closures are called with (AlertManagerInterface, UrlGeneratorInterface, Request) and declare only what they use.
 */
final class Examples
{
    public const HOME = 'home';

    /**
     * @return array<string, \Closure>
     */
    public static function for(string $page): array
    {
        return match ($page) {
            self::HOME                     => self::home(),
            Page::Alerts->value            => self::alerts(),
            Page::Toasts->value            => self::toasts(),
            Page::InputDialogs->value      => self::inputDialogs(),
            Page::Themes->value            => self::themes(),
            default                        => [],
        };
    }

    /**
     * @return array<string, \Closure>
     */
    private static function home(): array
    {
        return [
            'fire-an-alert' => static function (AlertManagerInterface $alertManager): void {
                $alertManager->success(
                    title: 'Hello from Symfony!',
                    text: 'Queued in PHP, fired by the Stimulus controller.',
                );
            },
        ];
    }

    /**
     * @return array<string, \Closure>
     */
    private static function alerts(): array
    {
        return [
            'success' => static function (AlertManagerInterface $alertManager): void {
                $alertManager->success(title: 'Saved!', text: 'Your changes are live.');
            },
            'error' => static function (AlertManagerInterface $alertManager): void {
                $alertManager->error(title: 'Payment failed', text: 'Your card was declined.');
            },
            'warning' => static function (AlertManagerInterface $alertManager): void {
                $alertManager->warning(title: 'Storage almost full', text: '92% of your quota is used.');
            },
            'info' => static function (AlertManagerInterface $alertManager): void {
                $alertManager->info(title: 'New version', text: 'Version 2.0 ships next week.');
            },
            'question' => static function (AlertManagerInterface $alertManager): void {
                $alertManager->question(title: 'Need help?', text: 'Our docs cover every option.');
            },
            'timer' => static function (AlertManagerInterface $alertManager): void {
                $alertManager->info(
                    title: 'Closing soon',
                    text: 'This alert closes itself after 3 seconds.',
                    timer: 3000,
                    timerProgressBar: true,
                );
            },
            'three-buttons' => static function (AlertManagerInterface $alertManager): void {
                $alertManager
                    ->question(title: 'Save your changes?')
                    ->confirmButtonText('Save')
                    ->withDenyButton()
                    ->setDenyButtonText("Don't save")
                    ->withCancelButton();
            },
            'html' => static function (AlertManagerInterface $alertManager): void {
                $alertManager
                    ->info(title: 'Rich content')
                    ->html('You can use <b>bold</b>, <i>italic</i> and <code>code</code>.');
            },
            'wizard' => static function (AlertManagerInterface $alertManager): void {
                $alertManager->steps(
                    Alert::new(title: 'Welcome', text: 'Step one of three.', icon: Icon::INFO, position: Position::CENTER),
                    Alert::new(title: 'Almost there', text: 'Dismiss to skip the rest.', icon: Icon::QUESTION, position: Position::CENTER),
                    Alert::new(title: 'All set', position: Position::CENTER),
                );
            },
            'flash-message' => static function (AlertManagerInterface $alertManager, UrlGeneratorInterface $urls, Request $request): void {
                // auto_convert_flash_messages: true turns this plain flash into a SweetAlert.
                $request->getSession()->getFlashBag()->add('success', 'This started as a plain addFlash().');
            },
        ];
    }

    /**
     * @return array<string, \Closure>
     */
    private static function toasts(): array
    {
        return [
            'position' => static function (AlertManagerInterface $alertManager, UrlGeneratorInterface $urls, Request $request): void {
                $position = Position::from($request->getPayload()->getString('position'));

                $alertManager->toast(title: 'Toast at '.$position->value, position: $position, timer: 3000);
            },
            'progress-bar' => static function (AlertManagerInterface $alertManager): void {
                $alertManager->toast(
                    title: 'Uploading...',
                    icon: Icon::INFO,
                    position: Position::TOP_END,
                    timer: 5000,
                    timerProgressBar: true,
                );
            },
            'error-toast' => static function (AlertManagerInterface $alertManager): void {
                $alertManager->toast(title: 'Connection lost', icon: Icon::ERROR, position: Position::BOTTOM_END, timer: 4000);
            },
        ];
    }

    /**
     * @return array<string, \Closure>
     */
    private static function inputDialogs(): array
    {
        return [
            'text' => static function (AlertManagerInterface $alertManager, UrlGeneratorInterface $urls): void {
                $alertManager->input(
                    inputType: new Text(label: 'Display name', placeholder: 'Enter your public name'),
                    title: 'Update profile',
                    callback: $urls->generate('result', referenceType: UrlGeneratorInterface::ABSOLUTE_URL),
                );
            },
            'textarea' => static function (AlertManagerInterface $alertManager, UrlGeneratorInterface $urls): void {
                $alertManager->input(
                    inputType: new Textarea(label: 'Feedback', placeholder: 'Tell us everything'),
                    title: 'Send feedback',
                    callback: $urls->generate('result', referenceType: UrlGeneratorInterface::ABSOLUTE_URL),
                );
            },
            'select' => static function (AlertManagerInterface $alertManager, UrlGeneratorInterface $urls): void {
                $alertManager->input(
                    inputType: new Select(options: ['admin' => 'Admin', 'editor' => 'Editor'], label: 'Role', value: 'editor'),
                    title: 'Invite user',
                    callback: $urls->generate('result', referenceType: UrlGeneratorInterface::ABSOLUTE_URL),
                );
            },
            'radio' => static function (AlertManagerInterface $alertManager, UrlGeneratorInterface $urls): void {
                $alertManager->input(
                    inputType: new Radio(options: ['monthly' => 'Monthly', 'yearly' => 'Yearly'], value: 'yearly'),
                    title: 'Billing cycle',
                    callback: $urls->generate('result', referenceType: UrlGeneratorInterface::ABSOLUTE_URL),
                );
            },
            'checkbox' => static function (AlertManagerInterface $alertManager, UrlGeneratorInterface $urls): void {
                $alertManager->input(
                    inputType: new Checkbox(label: 'I accept the terms'),
                    title: 'Terms of service',
                    callback: $urls->generate('result', referenceType: UrlGeneratorInterface::ABSOLUTE_URL),
                );
            },
            'range' => static function (AlertManagerInterface $alertManager, UrlGeneratorInterface $urls): void {
                $alertManager->input(
                    inputType: new Range(label: 'Volume', value: '50', min: 0, max: 100, step: 5),
                    title: 'Adjust volume',
                    callback: $urls->generate('result', referenceType: UrlGeneratorInterface::ABSOLUTE_URL),
                );
            },
            'file' => static function (AlertManagerInterface $alertManager, UrlGeneratorInterface $urls): void {
                $alertManager->input(
                    inputType: new File(label: 'Avatar', accept: 'image/*'),
                    title: 'Upload an avatar',
                    callback: $urls->generate('result', referenceType: UrlGeneratorInterface::ABSOLUTE_URL),
                );
            },
            'email' => static function (AlertManagerInterface $alertManager, UrlGeneratorInterface $urls): void {
                $alertManager
                    ->input(
                        inputType: new HtmlInputType(InputType::Email, label: 'Email', placeholder: 'you@example.com'),
                        title: 'Join the newsletter',
                        callback: $urls->generate('result', referenceType: UrlGeneratorInterface::ABSOLUTE_URL),
                    )
                    ->validationMessage('Please enter a valid email address.');
            },
            'server-validation' => static function (AlertManagerInterface $alertManager, UrlGeneratorInterface $urls): void {
                $alertManager
                    ->input(
                        inputType: new Text(label: 'Username', placeholder: 'At least 3 characters'),
                        title: 'Pick a username',
                        callback: $urls->generate('result', referenceType: UrlGeneratorInterface::ABSOLUTE_URL),
                    )
                    ->withCancelButton()
                    ->preConfirmUrl($urls->generate('username_check'));
            },
        ];
    }

    /**
     * @return array<string, \Closure>
     */
    private static function themes(): array
    {
        return [
            'preview' => static function (AlertManagerInterface $alertManager, UrlGeneratorInterface $urls, Request $request): void {
                $theme = Theme::from($request->getPayload()->getString('theme'));

                // Alert::new() skips this demo's coral button defaults, so the theme styles every button.
                $alertManager->addAlert(
                    Alert::new(title: 'Theme: '.$theme->value, position: Position::CENTER)->theme($theme)->withCancelButton(),
                );
            },
        ];
    }
}
