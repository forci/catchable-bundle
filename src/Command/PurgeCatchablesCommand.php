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

namespace Forci\Bundle\Catchable\Command;

use Forci\Bundle\Catchable\Repository\CatchableRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Deletes catchables older than a retention window so the table does not grow unbounded.
 *
 * The window comes from --days, falling back to the configured
 * forci_catchable.purge.keep_for_days. Meant to be run from cron.
 */
class PurgeCatchablesCommand extends Command
{
    public function __construct(private CatchableRepository $repository, private ?int $defaultKeepForDays = null)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('forci:catchable:purge')
            ->setDescription('Delete catchables older than a retention window to keep the table from growing unbounded.')
            ->addOption('days', null, InputOption::VALUE_REQUIRED, 'Delete catchables older than this many days. Overrides the configured forci_catchable.purge.keep_for_days.')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Report how many catchables would be deleted, without deleting anything.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $days = $input->getOption('days');

        if (null === $days) {
            $days = $this->defaultKeepForDays;
        }

        if (null === $days) {
            $io->error('No retention window given. Pass --days=N or configure forci_catchable.purge.keep_for_days.');

            return Command::FAILURE;
        }

        $days = (int) $days;

        if ($days < 1) {
            $io->error(sprintf('The retention window must be a positive number of days, got "%s".', $days));

            return Command::FAILURE;
        }

        $threshold = (new \DateTimeImmutable())->modify(sprintf('-%d days', $days));

        if ($input->getOption('dry-run')) {
            $count = $this->repository->countCreatedBefore($threshold);
            $io->note(sprintf('Dry run: %d catchable(s) older than %s would be deleted.', $count, $threshold->format('Y-m-d H:i:s')));

            return Command::SUCCESS;
        }

        $deleted = $this->repository->removeCreatedBefore($threshold);
        $io->success(sprintf('Deleted %d catchable(s) older than %s.', $deleted, $threshold->format('Y-m-d H:i:s')));

        return Command::SUCCESS;
    }
}
