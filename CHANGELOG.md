# Changelog

## Unreleased

- HX-Trigger now carries all queued alerts as an `alerts` array and merges with an existing `HX-Trigger` header instead of overwriting it.
- The JSON key is now `ux_sweet_alert`, an array of alert objects (was `alerts` holding Turbo stream HTML), and is not set if the key already exists.
- Responses that cannot display alerts, redirects and sessionless requests no longer consume the alert queue; sessions are never started to look for alerts.
- Turbo streams are now delivered on turbo-stream responses and `Turbo-Frame` requests; Turbo-Frame responses get the streams inserted inside the requested frame, and if the frame isn't in the response the alerts stay queued.
- Removed `HxTriggerHelper`.
- JS assets (`assets/dist`) were rebuilt.
- BC break: the public constructors of `AlertExtension` and `RenderAlertListener` now take the internal `AlertDelivery`; this affects anyone instantiating or extending them directly.
- JSON delivery skips streamed/binary responses and `_stateless` routes.
