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

#[Description('Send an SMS from a Termii auto-generated number. No Sender ID is required, so this works before any Sender ID has been approved.')]
class SendMessageWithNumberTool extends Tool
{
    use InteractsWithTermii;

    public function handle(Request $request): Response
    {
        $request->validate([
            'to' => ['required', 'string'],
            'sms' => ['required', 'string'],
        ]);

        return $this->run(fn (LaraTermii $termii) => $termii->sendMessageWithNumber(
            to: $request->get('to'),
            sms: $request->get('sms'),
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
        ];
    }
}
