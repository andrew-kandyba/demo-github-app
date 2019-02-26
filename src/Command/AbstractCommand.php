<?php
namespace App\Command;

use React\EventLoop\LoopInterface;

/**
 * Abstract command.
 */
abstract class AbstractCommand implements CommandInterface
{
    /**
     * Event loop instance.
     *
     * @var LoopInterface
     */
    protected $loop;

    /**
     * Command working directory.
     *
     * @var string
     */
    protected $workingDir;

    /**
     * Installation access token.
     *
     * @var string
     */
    protected $installationAccessToken;

    /**
     * Repository full name (owner/repo-name).
     *
     * @var string
     */
    protected $repoFullName;

    /**
     * Command constructor.
     *
     * @param LoopInterface $loop                    Event loop instance.
     * @param string        $workingDir              Working directory.
     * @param string        $installationAccessToken Installation access token,
     * @param string        $repoFullName            Repo full name (owner/repo-name)
     */
    public function __construct(
        LoopInterface $loop,
        string $workingDir,
        string $installationAccessToken,
        string $repoFullName
    ) {
        $this->loop = $loop;
        $this->workingDir = $workingDir;
        $this->installationAccessToken = $installationAccessToken;
        $this->repoFullName = $repoFullName;
    }

    /**
     * Run command.
     */
    abstract public function run(): void;
}
