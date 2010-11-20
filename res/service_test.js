/*
 * This file contains JavaScript functions related to the connector BE module
 *
 * @author		Francois Suter (Cobweb) <typo3@cobweb.ch>
 * @package		TYPO3
 * @subpackage	tx_svconnector
 *
 * $Id$
 */

/**
 * This method fires an AJAX call to test the requested service and displays the results
 *
 * @return	void
 */
function testService() {
	var serviceMenu = Ext.get('tx_svconnector_mod1_service');
	var parametersField = Ext.get('tx_svconnector_mod1_parameters');
	Ext.Ajax.request({
		url: 'ajax.php',
		method: 'post',
		params: {
			ajaxID: 'svconnector::query',
			service: serviceMenu.dom.options[serviceMenu.dom.selectedIndex].value,
			parameters: parametersField.dom.value
		},
		success: function(result){
			Ext.get('tx_svconnector_resultarea').update(result.responseText);
		}
	});
}
