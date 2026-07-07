<?php

declare(strict_types=1);

use Laravel\Mcp\Facades\Mcp;
use App\Mcp\Servers\TermiiServer;

Mcp::web('/mcp', TermiiServer::class)->middleware('throttle:mcp');
