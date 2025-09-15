# Events Plugin

This plugin provides a simple Events management module for Mautic and now supports attaching contacts to events.

## Setup

1. Install the plugin under `plugins/MauticEventsBundle` and clear the cache.
2. Run the plugin migrations to create the `event_contacts` join table:
   ```bash
   php bin/console doctrine:migrations:migrate --prefix='MauticPlugin\\MauticEventsBundle'
   ```
3. Navigate to **Events** in the Mautic sidebar.

## Features

- Create, edit, and delete events.
- Search for contacts and attach one or many to an event.
- List and remove attached contacts from the event detail page.

## Tests

Run the test suite with:

```bash
composer test
```

Note: The testing stack requires PHPUnit to be installed.
