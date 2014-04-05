.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt


.. _developers-conversions:

Format conversions
^^^^^^^^^^^^^^^^^^

The extension also provides a utility class. The main method provided
by this class is a XML to array conversion utility, which transforms a
XML structure into a PHP array without losing any information. This
method is simply called as:

.. code-block:: php

   $phpArray = tx_svconnector_utility::convertXmlToArray($xml)

Of course one's own conversion method may be used if needed. The
conversion from a PHP array to a XML structure can safely rely on
TYPO3's API. e.g.:

.. code-block:: php

   $xml = t3lib_div::array2xml_cs($phpArray);

Again one's own conversion method may be used if needed.

