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
 * Utility class for the Connector family of services
 *
 * @author Francois Suter (Cobweb) <typo3@cobweb.ch>
 * @package TYPO3
 * @subpackage tx_svconnector
 */
class ConnectorUtility
{
    /**
     * This method takes a XML structure and transforms it into a PHP array.
     *
     * The resulting array is rather complicated as the method tries not to loose information from the XML structure.
     *
     * Considering the following XML:
     *
     * <test>
     *        <foo>
     *            bar
     *            <child rank="#1">Junior</child>
     *            <child rank="#2">Baby</child>
     *        </foo>
     * </test>
     *
     * the resulting array will look like:
     *
     * 'output' => [
     *         'children' => [
     *                 'foo' => [
     *                         0 => [
     *                                 'value' => 'bar',
     *                                 'children' => [
     *                                         'baz' => [
     *                                                 0 => [
     *                                                         'value' => 'Junior',
     *                                                         'attributes' => [
     *                                                                 'rank' => '#1'
     *                                                         ]
     *                                                 ],
     *                                                 1 => [
     *                                                         'value' => 'Baby',
     *                                                         'attributes' => [
     *                                                                 'rank' => '#2'
     *                                                         ]
     *                                                 ]
     *                                         ]
     *                                 ]
     *                         ]
     *                 ]
     *         ]
     * ]
     *
     * NOTE: this method was written because t3lib_div::xml2array() is much too keyed
     * to TYPO3's specifics and produces weird or even outright wrong array structures.
     * On the other hand the reverse conversion is fine with t3lib_div::array2xml_cs().
     *
     * @param string $string XML to parse
     * @param int $options LIBXML options for XML parsing (optional)
     * @throws \Exception
     * @return array PHP array
     */
    public static function convertXmlToArray($string, $options = null): array
    {
        // If input string is empty, exit with exception
        if (empty($string)) {
            throw new \Cobweb\Svconnector\Exception\EmptySourceException(
                    'XML string is empty!',
                    1294325109
            );
        }

        // Try loading the string into the Simple XML library
        $xmlObject = @simplexml_load_string($string, null, $options);

        // Transform XML into a PHP array
        if ($xmlObject === false) {
            throw new \Cobweb\Svconnector\Exception\InvalidSourceException(
                    'XML is invalid!',
                    1545687481
            );
        }
        return self::handleXmlNode($xmlObject, array_keys($xmlObject->getDocNamespaces()));
    }

    /**
     * This method converts a given XML node into a PHP array, preserving all
     * the attribute and children information. Calls itself recursively on child nodes.
     *
     * @param \SimpleXMLElement $node XML node to transform
     * @param array $namespaces List of namespaces used (optional)
     * @return array Transformed XML node and children
     */
    public static function handleXmlNode(\SimpleXMLElement $node, $namespaces = [])
    {
        // Init
        $nodeArray = [];

        // Set value if there is any
        if (($value = trim((string)$node)) !== '') {
            $nodeArray['value'] = $value;
        }

        // Fill attributes
        $attributes = self::handleAttributes($node);
        foreach ($namespaces as $namespace) {
            if ($namespace !== '') {
                $attributes = array_merge($attributes, self::handleAttributes($node, $namespace));
            }
        }
        // only add attributes if there are any
        if (count($attributes)) {
            $nodeArray['attributes'] = $attributes;
        }

        // Fill children
        $children = self::handleChildren($node, $namespaces);
        foreach ($namespaces as $namespace) {
            if ($namespace !== '') {
                $children = array_merge($children, self::handleChildren($node, $namespaces, $namespace));
            }
        }
        // only add children if there are any
        if (count($children)) {
            $nodeArray['children'] = $children;
        }

        return $nodeArray;
    }

    /**
     * Go through children of a node and parse them recursively
     *
     * @param \SimpleXMLElement $node
     * @param array $namespaces List of namespaces used (optional)
     * @param string $namespace Namespace to be parsed (optional)
     * @return array
     */
    public static function handleChildren(\SimpleXMLElement $node, $namespaces = [], $namespace = null) {
        $children = $node->children($namespace, true);
        $array = [];
        if ($children->count() > 0) {
            // set base of array key in case of a namespace
            $base = isset($namespace) ? $namespace . ':' : '';
            // Go through all child nodes and recursively convert them to arrays
            foreach ($children as $key => $subnode) {
                $parsed = self::handleXmlNode(
                        $subnode,
                        array_unique(array_merge($namespaces, array_keys($subnode->getDocNamespaces(false, false))))
                    );
                // define the array key for this child
                $keyname = $base . $key;
                // define child array once
                if (!isset($array[$keyname])) {
                    $array[$keyname] = [];
                }
                // add new entry to the child array
                $array[$keyname][] = $parsed;
            }
        }
        return $array;
    }

    /**
     * Extracts all regular attributes or all attributes of a namespace
     *
     * @param \SimpleXMLElement $node XML node
     * @param string $namespace Namespace to be used (optional)
     * @return array All attributes of the specified namespace (if any)
     */
    public static function handleAttributes(\SimpleXMLElement $node, $namespace = null) {
        // Get attributes of a namespace
        $attributes = $node->attributes($namespace, true);
        $parsed = [];
        if (isset($attributes) && $attributes->count() > 0) {
            // Define base to be used for the array key
            $base = isset($namespace) ? $namespace . ':' : '';
            // Go through the attributes and add them to the array
            foreach ($attributes as $attribute => $value) {
                $parsed[$base . $attribute] = trim((string)$value);
            }
        }
        return $parsed;
    }
}
