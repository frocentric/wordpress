# The Events Calendar Pro JavaScript

The Events Calendar Pro uses JavaScript to support the functionalities of the views. Below is a breakdown of the main categories of JavaScript we have to support our plugin.

## Extensions

These are extensions of The Events Calendar JavaScript files. Extendable scripts are described in the README.md of The Events Calendar JavaScript.

### Datepicker Pro

The Datepicker Pro JavaScript extends the datepicker from The Events Calendar to apply it to week view. This includes week selection and selected week highlight. The datepicker was not mentioned as an extendable script in the [JavaScript README.md of The Events Calendar](https://github.com/moderntribe/the-events-calendar/blob/master/src/resources/js/views/README.md) as it is a custom application and fairly complex. However, anyone who wishes to extend the datepicker can review the script on their own.

### Multiday Events Pro

The Multiday Events Pro JavaScript extends the multiday events script from The Events Calendar to apply it to week view. The same functionality as month view multiday events applies to the week view multiday events.

### Tooltip Pro

The Tooltip Pro JavaScript extends the tooltips from The Events Calendar to apply it to week view. The same functionality as month view tooltips applies to the week view tooltips.

## Map View

### Map Events Scroller

The Map Events Scroller JavaScript allows scrolling of the events in map view.

### Map Events

The Map Events JavaScript is an extendable script that listens for and fires events based on the interactions with the events. The current implementation of map view uses Google Maps, but theoretically any map can be used.

The script first initializes the map via the `beforeMapInit.tribeEvents`, `mapInit.tribeEvents`, and `afterMapInit.tribeEvents` events on the container. This is where the map initialization should occur. Once the map is initialized, the container fires a `beforeMapBindEvents.tribeEvents` event, each event is bound with click event listeners, and the container finally fires a `afterMapBindEvents.tribeEvents` event.

When an event is clicked, a `beforeMapEventClick.tribeEvents` event is fired from the container. This is where any actions should go before running the click handler runs. After the click handler, a `afterMapEventClick.tribeEvents` event is fired from the container. This is where any actions should go after running the click handler.

Upon any successful AJAX requests, the map events script will run a deinitialization method. The `beforeMapDeinit.tribeEvents`, `mapDeinit.tribeEvents`, and `afterMapDeinit.tribeEvents` events are fired from the container. This is where the map deinitialization should occur. Once the map is deinitialized, the container fires a `beforeMapUnbindEvents.tribeEvents` event, each event's click event listener is removed, and the container finally fires a `afterMapUnbindEvents.tribeEvents` event.

### Map No Venue Modal

The Map No Venue Modal JavaScript powers the no venue modal. This modal opens when an event with no venue is clicked.

### Map Provider Google Maps

The Map Provider Google Maps JavaScript runs Google Maps on top of the Map Events JavaScript events. This script initializes Google Maps, handles various events, and caches the map on a successful AJAX request.

## Week View

### Week Day Selector

The Week Day Selector JavaScript powers the day selector on mobile week view.

### Week Event Link

The Week Event Link JavaScript powers the week view events hover and focus intents. On intentional hover or focus, the script brings the event above the other events that may overlay it.

### Week Grid Scroller

The Week Grid Scroller JavaScript allows scrolling of events in week view.

### Week Multiday Toggle

The Week Multiday Toggle JavaScript powers the multiday toggle button on week view.

## Widgets

### Countdown Widget

The Countdown Widget JavaScript powers the countdown widget

## Others

### Toggle Recurrence

The Toggle Recurrence JavaScript powers the hide recurring events toggle on all views except month view.
