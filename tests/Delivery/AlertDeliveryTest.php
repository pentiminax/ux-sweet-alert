<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\Tests\Delivery;

use Pentiminax\UX\SweetAlert\AlertManager;
use Pentiminax\UX\SweetAlert\AlertManagerInterface;
use Pentiminax\UX\SweetAlert\Context\SweetAlertContext;
use Pentiminax\UX\SweetAlert\Delivery\AlertDelivery;
use Pentiminax\UX\SweetAlert\FlashMessageConverter;
use Pentiminax\UX\SweetAlert\Model\AlertDefaults;
use Pentiminax\UX\SweetAlert\Tests\Fixtures\DummyTurboBundle;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

/**
 * @internal
 */
#[CoversClass(AlertDelivery::class)]
final class AlertDeliveryTest extends TestCase
{
    private AlertManager $manager;

    private AlertDelivery $delivery;

    private Session $session;

    private Request $request;

    protected function setUp(): void
    {
        parent::setUp();

        if (!class_exists('Symfony\UX\Turbo\TurboBundle')) {
            class_alias(DummyTurboBundle::class, 'Symfony\UX\Turbo\TurboBundle');
        }

        $this->session = new Session(new MockArraySessionStorage());
        $this->request = new Request();
        $this->request->setSession($this->session);

        $requestStack = new RequestStack();
        $requestStack->push($this->request);

        $defaults      = new AlertDefaults();
        $this->manager = new AlertManager($requestStack, new SweetAlertContext(), new FlashMessageConverter($defaults), $defaults, new EventDispatcher());

        $twig = new Environment(new ArrayLoader([
            '@SweetAlert/turbo/alert.html.twig' => file_get_contents(__DIR__.'/../../templates/turbo/alert.html.twig'),
        ]));

        $this->delivery = new AlertDelivery($this->manager, $twig, $requestStack);
    }

    #[Test]
    public function it_delivers_all_alerts_in_hx_trigger(): void
    {
        $this->request->headers->set('HX-Request', 'true');
        $this->manager->success('One');
        $this->manager->error('Two');

        $response = new Response('ok');
        $this->delivery->deliver($this->request, $response);

        $trigger = json_decode($response->headers->get('HX-Trigger'), true);
        $alerts  = $trigger[AlertDelivery::HX_EVENT_KEY]['alerts'];
        $this->assertSame(['One', 'Two'], array_column($alerts, 'title'));
        $this->assertSame(0, $this->queueSize());
    }

    #[Test]
    public function it_merges_into_existing_json_hx_trigger(): void
    {
        $this->request->headers->set('HX-Request', 'true');
        $this->manager->success('One');

        $response = new Response('ok', headers: ['HX-Trigger' => '{"other":{"a":1}}']);
        $this->delivery->deliver($this->request, $response);

        $trigger = json_decode($response->headers->get('HX-Trigger'), true);
        $this->assertSame(['a' => 1], $trigger['other']);
        $this->assertSame('One', $trigger[AlertDelivery::HX_EVENT_KEY]['alerts'][0]['title']);
    }

    #[Test]
    public function it_merges_into_existing_plain_string_hx_trigger(): void
    {
        $this->request->headers->set('HX-Request', 'true');
        $this->manager->success('One');

        $response = new Response('ok', headers: ['HX-Trigger' => 'first, second']);
        $this->delivery->deliver($this->request, $response);

        $trigger = json_decode($response->headers->get('HX-Trigger'), true);
        $this->assertNull($trigger['first']);
        $this->assertNull($trigger['second']);
        $this->assertArrayHasKey(AlertDelivery::HX_EVENT_KEY, $trigger);
    }

    #[Test]
    public function it_appends_turbo_streams_to_turbo_stream_responses(): void
    {
        $this->manager->success('One');
        $this->manager->error('Two');

        $response = new Response('<turbo-stream></turbo-stream>', headers: ['Content-Type' => 'text/vnd.turbo-stream.html']);
        $this->delivery->deliver($this->request, $response);

        $this->assertSame(2, substr_count($response->getContent(), '<turbo-stream action="alert">'));
        $this->assertStringContainsString('One', $response->getContent());
        $this->assertSame(0, $this->queueSize());
    }

    #[Test]
    public function it_inserts_turbo_streams_inside_the_requested_frame(): void
    {
        $this->request->headers->set('Turbo-Frame', 'x');
        $this->manager->success('One');

        $response = new Response('<h1>t</h1><turbo-frame id="x" class="c"><p>hi</p></turbo-frame>', headers: ['Content-Type' => 'text/html']);
        $this->delivery->deliver($this->request, $response);

        $this->assertMatchesRegularExpression('#^<h1>t</h1><turbo-frame id="x" class="c"><turbo-stream action="alert">.*</turbo-stream><p>hi</p></turbo-frame>$#s', $response->getContent());
        $this->assertSame(0, $this->queueSize());
    }

    #[Test]
    #[DataProvider('frameOpeningTags')]
    public function it_finds_the_frame_whatever_its_attribute_order_or_quotes(string $tag): void
    {
        $this->request->headers->set('Turbo-Frame', 'x');
        $this->manager->success('One');

        $response = new Response($tag.'<p>hi</p></turbo-frame>', headers: ['Content-Type' => 'text/html']);
        $this->delivery->deliver($this->request, $response);

        $this->assertStringStartsWith($tag.'<turbo-stream action="alert">', $response->getContent());
        $this->assertSame(0, $this->queueSize());
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function frameOpeningTags(): iterable
    {
        yield 'src before id' => ['<turbo-frame src="/a" id="x">'];
        yield 'single quotes' => ["<turbo-frame id='x'>"];
        yield 'class before id' => ['<turbo-frame class="c" id="x" loading="lazy">'];
        yield 'unquoted id' => ['<turbo-frame id=x>'];
        yield 'uppercase tag and attribute names' => ['<TURBO-FRAME ID="x">'];
        yield '> inside a quoted value' => ['<turbo-frame data-x="a>b" id="x">'];
    }

    #[Test]
    #[DataProvider('framesThatAreNotTheRequestedOne')]
    public function it_only_matches_the_frame_whose_id_attribute_is_exactly_the_requested_one(string $decoy): void
    {
        $this->request->headers->set('Turbo-Frame', 'x');
        $this->manager->success('One');

        $response = new Response($decoy.'<p>decoy</p></turbo-frame><turbo-frame id="x"><p>hi</p></turbo-frame>', headers: ['Content-Type' => 'text/html']);
        $this->delivery->deliver($this->request, $response);

        $this->assertStringStartsWith($decoy.'<p>decoy</p></turbo-frame><turbo-frame id="x"><turbo-stream action="alert">', $response->getContent());
        $this->assertSame(0, $this->queueSize());
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function framesThatAreNotTheRequestedOne(): iterable
    {
        yield 'data-id attribute' => ['<turbo-frame id="other" data-id="x">'];
        yield 'id= inside another value' => ['<turbo-frame title=\'a id="x"\' id="other">'];
        yield 'different case value' => ['<turbo-frame id="X">'];
        yield 'second id attribute ignored' => ['<turbo-frame id="other" id="x">'];
    }

    #[Test]
    public function it_leaves_queue_when_requested_frame_is_not_in_the_response(): void
    {
        $this->request->headers->set('Turbo-Frame', 'x');
        $this->manager->success('One');

        $body     = '<turbo-frame id="other"><p>hi</p></turbo-frame>';
        $response = new Response($body, headers: ['Content-Type' => 'text/html']);
        $this->delivery->deliver($this->request, $response);

        $this->assertSame($body, $response->getContent());
        $this->assertSame(1, $this->queueSize());
    }

    #[Test]
    public function it_escapes_the_frame_id_when_matching(): void
    {
        $this->request->headers->set('Turbo-Frame', 'a.b');
        $this->manager->success('One');

        $body     = '<turbo-frame id="aXb"><p>hi</p></turbo-frame>';
        $response = new Response($body, headers: ['Content-Type' => 'text/html']);
        $this->delivery->deliver($this->request, $response);

        $this->assertSame($body, $response->getContent());
        $this->assertSame(1, $this->queueSize());
    }

    #[Test]
    public function it_leaves_queue_on_html_without_turbo_frame(): void
    {
        $this->manager->success('One');

        $response = new Response('<p>hi</p>', headers: ['Content-Type' => 'text/html']);
        $this->delivery->deliver($this->request, $response);

        $this->assertSame('<p>hi</p>', $response->getContent());
        $this->assertSame(1, $this->queueSize());
    }

    #[Test]
    public function it_sets_namespaced_key_on_json_objects(): void
    {
        $this->manager->success('One');
        $this->manager->error('Two');

        $response = new JsonResponse(['status' => 'ok']);
        $this->delivery->deliver($this->request, $response);

        $data = json_decode($response->getContent(), true);
        $this->assertSame('ok', $data['status']);
        $this->assertSame(['One', 'Two'], array_column($data[AlertDelivery::JSON_KEY], 'title'));
        $this->assertSame(0, $this->queueSize());
    }

    #[Test]
    public function it_leaves_queue_on_pre_confirm_requests(): void
    {
        $this->request->headers->set(AlertDelivery::PRE_CONFIRM_HEADER, '1');
        $this->manager->success('One');

        $response = new JsonResponse(['status' => 'ok']);
        $this->delivery->deliver($this->request, $response);

        $this->assertSame(['status' => 'ok'], json_decode($response->getContent(), true));
        $this->assertSame(1, $this->queueSize());
    }

    #[Test]
    public function it_leaves_queue_when_json_already_has_the_key(): void
    {
        $this->manager->success('One');

        $response = new JsonResponse([AlertDelivery::JSON_KEY => 'mine']);
        $this->delivery->deliver($this->request, $response);

        $this->assertSame([AlertDelivery::JSON_KEY => 'mine'], json_decode($response->getContent(), true));
        $this->assertSame(1, $this->queueSize());
    }

    #[Test]
    public function it_leaves_queue_on_json_list(): void
    {
        $this->manager->success('One');

        $response = new JsonResponse([1, 2]);
        $this->delivery->deliver($this->request, $response);

        $this->assertSame('[1,2]', $response->getContent());
        $this->assertSame(1, $this->queueSize());
    }

    #[Test]
    public function it_leaves_queue_on_redirect(): void
    {
        $this->request->headers->set('HX-Request', 'true');
        $this->manager->success('One');

        $response = new RedirectResponse('/elsewhere');
        $this->delivery->deliver($this->request, $response);

        $this->assertFalse($response->headers->has('HX-Trigger'));
        $this->assertSame(1, $this->queueSize());
    }

    #[Test]
    public function it_ignores_requests_without_session(): void
    {
        $this->manager->success('One');

        $response = new JsonResponse(['status' => 'ok']);
        $this->delivery->deliver(new Request(), $response);

        $this->assertSame('{"status":"ok"}', $response->getContent());
        $this->assertSame(1, $this->queueSize());
    }

    #[Test]
    public function it_does_not_start_session_without_previous_session(): void
    {
        $this->manager->success('One');
        $this->session->save();
        $this->assertFalse($this->session->isStarted());

        $response = new JsonResponse(['status' => 'ok']);
        $this->delivery->deliver($this->request, $response);

        $this->assertFalse($this->session->isStarted());
        $this->assertSame('{"status":"ok"}', $response->getContent());
        $this->assertSame([], $this->delivery->forView());
        $this->assertFalse($this->session->isStarted());
    }

    #[Test]
    public function it_delivers_when_unstarted_session_has_previous_session(): void
    {
        $this->manager->success('One');
        $this->session->save();
        $this->request->cookies->set($this->session->getName(), 'abc');

        $response = new JsonResponse(['status' => 'ok']);
        $this->delivery->deliver($this->request, $response);

        $this->assertArrayHasKey(AlertDelivery::JSON_KEY, json_decode($response->getContent(), true));
    }

    #[Test]
    public function it_leaves_queue_on_other_content_types(): void
    {
        $this->manager->success('One');

        $response = new BinaryFileResponse(__FILE__);
        $this->delivery->deliver($this->request, $response);

        $this->assertSame(1, $this->queueSize());
    }

    #[Test]
    public function it_does_not_append_turbo_streams_to_streamed_html_in_turbo_frame(): void
    {
        $this->request->headers->set('Turbo-Frame', 'frame');
        $this->manager->success('One');

        $response = new StreamedResponse(static function (): void {}, 200, ['Content-Type' => 'text/html']);
        $this->delivery->deliver($this->request, $response);

        $this->assertSame(1, $this->queueSize());
    }

    #[Test]
    public function it_does_not_append_turbo_streams_to_streamed_turbo_stream(): void
    {
        $this->manager->success('One');

        $response = new StreamedResponse(static function (): void {}, 200, ['Content-Type' => 'text/vnd.turbo-stream.html']);
        $this->delivery->deliver($this->request, $response);

        $this->assertSame(1, $this->queueSize());
    }

    #[Test]
    public function it_does_not_append_turbo_streams_to_binary_file_response(): void
    {
        $this->request->headers->set('Turbo-Frame', 'frame');
        $this->manager->success('One');

        $file = tempnam(sys_get_temp_dir(), 'alert');
        try {
            $response = new BinaryFileResponse($file, 200, ['Content-Type' => 'text/html']);
            $this->delivery->deliver($this->request, $response);
        } finally {
            unlink($file);
        }

        $this->assertSame(1, $this->queueSize());
    }

    #[Test]
    public function it_sets_hx_trigger_on_streamed_responses(): void
    {
        $this->request->headers->set('HX-Request', 'true');
        $this->manager->success('One');

        $response = new StreamedResponse(static function (): void {});
        $this->delivery->deliver($this->request, $response);

        $trigger = json_decode($response->headers->get('HX-Trigger'), true);
        $this->assertSame('One', $trigger[AlertDelivery::HX_EVENT_KEY]['alerts'][0]['title']);
        $this->assertSame(0, $this->queueSize());
    }

    #[Test]
    public function it_sets_alerts_on_empty_json_response(): void
    {
        $this->manager->success('One');

        $response = new JsonResponse();
        $this->delivery->deliver($this->request, $response);

        $data = json_decode($response->getContent(), true);
        $this->assertSame(['One'], array_column($data[AlertDelivery::JSON_KEY], 'title'));
        $this->assertSame(0, $this->queueSize());
    }

    #[Test]
    public function it_preserves_empty_objects_in_json(): void
    {
        $this->manager->success('One');

        $response = new JsonResponse(['meta' => new \stdClass()]);
        $this->delivery->deliver($this->request, $response);

        $this->assertStringContainsString('"meta":{}', $response->getContent());
        $this->assertSame(0, $this->queueSize());
    }

    #[Test]
    public function it_survives_invalid_utf8_in_hx_trigger(): void
    {
        $this->request->headers->set('HX-Request', 'true');
        $this->manager->success("bad\xB1");

        $response = new Response('ok');
        $this->delivery->deliver($this->request, $response);

        $this->assertIsArray(json_decode($response->headers->get('HX-Trigger'), true, flags: \JSON_THROW_ON_ERROR));
        $this->assertSame(0, $this->queueSize());
    }

    #[Test]
    public function it_survives_invalid_utf8_in_json(): void
    {
        $this->manager->success("bad\xB1");

        $response = new JsonResponse(['status' => 'ok']);
        $this->delivery->deliver($this->request, $response);

        $data = json_decode($response->getContent(), true, flags: \JSON_THROW_ON_ERROR);
        $this->assertArrayHasKey(AlertDelivery::JSON_KEY, $data);
        $this->assertSame(0, $this->queueSize());
    }

    #[Test]
    public function it_does_not_use_session_on_stateless_requests(): void
    {
        $this->manager->success('One');
        $this->session->save();
        $this->request->cookies->set($this->session->getName(), 'abc');
        $this->request->attributes->set('_stateless', true);

        $response = new JsonResponse(['status' => 'ok']);
        $this->delivery->deliver($this->request, $response);

        $this->assertFalse($this->session->isStarted());
        $this->assertSame('{"status":"ok"}', $response->getContent());
        $this->assertSame([], $this->delivery->forView());
        $this->assertFalse($this->session->isStarted());
    }

    #[Test]
    public function it_leaves_queue_when_existing_hx_trigger_is_non_object_json(): void
    {
        $this->request->headers->set('HX-Request', 'true');
        $this->manager->success('One');

        $response = new Response('ok', headers: ['HX-Trigger' => '["a"]']);
        $this->delivery->deliver($this->request, $response);

        $this->assertSame('["a"]', $response->headers->get('HX-Trigger'));
        $this->assertSame(1, $this->queueSize());
    }

    #[Test]
    public function it_preserves_empty_objects_in_existing_hx_trigger(): void
    {
        $this->request->headers->set('HX-Request', 'true');
        $this->manager->success('One');

        $response = new Response('ok', headers: ['HX-Trigger' => '{"x":{}}']);
        $this->delivery->deliver($this->request, $response);

        $this->assertStringContainsString('"x":{}', $response->headers->get('HX-Trigger'));
        $this->assertSame(0, $this->queueSize());
    }

    #[Test]
    public function it_drains_queue_for_view(): void
    {
        $this->session->start();
        $this->manager->success('One');

        $alerts = $this->delivery->forView();

        $this->assertCount(1, $alerts);
        $this->assertSame(0, $this->queueSize());
    }

    private function queueSize(): int
    {
        return \count($this->session->get(AlertManagerInterface::ALERT_STORAGE_KEY, []));
    }
}
