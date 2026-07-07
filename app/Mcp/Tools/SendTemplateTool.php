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

#[Description('Send a pre-approved WhatsApp/SMS template message via a Termii device, filling in template variables.')]
class SendTemplateTool extends Tool
{
    use InteractsWithTermii;

    public function handle(Request $request): Response
    {
        $request->validate([
            'to' => ['required', 'string'],
            'device_id' => ['required', 'string'],
            'template_id' => ['required', 'string'],
            'data' => ['nullable', 'array'],
        ]);

        return $this->run(fn (LaraTermii $termii) => $termii->sendTemplate(
            to: $request->get('to'),
            deviceId: $request->get('device_id'),
            templateId: $request->get('template_id'),
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
            'data' => $schema->object()
                ->description('Key-value map of template placeholder values, e.g. {"product_name": "Widget", "code": "1234"}.'),
        ];
    }
}
