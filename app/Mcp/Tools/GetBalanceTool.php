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

#[Description('Retrieve the current wallet balance and currency for the connected Termii account.')]
#[IsReadOnly]
class GetBalanceTool extends Tool
{
    use InteractsWithTermii;

    public function handle(Request $request): Response
    {
        return $this->run(fn (LaraTermii $termii) => $termii->balance());
    }

    public function schema(JsonSchema $schema): array
    {
        return [];
    }
}
