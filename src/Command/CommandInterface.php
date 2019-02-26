<?php
namespace App\Command;

/**
 * Command interface.
 */
interface CommandInterface
{
    /**
     * Run command.
     */
    public function run(): void;
}
