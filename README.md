# Termii MCP

A hosted [Model Context Protocol](https://modelcontextprotocol.io) (MCP) server that gives AI
assistants like **Claude** and **ChatGPT** the ability to send SMS and WhatsApp messages, run
campaigns, and manage phonebooks through [Termii](https://termii.com), in natural language.

It wraps the [`zeevx/lara-termii`](https://github.com/zeevx/lara-termii) package and exposes the full
Termii API as **22 MCP tools**, built with the official [`laravel/mcp`](https://laravel.com/ai/mcp)
package.

> Live at **[termiimcp.com](https://termiimcp.com)**. The landing page turns your Termii API key into a
> personal connector link that you add to your assistant.

## How it works

The MCP server is **multi-tenant and stateless**. Each user connects with a server URL that carries an
encrypted token in a `key` query parameter:

```
https://termiimcp.com/mcp?key=<ENCRYPTED_TOKEN>
```

This is because Claude's and ChatGPT's connector forms only let you edit the URL; Claude has no field
for a static bearer token (it supports OAuth only). To avoid putting the raw key in the URL, the landing
page posts your key to `/connect`, which encrypts it with the server's `APP_KEY` and returns the token.
Only this server can decrypt it, and every tool call decrypts the token to reach the underlying Termii
account. Programmatic clients may instead send the raw key as an `Authorization: Bearer <key>` header.

The key is never stored; it lives only for the duration of the request. There is no database, no
sign-up, and no shared credentials.

## Capabilities

| Group | Tools |
| --- | --- |
| **Messaging** | `send-message`, `send-bulk-message`, `send-template`, `send-template-with-media` |
| **Account & lookup** | `get-balance`, `get-message-history`, `get-phone-status`, `search-phone-number` |
| **Sender IDs** | `list-sender-ids`, `submit-sender-id` |
| **Phonebooks** | `list-phonebooks`, `create-phonebook`, `update-phonebook`, `delete-phonebook` |
| **Contacts** | `list-contacts`, `add-contact`, `add-contacts-from-contents`, `delete-contact` |
| **Campaigns** | `send-campaign`, `list-campaigns`, `get-campaign-history`, `retry-campaign` |

Read-only tools are annotated `IsReadOnly`, deletes `IsDestructive`, and updates `IsIdempotent`, so
clients can surface the right confirmations.

## Requirements

- PHP 8.3+
- Composer
- Node.js 20+ (for building the landing page assets)

## Installation

```bash
git clone https://github.com/<your-org>/termiimcp.git
cd termiimcp

composer install
npm install

cp .env.example .env
php artisan key:generate

npm run build
```

Then serve it:

```bash
php artisan serve
# MCP endpoint: http://localhost:8000/mcp
# Landing page: http://localhost:8000
```

Or run the full dev stack (server + Vite) with `composer dev`.

### Configuration

This is a light, database-free application. Cache, sessions, and the queue use file/sync drivers out
of the box, so there is nothing to migrate.

Because every request authenticates with the caller's own key, **no `TERMII_API_KEY` is required** on
the server. The `TERMII_*` values in `.env` act only as optional fallbacks for a single-tenant
deployment. In production, set `APP_URL` to your public HTTPS domain so generated URLs (and the OG
image) resolve correctly.

Connector tokens are encrypted with the app's `APP_KEY`, so keep it stable in production. Rotating
`APP_KEY` invalidates every existing connector link, and users would need to reconnect.

The public endpoint is rate limited per API token (60 requests/minute), configured in
`app/Providers/AppServiceProvider.php`.

## Connecting from an AI client

The easiest path is the [termiimcp.com](https://termiimcp.com) landing page: paste your Termii API key
and it generates your personal connector link (with the encrypted token) plus one-click buttons.

Under the hood, your link looks like `https://termiimcp.com/mcp?key=<ENCRYPTED_TOKEN>`, where the token
comes from the `/connect` endpoint.

**Claude**: the "Add to Claude" button opens a prefilled connector dialog of the form:

```
https://claude.ai/customize/connectors?modal=add-custom-connector&connectorName=Termii+MCP&connectorUrl=<URL-ENCODED_CONNECTOR_URL>
```

**ChatGPT**: enable *Settings, Connectors, Advanced, Developer mode*, choose **Create**, and paste the
connector URL. (ChatGPT connectors require a paid plan.)

Once connected, ask things like _"check my Termii balance"_ or _"send an SMS to 2348012345678"_.

## Testing

```bash
php artisan test --compact
```

Tests use Laravel's HTTP client fakes, so they never hit the real Termii API.

## Project layout

```
app/Mcp/Servers/TermiiServer.php                  The MCP server: registers tools + instructions
app/Mcp/Tools/                                    One class per Termii action (22 tools)
app/Mcp/Tools/Concerns/InteractsWithTermii.php    Resolves the client from the bearer token
app/Http/Controllers/HomeController.php            The landing page
routes/ai.php                                     Registers the web MCP server at /mcp
```

> Note: `LaraTermii::addContactsFromFile()` is intentionally **not** exposed as a tool. It reads a file
> from a server-side storage disk, which a remote AI client cannot reference. Use
> `add-contacts-from-contents` (raw CSV text) instead.

## Contributing

Contributions are welcome. Please run `vendor/bin/pint` and `php artisan test` before opening a pull
request. New tools follow the pattern in `app/Mcp/Tools` and register in `TermiiServer::$tools`.

## Credits

- [Laravel MCP](https://laravel.com/ai/mcp)
- [zeevx/lara-termii](https://github.com/zeevx/lara-termii)
- [Termii](https://termii.com)

## License

Released under the [MIT License](LICENSE).
