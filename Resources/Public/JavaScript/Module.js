/**
 * ExtJS code for svconnector's BE module
 *
 * $Id$
 */
Ext.namespace('TYPO3.SvConnector');

/**
 * Loads the appropriate sample configuration into the parameters field
 *
 * @param event Event that was triggered
 * @param target Target of the event
 */
TYPO3.SvConnector.updateConfigurationField = function(event, target) {
	var selectedService = Ext.get(target).dom.value;
	var configuration = '';
	if (TYPO3.settings.svconnector.samples[selectedService]) {
		configuration = TYPO3.settings.svconnector.samples[selectedService];
	}
	Ext.get('tx_svconnector_parameters').dom.value = configuration;
};

Ext.onReady(function() {
	Ext.addBehaviors({
			// Activate on change
		'#tx_svconnector_service@change': TYPO3.SvConnector.updateConfigurationField
	});
});
