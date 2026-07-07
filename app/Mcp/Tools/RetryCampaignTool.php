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

#[Description('Retry a previously failed Termii campaign by its ID.')]
class RetryCampaignTool extends Tool
{
    use InteractsWithTermii;

    public function handle(Request $request): Response
    {
        $request->validate([
            'campaign_id' => ['required', 'string'],
        ]);

        return $this->run(fn (LaraTermii $termii) => $termii->retryCampaign(
            campaignId: $request->get('campaign_id'),
        ));
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'campaign_id' => $schema->string()
                ->description('The ID of the campaign to retry.')
                ->required(),
        ];
    }
}
