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

It is strongly recommended that you trigger these events in your custom connector services,
because use case may strongly vary from user to user, and they will welcome the possibility
of manipulating the data retrieved by the service in its various forms.


.. _developers-events-initialize:

\\Cobweb\\Svconnector\\Event\\InitializeConnectorEvent
""""""""""""""""""""""""""""""""""""""""""""""""""""""

This event is fired by the `initialize()` method. It is meant to perform custom
initializations needed by specific uses of the service, and can store results in
the :ref`connection information object <developers-api-connection-information>` for
dynamic usage in the connector parameters.


.. _developers-events-parameters:

\\Cobweb\\Svconnector\\Event\\ProcessParametersEvent
""""""""""""""""""""""""""""""""""""""""""""""""""""

This event is fired after :ref`parameters have been parsed <developers-api-connection-information>`
and allows for further manipulation of the connector parameters.


.. _developers-events-rawdata:

\\Cobweb\\Svconnector\\Event\\ProcessRawDataEvent
"""""""""""""""""""""""""""""""""""""""""""""""""

This event is fired after the service has retrieved data in raw format. It is designed
for use in the :code:`fetchRaw()` method.


.. _developers-events-arraydata:

\\Cobweb\\Svconnector\\Event\\ProcessArrayDataEvent
"""""""""""""""""""""""""""""""""""""""""""""""""""

This event is fired after the service has retrieved data in array format. It is designed
for use in the :code:`fetchArray()` method.


.. _developers-events-xmldata:

\\Cobweb\\Svconnector\\Event\\ProcessXmlDataEvent
"""""""""""""""""""""""""""""""""""""""""""""""""

This event is fired after the service has retrieved data in XML format. It is designed
for use in the :code:`fetchXml()` method.


.. _developers-events-response:

\\Cobweb\\Svconnector\\Event\\ProcessResponseEvent
""""""""""""""""""""""""""""""""""""""""""""""""""

This event is fired after the service has called the distant source and received a
response from that source. It is designed for use in the :code:`query()` method.


.. _developers-events-postprocess:

\\Cobweb\\Svconnector\\Event\\PostProcessOperationsEvent
""""""""""""""""""""""""""""""""""""""""""""""""""""""""

This event is fired after all operations have been performed by the connector services.
Actually, it will be triggered only if the code that used the service called the
:code:`\Cobweb\Svconnector\Service\ConnectorServiceInterface::postProcessOperations()` method.
