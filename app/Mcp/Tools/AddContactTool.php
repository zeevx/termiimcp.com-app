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

#[Description('Add a single contact to a Termii phonebook.')]
class AddContactTool extends Tool
{
    use InteractsWithTermii;

    public function handle(Request $request): Response
    {
        $request->validate([
            'phonebook_id' => ['required', 'string'],
            'phone_number' => ['required', 'string'],
            'country_code' => ['nullable', 'string'],
            'email_address' => ['nullable', 'email'],
            'first_name' => ['nullable', 'string'],
            'last_name' => ['nullable', 'string'],
            'company' => ['nullable', 'string'],
        ]);

        return $this->run(fn (LaraTermii $termii) => $termii->addContact(
            phonebookId: $request->get('phonebook_id'),
            phoneNumber: $request->get('phone_number'),
            countryCode: $request->get('country_code'),
            emailAddress: $request->get('email_address'),
            firstName: $request->get('first_name'),
            lastName: $request->get('last_name'),
            company: $request->get('company'),
        ));
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'phonebook_id' => $schema->string()
                ->description('The ID of the phonebook to add the contact to.')
                ->required(),
            'phone_number' => $schema->string()
                ->description('The contact phone number.')
                ->required(),
            'country_code' => $schema->string()
                ->description('Dialing country code without the plus sign, e.g. "234".'),
            'email_address' => $schema->string()
                ->description('Optional contact email address.'),
            'first_name' => $schema->string()
                ->description('Optional contact first name.'),
            'last_name' => $schema->string()
                ->description('Optional contact last name.'),
            'company' => $schema->string()
                ->description('Optional contact company name.'),
        ];
    }
}
