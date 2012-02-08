<?php

/*
 * This file is part of the PhpJobQueue package.
 *
 * (c) Mark Fullbrook <mark.fullbrook@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpJobQueue\Storage;

use Predis\Client;
use PhpJobQueue\Config\RedisConfig;

/**
 * Extend Predis Client so that we can manipulate the config
 */
class Redis extends Client
{
    public function __construct(RedisConfig $config)
    {
        parent::__construct($config->getParameters(), $config->getOptions());
    }
}