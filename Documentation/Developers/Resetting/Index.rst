.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt


.. _developers-resetting:

Resetting the service
^^^^^^^^^^^^^^^^^^^^^

Services are singletons. Existing instances are stored in a global
array. This may have the undesirable effect
that the instance you get is not brand new and shiny, but loaded with
data from its previous call. To avoid that, whenever an instance is
recalled, TYPO3 will call the :code:`reset()` method of the service, where any
necessary clean up can be performed.

If you think this is needed for the particular service that you are
developing, then don't forget to implement the :code:`reset()` method.

