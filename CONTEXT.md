# UX SweetAlert

A Symfony bundle that lets server-side code queue SweetAlert2 popups and shows them in the browser on whichever response can display them.

## Language

### Alerts

**Alert**:
A popup queued by server code (or a Live component) to be shown in the browser.
_Avoid_: notification, message, modal

**Toast**:
An Alert shown as a small, non-blocking popup in a corner, usually auto-dismissed.
_Avoid_: snackbar

**Flash message**:
A Symfony session flash that, when auto-conversion is enabled, becomes an Alert.

**Result**:
The user's answer to an Alert: confirmed, denied or dismissed, plus any input value.
_Avoid_: response, outcome

**Callback**:
Where a Result is sent after the Alert closes: a URL, a window function or a Live component action.

### Delivery

**Alert queue**:
The Alerts waiting in the session to be shown; draining it removes them.
_Avoid_: alert store, alert bag

**Alert delivery**:
Draining the Alert queue into the current response, exactly once, using the one Delivery transport that response can display.
_Avoid_: rendering alerts

**Delivery transport**:
A way an Alert reaches the browser: page view, Turbo stream, JSON `ux_sweet_alert` key or HX-Trigger header. When no transport fits the response, the Alert queue is left untouched.
_Avoid_: channel, strategy

### Presentation

**Alert presentation**:
Showing one delivered Alert in the browser, after the previous one has closed, and resolving its Result through at most one Callback.
_Avoid_: firing, dispatching
