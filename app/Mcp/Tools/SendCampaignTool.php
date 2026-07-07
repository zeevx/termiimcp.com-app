<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Zeevx\LaraTermii\LaraTermii;
use Laravel\Mcp\Server\Attributes\Description;
use App\Mcp\Tools\Concerns\InteractsWithTermii;
use Illuminate\Contracts\JsonSchema\JsonSchema;

#[Description('Launch an SMS/WhatsApp campaign to every contact in a Termii phonebook.')]
class SendCampaignTool extends Tool
{
    use InteractsWithTermii;

    public function handle(Request $request): Response
    {
        $request->validate([
            'country_code' => ['required', 'string'],
            'sender_id' => ['required', 'string'],
            'message' => ['required', 'string'],
            'phonebook_id' => ['required', 'string'],
            'channel' => ['nullable', 'string', 'in:generic,dnd,whatsapp'],
            'message_type' => ['nullable', 'string', 'in:plain,flash'],
            'options' => ['nullable', 'array'],
        ]);

        return $this->run(fn (LaraTermii $termii) => $termii->sendCampaign(
            countryCode: $request->get('country_code'),
            senderId: $request->get('sender_id'),
            message: $request->get('message'),
            phonebookId: $request->get('phonebook_id'),
            channel: $request->get('channel') ?: 'generic',
            messageType: $request->get('message_type') ?: 'plain',
            options: $request->get('options') ?: [],
        ));
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'country_code' => $schema->string()
                ->description('Dialing country code for the campaign, e.g. "234".')
                ->required(),
            'sender_id' => $schema->string()
                ->description('Approved Sender ID to send the campaign from.')
                ->required(),
            'message' => $schema->string()
                ->description('The campaign message body.')
                ->required(),
            'phonebook_id' => $schema->string()
                ->description('The ID of the phonebook to send the campaign to.')
                ->required(),
            'channel' => $schema->string()
                ->description('Route for the campaign.')
                ->enum(['generic', 'dnd', 'whatsapp'])
                ->default('generic'),
            'message_type' => $schema->string()
                ->description('Message type.')
                ->enum(['plain', 'flash'])
                ->default('plain'),
            'options' => $schema->object()
                ->description('Optional additional campaign parameters, e.g. scheduling options, as a key-value map.'),
        ];
    }
}
