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
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;

#[Description('Update the name (and optionally description) of an existing phonebook on the connected Termii account.')]
#[IsIdempotent]
class UpdatePhonebookTool extends Tool
{
    use InteractsWithTermii;

    public function handle(Request $request): Response
    {
        $request->validate([
            'phonebook_id' => ['required', 'string'],
            'name' => ['required', 'string'],
            'description' => ['nullable', 'string'],
        ]);

        return $this->run(fn (LaraTermii $termii) => $termii->updatePhonebook(
            phonebookId: $request->get('phonebook_id'),
            name: $request->get('name'),
            description: $request->get('description'),
        ));
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'phonebook_id' => $schema->string()
                ->description('The ID of the phonebook to update.')
                ->required(),
            'name' => $schema->string()
                ->description('The new name for the phonebook.')
                ->required(),
            'description' => $schema->string()
                ->description('An optional new description for the phonebook.'),
        ];
    }
}
