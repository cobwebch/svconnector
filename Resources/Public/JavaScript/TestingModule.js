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

import DocumentService from "@typo3/core/document-service.js";

class TestingModule {
	constructor() {
		this.initialize();
	}

	async initialize(){
		DocumentService.ready().then((document) => {
			document.getElementById('tx_svconnector_service').addEventListener('change', (event) => {
				document.getElementById('tx_svconnector_parameters').value = TYPO3.settings.svconnector[event.currentTarget.value];
			});
		});
	}
}

export default new TestingModule();
