# Input Callback URL — Design Spec

**Date**: 2026-04-12  
**Status**: Approved

## Context

`AlertManager::input()` permet d'afficher un dialog SweetAlert2 avec un champ de saisie. Actuellement, aucun mécanisme PHP ne permet de récupérer la valeur saisie par l'utilisateur après validation. Le seul hook disponible (`InputModal::onResult()`) impose de créer un LiveComponent dédié pour chaque usage.

L'objectif est d'offrir une DX maximale dans les deux contextes d'usage (controller HTTP classique et LiveComponent) sans rupture de compatibilité avec l'existant.

## Solution retenue : fetch + ValueResolver

Quand un `callback` (URL) est fourni à `input()`, le Stimulus controller fait un `fetch` POST vers cette URL après confirmation. Symfony injecte le `Result` désérialisé directement comme paramètre du controller via un `ValueResolver`.

## Architecture & Flow

```
[Controller PHP]
    └─ $alertManager->input(..., callback: '/profile/update')
         └─ Alert stockée en session avec callbackUrl

[Twig render]
    └─ alerts sérialisées en JSON → Stimulus controller

[SweetAlert2 JS]
    └─ Utilisateur confirme
         └─ Stimulus détecte callbackUrl → fetch POST JSON
              ├─ body: { isConfirmed, isDenied, isDismissed, value }
              └─ header: X-Requested-With: XMLHttpRequest

[Symfony routing]
    └─ POST /profile/update → updateProfile(Result $result)
         └─ ResultValueResolver désérialise le body → Result

[Controller callback]
    └─ Retourne redirect OU JsonResponse

[JS — gestion réponse]
    ├─ response.redirected = true → window.location.href = response.url
    └─ Content-Type: application/json → dispatch 'ux-sweet-alert:callback:response'
```

## Fichiers à créer / modifier

### Nouveau : `src/ValueResolver/ResultValueResolver.php`

```php
final class ResultValueResolver implements ValueResolverInterface
{
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if ($argument->getType() !== Result::class) {
            return [];
        }

        $data = json_decode($request->getContent(), true);
        if (!is_array($data)) {
            return [];
        }

        return [Result::fromArray($data)];
    }
}
```

### Modifié : `src/Model/Alert.php`

Ajout de la propriété `callbackUrl` et de son setter fluent :

```php
private string $callbackUrl = '';

public function callbackUrl(string $url): static
{
    $this->callbackUrl = $url;
    return $this;
}
```

`jsonSerialize()` inclut `callbackUrl` si non vide :

```php
'callbackUrl' => $this->callbackUrl ?: null,
```

### Modifié : `src/AlertManager.php`

Nouveau paramètre `callback` sur `input()` :

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
): Alert
```

Si `$callback !== ''`, appel de `$alert->callbackUrl($callback)` avant `addAlert()`.

### Modifié : `src/AlertManagerInterface.php`

Même signature que `AlertManager::input()`.

### Modifié : `config/services.php`

```php
$services->set(ResultValueResolver::class)
    ->tag('controller.argument_value_resolver', ['priority' => 50]);
```

### Modifié : Stimulus controller JS

Après que SweetAlert2 resolve, avant la logique LiveComponent existante :

```javascript
if (alertConfig.callbackUrl) {
    const response = await fetch(alertConfig.callbackUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({
            isConfirmed: swalResult.isConfirmed,
            isDenied:    swalResult.isDenied,
            isDismissed: swalResult.isDismissed,
            value:       swalResult.value ?? null,
        }),
        redirect: 'follow',
    });

    if (response.redirected) {
        window.location.href = response.url;
    } else if (response.headers.get('Content-Type')?.includes('application/json')) {
        const data = await response.json();
        this.dispatch('callback:response', { detail: data });
    }
    return;
}
// ... logique LiveComponent existante inchangée
```

## Compatibilité

- Pas de `callback` fourni → comportement actuel inchangé (LiveComponent flow)
- `callback` fourni → fetch URL, court-circuite le LiveComponent flow
- Les deux contextes (controller et LiveComponent) sont supportés

## Usage final

```php
// Création de l'alerte
$alertManager->input(
    inputType: new Text(
        label: 'Display name',
        value: 'Tanguy',
        placeholder: 'Enter your display name',
    ),
    title: 'Update profile',
    text: 'This change is visible to other users.',
    callback: $this->generateUrl('app_profile_update'),
);

// Réception de la valeur
#[Route('/profile/update', name: 'app_profile_update', methods: ['POST'])]
public function updateProfile(Result $result): Response
{
    if ($result->isConfirmed) {
        $this->userService->updateDisplayName($result->value);
    }

    return $this->redirectToRoute('app_profile');
    // ou : return $this->json(['message' => 'Updated']);
}
```

## Hors scope (v1)

- Validation CSRF automatique (documenté comme responsabilité du dev)
- Support Turbo Streams (opt-in futur)
- Validation côté serveur de la valeur d'input
