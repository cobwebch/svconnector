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

   class tx_svconnectorspecial_sv1 extends tx_svconnector_base {
   }

after having included the base connector class:

.. code-block:: php

   require_once(t3lib_extMgm::extPath('svconnector', 'class.tx_svconnector_base.php'));

If you're using namespaces, the class declaration will be like:

.. code-block:: php

   class MySpecialService extends \tx_svconnector_base {
   }

The require statement is still needed, since the base class is not
yet using namespaces.

You must then register your service with the TYPO3 service API. This
goes into your extension's :file:`ext_localconf.php` file and will look like
that:

.. code-block:: php

	t3lib_extMgm::addService($_EXTKEY,  'connector' /* sv type */,  'tx_svconnectorspecial_sv1' /* sv key */,
		array(
			'title' => 'Special Connector',
			'description' => 'Connect to a special server',
			'subtype' => 'special',
			'available' => TRUE,
			'priority' => 50,
			'quality' => 50,
			'os' => '',
			'exec' => '',
			'classFile' => t3lib_extMgm::extPath($_EXTKEY) . 'sv1/class.tx_svconnectorspecial_sv1.php',
			'className' => 'tx_svconnectorspecial_sv1',
		)
	);

The base service provides several utility methods to access method or
properties of the :code:`\TYPO3\CMS\Lang\LanguageService` class and of
the :code:`\TYPO3\CMS\Core\Charset\CharsetConverter` class
independently of context (FE or BE).

