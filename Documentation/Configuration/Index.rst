.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _configuration:

Configuration
-------------

The configuration is up to each specific connector according to its
needs.

A BE module is provided to test connections.

.. figure:: ../Images/TestModule.png
   :alt: The connector test module

   A view of the connector test module in the BE

The steps to use this tool are simple:

#. Choose a particular service from the drop-down menu. Only available
   services are in the list. If some services are unavailable, a warning
   will be displayed at the top of the screen.

#. Enter all the necessary parameters in the text field. For each
   parameter, enter its name, an equal (=) sign and its value (as in the
   screenshot above).

#. Choose the output format ("raw" will return the native format for the
   type of resource being connected to).

#. Click on the "Test!" button. If the connection is successful, the data
   fetched by the connector service will be displayed below the form. If
   some error happens, a message will be displayed at the top of the
   page.


