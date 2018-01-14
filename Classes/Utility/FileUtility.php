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
     * Reads data from a file pointed to by a versatile URI.
     *
     * The URI cannot only be the usual fully-qualified URI, but also use a syntax to trigger reading from the FAL
     * (FAL:storage_uid:file_identifier) or start with a special key pointing to a dedicated file reader
     * (declared as a hook).
     *
     * @param string $uri Address of the file to read
     * @param array $headers Headers to pass on to the request
     * @return string|bool
     */
    public function getFileContent($uri, array $headers = [])
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
            // If the URI starts with "EXT", use the TYPO3 API to get the full file name
            if ($key === 'EXT') {
                $uri = GeneralUtility::getFileAbsFileName($uri);
            }
            $report = [];
            $data = GeneralUtility::getUrl(
                    $uri,
                    0,
                    $headers,
                    $report
            );
            if ($data === false) {
                $this->setError($report['message']);
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
    public function setError(string $error): void
    {
        $this->error = $error;
    }
}