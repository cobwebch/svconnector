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
      ...
   }

It is considered a best practice to place your class file in the
:file:`Classes/Services` folder of your extension.

You must then register your service with the TYPO3 CMS service API. This
goes into your extension's :file:`ext_localconf.php` file and will look like
that:

.. code-block:: php

   \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addService(
      $_EXTKEY,
      // Service type
      'connector',
      // Service key
      'tx_svconnectorspecial_sv1',
      array(
         'title' => 'Special Connector',
         'description' => 'Connect to a special server',
         'subtype' => 'special',
         'available' => true,
         'priority' => 50,
         'quality' => 50,
         'os' => '',
         'exec' => '',
         'className' => \MyName\MyExt\Service\ConnectorSpecialThingy::class,
      )
   );

The base service provides several utility methods to access method or
properties of the :code:`\TYPO3\CMS\Lang\LanguageService` class and of
the :code:`\TYPO3\CMS\Core\Charset\CharsetConverter` class
independently of context (FE or BE).

