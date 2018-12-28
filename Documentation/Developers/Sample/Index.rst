.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt


.. _developers-sample:

Configuration sample
^^^^^^^^^^^^^^^^^^^^

The testing BE module can read configuration samples from existing connector services.
This makes it easier to enter a configuration for testing, in particular to avoid
forgetting some important parameter. This sample configuration does
not have to be declared in any way, but is expected to be strictly
located in :code:`Resources/Public/Samples/Configuration.txt` .

It consists of a simple text file, with one configuration option per
line, as you would input it in the BE module. Please check the
existing connector services (Feed, JSON, SQL, CSV) for examples.

