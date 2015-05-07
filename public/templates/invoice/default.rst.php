.. |name| replace:: <?php echo $receiver['name']; ?>

.. |street| replace:: <?php echo $receiver['street']; ?>

.. |zipcity| replace:: <?php echo $receiver['zipcity']; ?>

.. |date| replace:: <?php echo isset($date) ? $date : date('d.m.Y'); ?>

.. |month| replace:: <?php echo isset($month) ? $month : 'vergangenen Monat'; ?>

.. |invoicenumber| replace:: <?php echo $number ?>

.. |salutation| replace:: <?php echo isset($salutation) ? $salutation : 'Sehr geehrte Damen und Herren' ?>

.. |sender-name| replace:: <?php echo $sender['name'] ?>

.. |sender-street| replace:: <?php echo $sender['street'] ?>

.. |sender-zipcity| replace:: <?php echo $sender['zipcity'] ?>

.. |sender-email| replace:: <?php echo $sender['email']; ?>

.. |sender-vatid| replace:: <?php echo $sender['vatid']; ?>

.. |iban| replace:: <?php echo $sender['iban']; ?>

.. |bic| replace:: <?php echo $sender['bic']; ?>

.. |currency| replace:: <?php echo $currency; ?>


.. class:: sender

  |sender-name|

  |sender-street|

  |

  |sender-zipcity|

  |
  |
  |
  |

.. class:: small

  |sender-name| - |sender-street| - |sender-zipcity|

.. class:: receiver

  |name|

  |street|

  |zipcity|

|
|
|
|

.. class:: datetable

  +----------------------+------------------+
  | .. class:: small     |                  |
  |                      |                  |
  | Rechnungsnummer      |                  |
  +----------------------+------------------+
  | |invoicenumber|      | .. class:: right |
  |                      |                  |
  |                      |           |date| |
  +----------------------+------------------+

Rechnung
========


.. class:: salutation

  |salutation|,

für die im |month| erbrachten Leistungen stelle ich Ihnen in Rechnung:

|

.. list-table::
  :widths: 13 2
  :class: invoicetableheader

  * - **Leistungen**
    - **Betrag**

.. list-table::
  :widths: 10 2 1 2

<?php $sum = 0; ?>
<?php $taxsum = 0; ?>
<?php foreach ($items as $item): ?>
    <?php $price = isset($item['price']) ? $item['price'] : $item['rate']*$item['qty']; ?>
    <?php $sum += $price; ?>
    <?php $taxrate = isset($item['taxrate']) ? $item['taxrate'] : 19.0; ?>
    <?php $taxsum += $taxrate * $price / 100.0; ?>

  * - <?php echo $item['description']; ?>

    - .. class:: right

        <?php echo $item['qty']; ?>

    - .. class:: left

        <?php echo isset($item['unit']) ? $item['unit'] : 'h'; ?>

    - .. class:: right

        <?php echo number_format($price, 2, ',', '.'); ?> |currency|


<?php endforeach; ?>

.. list-table::
  :widths: 13 2
  :class: invoicetotals

  * - .. class:: right

        Summe
    - .. class:: right

        <?php echo number_format($sum, 2, ',', '.'); ?> |currency|

  * - .. class:: right

        zzgl. 19% USt.
    - .. class:: right

        <?php echo number_format($taxsum, 2, ',', '.'); ?> |currency|


.. list-table::
  :widths: 13 2
  :class: invoicegrandtotal

  * - .. class:: right

        **Rechnungsbetrag**
    - .. class:: right

        **<?php echo number_format($sum + $taxsum, 2, ',', '.') . " $currency"; ?>**

|

Bitte überweisen Sie den Rechnungsbetrag innerhalb von 14 Tagen an die unten angegebene Bankverbindung.

.. class:: greeting

  Mit freundlichen Grüßen

  |sender-name|

.. footer::

  .. class:: footertable

    +----------------------+---------------------------------+-----------------------------------+
    | |sender-name|        |                                 | IBAN: |iban|                      |
    |                      |                                 |                                   |
    | |sender-email|       | Steuernummer: |sender-vatid|    | BIC: |bic|                        |
    +----------------------+---------------------------------+-----------------------------------+
