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

namespace Cobweb\Svconnector\Tests\Utility;

use Cobweb\Svconnector\Utility\ParameterParser;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class ParameterParserTest extends UnitTestCase
{
    protected ParameterParser $subject;

    public function setUp(): void
    {
        parent::setUp();
        $this->subject = new ParameterParser();
    }

    public static function parseProvider(): array
    {
        $data = [
            'token' => 'bar',
            'sso' => [
                'token' => 'foo',
            ],
        ];
        return [
            'empty array' => [
                'parameters' => [],
                'data' => $data,
                'result' => [],
            ],
            'nothing to substitute' => [
                'parameters' => [
                    'hello' => 'world',
                ],
                'data' => $data,
                'result' => [
                    'hello' => 'world',
                ],
            ],
            'with substitutions' => [
                'parameters' => [
                    'auth1' => 'https://example.com/auth/{token}/',
                    'auth2' => [
                        'uri' => 'https://example.com/auth/{sso.token}',
                    ],
                ],
                'data' => $data,
                'result' => [
                    'auth1' => 'https://example.com/auth/bar/',
                    'auth2' => [
                        'uri' => 'https://example.com/auth/foo',
                    ],
                ],
            ],
        ];
    }

    #[Test] #[DataProvider('parseProvider')]
    public function parseRecursivelySubstitutes(array $parameters, array $data, array $result): void
    {
        self::assertSame(
            $result,
            $this->subject->parse($parameters, $data)
        );
    }

    public static function substituteProvider(): array
    {
        $data = [
            'token' => 'bar',
            'sso' => [
                'token' => 'foo',
            ],
        ];
        return [
            'empty string' => [
                'parameter' => '',
                'data' => $data,
                'result' => '',
            ],
            'nothing to substitute' => [
                'parameter' => 'Hello world',
                'data' => $data,
                'result' => 'Hello world',
            ],
            'not a string - integer' => [
                'parameter' => 1,
                'data' => $data,
                'result' => 1,
            ],
            'not a string - boolean' => [
                'parameter' => false,
                'data' => $data,
                'result' => false,
            ],
            'with one-dimensional substitution' => [
                'parameter' => 'https://example.com/auth/{token}/',
                'data' => $data,
                'result' => 'https://example.com/auth/bar/',
            ],
            'with two-dimensional substitution' => [
                'parameter' => 'https://example.com/auth/{sso.token}',
                'data' => $data,
                'result' => 'https://example.com/auth/foo',
            ],
            'with back-to-back substitution' => [
                'parameter' => 'https://example.com/auth/{sso.token}{token}/login',
                'data' => $data,
                'result' => 'https://example.com/auth/foobar/login',
            ],
            'with non-matching data' => [
                'parameter' => 'https://example.com/auth/{hooray}/',
                'data' => $data,
                'result' => 'https://example.com/auth/{hooray}/',
            ],
        ];
    }

    #[Test] #[DataProvider('substituteProvider')]
    public function substituteSubstitutesAvailableVariables(mixed $parameter, array $data, mixed $result): void
    {
        self::assertEquals(
            $result,
            $this->subject->substitute($parameter, $data)
        );
    }
}
