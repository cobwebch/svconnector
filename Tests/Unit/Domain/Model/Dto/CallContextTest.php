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

namespace Cobweb\Svconnector\Tests\Domain\Model\Dto;

use Cobweb\Svconnector\Domain\Model\Dto\CallContext;
use Cobweb\Svconnector\Exception\NoSuchContextException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class CallContextTest extends UnitTestCase
{
    protected CallContext $subject;

    public function setUp(): void
    {
        parent::setUp();
        $this->subject = new CallContext();
    }

    #[Test]
    public function getContextInitiallyReturnsEmptyArray(): void
    {
        self::assertSame(
            [],
            $this->subject->get()
        );
    }

    #[Test]
    public function getForKeyReturnsExpectedElement(): void
    {
        $this->subject->set(
            [
                'context1' => [
                    'location' => 'here',
                ]
            ]
        );
        self::assertSame(
            [
                'location' => 'here',
            ],
            $this->subject->getForKey('context1')
        );
    }

    #[Test]
    public function getForKeyWithUnknownKeyThrowsException(): void
    {
        $this->expectException(NoSuchContextException::class);
        self::assertSame(
            [
                'location' => 'here',
            ],
            $this->subject->getForKey('context1')
        );
    }

    public static function addToContextProvider(): array
    {
        return [
            'add new data' => [
                'existingContext' => [
                    'context1' => [
                        'location' => 'here',
                    ],
                ],
                'newContextKey' => 'context2',
                'newContextData' => [
                    'location' => 'there',
                ],
                'result' => [
                    'context1' => [
                        'location' => 'here',
                    ],
                    'context2' => [
                        'location' => 'there',
                    ],
                ],
            ],
            'replace existing data' => [
                'existingContext' => [
                    'context1' => [
                        'location' => 'here',
                    ],
                    'context2' => [
                        'location' => 'not here',
                    ],
                ],
                'newContextKey' => 'context2',
                'newContextData' => [
                    'location' => 'there',
                ],
                'result' => [
                    'context1' => [
                        'location' => 'here',
                    ],
                    'context2' => [
                        'location' => 'there',
                    ],
                ],
            ],
        ];
    }

    #[Test] #[DataProvider('addToContextProvider')]
    public function addToContextInsertsOrReplacesData(array $existingContext, string $newContextKey, array $newContextData, array $result): void
    {
        $this->subject->set($existingContext);
        $this->subject->add($newContextKey, $newContextData);
        self::assertSame(
            $result,
            $this->subject->get()
        );
    }

    public static function setContextProvider(): array
    {
        return [
            'set new data' => [
                'existingContext' => [],
                'newContext' => [
                    'context1' => [
                        'location' => 'here',
                    ],
                ],
                'result' => [
                    'context1' => [
                        'location' => 'here',
                    ],
                ],
            ],
            'override existing data' => [
                'existingContext' => [
                    'context2' => [
                        'location' => 'not here',
                    ],
                ],
                'newContext' => [
                    'context1' => [
                        'location' => 'here',
                    ],
                ],
                'result' => [
                    'context1' => [
                        'location' => 'here',
                    ],
                ],
            ],
        ];
    }

    #[Test] #[DataProvider('setContextProvider')]
    public function setContextSetsOrReplacesContext(array $existingContext, array $newContext, array $result): void
    {
        $this->subject->set($existingContext);
        $this->subject->set($newContext);
        self::assertSame(
            $result,
            $this->subject->get()
        );
    }

    #[Test]
    public function resetContextErasesAllData(): void
    {
        $this->subject->set(
            [
                'context1' => [
                    'location' => 'here',
                ],
            ]
        );
        $this->subject->reset();
        self::assertSame(
            [],
            $this->subject->get()
        );
    }
}