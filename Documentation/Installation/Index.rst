.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: /Includes.rst.txt


.. _installation:

Installation
------------

This extension does nothing all by itself. Connectors must be
developed for specific third-party applications. However this
extension must be installed since it provides the base class from
which all connector services inherit.


.. _installation-updating-600:

Updating to 6.0.0
^^^^^^^^^^^^^^^^^

Version 6.0.0 adds support for TYPO3 13 and drops support for TYPO3 11.

It introduces one new important method to the base API: :code:`initialize()`, which
is called by the registry when it collects all available services.
:ref:`Read more about it in the Developer's Guide <developers-api>`.

Also new is the availability of a :ref:`call context <developers-api-context>` which - as its name implies -
contains information about the context in which the service is being used.
Another object called :ref:`connection information <developers-api-connection-information>` may contain data than can
be used to dynamically change the parameters passed to the service.

Most importantly, passing the connector parameters in the :code:`fetch*()` methods
has been deprecated. Instead, parameters must be passed when getting the connector
from the registry:

.. code-block:: php

    $service = $connectorRegistry->getServiceForType('json', $parameters);


.. _installation-updating-500:

Updating to 5.0.0
^^^^^^^^^^^^^^^^^

Version 5.0.0 adds support for TYPO3 12 and drops support for TYPO3 10.
Most importantly it adapts to the deprecation of Services in the TYPO3 Core
by implementing its own registry. As such the way to :ref:`register services <developers-implementing>`
has completely changed and the :ref:`Connector API <developers-api>`
has been modified as well (some methods were added, some removed
and most methods were hardened with regards to strict typing, changing their
signature).

All existing services need to be adapted, none will work anymore without some work.
The changes to perform are:

- change registration from :file:`ext_localconf.php` to :file:`Configurations/Services.yaml`

- remove methods :code:`init()` and :code:`reset()`

- add methods :code:`getType()`, :code:`getName()` and :code:`isAvailable()`

- all :code:`fetch*()` methods, :code:`checkConfiguration()` and :code:`query()`
  need to adapt to the new base method signatures

- ensure member variable :code:`protected string $extensionKey` is declared



.. _installation-updating-400:

Updating to 4.0.0
^^^^^^^^^^^^^^^^^

Version 4.0.0 adds support for TYPO3 11 and PHP 8.0, while dropping support
for TYPO3 9. Apart from that it does not contain other changes and
the update process should be smooth.


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
