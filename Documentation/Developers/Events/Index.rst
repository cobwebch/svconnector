.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: /Includes.rst.txt


.. _developers-events:

Events
^^^^^^

The "svconnector" extension provides a number of events which can be used by the concrete
connector implementations. Except is special cases - since all connector services
share a common way of working - it should not be necessary to develop custom events.


.. _developers-events-initialize:

\Cobweb\Svconnector\Event\InitializeConnectorEvent
""""""""""""""""""""""""""""""""""""""""""""""""""

This event is fired by the `initialize()` method. It is meant to perform custom
initializations needed by specific uses of the service, and can store results in
the :ref`connection information object <developers-api-connection-information>` for
dynamic usage in the connector parameters.


.. _developers-events-parameters:

\Cobweb\Svconnector\Event\ProcessParametersEvent
""""""""""""""""""""""""""""""""""""""""""""""""

This event is fired after :ref`parameters have been parsed <developers-api-connection-information>`
and allows for further manipulation of the connector parameters.
