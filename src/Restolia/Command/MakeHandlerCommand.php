<?php

namespace Restolia\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeHandlerCommand extends Command
{
    protected static $defaultName = 'make:handler';

    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'The handler name.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = self::formatName($input->getArgument('name'));
        if ($name === '') {
            return Command::FAILURE;
        }
        $target = self::makeTarget($name);

        if(file_exists($target)) {
            $output->writeln(sprintf('Handler already exists at "%s".', $target));
            return Command::FAILURE;
        }

        file_put_contents($target, self::parseStub((string)file_get_contents(__DIR__ . '/stubs/Handler.stub'), $name));

        $output->writeln(sprintf('Handler created at "%s".', $target));

        return self::SUCCESS;
    }

    private static function makeTarget(string $name): string
    {
        return sprintf(APP_ROOT . '/app/Handlers/%sHandler.php', $name);
    }

    private static function formatName(string $name): string
    {
        $name = preg_replace('/Handler$/', '', trim($name));

        return ucwords((string)$name);
    }

    private static function parseStub(string $contents, string $name): string
    {
        return str_replace('HandlerName', $name . 'Handler', $contents);
    }
}