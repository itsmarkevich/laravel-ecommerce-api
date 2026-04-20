<?php

namespace Tests\Concerns;

use App\Services\Auth\Sms\SmsGateway;
use App\Services\Auth\TokenService;
use Mockery\Expectation;
use Mockery\MockInterface;
use Throwable;

/**
 * @mixin \Tests\TestCase
 * @phpstan-require-extends \Tests\TestCase
 */
trait InteractsWithAuthMocks
{
    private function expectSmsSendOnce(): void
    {
        /** @var MockInterface&SmsGateway $mock */
        $mock = $this->mock(SmsGateway::class);

        /** @var Expectation $expectation */
        $expectation = $mock->shouldReceive('send');

        $expectation->once();
    }

    /**
     * @param array<string, mixed> $tokenData
     */
    private function expectTokenLogin(array $tokenData): void
    {
        /** @var MockInterface&TokenService $mock */
        $mock = $this->mock(TokenService::class);

        /** @var Expectation $expectation */
        $expectation = $mock->shouldReceive('login');

        $expectation->once()
            ->andReturn($tokenData);
    }

    /**
     * @return MockInterface&TokenService
     */
    private function mockTokenService(): MockInterface
    {
        /** @var MockInterface&TokenService $mock */
        $mock = \Mockery::mock(TokenService::class);

        $this->swap(TokenService::class, $mock);

        return $mock;
    }

    /**
     * @param array<string, mixed> $tokenData
     */
    private function expectTokenRefresh(string $token, array $tokenData): void
    {
        $mock = $this->mockTokenService();

        /** @var Expectation $expectation */
        $expectation = $mock->shouldReceive('refresh');

        $expectation->once()
            ->with($token)
            ->andReturn($tokenData);
    }

    private function expectTokenRefreshToThrow(string $token, Throwable $exception): void
    {
        $mock = $this->mockTokenService();

        /** @var Expectation $expectation */
        $expectation = $mock->shouldReceive('refresh');

        $expectation->once()
            ->with($token)
            ->andThrow($exception);
    }

    private function expectTokenLogoutOnce(): void
    {
        /** @var MockInterface&TokenService $mock */
        $mock = $this->mock(TokenService::class);

        /** @var Expectation $expectation */
        $expectation = $mock->shouldReceive('logout');

        $expectation->once();
    }
}
