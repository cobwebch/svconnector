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

namespace Cobweb\Svconnector\Domain\Model\Dto;

/**
 * DTO class for managing connection information.
 *
 * Connection to third-party sources may require some form of authentication, for example
 * returning a token. This kind of information can be stored in the connection information,
 * which can later be used in the connection parameters.
 */
class ConnectionInformation extends AbstractDataObject {}
