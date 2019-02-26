<?php
namespace App\Service;

use App\Command\Pull;
use App\Command\DeleteResources;
use App\Command\CloneRepository;
use App\Command\CommandInterface;
use React\EventLoop\LoopInterface;

/**
 * Command factory.
 */
class CommandFactory
{
    /**
     * Command map
     */
    private const COMMAND_MAP = [
        'clone'           => CloneRepository::class,
        'pull'            => Pull::class,
        'deleteResources' => DeleteResources::class,
    ];

    /**
     * Event loop instance.
     *
     * @var LoopInterface
     */
    private $loop;

    /**
     * Current working directory.
     *
     * @var string
     */
    private $currentWorkingDirectory;

    /**
     * CommandFactory constructor.
     *
     * @param LoopInterface $loop                    Event loop instance.
     * @param string        $currentWorkingDirectory Current working directory.
     */
    public function __construct(LoopInterface $loop, string $currentWorkingDirectory)
    {
        $this->currentWorkingDirectory = $currentWorkingDirectory;
        $this->loop = $loop;
    }

    /**
     * Create process.
     *
     * @param string $command Command.
     * @param array  $args Command arguments
     *
     * @return CommandInterface
     */
    public function create(string $command, ...$args): CommandInterface
    {
        $command = self::COMMAND_MAP[$command];

        return new $command($this->loop, $this->currentWorkingDirectory, ...$args);
    }
}
