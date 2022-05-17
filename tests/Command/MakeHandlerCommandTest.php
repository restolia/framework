<?php

namespace Tests\Command;

use PHPUnit\Framework\TestCase;
use Restolia\Command\MakeHandlerCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

define('APP_ROOT', dirname(__DIR__) . '/temp/');

class MakeHandlerCommandTest extends TestCase
{
    private Filesystem $filesystem;

    protected function setUp(): void
    {
        parent::setUp();

        $this->filesystem = new Filesystem();
        $this->filesystem->mkdir(APP_ROOT . '/app/Handlers/');
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->filesystem->remove(APP_ROOT . '/app/Handlers/');
    }

    /**
     * @dataProvider handlerCases
     * @param string        $argName
     * @param int           $code
     * @param string        $expectedPath
     * @param string        $expectedName
     * @param string        $expectedOutput
     * @param bool          $expectFileToExist
     * @param callable|null $before
     * @return void
     */
    public function testDoesWriteFile(
        string $argName,
        int $code,
        string $expectedPath,
        string $expectedName,
        string $expectedOutput,
        bool $expectFileToExist,
        ?callable $before = null
    ): void {
        if ($before) {
            $before();
        }

        $application = new Application();
        $application->add(new MakeHandlerCommand());

        $command = $application->find('make:handler');
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'name' => $argName
        ]);
        $this->assertEquals($code, $commandTester->getStatusCode());

        $this->assertStringContainsString(
            $expectedOutput,
            $commandTester->getDisplay()
        );

        if ($commandTester->getStatusCode() === Command::SUCCESS) {
            $this->assertEquals(
                '<?php

namespace App\Handlers;

use Restolia\Http\Response;
use Symfony\Component\HttpFoundation\Request;

class ' . $expectedName . '
{
    public function handle(Request $request, Response $response): void
    {
        // TODO: implement me
    }
}',
                file_get_contents($expectedPath)
            );
        }

        if ($expectFileToExist) {
            $this->assertFileExists($expectedPath);
        } else {
            $this->assertFileDoesNotExist($expectedPath);
        }
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function handlerCases(): array
    {
        return [
            'empty name' => [
                'argName' => '',
                'code' => Command::FAILURE,
                'expectedPath' => APP_ROOT . '/app/Handlers/UserHandler.php',
                'expectedName' => '',
                'expectedOutput' => '',
                'expectFileToExist' => false,
            ],
            'with name' => [
                'argName' => 'User',
                'code' => Command::SUCCESS,
                'expectedPath' => APP_ROOT . '/app/Handlers/UserHandler.php',
                'expectedName' => 'UserHandler',
                'expectedOutput' => sprintf('Handler created at "%s".', APP_ROOT . '/app/Handlers/UserHandler.php'),
                'expectFileToExist' => true,
            ],
            'name that ends with "Handler"' => [
                'argName' => 'UserHandler',
                'code' => Command::SUCCESS,
                'expectedPath' => APP_ROOT . '/app/Handlers/UserHandler.php',
                'expectedName' => 'UserHandler',
                'expectedOutput' => sprintf('Handler created at "%s".', APP_ROOT . '/app/Handlers/UserHandler.php'),
                'expectFileToExist' => true,
            ],
            'name that starts with "Handler"' => [
                'argName' => 'HandlerUser',
                'code' => Command::SUCCESS,
                'expectedPath' => APP_ROOT . '/app/Handlers/HandlerUserHandler.php',
                'expectedName' => 'HandlerUserHandler',
                'expectedOutput' => sprintf(
                    'Handler created at "%s".',
                    APP_ROOT . '/app/Handlers/HandlerUserHandler.php'
                ),
                'expectFileToExist' => true,
            ],
            'file that already exists' => [
                'argName' => 'User',
                'code' => Command::FAILURE,
                'expectedPath' => APP_ROOT . '/app/Handlers/UserHandler.php',
                'expectedName' => 'UserHandler',
                'expectedOutput' => sprintf(
                    'Handler already exists at "%s".',
                    APP_ROOT . '/app/Handlers/UserHandler.php'
                ),
                'expectFileToExist' => true,
                'before' => fn() => file_put_contents(APP_ROOT . '/app/Handlers/UserHandler.php', '')
            ],
        ];
    }
}