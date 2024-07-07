<?php

declare(strict_types=1);

namespace Cobweb\Svconnector\ViewHelpers\Be;

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

use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Fluid\ViewHelpers\Be\AbstractBackendViewHelper;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * This view helper is designed to output the result of the connection test appropriately, depending on its format
 */
class ResultViewHelper extends AbstractBackendViewHelper
{
    /**
     * Do not escape output of child nodes.
     *
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * Initialize the arguments of the ViewHelper.
     */
    public function initializeArguments(): void
    {
        $this->registerArgument('result', 'mixed', 'Result of the connection test', true);
    }

    /**
     * Renders whatever result the connection test returned.
     *
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return mixed
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        $result = $arguments['result'];
        // If the result is an array, dump it in a formatted display
        // Otherwise display a formatted string
        if (is_array($result)) {
            $content = DebuggerUtility::var_dump($result, '', 10, true, false, true);
        } else {
            $content = htmlspecialchars((string)$result);
        }
        return '<pre>' . $content . '</pre>';
    }
}
