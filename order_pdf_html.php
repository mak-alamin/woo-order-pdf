<div id="woo_order_items_html">
    <?php require_once __DIR__ . '/order_pdf_style.php'; ?>

    <h2 class="pdf_heading">Rechnung für Bestellung#<?php echo $order->get_id(); ?></h2>

    <?php
    foreach ($order->get_items() as $item_id => $item) {
        $meta_data = $item->get_all_formatted_meta_data('');
    ?>
        <table class="order-item">
            <tr class="item-heading">
                <th>Item Name</th>
                <th>Item Cost</th>
                <th>Quantity</th>
                <th>Total</th>
            </tr>

            <tr class="item-info">
                <td class="item-name">
                    <h2> <?php echo $item->get_name() . ' - (' . $item->get_quantity() . ')'; ?> </h2>

                    <?php
                    if (!empty($meta_data)) {
                    ?>
                        <table cellspacing="0" class="display_meta">
                            <?php
                            foreach ($meta_data as $meta_id => $meta) { ?>
                                <tr>
                                    <th><?php echo $meta->display_key; ?>: </th>
                                    <td><?php echo $meta->display_value; ?></td>
                                </tr>
                            <?php } ?>
                        </table>
                    <?php } ?>
                </td>

                <td class="item-cost" width="15%">
                    <?php
                    echo wc_price($order->get_item_subtotal($item, false, true), array('currency' => $order->get_currency())); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    ?>
                </td>

                <td class="item-quantity" width="15%">
                    <?php
                    echo '<small class="times">&times;</small> ' . esc_html($item->get_quantity());

                    $refunded_qty = -1 * $order->get_qty_refunded_for_item($item_id);

                    if ($refunded_qty) {
                        echo '<small class="refunded">' . esc_html($refunded_qty * -1) . '</small>';
                    }
                    ?>
                </td>

                <td class="line-cost" width="15%">
                    <?php
                    echo wc_price($item->get_total(), array('currency' => $order->get_currency()));

                    if ($item->get_subtotal() !== $item->get_total()) {
                        /* translators: %s: discount amount */
                        echo '<span class="wc-order-item-discount">' . sprintf(esc_html__('%s discount', 'woocommerce'), wc_price(wc_format_decimal($item->get_subtotal() - $item->get_total(), ''), array('currency' => $order->get_currency()))) . '</span>';
                    }

                    $refunded = -1 * $order->get_total_refunded_for_item($item_id);

                    if ($refunded) {
                        echo '<small class="refunded">' . wc_price($refunded, array('currency' => $order->get_currency())) . '</small>';
                    }
                    ?>
                </td>
            </tr>
        </table>
    <?php
    }
    ?>
</div>