.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt


.. _developers-exceptions:

Throwing exceptions
^^^^^^^^^^^^^^^^^^^

A connector may encounter errors while performing its task that could
not be detected upstream during the initialization phase. In such a
case, it is recommended to interrupt the process and throw an
exception with an informative error message.

Applications using connector services should be ready to receive
exceptions and should thus encapsulate calls to any of the "fetch"
methods in a try/catch block:

.. code-block:: php

	try {
		$result = $serviceObject->fetchRaw($parameters);
		// Do something...
	}
	catch (\Exception $e) {
		// Issue error message or log error, whatever...
	}

.. note::

   Since version 3.0.0, there exists a base exception class which
   should be used by all connector services (:code:`\Cobweb\Svconnector\Exception\ConnectorException`).
