.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt


.. _developers-initializing:

Initializing the service
^^^^^^^^^^^^^^^^^^^^^^^^

Every service must have an :code:`init()` method which tells TYPO3 whether the
service is available or not. This is where you should test the
connection to whatever remote application you are connecting to. If
that test connection fails, the :code:`init()` method should return false.
Otherwise it should return true.

If your connector has no risk of failing (for example, because it
operates locally and is not dependent on anything special), you must
still implement the :code:`init()` method and have it return true all the
time.

The :code:`init()` method of the base connector class
(:code:`tx_svconnector_base`) performs some initializations,
including reading its own configuration. So in your own service
you should always call the parent method and - if reading a specific
configuration - you should store it in its own member variable
or merge it with the base class configuration.
The code should look something like:

.. code-block:: php

	protected function init() {
		parent::init();
		$localConfiguration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey]);
		$this->extConfiguration = array_merge($this->extConfiguration, $localConfiguration);
	}

Obviously you can ignore this if your extension has no configuration.

