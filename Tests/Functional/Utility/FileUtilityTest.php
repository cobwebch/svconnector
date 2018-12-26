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

    protected $testExtensionsToLoad = [
        'typo3conf/ext/svconnector'
    ];

    public function setUp()
    {
        parent::setUp();
        try {
            $this->setUpBackendUserFromFixture(1);
            $this->subject = GeneralUtility::makeInstance(FileUtility::class);
            $this->importDataSet(__DIR__ . '/Fixtures/Database/sys_file.xml');
        }
        catch (\Exception $e) {
            self::markTestSkipped('Could not load fixtures');
        }
    }

    public function filePathProvider(): array
    {
        return [
                'FAL pointer' => [
                        'FAL:1:test.csv',
                        "code;name\n0x2;Foo\n1y7;Bar\n"
                ],
                'EXT: syntax' => [
                        'EXT:svconnector/Tests/Functional/Utility/Fixtures/Files/test.csv',
                        "code;name\n0x2;Foo\n1y7;Bar\n"
                ],
                'Relative path' => [
                        'typo3conf/ext/svconnector/Tests/Functional/Utility/Fixtures/Files/test.csv',
                        "code;name\n0x2;Foo\n1y7;Bar\n"
                ],
                'Remote URI' => [
                        'https://raw.githubusercontent.com/cobwebch/svconnector/master/Tests/Functional/Utility/Fixtures/Files/test.csv',
                        "code;name\n0x2;Foo\n1y7;Bar\n"
                ]
        ];
    }

    /**
     * @test
     * @dataProvider filePathProvider
     * @param string $uri
     * @param string $expectedContent
     */
    public function getFileContentWithValidUriReturnsContent($uri, $expectedContent) {
        $content = $this->subject->getFileContent($uri);
        self::assertSame($expectedContent, $content);
    }

    public function badFilePathProvider()
    {
        return [
                'Non-existing file' => [
                        'typo3conf/ext/svconnector/Tests/Functional/Utility/Fixtures/Files/testxxx.csv'
                ],
                'Outside root path' => [
                        ORIGINAL_ROOT . '../foo/test.csv'
                ]
        ];
    }

    /**
     * @test
     * @dataProvider badFilePathProvider
     * @param string $uri
     */
    public function getFileContentWithInvalidUriReturnsFalse($uri) {
        var_dump($uri);
        $content = $this->subject->getFileContent($uri);
        self::assertFalse($content);
    }
}