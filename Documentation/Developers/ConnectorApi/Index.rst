.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt


.. _developers-api:

Connector API
^^^^^^^^^^^^^

The list below describes the methods that make up the connector API.
These are the methods that you must absolutely implement in your own
connector services if you want to make them usable by extensions that
rely on such services.

init()
  This method is called when the connector is instantiated by the TYPO3
  service API. It is expected to return a boolean value: true if the
  distant source is available, false otherwise.

  Input
    none

  Output
    boolean


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

  It doesn't do anything by itself, but just calls hooks.

  Input
    array of parameters and a status

  Output
    void

