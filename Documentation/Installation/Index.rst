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


.. _installation-updating:

Updating to 3.0.0
^^^^^^^^^^^^^^^^^

The base connector service class was renamed from :code:`sv_connector_base`
to :code:`\Cobweb\Svconnector\Service\ConnectorBase`. There is
**no backward compatibility layer**. You need to change your existing services
to use that new class.

Furthermore, the even older "sv1" class was also definitively removed.
