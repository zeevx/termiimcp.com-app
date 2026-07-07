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

#[Description('Bulk-add contacts to a Termii phonebook from raw CSV contents. Provide the CSV text directly rather than a file path.')]
class AddContactsFromContentsTool extends Tool
{
    use InteractsWithTermii;

    public function handle(Request $request): Response
    {
        $request->validate([
            'phonebook_id' => ['required', 'string'],
            'contents' => ['required', 'string'],
            'filename' => ['required', 'string'],
            'country_code' => ['required', 'string'],
        ]);

        return $this->run(fn (LaraTermii $termii) => $termii->addContactsFromContents(
            phonebookId: $request->get('phonebook_id'),
            contents: $request->get('contents'),
            filename: $request->get('filename'),
            countryCode: $request->get('country_code'),
        ));
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'phonebook_id' => $schema->string()
                ->description('The ID of the phonebook to add the contacts to.')
                ->required(),
            'contents' => $schema->string()
                ->description('The raw CSV contents. Each row should contain a phone number (and optional details) as accepted by Termii.')
                ->required(),
            'filename' => $schema->string()
                ->description('A filename to associate with the upload, e.g. "contacts.csv".')
                ->required(),
            'country_code' => $schema->string()
                ->description('Dialing country code applied to the contacts, e.g. "234".')
                ->required(),
        ];
    }
}
