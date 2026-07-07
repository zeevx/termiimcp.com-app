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

#[Description('Send a single SMS or WhatsApp message to one recipient through Termii.')]
class SendMessageTool extends Tool
{
    use InteractsWithTermii;

    public function handle(Request $request): Response
    {
        $request->validate([
            'to' => ['required', 'string'],
            'sms' => ['required', 'string'],
            'from' => ['nullable', 'string'],
            'channel' => ['nullable', 'string', 'in:generic,dnd,whatsapp'],
            'media_url' => ['nullable', 'string', 'url'],
            'media_caption' => ['nullable', 'string'],
            'type' => ['nullable', 'string', 'in:plain,flash'],
        ]);

        return $this->run(fn (LaraTermii $termii) => $termii->sendMessage(
            to: $request->get('to'),
            from: $request->get('from'),
            sms: $request->get('sms'),
            channel: $request->get('channel'),
            mediaUrl: $request->get('media_url'),
            mediaCaption: $request->get('media_caption'),
            type: $request->get('type') ?: 'plain',
        ));
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'to' => $schema->string()
                ->description('Destination phone number in international format, e.g. 2348012345678.')
                ->required(),
            'sms' => $schema->string()
                ->description('The message body to send.')
                ->required(),
            'from' => $schema->string()
                ->description('Approved Sender ID or device to send from. Defaults to the account Sender ID when omitted.'),
            'channel' => $schema->string()
                ->description('Route for the message.')
                ->enum(['generic', 'dnd', 'whatsapp']),
            'media_url' => $schema->string()
                ->description('Publicly accessible URL of a media file (WhatsApp only).'),
            'media_caption' => $schema->string()
                ->description('Caption for the attached media (WhatsApp only).'),
            'type' => $schema->string()
                ->description('Message type.')
                ->enum(['plain', 'flash'])
                ->default('plain'),
        ];
    }
}
