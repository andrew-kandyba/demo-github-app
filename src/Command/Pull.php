<?php
namespace App\Command;

use React\ChildProcess\Process;
use React\EventLoop\LoopInterface;

/**
 * 'git pull' command.
 */
class Pull extends AbstractCommand
{
    /**
     * {@inheritdoc}
     * @see AbstractCommand::__construct()
     */
    public function __construct(LoopInterface $loop, string $workingDir, string $installationAccessToken, string $repoFullName)
    {
        parent::__construct($loop, $workingDir, $installationAccessToken, $repoFullName);

        $this->workingDir = $workingDir . '/' . $this->getRepoName();
    }

    /**
     * {@inheritdoc}
     * @see CommandInterface::run()
     */
    public function run(): void
    {
        $repositoryClone = sprintf(
            'git pull https://x-access-token:%s@github.com/%s.git',
            $this->installationAccessToken,
            $this->repoFullName
        );

        $childProcess = new Process($repositoryClone, $this->workingDir);
        $childProcess->start($this->loop);

        $childProcess->on('exit', function ($exitCode, $termSignal) use ($childProcess) {
            if (null !== $termSignal) {
                $childProcess->close();
            }
        });
    }

    /**
     * Get repository name (without owner data).
     *
     * @return string
     */
    private function getRepoName(): string
    {
        $repoNameData = explode('/', $this->repoFullName);

        return end($repoNameData);
    }
}
