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

#[Description('Send the same SMS or WhatsApp message to many recipients (up to 100) in one Termii request.')]
class SendBulkMessageTool extends Tool
{
    use InteractsWithTermii;

    public function handle(Request $request): Response
    {
        $request->validate([
            'to' => ['required', 'array', 'min:1', 'max:100'],
            'to.*' => ['required', 'string'],
            'sms' => ['required', 'string'],
            'from' => ['nullable', 'string'],
            'channel' => ['nullable', 'string', 'in:generic,dnd,whatsapp'],
            'type' => ['nullable', 'string', 'in:plain,flash'],
        ]);

        return $this->run(fn (LaraTermii $termii) => $termii->sendBulkMessage(
            to: $request->get('to'),
            from: $request->get('from'),
            sms: $request->get('sms'),
            channel: $request->get('channel'),
            type: $request->get('type') ?: 'plain',
        ));
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'to' => $schema->array()
                ->items($schema->string())
                ->description('List of destination phone numbers in international format. Maximum of 100.')
                ->required(),
            'sms' => $schema->string()
                ->description('The message body to send to every recipient.')
                ->required(),
            'from' => $schema->string()
                ->description('Approved Sender ID to send from. Defaults to the account Sender ID when omitted.'),
            'channel' => $schema->string()
                ->description('Route for the message.')
                ->enum(['generic', 'dnd', 'whatsapp']),
            'type' => $schema->string()
                ->description('Message type.')
                ->enum(['plain', 'flash'])
                ->default('plain'),
        ];
    }
}
