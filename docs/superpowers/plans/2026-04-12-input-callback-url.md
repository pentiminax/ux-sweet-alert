# Input Callback URL Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Permettre de récupérer la valeur d'un input SweetAlert2 côté PHP via un `callback` URL, en injectant automatiquement un objet `Result` dans le controller grâce à un `ValueResolver` Symfony.

**Architecture:** `AlertManager::input()` accepte un paramètre `callback` (URL). L'URL est sérialisée dans le JSON de l'alerte et transmise au Stimulus controller. Quand l'utilisateur confirme, le controller fait un `fetch` POST vers cette URL avec le résultat en JSON body. Symfony désérialise automatiquement le body en objet `Result` via un `ValueResolver`.

**Tech Stack:** PHP 8.3, Symfony 7, PHPUnit 11, Stimulus (JS), SweetAlert2

---

## Fichiers concernés

| Action | Fichier | Responsabilité |
|--------|---------|----------------|
| Modify | `src/Model/Alert.php` | Ajouter propriété `callbackUrl` + setter + `jsonSerialize` |
| Modify | `src/AlertManager.php` | Ajouter paramètre `callback` à `input()` |
| Modify | `src/AlertManagerInterface.php` | Mettre à jour la signature de `input()` |
| Create | `src/ValueResolver/ResultValueResolver.php` | Désérialiser le JSON body en `Result` |
| Modify | `config/services.php` | Enregistrer `ResultValueResolver` comme service tagué |
| Modify | `assets/dist/controller.js` | Détecter `callbackUrl` et faire le `fetch` POST |
| Modify | `tests/Model/AlertTest.php` | Tester `callbackUrl` dans `jsonSerialize()` |
| Modify | `tests/AlertManagerTest.php` | Tester `input()` avec `callback` |
| Create | `tests/ValueResolver/ResultValueResolverTest.php` | Tester le ValueResolver |

---

## Task 1 — Ajouter `callbackUrl` à `Alert`

**Files:**
- Modify: `src/Model/Alert.php`
- Modify: `tests/Model/AlertTest.php`

- [ ] **Step 1 : Écrire le test qui échoue**

Dans `tests/Model/AlertTest.php`, ajouter après le dernier test :

```php
#[Test]
public function it_includes_callback_url_in_serialized_output(): void
{
    $alert = Alert::new(title: 'Test');
    $alert->callbackUrl('/api/callback');

    $data = $alert->jsonSerialize();

    $this->assertSame('/api/callback', $data['callbackUrl']);
}

#[Test]
public function it_omits_callback_url_from_serialized_output_when_not_set(): void
{
    $alert = Alert::new(title: 'Test');

    $data = $alert->jsonSerialize();

    $this->assertArrayNotHasKey('callbackUrl', $data);
}
```

- [ ] **Step 2 : Lancer les tests pour vérifier qu'ils échouent**

```bash
cd /Users/tanguylemarie/Workspace/pentiminax/ux-sweet-alert
vendor/bin/phpunit tests/Model/AlertTest.php --filter "it_includes_callback_url|it_omits_callback_url" -v
```

Résultat attendu : FAIL — `Call to undefined method callbackUrl()`

- [ ] **Step 3 : Implémenter dans `Alert.php`**

Dans `src/Model/Alert.php`, après la propriété `validationMessage` (ligne ~91), ajouter :

```php
private string $callbackUrl = '';
```

Après la méthode `getValidationMessage()` (ligne ~504), ajouter :

```php
public function callbackUrl(string $url): self
{
    $this->callbackUrl = $url;

    return $this;
}
```

Dans `jsonSerialize()`, après `'validationMessage' => $this->validationMessage,` (avant le `if ($this->toast)`), ajouter :

```php
if ($this->callbackUrl !== '') {
    $data['callbackUrl'] = $this->callbackUrl;
}
```

- [ ] **Step 4 : Lancer les tests pour vérifier qu'ils passent**

```bash
vendor/bin/phpunit tests/Model/AlertTest.php -v
```

Résultat attendu : OK — tous les tests de `AlertTest` passent.

- [ ] **Step 5 : Commit**

```bash
git add src/Model/Alert.php tests/Model/AlertTest.php
git commit -m "feat(model): add callbackUrl property to Alert"
```

---

## Task 2 — Mettre à jour `AlertManager::input()` et l'interface

**Files:**
- Modify: `src/AlertManager.php`
- Modify: `src/AlertManagerInterface.php`
- Modify: `tests/AlertManagerTest.php`

- [ ] **Step 1 : Écrire le test qui échoue**

Dans `tests/AlertManagerTest.php`, ajouter après `it_configures_input_alert_via_input_type` :

```php
#[Test]
public function it_stores_callback_url_on_input_alert(): void
{
    $alert = $this->alertManager->input(
        inputType: new \Pentiminax\UX\SweetAlert\InputType\Text(label: 'Name'),
        title: 'Enter name',
        callback: '/profile/update',
    );

    $data = $alert->jsonSerialize();

    $this->assertSame('/profile/update', $data['callbackUrl']);
}

#[Test]
public function it_does_not_include_callback_url_when_not_provided(): void
{
    $alert = $this->alertManager->input(
        inputType: new \Pentiminax\UX\SweetAlert\InputType\Text(label: 'Name'),
        title: 'Enter name',
    );

    $data = $alert->jsonSerialize();

    $this->assertArrayNotHasKey('callbackUrl', $data);
}
```

- [ ] **Step 2 : Lancer les tests pour vérifier qu'ils échouent**

```bash
vendor/bin/phpunit tests/AlertManagerTest.php --filter "it_stores_callback_url|it_does_not_include_callback_url" -v
```

Résultat attendu : FAIL — `Unknown named argument $callback`

- [ ] **Step 3 : Mettre à jour `AlertManagerInterface.php`**

Remplacer la signature de `input()` (lignes 95–104) :

```php
public function input(
    InputTypeInterface $inputType,
    string $title,
    string $id = '',
    string $text = '',
    ?Icon $icon = null,
    Position $position = Position::CENTER,
    ?Theme $theme = null,
    array $customClass = [],
    string $callback = '',
): Alert;
```

- [ ] **Step 4 : Mettre à jour `AlertManager.php`**

Remplacer la méthode `input()` (lignes 207–224) :

```php
public function input(
    InputTypeInterface $inputType,
    string $title,
    string $id = '',
    string $text = '',
    ?Icon $icon = null,
    Position $position = Position::CENTER,
    ?Theme $theme = null,
    array $customClass = [],
    string $callback = '',
): Alert {
    $alert = $this->createAlert($id, $title, $text, $position, $theme, $icon, $customClass);

    $inputType->configure($alert);

    if ($callback !== '') {
        $alert->callbackUrl($callback);
    }

    $this->addAlert($alert);

    return $alert;
}
```

- [ ] **Step 5 : Lancer les tests pour vérifier qu'ils passent**

```bash
vendor/bin/phpunit tests/AlertManagerTest.php -v
```

Résultat attendu : OK — tous les tests de `AlertManagerTest` passent.

- [ ] **Step 6 : Commit**

```bash
git add src/AlertManager.php src/AlertManagerInterface.php tests/AlertManagerTest.php
git commit -m "feat(manager): add callback URL parameter to AlertManager::input()"
```

---

## Task 3 — Créer `ResultValueResolver`

**Files:**
- Create: `src/ValueResolver/ResultValueResolver.php`
- Create: `tests/ValueResolver/ResultValueResolverTest.php`
- Modify: `config/services.php`

- [ ] **Step 1 : Écrire le test qui échoue**

Créer `tests/ValueResolver/ResultValueResolverTest.php` :

```php
<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\Tests\ValueResolver;

use Pentiminax\UX\SweetAlert\Model\Result;
use Pentiminax\UX\SweetAlert\ValueResolver\ResultValueResolver;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

/**
 * @internal
 */
#[CoversClass(ResultValueResolver::class)]
final class ResultValueResolverTest extends TestCase
{
    private ResultValueResolver $resolver;

    protected function setUp(): void
    {
        $this->resolver = new ResultValueResolver();
    }

    #[Test]
    public function it_resolves_result_from_json_body(): void
    {
        $request = Request::create('/', 'POST', content: json_encode([
            'isConfirmed' => true,
            'isDenied'    => false,
            'isDismissed' => false,
            'value'       => 'Tanguy',
        ]));

        $argument = $this->createArgumentMetadata(Result::class);

        $results = [...$this->resolver->resolve($request, $argument)];

        $this->assertCount(1, $results);
        $this->assertInstanceOf(Result::class, $results[0]);
        $this->assertTrue($results[0]->isConfirmed);
        $this->assertFalse($results[0]->isDenied);
        $this->assertFalse($results[0]->isDismissed);
        $this->assertSame('Tanguy', $results[0]->value);
    }

    #[Test]
    public function it_resolves_dismissed_result(): void
    {
        $request = Request::create('/', 'POST', content: json_encode([
            'isConfirmed' => false,
            'isDenied'    => false,
            'isDismissed' => true,
            'value'       => null,
        ]));

        $argument = $this->createArgumentMetadata(Result::class);

        $results = [...$this->resolver->resolve($request, $argument)];

        $this->assertCount(1, $results);
        $this->assertFalse($results[0]->isConfirmed);
        $this->assertTrue($results[0]->isDismissed);
        $this->assertNull($results[0]->value);
    }

    #[Test]
    public function it_returns_empty_when_argument_type_is_not_result(): void
    {
        $request  = Request::create('/', 'POST', content: '{}');
        $argument = $this->createArgumentMetadata(\stdClass::class);

        $results = [...$this->resolver->resolve($request, $argument)];

        $this->assertEmpty($results);
    }

    #[Test]
    public function it_returns_empty_when_body_is_not_valid_json(): void
    {
        $request  = Request::create('/', 'POST', content: 'not-json');
        $argument = $this->createArgumentMetadata(Result::class);

        $results = [...$this->resolver->resolve($request, $argument)];

        $this->assertEmpty($results);
    }

    #[Test]
    public function it_returns_empty_when_body_is_empty(): void
    {
        $request  = Request::create('/', 'POST', content: '');
        $argument = $this->createArgumentMetadata(Result::class);

        $results = [...$this->resolver->resolve($request, $argument)];

        $this->assertEmpty($results);
    }

    private function createArgumentMetadata(string $type): ArgumentMetadata
    {
        $argument = $this->createMock(ArgumentMetadata::class);
        $argument->method('getType')->willReturn($type);

        return $argument;
    }
}
```

- [ ] **Step 2 : Lancer le test pour vérifier qu'il échoue**

```bash
vendor/bin/phpunit tests/ValueResolver/ResultValueResolverTest.php -v
```

Résultat attendu : FAIL — `Class "Pentiminax\UX\SweetAlert\ValueResolver\ResultValueResolver" not found`

- [ ] **Step 3 : Créer `ResultValueResolver.php`**

Créer `src/ValueResolver/ResultValueResolver.php` :

```php
<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\ValueResolver;

use Pentiminax\UX\SweetAlert\Model\Result;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final class ResultValueResolver implements ValueResolverInterface
{
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (Result::class !== $argument->getType()) {
            return [];
        }

        $content = $request->getContent();

        if ('' === $content) {
            return [];
        }

        $data = json_decode($content, true);

        if (!\is_array($data)) {
            return [];
        }

        return [Result::fromArray($data)];
    }
}
```

- [ ] **Step 4 : Lancer les tests pour vérifier qu'ils passent**

```bash
vendor/bin/phpunit tests/ValueResolver/ResultValueResolverTest.php -v
```

Résultat attendu : OK — 5 tests passent.

- [ ] **Step 5 : Enregistrer le service dans `config/services.php`**

Dans `config/services.php`, ajouter avant la ligne `return static function` (ou à la fin du bloc des services), après les imports existants, ajouter l'import :

```php
use Pentiminax\UX\SweetAlert\ValueResolver\ResultValueResolver;
```

Puis dans le corps de la fonction, après l'enregistrement de `RenderAlertListener`, ajouter :

```php
$services
    ->set(ResultValueResolver::class)
    ->tag('controller.argument_value_resolver', ['priority' => 50]);
```

- [ ] **Step 6 : Lancer toute la suite de tests**

```bash
vendor/bin/phpunit -v
```

Résultat attendu : OK — toute la suite passe.

- [ ] **Step 7 : Commit**

```bash
git add src/ValueResolver/ResultValueResolver.php tests/ValueResolver/ResultValueResolverTest.php config/services.php
git commit -m "feat(resolver): add ResultValueResolver to inject Result from POST JSON body"
```

---

## Task 4 — Mettre à jour le Stimulus controller JS

**Files:**
- Modify: `assets/dist/controller.js`

- [ ] **Step 1 : Localiser la boucle `connect()` dans le controller**

Dans `assets/dist/controller.js`, la boucle à modifier se trouve lignes 88–97 :

```js
const toasts = this.viewValue;
for (const toast of toasts) {
    const toastId = toast.id;
    const result = await this.fireAlert(toast, this.element);

    document.dispatchEvent(new CustomEvent(`ux-sweet-alert:${toastId}:closed`, {
        detail: result
    }));
}
```

- [ ] **Step 2 : Remplacer la boucle par la version avec gestion du `callbackUrl`**

Remplacer les lignes 88–97 par :

```js
const toasts = this.viewValue;
for (const toast of toasts) {
    const toastId = toast.id;
    const callbackUrl = toast.callbackUrl ?? null;

    const swalOptions = Object.assign({}, toast);
    delete swalOptions.callbackUrl;

    const result = await this.fireAlert(swalOptions, this.element);

    if (callbackUrl) {
        const response = await fetch(callbackUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                isConfirmed: result.isConfirmed,
                isDenied: result.isDenied,
                isDismissed: result.isDismissed,
                value: result.value ?? null,
            }),
            redirect: 'follow',
        });

        if (response.redirected) {
            window.location.href = response.url;
        } else if (response.headers.get('Content-Type')?.includes('application/json')) {
            const data = await response.json();
            this.element.dispatchEvent(new CustomEvent('ux-sweet-alert:callback:response', {
                bubbles: true,
                detail: data,
            }));
        }
    } else {
        document.dispatchEvent(new CustomEvent(`ux-sweet-alert:${toastId}:closed`, {
            detail: result
        }));
    }
}
```

- [ ] **Step 3 : Vérifier manuellement le comportement**

Sans callbackUrl (comportement inchangé) :
- Ajouter une alerte sans `callback` → le controller dispatch bien `ux-sweet-alert:{id}:closed`

Avec callbackUrl :
1. Créer une route de test `POST /test-callback` qui reçoit `Result $result` et retourne `$this->redirectToRoute('app_home')`
2. Appeler `$alertManager->input(..., callback: '/test-callback')` dans un controller
3. Ouvrir la page, confirmer l'input → la page doit se rediriger correctement

Avec JSON response :
1. Créer une route de test qui retourne `$this->json(['status' => 'ok'])`
2. Confirmer l'input → inspecter les browser events pour voir `ux-sweet-alert:callback:response` avec `{ status: 'ok' }`

- [ ] **Step 4 : Commit**

```bash
git add assets/dist/controller.js
git commit -m "feat(js): handle callbackUrl in Stimulus controller for fetch-based result submission"
```

---

## Vérification finale

```bash
# Toute la suite PHP
vendor/bin/phpunit -v

# Vérifier que controller.js est valide JS (pas d'erreur de syntaxe)
node --check assets/dist/controller.js
```

Résultat attendu : 0 erreur, toute la suite PHP passe.
