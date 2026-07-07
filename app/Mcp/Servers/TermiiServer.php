<?php

declare(strict_types=1);

namespace App\Mcp\Servers;

use Laravel\Mcp\Server;
use App\Mcp\Tools\AddContactTool;
use App\Mcp\Tools\GetBalanceTool;
use App\Mcp\Tools\SendMessageTool;
use App\Mcp\Tools\ListContactsTool;
use App\Mcp\Tools\SendCampaignTool;
use App\Mcp\Tools\SendTemplateTool;
use App\Mcp\Tools\DeleteContactTool;
use App\Mcp\Tools\ListCampaignsTool;
use App\Mcp\Tools\ListSenderIdsTool;
use App\Mcp\Tools\RetryCampaignTool;
use App\Mcp\Tools\GetPhoneStatusTool;
use App\Mcp\Tools\ListPhonebooksTool;
use App\Mcp\Tools\SubmitSenderIdTool;
use App\Mcp\Tools\CreatePhonebookTool;
use App\Mcp\Tools\DeletePhonebookTool;
use App\Mcp\Tools\SendBulkMessageTool;
use App\Mcp\Tools\UpdatePhonebookTool;
use Laravel\Mcp\Server\Attributes\Name;
use App\Mcp\Tools\GetMessageHistoryTool;
use App\Mcp\Tools\SearchPhoneNumberTool;
use App\Mcp\Tools\GetCampaignHistoryTool;
use Laravel\Mcp\Server\Attributes\Version;
use App\Mcp\Tools\SendTemplateWithMediaTool;
use App\Mcp\Tools\AddContactsFromContentsTool;
use Laravel\Mcp\Server\Attributes\Instructions;

#[Name('Termii')]
#[Version('1.0.0')]
#[Instructions(<<<'INSTRUCTIONS'
    This server exposes the Termii messaging platform (SMS, WhatsApp, phonebooks and campaigns) as
    MCP tools.

    Authentication: every request runs against the Termii account whose API key is embedded in the
    server URL as a "?key=" query parameter (or, for programmatic clients, sent as an Authorization
    bearer token). You do not pass the API key as a tool argument.

    Conventions:
    - Phone numbers must be in international format without a leading "+", e.g. 2348012345678.
    - The "from" argument is your approved Termii Sender ID; pass it when sending messages.
    - "channel" is the delivery route: generic, dnd (transactional / bypasses DND) or whatsapp.
    - Tool results are the raw JSON returned by the Termii API.
    INSTRUCTIONS)]
class TermiiServer extends Server
{
    protected array $tools = [
        SendMessageTool::class,
        SendBulkMessageTool::class,
        SendTemplateTool::class,
        SendTemplateWithMediaTool::class,
        GetBalanceTool::class,
        GetMessageHistoryTool::class,
        GetPhoneStatusTool::class,
        SearchPhoneNumberTool::class,
        ListSenderIdsTool::class,
        SubmitSenderIdTool::class,
        ListPhonebooksTool::class,
        CreatePhonebookTool::class,
        UpdatePhonebookTool::class,
        DeletePhonebookTool::class,
        ListContactsTool::class,
        AddContactTool::class,
        AddContactsFromContentsTool::class,
        DeleteContactTool::class,
        SendCampaignTool::class,
        ListCampaignsTool::class,
        GetCampaignHistoryTool::class,
        RetryCampaignTool::class,
    ];
}
