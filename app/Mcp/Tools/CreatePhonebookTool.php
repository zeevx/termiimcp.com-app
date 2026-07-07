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

#[Description('Create a new phonebook on the connected Termii account.')]
class CreatePhonebookTool extends Tool
{
    use InteractsWithTermii;

    public function handle(Request $request): Response
    {
        $request->validate([
            'name' => ['required', 'string'],
            'description' => ['nullable', 'string'],
        ]);

        return $this->run(fn (LaraTermii $termii) => $termii->createPhonebook(
            name: $request->get('name'),
            description: $request->get('description'),
        ));
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'name' => $schema->string()
                ->description('The name of the phonebook.')
                ->required(),
            'description' => $schema->string()
                ->description('An optional description of the phonebook.'),
        ];
    }
}
