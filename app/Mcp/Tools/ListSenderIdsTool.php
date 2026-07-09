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

#[Description('List all Sender IDs registered on the connected Termii account, including their approval status. Results can optionally be filtered by name and/or status.')]
#[IsReadOnly]
class ListSenderIdsTool extends Tool
{
    use InteractsWithTermii;

    public function handle(Request $request): Response
    {
        $request->validate([
            'name' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'in:active,pending,blocked'],
        ]);

        return $this->run(fn (LaraTermii $termii) => $termii->allSenderId(
            name: $request->get('name'),
            status: $request->get('status'),
        ));
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'name' => $schema->string()
                ->description('Filter results to Sender IDs matching this name.'),
            'status' => $schema->string()
                ->description('Filter results by approval status.')
                ->enum(['active', 'pending', 'blocked']),
        ];
    }
}
