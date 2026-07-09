<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('welcome', [
            'mcpUrl' => 'https://termiimcp.com',
            'features' => [
                ['title' => 'SMS &amp; WhatsApp', 'desc' => 'Send single or bulk messages, flash SMS, and pre-approved WhatsApp templates with media.', 'tools' => 'send-message, send-bulk-message, send-template, send-template-with-media'],
                ['title' => 'Numbers &amp; account', 'desc' => 'Check wallet balance, look up number status &amp; network, run DND checks and view delivery history.', 'tools' => 'get-balance, get-message-history, get-phone-status, search-phone-number'],
                ['title' => 'Sender IDs', 'desc' => 'List registered Sender IDs and request approval for new ones, right from the conversation.', 'tools' => 'list-sender-ids, submit-sender-id'],
                ['title' => 'Phonebooks &amp; contacts', 'desc' => 'Create and manage phonebooks, add single or bulk contacts from CSV, and clean up your lists.', 'tools' => 'list-phonebooks, create/update/delete-phonebook, list/add/delete-contact, add-contacts-from-contents'],
                ['title' => 'Campaigns', 'desc' => 'Launch messaging campaigns to a phonebook, list campaigns, inspect history and retry failures.', 'tools' => 'send-campaign, list-campaigns, get-campaign-history, retry-campaign'],
            ],
        ]);
    }
}
