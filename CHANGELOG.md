# Changelog

## Unreleased

- Added `Alert::width()`, `padding()`, `background()`, `color()`, `setImageWidth()`, `showClass()` and `hideClass()`, serialized only when set. `showClass()`/`hideClass()` accept a free class string or an `Animation` enum preset (Animate.css classes; Animate.css must be loaded by the app).
- Added `PreConfirmResponse::fromViolations()` to answer `preConfirmUrl()` requests from a Symfony Validator violation list (`422 {"message": first violation}`, else `200` with the given value or an empty `204`). `symfony/validator` is suggested, not required.
- Added `Alert::preConfirmUrl()`: the confirmed value is POSTed as `{value}` to the URL while the modal shows a loader; a non-2xx response (e.g. `422 {"message": "..."}`) keeps the modal open with that message, a 2xx JSON body becomes `result.value`. Requests carry `X-SweetAlert-PreConfirm`, which keeps `AlertDelivery` from draining the alert queue.
- HX-Trigger now carries all queued alerts as an `alerts` array and merges with an existing `HX-Trigger` header instead of overwriting it.
- The JSON key is now `ux_sweet_alert`, an array of alert objects (was `alerts` holding Turbo stream HTML), and is not set if the key already exists.
- Responses that cannot display alerts, redirects and sessionless requests no longer consume the alert queue; sessions are never started to look for alerts.
- Turbo streams are now delivered on turbo-stream responses and `Turbo-Frame` requests; Turbo-Frame responses get the streams inserted inside the requested frame, and if the frame isn't in the response the alerts stay queued.
- Removed `HxTriggerHelper`.
- JS assets (`assets/dist`) were rebuilt.
- BC break: the public constructors of `AlertExtension` and `RenderAlertListener` now take the internal `AlertDelivery`; this affects anyone instantiating or extending them directly.
- JSON delivery skips streamed/binary responses and `_stateless` routes.
