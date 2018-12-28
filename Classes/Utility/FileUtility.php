<?php

namespace Cobweb\Svconnector\Utility;

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

use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class for opening either local or remote files.
 *
 * @package Cobweb\Svconnector\Utility
 */
class FileUtility implements SingletonInterface
{
    /**
     * @var string Error message from reading the URI
     */
    protected $error = '';

    /**
     * Returns the class as a string. Seems to be needed by phpunit when an exception occurs during a test run.
     *
     * @return string
     */
    public function __toString()
    {
        return 'FileUtility';
    }

    /**
     * Reads data from a file pointed to by a versatile URI.
     *
     * The URI cannot only be the usual fully-qualified URI, but also use a syntax to trigger reading from the FAL
     * (FAL:storage_uid:file_identifier) or start with a special key pointing to a dedicated file reader
     * (declared as a hook).
     *
     * @param string $uri Address of the file to read
     * @param array|null $headers Headers to pass on to the request
     * @return string|bool
     */
    public function getFileContent($uri, $headers = null)
    {
        // Reset the error message
        $this->setError('');
        // The first part of the URI may be a key to a dedicated file reader
        $uriParts = explode(':', $uri);
        $key = array_shift($uriParts);
        $key = strtoupper($key);
        // Check if a corresponding key exists
        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['svconnector']['fileReader']) && array_key_exists($key, $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['svconnector']['fileReader'])) {
            /** @var AbstractFileReader $readerObject */
            $reader = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['svconnector']['fileReader'][$key];
            $readerObject = GeneralUtility::makeInstance($reader, $this);
            // Check inheritance and read data if okay
            if ($readerObject instanceof AbstractFileReader) {
                $data = $readerObject->read($uri);
            } else {
                $data = false;
                $this->setError(
                        sprintf(
                                'Class %1$s does not inherit from %2$s',
                                $reader,
                                AbstractFileReader::class
                        )
                );
            }
        // If the key is "FAL", read the data using FAL API
        } elseif ($key === 'FAL') {
            $falPath = substr($uri, 4);
            $resourceFactory = ResourceFactory::getInstance();
            try {
                $file = $resourceFactory->getObjectFromCombinedIdentifier($falPath);
                $data = $file->getContents();
            }
            catch (\Exception $exception) {
                $data = false;
                $this->setError($exception->getMessage());
            }
        // In all other cases, fall back to the general TYPO3 file-reading tool
        } else {
            // If the URI looks like a fully qualified URL, use it as is
            // NOTE: this is the same test that is done by GeneralUtility::getUrl() called below.
            // However we need to use it here first, in order to continue "interpreting" $uri for
            // other syntax of file references prior to passing it to GeneralUtility::getUrl().
            if (preg_match('/^(?:http|ftp)s?|s(?:ftp|cp):/', $uri)) {
                $finalUri = $uri;
            } elseif (GeneralUtility::isAllowedAbsPath($uri)) {
                // Keep path as is if allowed absolute path
                $finalUri = $uri;
            } else {
                // This will resolve "EXT:" syntax, resolve paths relative to the TYPO3 root
                // and preserve absolute paths that are allowed by the TYPO3 configuration.
                $finalUri = GeneralUtility::getFileAbsFileName($uri);
            }

            $report = [];
            // The final URI might be empty, if GeneralUtility::getFileAbsFileName() wasn't happy with it
            if ($finalUri === '') {
                $data = false;
                $this->setError(
                        sprintf(
                                'File %s is not a valid or allowed path',
                                $uri
                        )
                );
            } else {
                $data = GeneralUtility::getUrl(
                        $finalUri,
                        0,
                        $headers,
                        $report
                );
                if ($data === false) {
                    $this->setError($report['message']);
                }
            }
        }

        // If some data was read, remove the BOM from the beginning of the file
        if ($data !== false) {
            $byteOrderMark = pack('H*', 'EFBBBF');
            $data = preg_replace('/^' . $byteOrderMark . '/', '', $data);
        }

        return $data;
    }

    /**
     * Reads a file and stores it locally in the typo3temp folder. Returns false if the operation failed.
     *
     * NOTE: if you use this API, it is up to you to clean up the temporary file after use.
     *
     * @param string $uri Address of the file to read
     * @param array|null $headers Headers to pass on to the request
     * @return string|bool
     * @see getFileContent
     */
    public function getFileAsTemporaryFile($uri, $headers = null)
    {
        $fileContent = $this->getFileContent($uri, $headers);
        // Exit early if file content could not be read
        if ($fileContent === false) {
            return false;
        }

        $filename = GeneralUtility::tempnam('svconnector', '.txt');
        $result = GeneralUtility::writeFileToTypo3tempDir(
                $filename,
                $fileContent
        );
        // A null result means that the temporary file was written successfully, return the file name
        if ($result === null) {
            return $filename;
        }
        // Otherwise, set an error and return false
        $this->setError(
                sprintf(
                        'An error happened generating the temporay file: %s',
                        $result
                )
        );
        return false;
    }

    /**
     * Gets the current error message.
     *
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }

    /**
     * Sets the error message.
     *
     * @param string $error
     */
    public function setError($error)
    {
        $this->error = (string) $error;
    }
}