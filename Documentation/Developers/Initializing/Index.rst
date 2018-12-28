.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt


.. _developers-initializing:

Initializing the service
^^^^^^^^^^^^^^^^^^^^^^^^

Every service must have an :code:`init()` method which tells TYPO3 CMS whether the
service is available or not. This is where you should test the
connection to whatever remote application you are connecting to. If
that test connection fails, the :code:`init()` method should return false.
Otherwise it should return true.

If your connector has no risk of failing (for example, because it
operates locally and is not dependent on anything special), you must
still implement the :code:`init()` method and have it return true all the
time.

The :code:`init()` method of the base connector class
(:code:`\Cobweb\Svconnector\Service\ConnectorBase`) performs some initializations,
in particular creating an instance of the :code:`\TYPO3\CMS\Core\Log\Logger` class.
So in your own service you should always call the parent method.
