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

#[Description('Retrieve delivery reports and message history (SMS, voice and WhatsApp) for the connected Termii account.')]
#[IsReadOnly]
class GetMessageHistoryTool extends Tool
{
    use InteractsWithTermii;

    public function handle(Request $request): Response
    {
        return $this->run(fn (LaraTermii $termii) => $termii->history());
    }

    public function schema(JsonSchema $schema): array
    {
        return [];
    }
}
