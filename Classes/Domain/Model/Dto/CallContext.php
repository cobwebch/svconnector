<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace Cobweb\Svconnector\Domain\Model\Dto;

use Cobweb\Svconnector\Exception\NoSuchContextException;

/**
 * DTO class for managing a call context.
 *
 * A call context can be made up of a collection of information, each piece of information being stored
 * with a specific associative key in the context array.
 */
class CallContext
{
    protected array $context = [];

    public function get(): array
    {
        return $this->context;
    }

    /**
     * @throws NoSuchContextException
     */
    public function getForKey(string $key): ?array
    {
        if (array_key_exists($key, $this->context)) {
            return $this->context[$key];
        }
        throw new NoSuchContextException(
            sprintf('No value found in context for key "%s".', $key),
            1721494427
        );
    }

    public function set(array $context): void
    {
        $this->context = $context;
    }

    public function add(string $key, array $value): void
    {
        $this->context[$key] = $value;
    }

    public function reset(): void
    {
        $this->context = [];
    }
}
