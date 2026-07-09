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

#[Description('List countries supported by Termii eSIM (paginated).')]
#[IsReadOnly]
class ListEsimCountriesTool extends Tool
{
    use InteractsWithTermii;

    public function handle(Request $request): Response
    {
        $request->validate([
            'page' => ['nullable', 'integer', 'min:0'],
            'size' => ['nullable', 'integer', 'min:1'],
        ]);

        return $this->run(fn (LaraTermii $termii) => $termii->esim()->countries(
            page: (int) $request->get('page', 0),
            size: (int) $request->get('size', 15),
        ));
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'page' => $schema->integer()
                ->description('Zero-based page number.')
                ->default(0),
            'size' => $schema->integer()
                ->description('Number of results per page.')
                ->default(15),
        ];
    }
}
