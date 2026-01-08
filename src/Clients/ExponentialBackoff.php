<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Clients;

use Exception;

final class ExponentialBackoff
{
    public const MAX_DELAY_MICROSECONDS = 120000000;

    private int $retries;

    /** @var callable|null */
    private $retryFunction;

    /** @var callable */
    private $delayFunction;

    /**
     * @param  int|null  $retries  Number of retries for a failed request.
     * @param  callable|null  $retryFunction  returns bool for whether or not to retry
     */
    public function __construct(?int $retries = null, ?callable $retryFunction = null)
    {
        $this->retries = $retries ?? 3;
        $this->retryFunction = $retryFunction;
        $this->delayFunction = function (int $delay): void {
            usleep($delay);
        };
    }

    /**
     * Executes the retry process.
     *
     * @param  array<int, mixed>  $arguments
     *
     * @throws Exception The last exception caught while retrying.
     */
    public function execute(callable $function, array $arguments = []): mixed
    {
        $delayFunction = $this->delayFunction;
        $retryAttempt = 0;
        $exception = null;

        while (true) {
            try {
                return call_user_func_array($function, $arguments);
            } catch (Exception $exception) {
                if ($this->retryFunction) {
                    if (! call_user_func($this->retryFunction, $exception)) {
                        throw $exception;
                    }
                }

                if ($exception->getCode() >= 400 && $exception->getCode() <= 499) {
                    break;
                }

                if ($retryAttempt >= $this->retries) {
                    break;
                }

                $delayFunction($this->calculateDelay($retryAttempt));
                $retryAttempt++;
            }
        }

        throw $exception;
    }

    public function setDelayFunction(callable $delayFunction): void
    {
        $this->delayFunction = $delayFunction;
    }

    /**
     * Calculates exponential delay.
     */
    private function calculateDelay(int $attempt): int
    {
        return (int) min(
            mt_rand(0, 1000000) + (pow(2, $attempt) * 1000000),
            self::MAX_DELAY_MICROSECONDS
        );
    }
}
