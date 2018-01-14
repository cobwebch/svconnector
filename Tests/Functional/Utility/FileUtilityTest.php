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

use Cobweb\Svconnector\Utility\FileUtility;
use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Test case for FileUtility class.
 *
 * @package Cobweb\Svconnector\Tests\Utility
 */
class FileUtilityTest extends FunctionalTestCase
{
    /**
     * @var FileUtility
     */
    protected $subject;

    public function setUp()
    {
        parent::setUp();
        $this->setUpBackendUserFromFixture(1);
        $this->subject = GeneralUtility::makeInstance(FileUtility::class);
        $this->importDataSet(__DIR__ . '/Fixtures/Database/sys_file.xml');
    }

    public function filePathProvider()
    {
        return [
                'FAL pointer' => [
                        'FAL:1:test.csv',
                        "code;name\r\n0x2;Foo\r\n1y7;Bar"
                ],
                'EXT: syntax' => [
                        'EXT:svconnector/Tests/Functional/Utility/Fixtures/Files/test.csv',
                        "code;name\r\n0x2;Foo\r\n1y7;Bar"
                ],
                'Remote URI' => [
                        'https://raw.githubusercontent.com/fsuter/externalimport_test/master/Resources/Private/ImportData/Test/Orders.csv',
                        'foo'
                ]
        ];
    }

    /**
     * @test
     * @dataProvider filePathProvider
     * @param string $uri
     * @param string $expectedContent
     */
    public function getFileContentReturnsContent($uri, $expectedContent) {
        $content = $this->subject->getFileContent($uri);
        self::assertSame($expectedContent, $content);
    }
}