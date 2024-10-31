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

namespace Cobweb\Svconnector\Utility;

use TYPO3\CMS\Core\Utility\ArrayUtility;

/**
 * Array parser for substituting strings following a syntax like "{...}" with values
 * found within a data array.
 *
 * This class is only meant for use for parsing connector parameters. Do not use for
 * other purposes.
 *
 * @internal
 */
class ParameterParser
{
    /**
     * Recursively go through the given array and substitute any data marked
     * with "{...}" within the array's values
     */
    public function parse(array $parameters, array $data): array
    {
        $parsedParameters = [];
        foreach ($parameters as $key => $parameter) {
            if (is_array($parameter)) {
                $parsedParameters[$key] = $this->parse($parameter, $data);
            } else {
                $parsedParameters[$key] = $this->substitute($parameter, $data);
            }
        }
        return $parsedParameters;
    }

    /**
     * Search for "{...}" patterns inside a string and replace with data found in an array
     *
     * The values in the array may in a deeper dimension. An array path is used, with "."
     * as a separator.
     */
    public function substitute(mixed $parameter, array $data): mixed
    {
        // If parameter is not a string, do not try to substitute anything
        if (!is_string($parameter)) {
            return $parameter;
        }

        // Early return if the string is empty
        if (empty($parameter)) {
            return '';
        }

        $variables = preg_match_all('/{(.*?)}/', $parameter, $matches);
        // If the matching failed or there were no matches, return string as
        if ($variables === false || count($matches[1]) === 0) {
            return $parameter;
        }

        $searches = [];
        $replacements = [];
        // For each match, try to find a replacement value in the data array
        foreach ($matches[1] as $match) {
            try {
                $replacements[] = ArrayUtility::getValueByPath($data, $match, '.');
                $searches[] = '{' . $match . '}';
            } catch (\Throwable $exception) {
                // Do nothing, variable was not matched and will not be substituted
            }
        }
        return str_replace($searches, $replacements, $parameter);
    }
}
