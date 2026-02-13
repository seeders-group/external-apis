<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Connectors\BrandTracker;

use Exception;
use Firebase\JWT\JWT;
use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;
use Seeders\ExternalApis\Contracts\LlmTrackerProjectInterface;

final class BrandTrackerConnector extends Connector
{
    use AcceptsJson;

    public function __construct(
        private readonly LlmTrackerProjectInterface $project
    ) {}

    public function resolveBaseUrl(): string
    {
        return config('external-apis.brand_tracker.api_url');
    }

    protected function defaultHeaders(): array
    {
        $user = auth()->user();

        // For Laravel API, use Sanctum token if available
        if ($user && method_exists($user, 'currentAccessToken') && $user->currentAccessToken()) {
            return [
                'Authorization' => 'Bearer '.$user->currentAccessToken()->plainTextToken,
            ];
        }

        // Fallback to session-based auth (most common in Livewire)
        // Laravel API routes will use web middleware session auth
        return [];
    }

    protected function defaultConfig(): array
    {
        return [
            'timeout' => config('external-apis.brand_tracker.timeout'),
        ];
    }

    private function generateJwtToken(): string
    {
        $user = auth()->user();

        if (! $user) {
            $payload = [
                'sub' => '0',
                'email' => 'guest@seeders.com',
                'name' => 'Guest User',
                'project_id' => $this->project->getId(),
                'is_guest' => true,
                'iat' => time(),
                'exp' => time() + (2 * 3600),
                'iss' => config('app.url'),
            ];
        } else {
            $payload = [
                'sub' => (string) $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'iat' => time(),
                'exp' => time() + (2 * 3600),
                'iss' => config('app.url'),
            ];
        }

        $jwt_secret = config('external-apis.brand_tracker.jwt_secret');

        if (! $jwt_secret) {
            throw new Exception('JWT secret not configured');
        }

        return JWT::encode($payload, $jwt_secret, 'HS256');
    }
}
