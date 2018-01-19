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

/**
 * Abstract base class for classes used in the $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['svconnector']['fileReader'] hook.
 *
 * @package Cobweb\Svconnector\Utility
 */
abstract class AbstractFileReader
{
    /**
     * @var FileUtility
     */
    protected $fileUtility;

    public function __construct($fileUtility)
    {
        $this->fileUtility = $fileUtility;
    }

    /**
     * Reads the data from given URI and returns it as a string.
     *
     * On error, should return an error message using FileUtility::setError().
     *
     * @param string $uri
     * @return string
     */
    abstract public function read($uri);
}