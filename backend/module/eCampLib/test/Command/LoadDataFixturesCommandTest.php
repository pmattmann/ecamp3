<?php

namespace eCamp\LibTest\Command;

use eCamp\Lib\Command\LoadDataFixturesCommand;
use eCamp\LibTest\PHPUnit\AbstractConsoleControllerTestCase;
use PHPUnit\Framework\Constraint\IsEqual;
use PHPUnit\Framework\Constraint\StringContains;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 */
class LoadDataFixturesCommandTest extends AbstractConsoleControllerTestCase {

    public function testLoadsFilesFromGivenPath() {
        // given
        $services = $this->getApplicationServiceLocator();
        /** @var LoadDataFixturesCommand $command */
        $command = $services->get(LoadDataFixturesCommand::class);
        $input = new StringInput('load-data-fixtures --path='.__DIR__.'/../data/fixtures');
        $output = new BufferedOutput();
        $services->setService(OutputInterface::class, $output);

        // when
        $result = $this->runCommand($command, $input, $output);

        // then
        $this->assertThat($result, new IsEqual(LoadDataFixturesCommand::SUCCESS));
        $consoleOutput = $output->fetch();
        $this->assertThat($consoleOutput, new StringContains('Fixture1'));
        $this->assertThat($consoleOutput, new StringContains('Fixture2'));
    }

    public function testDoesNotCrashWhenGivenNonexistentLocation() {
        // given
        $services = $this->getApplicationServiceLocator();
        /** @var LoadDataFixturesCommand $command */
        $command = $services->get(LoadDataFixturesCommand::class);
        $input = new StringInput('load-data-fixtures --path='.__DIR__.'/../data/some-dir-that-does-not-exist');
        $output = new BufferedOutput();
        $services->setService(OutputInterface::class, $output);

        // when
        $result = $this->runCommand($command, $input, $output);

        // then
        $this->assertThat($result, new IsEqual(LoadDataFixturesCommand::SUCCESS));
        $consoleOutput = $output->fetch();
        $this->assertThat($consoleOutput, new IsEqual(''));
    }

}