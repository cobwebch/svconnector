<?php

namespace Cobweb\Svconnector\Tests\Utility;

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
use Cobweb\Svconnector\Exception\EmptySourceException;
use Cobweb\Svconnector\Exception\InvalidSourceException;
use Cobweb\Svconnector\Utility\ConnectorUtility;
use Nimut\TestingFramework\TestCase\UnitTestCase;

/**
 * Test suite for the ConnectorUtility class.
 *
 * @package Cobweb\Svconnector\Tests\Utility
 */
class ConnectorUtilityTest extends UnitTestCase
{
    public function xmlProvider(): array
    {
        return [
            'no namespaces' => [
                'input' => '
                            <test>
                                   <foo>
                                       bar
                                       <baz rank="#1">Junior</baz>
                                       <baz rank="#2">Baby</baz>
                                   </foo>
                            </test>
                        ',
                'output' => [
                    'children' => [
                        'foo' => [
                            0 => [
                                'value' => 'bar',
                                'children' => [
                                    'baz' => [
                                        0 => [
                                            'value' => 'Junior',
                                            'attributes' => [
                                                'rank' => '#1'
                                            ]
                                        ],
                                        1 => [
                                            'value' => 'Baby',
                                            'attributes' => [
                                                'rank' => '#2'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'with namespace' => [
                'input' => '
                            <test xmlns:xx="http://example.org/ns">
                                   <xx:foo>
                                       bar
                                       <xx:baz rank="#1">Junior</xx:baz>
                                       <xx:baz rank="#2">Baby</xx:baz>
                                   </xx:foo>
                            </test>
                        ',
                'output' => [
                    'children' => [
                        'xx:foo' => [
                            0 => [
                                'value' => 'bar',
                                'children' => [
                                    'xx:baz' => [
                                        0 => [
                                            'value' => 'Junior',
                                            'attributes' => [
                                                'rank' => '#1'
                                            ]
                                        ],
                                        1 => [
                                            'value' => 'Baby',
                                            'attributes' => [
                                                'rank' => '#2'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * @param string $input
     * @param array $output
     * @test
     * @dataProvider xmlProvider
     */
    public function convertXmlToArrayReturnsStructuredArray(string $input, array $output): void
    {
        $result = ConnectorUtility::convertXmlToArray($input);
        self::assertSame(
            $output,
            $result
        );
    }

    public function emptyProvider(): array
    {
        return [
            'empty string' => [
                ''
            ]
        ];
    }

    /**
     * @param string $string
     * @test
     * @dataProvider emptyProvider
     */
    public function convertXmlToArrayThrowsExceptionOnEmptyString(string $string): void
    {
        $this->expectException(EmptySourceException::class);
        ConnectorUtility::convertXmlToArray($string);
    }

    public function invalidProvider()
    {
        return [
            'malformed XML' => [
                '<foo><unclosed_tag></foo>'
            ]
        ];
    }

    /**
     * @param string $string
     * @test
     * @dataProvider invalidProvider
     */
    public function convertXmlToArrayThrowsExceptionOnInvalidString(string $string): void
    {
        $this->expectException(InvalidSourceException::class);
        ConnectorUtility::convertXmlToArray($string);
    }
}