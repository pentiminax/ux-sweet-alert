<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\Tests\Functional;

use Pentiminax\UX\SweetAlert\Delivery\AlertDelivery;
use Pentiminax\UX\SweetAlert\Tests\Fixtures\DummyTurboBundle;
use Pentiminax\UX\SweetAlert\Tests\Functional\App\AlertController;
use Pentiminax\UX\SweetAlert\Tests\Functional\App\AlertQueuedRecorder;
use PHPUnit\Framework\Attributes\Test;

/**
 * End-to-end: a controller queues an alert, the bundle delivers it through the transport the response supports.
 *
 * @internal
 */
final class AlertDeliveryFlowTest extends FunctionalTestCase
{
    #[Test]
    public function it_shows_an_alert_queued_before_a_redirect_on_the_next_page_only(): void
    {
        $client = self::createAppClient();
        $client->followRedirects();

        $client->request('GET', '/alert-then-redirect');

        self::assertResponseIsSuccessful();
        $alerts = self::alertsOnPage($client);
        self::assertCount(1, $alerts);
        self::assertSame(AlertController::TITLE, $alerts[0]['title']);
        self::assertSame('Your changes were saved.', $alerts[0]['text']);
        self::assertSame('success', $alerts[0]['icon']);

        $client->request('GET', '/page');

        self::assertSame([], self::alertsOnPage($client), 'A delivered alert must not be shown twice.');
    }

    #[Test]
    public function it_lets_listeners_alter_alerts_before_they_are_queued(): void
    {
        $client = self::createAppClient();
        $client->followRedirects();

        $client->request('GET', '/alert-then-redirect');

        self::assertSame(AlertQueuedRecorder::FOOTER, self::alertsOnPage($client)[0]['footer']);
        $recorder = self::getContainer()->get(AlertQueuedRecorder::class);
        self::assertSame([AlertController::TITLE], $recorder->queuedTitles);
    }

    #[Test]
    public function it_ignores_flash_messages_by_default(): void
    {
        $client = self::createAppClient();
        $client->followRedirects();

        $client->request('GET', '/flash-then-redirect');

        self::assertSame([], self::alertsOnPage($client));
    }

    #[Test]
    public function it_converts_flash_messages_into_alerts_when_enabled(): void
    {
        $client = self::createAppClient(['auto_convert_flash_messages' => true]);
        $client->followRedirects();

        $client->request('GET', '/flash-then-redirect');

        $alerts = self::alertsOnPage($client);
        self::assertCount(1, $alerts);
        self::assertSame('Payment refused', $alerts[0]['title']);
        self::assertSame('error', $alerts[0]['icon']);
    }

    #[Test]
    public function it_adds_alerts_to_json_responses_without_touching_their_data(): void
    {
        $client = self::createAppClient();

        $client->request('GET', '/json');

        $data = self::jsonResponse($client);
        self::assertTrue($data['saved']);
        self::assertCount(1, $data[AlertDelivery::JSON_KEY]);
        self::assertSame(AlertController::TITLE, $data[AlertDelivery::JSON_KEY][0]['title']);
        self::assertSame('warning', $data[AlertDelivery::JSON_KEY][0]['icon']);
        self::assertSame('top-end', $data[AlertDelivery::JSON_KEY][0]['position']);
    }

    #[Test]
    public function it_sends_alerts_in_the_hx_trigger_header_and_keeps_existing_events(): void
    {
        $client = self::createAppClient();

        $client->request('GET', '/htmx', server: ['HTTP_HX_REQUEST' => 'true']);

        $trigger = json_decode((string) $client->getResponse()->headers->get('HX-Trigger'), true, 512, \JSON_THROW_ON_ERROR);
        self::assertArrayHasKey('profileSaved', $trigger);
        $alerts = $trigger[AlertDelivery::HX_EVENT_KEY]['alerts'];
        self::assertSame(AlertController::TITLE, $alerts[0]['title']);
        self::assertTrue($alerts[0]['toast']);
        self::assertSame('<div>saved</div>', $client->getResponse()->getContent());
    }

    #[Test]
    public function it_appends_an_alert_stream_to_turbo_stream_responses(): void
    {
        $client = self::createAppClient();

        $client->request('GET', '/turbo-stream');

        $content = (string) $client->getResponse()->getContent();
        self::assertStringStartsWith('<turbo-stream action="remove" target="form"></turbo-stream>', $content);
        self::assertStringContainsString('<turbo-stream action="alert">', $content);
        self::assertStringContainsString(AlertController::TITLE, $content);
    }

    #[Test]
    public function it_inserts_an_alert_stream_in_the_requested_turbo_frame_of_a_rendered_response(): void
    {
        if (!class_exists('Symfony\UX\Turbo\TurboBundle')) {
            class_alias(DummyTurboBundle::class, 'Symfony\UX\Turbo\TurboBundle');
        }
        $client = self::createAppClient();

        $client->request('GET', '/turbo-frame', server: ['HTTP_TURBO_FRAME' => 'profile']);

        $content = (string) $client->getResponse()->getContent();
        self::assertStringStartsWith('<turbo-frame id="profile"><turbo-stream action="alert">', $content);
        self::assertStringContainsString(AlertController::TITLE, $content);
    }

    #[Test]
    public function it_keeps_alerts_queued_during_a_pre_confirm_request(): void
    {
        $client = self::createAppClient();

        $client->request('GET', '/json', server: ['HTTP_X_SWEETALERT_PRECONFIRM' => '1']);

        self::assertArrayNotHasKey(AlertDelivery::JSON_KEY, self::jsonResponse($client));

        $client->request('GET', '/page');

        $alerts = self::alertsOnPage($client);
        self::assertCount(1, $alerts);
        self::assertSame(AlertController::TITLE, $alerts[0]['title']);
    }
}
