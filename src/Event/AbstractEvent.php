<?php
namespace App\Event;

use App\Service\Auth;
use App\Service\CommandFactory;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Abstract event.
 */
class AbstractEvent
{
    /**
     * Auth service instance.
     *
     * @var Auth
     */
    protected $authService;

    /**
     * Command factory instance.
     *
     * @var CommandFactory
     */
    protected $commandFactory;

    /**
     * Handler constructor.
     *
     * @param Auth           $authService
     * @param CommandFactory $commandFactory
     */
    public function __construct(Auth $authService, CommandFactory $commandFactory)
    {
        $this->authService = $authService;
        $this->commandFactory = $commandFactory;
    }

    /**
     * Get github webhook type from request header (X-GitHub-Event).
     *
     * @param ServerRequestInterface $request Request instance.
     *
     * @return string
     */
    protected function getWebhookType(ServerRequestInterface $request): string
    {
        return current($request->getHeader('X-GitHub-Event'));
    }
}
