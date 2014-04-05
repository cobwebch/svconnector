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

Updating to 1.1.0
^^^^^^^^^^^^^^^^^

In version 1.1.0 the “sv1” class was moved to the root of the
extension and renamed :code:`sv_connector_base` as this made much more
sense. The backward compatibility was ensured by keeping the “sv1”
class, which is now just an empty wrapper for the base class.

If you designed an extension which extended the “sv1” class, it would
be good to change it to extend the “base” class instead, although this
is not necessary.
