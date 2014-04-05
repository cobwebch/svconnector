.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt


.. _developers-api:

Hooks
^^^^^

It doesn't really make sense to have hooks in the base connector
class, since it cannot be instantiated directly. There are quite
some examples in the existing connector services and here is a list
of recommended hooks to place in your own service:

processParameters
  This hook should be implemented in the :code:`query()`
  method. The idea is that it makes it possible to manipulate the
  parameters of the call before actually querying the distant source. This should provide
  enough flexibility to other developers that they can use your
  connector service without modifications.

  The method called by the hook receives as parameters the array of parameters passed to
  the :code:`query()` method and a back-reference to the calling connector
  object.

processResponse
  This hook is designed to process the data inside
  the :code:`query()` method, just as it is returned from the distant source.
  Again this gives the flexibility to manipulate that data for special
  cases without changing the whole connector. Note that since all
  “fetch” methods are supposed to call the :code:`query()` method to get the
  data from the distant source, this hook actually has an impact on all
  data fetching methods.

  The method called by the hook receives as
  parameters the response of the distant source and a back-reference to
  the calling connector object.

processRawData
  This hook is very similar to the
  “processResponse” hook, but it is designed to be called inside
  :code:`fetchRaw()`, so that it will affect the output of that method only.

  The method called by the hook receives as parameters the output of the
  :code:`query()` method and a back-reference to the calling connector object.

processArray, processXML
  This is similar to processRawData, but inside the
  :code:`fetchArray()` and :code:`fetchXML()` methods
  respectively. The first parameter received by the hook's method are
  the PHP array and the XML string respectively.

postProcessOperations
  This hook is designed to perform
  operations after the process that initially called the connector is
  done. This can be any kind of clean up that might be necessary.

  The method called by the hook receives as parameters the array of
  parameters passed to the :code:`query()` method, a status indicator and a
  back-reference to the calling connector object. The nature of the
  status indicator is not clearly defined and will depend on the process
  that calls back the connector. In the simplest case, it may be a
  boolean value indicating success or failure.

