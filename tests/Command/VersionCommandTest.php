<?php

namespace Tests\Command;

use PHPUnit\Framework\TestCase;
use Restolia\Command\VersionCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class VersionCommandTest extends TestCase
{
    public function testDoesReturnCorrectVersion(): void
    {
        $application = new Application();
        $application->add(new VersionCommand());

        $command = $application->find('version');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);
        $commandTester->assertCommandIsSuccessful();

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('v2.1.2', $output);
    }
}