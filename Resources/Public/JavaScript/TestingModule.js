/**
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

define('TYPO3/CMS/Svconnector/TestingModule', ['jquery'], function ($) {
	'use strict';

	/**
	 * @exports TYPO3/CMS/Svconnector/TestingModule
	 */
	var TestingModule = {
	};

	/**
	 * Updates the configuration sample based on the current connector selection.
	 * 
	 * @param event
	 */
	TestingModule.updateConfiguration = function(event) {
		var configuration = TYPO3.settings.svconnector[$(this).val()];
		$('#tx_svconnector_parameters').val(configuration);
	};

	$(function () {
		$('#tx_svconnector_service').on('change', TestingModule.updateConfiguration);
	});

	return TestingModule;
});
