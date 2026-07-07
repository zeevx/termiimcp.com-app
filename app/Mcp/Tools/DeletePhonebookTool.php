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
use Laravel\Mcp\Server\Tools\Annotations\IsDestructive;

#[Description('Permanently delete a phonebook (and its contacts) from the connected Termii account.')]
#[IsDestructive]
class DeletePhonebookTool extends Tool
{
    use InteractsWithTermii;

    public function handle(Request $request): Response
    {
        $request->validate([
            'phonebook_id' => ['required', 'string'],
        ]);

        return $this->run(fn (LaraTermii $termii) => $termii->deletePhonebook(
            phonebookId: $request->get('phonebook_id'),
        ));
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'phonebook_id' => $schema->string()
                ->description('The ID of the phonebook to delete.')
                ->required(),
        ];
    }
}
