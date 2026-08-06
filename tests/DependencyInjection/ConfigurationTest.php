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

namespace Forci\Bundle\Catchable\Tests\DependencyInjection;

use Forci\Bundle\Catchable\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends TestCase {

    private function process(array $configs): array {
        return (new Processor())->processConfiguration(new Configuration(), $configs);
    }

    public function testKeepForDaysDefaultsToNull(): void {
        $config = $this->process([]);

        $this->assertNull($config['purge']['keep_for_days']);
    }

    public function testKeepForDaysIsConfigurable(): void {
        $config = $this->process([['purge' => ['keep_for_days' => 30]]]);

        $this->assertSame(30, $config['purge']['keep_for_days']);
    }

    public function testKeepForDaysRejectsZeroAndBelow(): void {
        $this->expectException(InvalidConfigurationException::class);

        $this->process([['purge' => ['keep_for_days' => 0]]]);
    }
}
