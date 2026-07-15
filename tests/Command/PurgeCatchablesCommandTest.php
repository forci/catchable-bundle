<?php

/*
 * This file is part of the ForciCatchableBundle package.
 *
 * Copyright (c) Forci Web Consulting Ltd.
 *
 * Author Tatyana Mincheva <tatjana@forci.com>
 * Author Martin Kirilov <martin@forci.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Forci\Bundle\Catchable\Tests\Command;

use Forci\Bundle\Catchable\Command\PurgeCatchablesCommand;
use Forci\Bundle\Catchable\Repository\CatchableRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class PurgeCatchablesCommandTest extends TestCase {

    private function tester(CatchableRepository $repository, ?int $defaultKeepForDays = null): CommandTester {
        $command = new PurgeCatchablesCommand($repository, $defaultKeepForDays);
        $application = new Application();
        $application->add($command);

        return new CommandTester($application->find('forci:catchable:purge'));
    }

    public function testDeletesOlderThanGivenDays(): void {
        $repository = $this->createMock(CatchableRepository::class);
        $repository->expects($this->never())->method('countCreatedBefore');
        $repository->expects($this->once())
            ->method('removeCreatedBefore')
            ->with($this->callback(function (\DateTimeInterface $threshold): bool {
                $expected = (new \DateTimeImmutable())->modify('-30 days')->getTimestamp();

                return abs($threshold->getTimestamp() - $expected) < 5;
            }))
            ->willReturn(7);

        $tester = $this->tester($repository);
        $tester->execute(['--days' => '30']);

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertStringContainsString('Deleted 7', $tester->getDisplay());
    }

    public function testDryRunCountsButDoesNotDelete(): void {
        $repository = $this->createMock(CatchableRepository::class);
        $repository->expects($this->once())->method('countCreatedBefore')->willReturn(42);
        $repository->expects($this->never())->method('removeCreatedBefore');

        $tester = $this->tester($repository);
        $tester->execute(['--days' => '30', '--dry-run' => true]);

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertStringContainsString('42', $tester->getDisplay());
    }

    public function testUsesConfiguredDefaultWhenDaysOmitted(): void {
        $repository = $this->createMock(CatchableRepository::class);
        $repository->expects($this->once())->method('removeCreatedBefore')->willReturn(0);

        $tester = $this->tester($repository, 90);
        $tester->execute([]);

        $this->assertSame(0, $tester->getStatusCode());
    }

    public function testFailsWhenNoRetentionWindowGiven(): void {
        $repository = $this->createMock(CatchableRepository::class);
        $repository->expects($this->never())->method('removeCreatedBefore');
        $repository->expects($this->never())->method('countCreatedBefore');

        $tester = $this->tester($repository);
        $tester->execute([]);

        $this->assertSame(1, $tester->getStatusCode());
        $this->assertStringContainsString('retention window', $tester->getDisplay());
    }

    public function testFailsWhenDaysIsNotPositive(): void {
        $repository = $this->createMock(CatchableRepository::class);
        $repository->expects($this->never())->method('removeCreatedBefore');

        $tester = $this->tester($repository);
        $tester->execute(['--days' => '0']);

        $this->assertSame(1, $tester->getStatusCode());
    }
}
