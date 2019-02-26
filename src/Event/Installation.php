<?php /** @noinspection PhpComposerExtensionStubsInspection */

namespace App\Event;

use Psr\Http\Message\ServerRequestInterface;

/**
 * 'Installation' github webhook event handler.
 * @see: https://developer.github.com/v3/activity/events/types/#installationevent
 */
class Installation extends AbstractEvent
{
    private const CREATE_ACTION = 'created';
    private const DELETE_ACTION = 'deleted';
    private const GITHUB_WEBHOOK_INSTALLATION = 'installation';

    /**
     * `Installation` github webhook handler.
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
        if (self::GITHUB_WEBHOOK_INSTALLATION === $this->getWebhookType($request)) {
            $payload = json_decode($request->getBody()->getContents());

            if (self::CREATE_ACTION === $payload->action) {
                $installationAccessToken = $this->authService->getInstallationAccessToken($payload->installation->id);
                foreach ($payload->repositories as $repository) {
                    $this->commandFactory->create('clone', $installationAccessToken, $repository->full_name)
                                         ->run();
                }
            }

            if (self::DELETE_ACTION === $payload->action) {
                $this->commandFactory->create('deleteResources')
                                     ->run();
            }
        }

        return $next($request);
    }
}
