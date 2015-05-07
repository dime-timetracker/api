.. |name| replace:: <?php echo isset($receiver['name']) ? $receiver['name'] : ''; ?>

.. |street| replace:: <?php echo isset($receiver['street']) ? $receiver['street'] : ''; ?>

.. |zip| replace:: <?php echo isset($receiver['city']) ? $receiver['city'] : ''; ?>

.. |city| replace:: <?php echo isset($receiver['zip']) ? $receiver['zip'] : ''; ?>

.. |date| replace:: <?php echo isset($date) ? $date : date('Y-m-d'); ?>

.. |month| replace:: <?php echo isset($month) ? $month : 'last month'; ?>

.. |invoicenumber| replace:: <?php echo isset($number) ? $number : ''; ?>

.. |salutation| replace:: <?php echo isset($salutation) ? $salutation : 'Dear ladies and gentlemen,' ?>

.. |introduction| replace:: <?php echo isset($introduction) ? $introduction : 'You ordered:' ?>

.. |ending| replace:: <?php echo isset($ending) ? $ending : '' ?>

.. |greeting| replace:: <?php echo isset($greeting) ? $greeting : 'Kind regards,' ?>

.. |sender-name| replace:: <?php echo isset($sender['name']) ? $sender['name'] : ''; ?>

.. |sender-street| replace:: <?php echo isset($sender['street']) ? $sender['street'] : ''; ?>

.. |sender-zip| replace:: <?php echo isset($sender['zip']) ? $sender['zip'] : ''; ?>

.. |sender-city| replace:: <?php echo isset($sender['city']) ? $sender['city'] : ''; ?>

.. |sender-email| replace:: <?php echo isset($sender['email']) ? $sender['email'] : ''; ?>

.. |sender-vatid| replace:: <?php echo isset($sender['vatid']) ? $sender['vatid'] : ''; ?>

.. |iban| replace:: <?php echo isset($sender['iban']) ? $sender['iban'] : ''; ?>

.. |bic| replace:: <?php echo isset($sender['bic']) ? $sender['bic'] : ''; ?>

.. |currency| replace:: <?php echo isset($currency) ? $currency : ''; ?>


.. class:: sender

  |sender-name|

  |sender-street|

  |

  |sender-city| |sender-zip|

  |
  |
  |
  |

.. class:: small

  |sender-name| - |sender-street| - |sender-city| |sender-zip|

.. class:: receiver

  |name|

  |street|

  |city| |zip|

|
|
|
|

.. class:: datetable

  +----------------------+------------------+
  | .. class:: small     |                  |
  |                      |                  |
  | Invoice #            |                  |
  +----------------------+------------------+
  | |invoicenumber|      | .. class:: right |
  |                      |                  |
  |                      |           |date| |
  +----------------------+------------------+

Rechnung
========


.. class:: salutation

  |salutation|,

  |introduction|

<?php if (isset($items) && is_array($items)): ?>

|

.. list-table::
  :widths: 13 2
  :class: invoicetableheader

  * - **Service**
    - **Amount**

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

        Sum
    - .. class:: right

        <?php echo number_format($sum, 2, ',', '.'); ?> |currency|

  * - .. class:: right

        Tax
    - .. class:: right

        <?php echo number_format($taxsum, 2, ',', '.'); ?> |currency|


.. list-table::
  :widths: 13 2
  :class: invoicegrandtotal

  * - .. class:: right

        **Total**
    - .. class:: right

        **<?php echo number_format($sum + $taxsum, 2, ',', '.') . " $currency"; ?>**

<?php endif; ?>

|additional_notes|

.. class:: greeting

  |greeting|

  |sender-name|

.. footer::

  .. class:: footertable

    +----------------------+---------------------------+-----------------------------------+
    | |sender-name|        |                           | IBAN: |iban|                      |
    |                      |                           |                                   |
    | |sender-email|       | VAT ID: |sender-vatid|    | BIC: |bic|                        |
    +----------------------+---------------------------+-----------------------------------+
