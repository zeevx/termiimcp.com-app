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

#[Description('Request registration of a new Sender ID on the connected Termii account for approval.')]
class SubmitSenderIdTool extends Tool
{
    use InteractsWithTermii;

    public function handle(Request $request): Response
    {
        $request->validate([
            'sender_id' => ['required', 'string', 'max:11'],
            'use_case' => ['required', 'string'],
            'company' => ['required', 'string'],
        ]);

        return $this->run(fn (LaraTermii $termii) => $termii->submitSenderId(
            senderId: $request->get('sender_id'),
            useCase: $request->get('use_case'),
            company: $request->get('company'),
        ));
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'sender_id' => $schema->string()
                ->description('The desired Sender ID (max 11 characters).')
                ->required(),
            'use_case' => $schema->string()
                ->description('A sample message / description of how the Sender ID will be used.')
                ->required(),
            'company' => $schema->string()
                ->description('The name of the company requesting the Sender ID.')
                ->required(),
        ];
    }
}
