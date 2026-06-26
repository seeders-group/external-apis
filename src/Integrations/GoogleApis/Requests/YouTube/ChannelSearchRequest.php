<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\GoogleApis\Requests\YouTube;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Seeders\ExternalApis\Exceptions\MissingConfigurationException;

class ChannelSearchRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        public string $searchQuery,
        public int $maxResults = 5,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/youtube/v3/search';
    }

    protected function defaultQuery(): array
    {
        $key = config('external-apis.youtube.key');

        if (empty($key)) {
            throw new MissingConfigurationException('external-apis.youtube.key');
        }

        return [
            'key' => $key,
            'q' => $this->searchQuery,
            'type' => 'channel',
            'part' => 'snippet',
            'maxResults' => $this->maxResults,
        ];
    }
}
