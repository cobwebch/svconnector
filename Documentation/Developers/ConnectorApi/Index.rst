.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: /Includes.rst.txt


.. _developers-api:

Connector API
^^^^^^^^^^^^^

The Connector Service API is described by interface :php:`\Cobweb\Svconnector\Service\ConnectorServiceInterface`.

This interface is implented in class :php:`\Cobweb\Svconnector\Service\ConnectorBase`,
which also contains a number of convenience methods. Among the methods described below,
some go beyond the public interfance and belong to the connector base. It is
recommended that all custom connector services extend the connector base rather
than reimplementing boilerplate code.


getType()
  This method returns the type of the connector service. The type is
  representative of the kind of data it can handle, e.g. :code:`csv`
  for the "svconnector_csv" service.

  Input
    none

  Output
    string


getName()
  This method returns a human-readable name for the service.

  Input
    none

  Output
    string


initialize()
  This method performs any necessary initialization for the connector service. It is
  called automatically by the registry when instanciating the service. The base class
  :php:`\Cobweb\Svconnector\Service\ConnectorBase` contains a default implementation which
  fires an event allowing custom initialization processes. If you should need to implement
  your own version of the `initialize()` method in your connector service, make sure to call
  the parent method or to dispatch the :php`\Cobweb\Svconnector\Event\InitializeConnectorEvent`,
  since users of your service will expect that.

  Input
    none

  Output
    void


isAvailable()
  This method can be called when a connector service is about to be used
  to check if it is available or not. It is expected to return a boolean value
  accordingly.

  Input
    none

  Output
    boolean


getSampleConfiguration()
  This method returns the sample configuration that gets loaded in the service
  testing backend module (see :ref:`Configuration sample <developers-sample>`.

  Input
    none

  Output
    string


checkConfiguration()
  This method is called whenever the connector configuration needs to be checked.
  It is typically called by the :php:`query()` method, but can also be called
  by third-party code. It receives the list of connector parameters as an input
  and is expected to return a list of errors, warnings and notices
  (see: :php:`\Cobweb\Svconnector\Service\ConnectorBase::checkConfiguration()`
  for the structure of the return array.

  Warnings and notices are not considered blocking.

  Input
    array of parameters

  Output
    array of errors, warnings or notices


getCallContext()
  This method returns the current :ref:`call context object <developers-api-context>`.

  Input
    none

  Output
    :php:`\Cobweb\Svconnector\Domain\Model\Dto\CallContext`


getConnectionInformation()
  This method returns any connection information that may have been set by
  the connector or events called by the connector.

  Input
    none

  Output
    php:`\Cobweb\Svconnector\Domain\Model\Dto\ConnectionInformation`


logConfigurationCheck()
  This method is used to cleanly report all configuration issues by logging them
  using the TYPO3 logging API. This is not automatically done by :php:`checkConfiguration()`
  because some other form of action might be taken when services are used in
  third-party code.

  When developing your own service, you should call this method right after
  :php:`checkConfiguration()` in the :php:`query()` method.

  Input
    array of errors, warnings or notices

  Output
    void


query()
  Strictly speaking this method is not part of the API, since it is
  protected and thus not designed to be called directly. It is designed
  to encapsulate the distant source querying mechanism, so it is good
  programming practice to use it.

  Input
    none

  Output
    mixed (result from the distant source)


fetchRaw()
  This method is expected to return the result of the query to the
  distant source as is, without transformation.

  Input
    none

  Output
    mixed (result from the distant source)


fetchXML()
  This method is expected to return the result of the query to the
  distant source transformed into a XML structure (as a string).

  Input
    none

  Output
    string (XML structure)


fetchArray()
  This method is expected to return the result of the query to the
  distant source transformed into a PHP array.

  Input
    none

  Output
    array


postProcessOperations()
  This method is designed to be called back by whatever process called
  the connector in the first place when said process is done. It
  receives as argument the usual list of parameters, plus some variable
  indicating the status of the process (typically this could be an
  indication of success or failure).

  It doesn't do anything by itself, but just calls events (or hooks).

  Input
    array of parameters (deprecated, pass an empty array instead) and a status

  Output
    void


.. _developers-api-context:

Call context API
""""""""""""""""

The call context is meant to contain information about the context in which the
Connector service was called. This can be useful when responding to events, in
order to react appropriately. The context may contain serveral pieces of information,
each referenced with a key (the context itself being an associative array). Each
piece of information is also an array. It is recommended to use/include the name
of the extension using the service in the keys, in order to avoid overwriting existing
data (even though the scenario is pretty unlikely).

Example usage:

.. code-block:: php

   // Set some data
   $service->getCallContext()->add('external_import', ['foo' => 'bar']);

   // Get some data
   $context = $service->getCallContext()->get();


.. _developers-api-connection-information:

Connection information API
""""""""""""""""""""""""""

The connection information is meant to contain data relative to the current
connection to the third-party service being accessed by the connector service.
For example, this could be an authentication token retrieved during the service
initialization (i.e. when the :code:`initialize()` method was called).

Assuming the following data is set:

.. code-block:: php

   // Set some data
   $service->getConnectionInformation()->add('token', ['sso' => '$$XyZSecureCode42']);

it is later used to substitute variables in the connector parameters. The parameters are parsed
when getting a service from the registry. Considering the following parameters:

.. code-block:: php

   [
      'headers' => [
         'token' => '{token.sso}'
      ]
   ]

the service would then have the following concrete parameters for usage:

.. code-block:: php

   [
      'headers' => [
         'token' => '$$XyZSecureCode42'
      ]
   ]

A :php:`\Cobweb\Svconnector\Event\ProcessParametersEvent` event is fired after this
parsing, allowing for further manipulation of the connector parameters.
