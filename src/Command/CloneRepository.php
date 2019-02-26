<?php
namespace App\Command;

use React\ChildProcess\Process;

/**
 * 'git clone' command.
 */
class CloneRepository extends AbstractCommand
{
    /**
     * {@inheritdoc}
     * @see CommandInterface::run()
     */
    public function run(): void
    {
        $repositoryClone = sprintf(
            'git clone https://x-access-token:%s@github.com/%s.git',
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
}
