<?php /** @noinspection PhpComposerExtensionStubsInspection */

namespace App\Event;

use Psr\Http\Message\ServerRequestInterface;

/**
 * 'Push' github webhook event handler.
 * @see: https://developer.github.com/v3/activity/events/types/#pushevent
 */
class Push extends AbstractEvent
{
    private const GITHUB_WEBHOOK_PUSH = 'push';

    /**
     * `Push` github webhook handler.
     *
     * @param ServerRequestInterface $request Request instance.
     * @param callable               $next    Next middleware.
     *
     *
     * @return callable
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        if (self::GITHUB_WEBHOOK_PUSH === $this->getWebhookType($request)) {
            $payload = json_decode($request->getBody()->getContents());
            $installationAccessToken = $this->authService->getInstallationAccessToken($payload->installation->id);

            $this->commandFactory->create('pull', $installationAccessToken, $payload->repository->full_name)
                                 ->run();
        }

        return $next($request);
    }
}
