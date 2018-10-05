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

use Nimut\TestingFramework\TestCase\UnitTestCase;

/**
 * Test suite for the ConnectorUtility class.
 *
 * @package Cobweb\Svconnector\Tests\Utility
 */
class ConnectorUtilityTest extends UnitTestCase
{
    public function xmlProvider()
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
                                'foo' => [
                                        0 => [
                                                'value' => 'bar',
                                                'children' => [
                                                        'baz' => [
                                                                0 => [
                                                                        'value' => 'Junior',
                                                                        'children' => [],
                                                                        'attributes' => [
                                                                                'rank' => '#1'
                                                                        ]
                                                                ],
                                                                1 => [
                                                                        'value' => 'Baby',
                                                                        'children' => [],
                                                                        'attributes' => [
                                                                                'rank' => '#2'
                                                                        ]
                                                                ]
                                                        ]
                                                ],
                                                'attributes' => []
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
    public function convertXmlToArrayReturnsStructuredArray($input, $output)
    {
        $result = \Cobweb\Svconnector\Utility\ConnectorUtility::convertXmlToArray($input);
        self::assertSame(
                $output,
                $result
        );
    }

    public function emptyProvider()
    {
        return [
                'empty string' => [
                        ''
                ],
                'null' => [
                        null
                ],
                'false' => [
                        false
                ],
                'zero' => [
                        0
                ]
        ];
    }

    /**
     * @param string $string
     * @test
     * @dataProvider emptyProvider
     * @expectedException \Exception
     */
    public function convertXmlToArrayThrowsExceptionOnEmptyString($string)
    {
        \Cobweb\Svconnector\Utility\ConnectorUtility::convertXmlToArray($string);
    }
}