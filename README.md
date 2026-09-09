# Tickets

Tickets is a blogging and comments package for MODX Revolution. It adds ticket sections, front-end ticket forms, threaded or flat comments, voting, favorites, subscriptions, file uploads, and author ratings.

Current release: **1.14.0-pl** (2026-07-12). That build targets MODX 2.x, cleans up PHP 8 / 8.2 issues, and is the baseline for a later MODX 3 port.

## Features

- Ticket and section resource classes with manager UI
- Front-end create/edit via `TicketForm`
- Comments via `TicketComments` (tree or flat with `&tree=0`)
- Multiple comment threads on one page
- Latest tickets/comments via `TicketLatest`
- Lists and meta: `getTickets`, `getTicketsSections`, `TicketMeta`, `getComments`, `getStars`
- Author subscribe flow (`subscribeAuthor`)
- Votes, stars, email notifications, BCC for admins
- Image/file uploads on tickets (and comments where enabled)
- Lexicons: ru, en, uk, de

## Requirements

- MODX Revolution 2.x
- PHP 8.0+ recommended; 1.14.0 is tested against PHP 8.2+ deprecations
- [pdoTools](https://github.com/modx-pro/pdoTools) (used by the snippets)

Exact MODX minor versions depend on your site; install from the package and run a smoke test on comments and ticket save.

## Install

1. Install from [modstore.pro](https://modstore.pro) or build a transport package with `_build/build.transport.php` against a local MODX 2 core.
2. Clear the MODX cache.
3. Create a Tickets section in the tree, then use the snippets on templates/resources.

See snippet properties in the manager for parameters. Changelog: [`core/components/tickets/docs/changelog.txt`](core/components/tickets/docs/changelog.txt).

## Development

```text
_build/          transport build scripts
assets/          web and manager JS/CSS
core/components/tickets/
```

Open pull requests against [modx-pro/Tickets](https://github.com/modx-pro/Tickets). Use conventional commits. Prefer one issue → one PR.

## Links

- Repository: https://github.com/modx-pro/Tickets
- Issues: https://github.com/modx-pro/Tickets/issues
- Release 1.14.0: https://github.com/modx-pro/Tickets/releases/tag/1.14.0
- Community note (RU): https://modx.pro/components/25646

## License

GPL. See [`core/components/tickets/docs/license.txt`](core/components/tickets/docs/license.txt).

Original author: Vasiliy Naumkin. Maintained under [modx-pro](https://github.com/modx-pro).
