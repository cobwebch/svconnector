.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt


.. _developers-utilities:

Utilities
^^^^^^^^^

The extension also provides a couple of utility classes. Their features
are described here.


.. _developers-utilities-conversions:
.. _developers-conversions:

Format conversions
""""""""""""""""""

The :code:`\Cobweb\Svconnector\Utility\ConnectorUtility` class provides a
conversion method utility, which transforms a
XML structure into a PHP array without losing any information. This
method is simply called as:

.. code-block:: php

   $phpArray = \Cobweb\Svconnector\Utility\ConnectorUtility::convertXmlToArray($xml)

Of course one's own conversion method may be used if needed. The
conversion from a PHP array to a XML structure can safely rely on
TYPO3 CMS API. e.g.:

.. code-block:: php

	$xml = '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>' . "\n" . GeneralUtility::array2xml($result);

Again one's own conversion method may be used if needed.


.. _developers-utilities-reading-files:

Reading files
"""""""""""""

The :code:`\Cobweb\Svconnector\Utility\FileUtility` provides a general
method for reading the content of a file. It will transparently handle
the following syntax for pointing to a file:

- an absolute file path (within the TYPO3 root path or :code:`TYPO3_CONF_VARS[BE][lockRootPath]`),
  e.g. :file:`/var/foo/web/fileadmin/import/bar.csv`

- a file path relative to the TYPO3 root, e.g. :file:`fileadmin/import/foo.txt`

- a file reference using the :code:`EXT:` syntax, e.g. :file:`EXT:foo/Resources/Private/Data/bar.txt`

- a fully qualified URL, e.g. :file:`http://www.example.com/foo.txt`

- a FAL reference including storage ID and file identifier, e.g. :file:`FAL:2:/foo.txt`

- a custom syntax, starting with whatever keyword you want, e.g. :file:`MYKEY:whatever_you_want`

For the latter, you need to implement a "reader" class which will handle this custom reference.
This class must inherit from :code:`\Cobweb\Svconnector\Utility\AbstractFileReader`. It must
be declared like a hook, using the custom keyword (without the colon) as a key. Example:

.. code-block:: php

	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['svconnector']['fileReader']['MYKEY']


The class must implement the :code:`read()` method, which is expected to return the
file's content as a string, or :code:`false` if some problem happened. An error message
about the problem can be reported from within the reader using:

.. code-block:: php

	$this->fileUtility->setError('Some reason here');


The :code:`read()` method receives the full syntax as input (in the above example,
:file:`MYKEY:whatever_you_want`).

Using the :code:`\Cobweb\Svconnector\Utility\FileUtility` in your own Connector service
is very easy. Here is how it's done in the "svconnector_csv" extension

.. code-block:: php

	$fileUtility = GeneralUtility::makeInstance(\Cobweb\Svconnector\Utility\FileUtility::class);
	$fileContent =  $fileUtility->getFileContent($parameters['filename']);


The :code:`getFileContent()` method will return :code:`false` if some error happened reading
the file. An error message is available to retrieve using:

.. code-block:: php

	$error =  $fileUtility->getError();


If you would rather have the content stored into a (temporary) file rather than returned
directly, you can use the :code:`getFileAsTemporaryFile()`, which will return the
full path to the file where the content is stored. It is up to you to read the file
and delete it once done:

.. code-block:: php

	$fileUtility = GeneralUtility::makeInstance(\Cobweb\Svconnector\Utility\FileUtility::class);
	// Get the content stored into a temp file
	$filename =  $fileUtility->getFileAsTemporaryFile($parameters['filename']);
	// Read from the file
	$content = file_get_contents($filename);
	// Remove the file
	unlink($filename);

Method :code:`getFileAsTemporaryFile()` will also return :code:`false` when something went
wrong reading the distant data.
