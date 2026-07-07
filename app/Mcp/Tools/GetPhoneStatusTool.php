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

#[Description('Verify a phone number and detect its mobile network operator and status via Termii.')]
#[IsReadOnly]
class GetPhoneStatusTool extends Tool
{
    use InteractsWithTermii;

    public function handle(Request $request): Response
    {
        $request->validate([
            'phone_number' => ['required', 'string'],
            'country_code' => ['required', 'string'],
        ]);

        return $this->run(fn (LaraTermii $termii) => $termii->status(
            phoneNumber: $request->get('phone_number'),
            countryCode: $request->get('country_code'),
        ));
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'phone_number' => $schema->string()
                ->description('The phone number to check, in international format.')
                ->required(),
            'country_code' => $schema->string()
                ->description('Two-letter ISO country code, e.g. "NG".')
                ->required(),
        ];
    }
}
