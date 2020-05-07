.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _installation:

Installation
------------

This extension does nothing all by itself. Connectors must be
developed for specific third-party applications. However this
extension must be installed since it provides the base class from
which all connector services inherit.


.. _installation-updating-340:

Updating to 3.4.0
^^^^^^^^^^^^^^^^^

The :ref:`sample configuration files <developers-sample>` have been
changed to JSON format to easily allow for nested properties. If you
have developed your own service and have defined a sample configuration
file, you will need to change it to the new format. As always look at
other existing connector services for examples (in particular, the
"svconnector_json" extension which uses nested properties).

Also a new method was introduced as part of the :ref:`Connector Sevice API <developers-api>`:
`checkConfiguration()` is expected to parse the connector configuration and return
errors, warnings or notices as needed.
