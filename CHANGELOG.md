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
- Alerts now display one at a time, in arrival order, across all transports.
- `callbackUrl` is now honored for Turbo-stream, htmx and JSON alerts.
- The `ux-sweet-alert:{id}:closed` event now fires for every alert with an id.
- `ux-sweet-alert:callback:response` is now dispatched on the alert's origin element.
- Turbo streams no longer block until the popup closes.
- An open popup is closed on Turbo Drive navigation.
- Malformed JSON responses no longer break the app's `fetch()`.
- Removed the internal modules `alert-dispatcher` and `callback-resolver` (replaced by `presenter`).
