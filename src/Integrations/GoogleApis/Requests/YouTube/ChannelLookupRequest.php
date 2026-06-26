<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\GoogleApis\Requests\YouTube;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Seeders\ExternalApis\Exceptions\MissingConfigurationException;

class ChannelLookupRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        public string $channelId,
        public string $part = 'snippet,statistics,brandingSettings,status',
    ) {}

    public function resolveEndpoint(): string
    {
        return '/youtube/v3/channels';
    }

    protected function defaultQuery(): array
    {
        $key = config('external-apis.youtube.key');

        if (empty($key)) {
            throw new MissingConfigurationException('external-apis.youtube.key');
        }

        return [
            'key' => $key,
            'id' => $this->channelId,
            'part' => $this->part,
        ];
    }
}
