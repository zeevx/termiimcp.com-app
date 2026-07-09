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

#[Description('Retrieve the data plan status and validity dates of a Termii eSIM.')]
#[IsReadOnly]
class GetEsimPlanStatusTool extends Tool
{
    use InteractsWithTermii;

    public function handle(Request $request): Response
    {
        $request->validate([
            'iccid' => ['required', 'string'],
        ]);

        return $this->run(fn (LaraTermii $termii) => $termii->esim()->planStatus(
            iccid: $request->get('iccid'),
        ));
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'iccid' => $schema->string()
                ->description('The ICCID of the eSIM to retrieve plan status for.')
                ->required(),
        ];
    }
}
