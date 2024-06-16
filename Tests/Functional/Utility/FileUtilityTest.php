<?php

declare(strict_types=1);

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
    protected FileUtility $subject;

    protected $testExtensionsToLoad = [
        'typo3conf/ext/svconnector'
    ];

    public function setUp(): void
    {
        try {
            $this->setUpBackendUserFromFixture(1);
            $this->subject = GeneralUtility::makeInstance(FileUtility::class);
            $this->importDataSet(__DIR__ . '/Fixtures/Database/sys_file.xml');
        } catch (\Exception) {
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
    public function getFileContentWithValidUriReturnsContent(string $uri, string $expectedContent): void
    {
        if (str_starts_with($uri, 'FAL:')) {
            $this->markTestSkipped('FAL support not implemented in tests yet.');
        }
        $content = $this->subject->getFileContent($uri);
        self::assertSame($expectedContent, $content);
    }

    /**
     * @test
     * @dataProvider filePathProvider
     * @param string $uri
     * @param string $expectedContent
     */
    public function getFileAsTemporaryFileWithValidUriReturnsFilename(string $uri, string $expectedContent): void
    {
        if (str_starts_with($uri, 'FAL:')) {
            $this->markTestSkipped('FAL support not implemented in tests yet.');
        }
        $filename = $this->subject->getFileAsTemporaryFile($uri);
        $content = file_get_contents($filename);
        self::assertSame($expectedContent, $content);
    }

    public function badFilePathProvider(): array
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
    public function getFileContentWithInvalidUriReturnsFalse(string $uri): void
    {
        $content = $this->subject->getFileContent($uri);
        self::assertFalse($content);
    }

    /**
     * @test
     * @dataProvider badFilePathProvider
     * @param string $uri
     */
    public function getFileAsTemporaryFileWithInvalidUriReturnsFalse(string $uri): void
    {
        $filename = $this->subject->getFileAsTemporaryFile($uri);
        self::assertFalse($filename);
    }
}