.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: /Includes.rst.txt


.. _developers-implementing:

Implementing a connector service
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

A custom connector service must implement the interface :php:`\Cobweb\Svconnector\Service\ConnectorServiceInterface`.
However, there is an abstract base class (:php:`\Cobweb\Svconnector\Service\ConnectorBase`)
which contains some base implementations and some useful helper methods, it is thus
recommended to extend that class rather than implementing the interface from scratch.


.. code-block:: php

   namespace MyName\MyExt\Service;
   class ConnectorSpecialThingy extends \Cobweb\Svconnector\Service\ConnectorBase {
       protected string $extensionKey = 'my_ext';
       ...
   }

It is considered a best practice to place your class file in the
:file:`Classes/Services` folder of your extension. It must declare an
:code:`$extensionKey` member variable, as this is used by the API to fetch
the sample configuration.

You must then register your service with the connector registry. This is done
using the :php:`\Cobweb\Svconnector\Attribute\AsConnectorService` attribute,
with the service's type and name passed as attribute arguments (the type is
critical, since this is what identifies a service; the name is a description):

.. code-block:: php

   #[AsConnectorService(type: 'json', name: 'JSON connector')]
   class ConnectorJson extends ConnectorBase
   {
      ...
   }

.. note::

   A connector service can also be registered in the :file:`Configuration/Services.yaml` file,
   but this is now deprecated in favor of using the PHP attribute described above. Example:

   .. code-block:: yaml

        Cobweb\SvconnectorCsv\Service\ConnectorCsv:
          public: true
          arguments:
            - !tagged_iterator connector.service

The base service provides several utility methods to access method or
properties of the :code:`\TYPO3\CMS\Lang\LanguageService` class and of
the :code:`\TYPO3\CMS\Core\Charset\CharsetConverter` class
independently of context (FE or BE).

If you need to implement the :code:`__construct()` method, make sure to call
:code:`parent::__construct()` within it.
