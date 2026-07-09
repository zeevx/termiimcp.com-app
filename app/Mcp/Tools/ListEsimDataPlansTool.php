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

#[Description('List available Termii eSIM data plans, optionally filtered by country and plan type.')]
#[IsReadOnly]
class ListEsimDataPlansTool extends Tool
{
    use InteractsWithTermii;

    public function handle(Request $request): Response
    {
        $request->validate([
            'country' => ['nullable', 'string'],
            'type' => ['nullable', 'string', 'in:LOCAL,REGION'],
        ]);

        return $this->run(fn (LaraTermii $termii) => $termii->esim()->dataPlans(
            country: $request->get('country'),
            type: $request->get('type'),
        ));
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'country' => $schema->string()
                ->description('Filter plans by country.'),
            'type' => $schema->string()
                ->description('Filter plans by type.')
                ->enum(['LOCAL', 'REGION']),
        ];
    }
}
