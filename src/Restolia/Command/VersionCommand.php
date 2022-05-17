<?php

namespace Restolia\Command;

use Restolia\Kernel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class VersionCommand extends Command
{
    protected static $defaultName = 'version';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('v' . Kernel::VERSION);

        return self::SUCCESS;
    }
}