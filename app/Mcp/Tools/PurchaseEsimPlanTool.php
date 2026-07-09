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

#[Description('Purchase or top up a data plan for an existing Termii eSIM.')]
class PurchaseEsimPlanTool extends Tool
{
    use InteractsWithTermii;

    public function handle(Request $request): Response
    {
        $request->validate([
            'iccid' => ['required', 'string'],
            'product_id' => ['required', 'string'],
            'iso3' => ['required', 'string'],
        ]);

        return $this->run(fn (LaraTermii $termii) => $termii->esim()->purchasePlan(
            iccid: $request->get('iccid'),
            productId: $request->get('product_id'),
            iso3: $request->get('iso3'),
        ));
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'iccid' => $schema->string()
                ->description('The ICCID of the eSIM to purchase the plan for.')
                ->required(),
            'product_id' => $schema->string()
                ->description('The ID of the data plan product, as returned by list-esim-data-plans.')
                ->required(),
            'iso3' => $schema->string()
                ->description('ISO3 country code for the plan, e.g. "NGA".')
                ->required(),
        ];
    }
}
