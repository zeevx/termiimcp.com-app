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

#[Description('Send a pre-approved WhatsApp template message that includes a media attachment via a Termii device.')]
class SendTemplateWithMediaTool extends Tool
{
    use InteractsWithTermii;

    public function handle(Request $request): Response
    {
        $request->validate([
            'to' => ['required', 'string'],
            'device_id' => ['required', 'string'],
            'template_id' => ['required', 'string'],
            'media_url' => ['required', 'string', 'url'],
            'media_caption' => ['nullable', 'string'],
            'data' => ['nullable', 'array'],
        ]);

        return $this->run(fn (LaraTermii $termii) => $termii->sendTemplateWithMedia(
            to: $request->get('to'),
            deviceId: $request->get('device_id'),
            templateId: $request->get('template_id'),
            mediaUrl: $request->get('media_url'),
            mediaCaption: $request->get('media_caption'),
            data: $request->get('data') ?: [],
        ));
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'to' => $schema->string()
                ->description('Destination phone number in international format.')
                ->required(),
            'device_id' => $schema->string()
                ->description('The Termii device ID registered for the template.')
                ->required(),
            'template_id' => $schema->string()
                ->description('The approved template ID to send.')
                ->required(),
            'media_url' => $schema->string()
                ->description('Publicly accessible URL of the media file to attach.')
                ->required(),
            'media_caption' => $schema->string()
                ->description('Optional caption for the attached media.'),
            'data' => $schema->object()
                ->description('Key-value map of template placeholder values.'),
        ];
    }
}
