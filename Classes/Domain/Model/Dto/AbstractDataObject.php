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

use Cobweb\Svconnector\Exception\NoSuchDataException;

/**
 * DTO class for managing a simple data storage.
 */
abstract class AbstractDataObject
{
    protected array $data = [];

    public function get(): array
    {
        return $this->data;
    }

    /**
     * @throws NoSuchDataException
     */
    public function getForKey(string $key): ?array
    {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }
        throw new NoSuchDataException(
            sprintf('No value found in context for key "%s".', $key),
            1721494427
        );
    }

    public function set(array $data): void
    {
        $this->data = $data;
    }

    public function add(string $key, mixed $value): void
    {
        $this->data[$key] = $value;
    }

    public function reset(): void
    {
        $this->data = [];
    }
}
