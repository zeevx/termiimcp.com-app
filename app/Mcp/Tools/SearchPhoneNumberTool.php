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

#[Description('Perform a Do-Not-Disturb (DND) lookup for a phone number to check whether it can receive promotional messages, via Termii.')]
#[IsReadOnly]
class SearchPhoneNumberTool extends Tool
{
    use InteractsWithTermii;

    public function handle(Request $request): Response
    {
        $request->validate([
            'phone_number' => ['required', 'string'],
        ]);

        return $this->run(fn (LaraTermii $termii) => $termii->search(
            phoneNumber: $request->get('phone_number'),
        ));
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'phone_number' => $schema->string()
                ->description('The phone number to look up, in international format.')
                ->required(),
        ];
    }
}
