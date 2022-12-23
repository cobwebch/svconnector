.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt


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
  representative of this kind of data it can handle, e.g. :code:`csv`
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


isAvailable()
  This method can be called when a connector service is about to be used
  to check if it is available or not. It is expected to return a boolean value
  accordiningly.

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
    array of parameters

  Output
    mixed (result from the distant source)


fetchRaw()
  This method is expected to return the result of the query to the
  distant source as is, without transformation.

  Input
    array of parameters

  Output
    mixed (result from the distant source)


fetchXML()
  This method is expected to return the result of the query to the
  distant source transformed into a XML structure (as a string).

  Input
    array of parameters

  Output
    string (XML structure)


fetchArray()
  This method is expected to return the result of the query to the
  distant source transformed into a PHP array.

  Input
    array of parameters

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
    array of parameters and a status

  Output
    void

