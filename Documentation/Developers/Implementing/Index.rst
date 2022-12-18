.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt


.. _developers-implementing:

Implementing a connector service
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

The first step is to create a class derived from the base connector
service and implement all the methods described in the API above. Your
class declaration should look something like:

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

You must then register your service with the connector registry. This goes
into the :file:`Configuration/Services.yaml` file. Example:

.. code-block:: yaml

     Cobweb\SvconnectorCsv\Service\ConnectorCsv:
       public: true
       arguments:
         - !tagged_iterator connector.service

The base service provides several utility methods to access method or
properties of the :code:`\TYPO3\CMS\Lang\LanguageService` class and of
the :code:`\TYPO3\CMS\Core\Charset\CharsetConverter` class
independently of context (FE or BE).
