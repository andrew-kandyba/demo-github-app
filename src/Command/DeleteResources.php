<?php
namespace App\Command;

use React\ChildProcess\Process;
use React\EventLoop\LoopInterface;

/**
 * Delete resources (cloned repos data) command.
 * Only internal usage.
 */
class DeleteResources implements CommandInterface
{
    /**
     * Event loop instance.
     *
     * @var LoopInterface
     */
    private $loop;

    /**
     * Working directory.
     *
     * @var string
     */
    private $workingDir;

    /**
     * Command constructor.
     *
     * @param LoopInterface $loop
     * @param string        $workingDir Working directory.
     */
    public function __construct(LoopInterface $loop, string $workingDir)
    {
        $this->loop = $loop;
        $this->workingDir = $workingDir;
    }

    /**
     * {@inheritdoc}
     * @see CommandInterface::run()
     */
    public function run(): void
    {
        $childProcess = new Process('yes | rm -rf *', $this->workingDir);
        $childProcess->start($this->loop);

        $childProcess->on('exit', function ($exitCode, $termSignal) use ($childProcess) {
            if (null !== $termSignal) {
                $childProcess->close();
            }
        });
    }
}
