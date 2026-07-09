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
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Description('Retrieve delivery reports and message history (SMS, voice and WhatsApp) for the connected Termii account. Pass a message ID to fetch the report for a single message.')]
#[IsReadOnly]
class GetMessageHistoryTool extends Tool
{
    use InteractsWithTermii;

    public function handle(Request $request): Response
    {
        $request->validate([
            'message_id' => ['nullable', 'string'],
        ]);

        return $this->run(fn (LaraTermii $termii) => $termii->history(
            messageId: $request->get('message_id'),
        ));
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'message_id' => $schema->string()
                ->description('Retrieve the delivery report for this single message ID only. Omit to list all messages.'),
        ];
    }
}
