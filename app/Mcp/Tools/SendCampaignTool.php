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

#[Description('Launch an SMS/WhatsApp campaign to every contact in a Termii phonebook.')]
class SendCampaignTool extends Tool
{
    use InteractsWithTermii;

    public function handle(Request $request): Response
    {
        $request->validate([
            'country_code' => ['required', 'string'],
            'sender_id' => ['required', 'string'],
            'message' => ['required', 'string'],
            'phonebook_id' => ['required', 'string'],
            'channel' => ['nullable', 'string', 'in:generic,dnd,whatsapp'],
            'message_type' => ['nullable', 'string', 'in:plain,flash'],
            'campaign_type' => ['nullable', 'string', 'in:regular,personalized'],
            'schedule_sms_status' => ['nullable', 'string', 'in:regular,scheduled'],
            'schedule_time' => ['nullable', 'string', 'required_if:schedule_sms_status,scheduled'],
            'options' => ['nullable', 'array'],
        ]);

        $options = $request->get('options') ?: [];

        if ($scheduleTime = $request->get('schedule_time')) {
            $options['schedule_time'] = $scheduleTime;
        }

        return $this->run(fn (LaraTermii $termii) => $termii->sendCampaign(
            countryCode: $request->get('country_code'),
            senderId: $request->get('sender_id'),
            message: $request->get('message'),
            phonebookId: $request->get('phonebook_id'),
            channel: $request->get('channel') ?: 'generic',
            messageType: $request->get('message_type') ?: 'plain',
            campaignType: $request->get('campaign_type') ?: 'regular',
            scheduleSmsStatus: $request->get('schedule_sms_status') ?: 'regular',
            options: $options,
        ));
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'country_code' => $schema->string()
                ->description('Dialing country code for the campaign, e.g. "234".')
                ->required(),
            'sender_id' => $schema->string()
                ->description('Approved Sender ID to send the campaign from.')
                ->required(),
            'message' => $schema->string()
                ->description('The campaign message body.')
                ->required(),
            'phonebook_id' => $schema->string()
                ->description('The ID of the phonebook to send the campaign to.')
                ->required(),
            'channel' => $schema->string()
                ->description('Route for the campaign.')
                ->enum(['generic', 'dnd', 'whatsapp'])
                ->default('generic'),
            'message_type' => $schema->string()
                ->description('Message type.')
                ->enum(['plain', 'flash'])
                ->default('plain'),
            'campaign_type' => $schema->string()
                ->description('Campaign type.')
                ->enum(['regular', 'personalized'])
                ->default('regular'),
            'schedule_sms_status' => $schema->string()
                ->description('Send immediately ("regular") or at a later time ("scheduled", requires schedule_time).')
                ->enum(['regular', 'scheduled'])
                ->default('regular'),
            'schedule_time' => $schema->string()
                ->description('When to send a scheduled campaign, e.g. "2026-07-10 15:00". Required when schedule_sms_status is "scheduled".'),
            'options' => $schema->object()
                ->description('Optional additional campaign parameters, e.g. delimiter, remove_duplicate or enable_link_tracking, as a key-value map.'),
        ];
    }
}
