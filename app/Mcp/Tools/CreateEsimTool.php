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

#[Description('Create a new eSIM on the connected Termii account for a data plan product and country.')]
class CreateEsimTool extends Tool
{
    use InteractsWithTermii;

    public function handle(Request $request): Response
    {
        $request->validate([
            'product_id' => ['required', 'string'],
            'iso3' => ['required', 'string'],
        ]);

        return $this->run(fn (LaraTermii $termii) => $termii->esim()->createEsim(
            productId: $request->get('product_id'),
            iso3: $request->get('iso3'),
        ));
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'product_id' => $schema->string()
                ->description('The ID of the data plan product, as returned by list-esim-data-plans.')
                ->required(),
            'iso3' => $schema->string()
                ->description('ISO3 country code for the eSIM, e.g. "NGA".')
                ->required(),
        ];
    }
}
