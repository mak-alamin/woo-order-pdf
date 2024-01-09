<h2 class="pdf_heading">Rechnung für Bestellung #<?php echo $order->get_id(); ?></h2>
<div class="address">
    <p><?php echo $order->get_billing_address_2(); ?></p>
    <p><?php
        $order_date = $order->get_date_created();
        $formatted_date = $order_date->format('d. F Y');
        echo "Datum " . $formatted_date;
        ?></p>
</div>

<table class="header-table" width="100%">
    <tr class="item-heading">
        <th width="55%">Item Name</th>
        <th width="15%">Item Cost</th>
        <th width="15%">Quantity</th>
        <th width="15%">Total</th>
    </tr>
</table>