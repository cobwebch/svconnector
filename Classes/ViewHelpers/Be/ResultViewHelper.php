<?php
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

/**
 * This view helper is designed to output the result of the connection test appropriately, depending on its format
 *
 * @author Francois Suter (Cobweb) <typo3@cobweb.ch>
 * @package TYPO3
 * @subpackage tx_svconnector
 */
class ResultViewHelper extends AbstractBackendViewHelper
{

    /**
     * Renders the view helper
     *
     * In this case, it renders whatever result the connection test returned
     *
     * @param mixed $result Result of the connection test
     * @return string The rendered result
     */
    public function render($result)
    {
        // If the result is an array, dump it in a formatted display
        // Otherwise display a preformatted string
        if (is_array($result)) {
            $content = DebuggerUtility::var_dump($result, '', 8, true, false, true);
        } else {
            $content = htmlspecialchars($result);
        }
        return '<pre>' . $content . '</pre>';
    }
}
