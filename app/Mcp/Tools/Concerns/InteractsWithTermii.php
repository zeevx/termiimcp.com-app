<?php

declare(strict_types=1);

namespace App\Mcp\Tools\Concerns;

use Closure;
use Throwable;
use Laravel\Mcp\Response;
use Illuminate\Support\Str;
use Zeevx\LaraTermii\LaraTermii;
use Illuminate\Support\Facades\Crypt;
use Zeevx\LaraTermii\Exceptions\TermiiException;
use Illuminate\Contracts\Encryption\DecryptException;

trait InteractsWithTermii
{
    public function name(): string
    {
        return Str::kebab(Str::beforeLast(class_basename($this), 'Tool'));
    }

    public function title(): string
    {
        return Str::headline(Str::beforeLast(class_basename($this), 'Tool'));
    }

    protected function resolveApiKey(): ?string
    {
        if ($token = request()->query('key')) {
            try {
                return Crypt::decryptString($token);
            } catch (DecryptException) {
                return null;
            }
        }

        return request()->bearerToken();
    }

    protected function resolveTermii(): LaraTermii
    {
        return new LaraTermii(
            apiKey: $this->resolveApiKey() ?: null,
        );
    }

    protected function run(Closure $callback): Response
    {
        try {
            $response = $callback($this->resolveTermii());
            $body = $response->json() ?? $response->body();

            if ($response->failed()) {
                return Response::error(
                    'Termii returned an error (HTTP '.$response->status().'): '
                    .(is_string($body) ? $body : json_encode($body))
                );
            }

            return Response::text((string) json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

        } catch (TermiiException $exception) {
            return Response::error(
                $this->resolveApiKey()
                ? $exception->getMessage()
                : 'No Termii API key was provided. Add "?key=YOUR_TERMII_API_KEY" to the MCP server URL '
                    .'(or send the key as an Authorization bearer token).'
            );
        } catch (Throwable $exception) {
            return Response::error('The Termii request could not be completed: '.$exception->getMessage());
        }
    }
}
