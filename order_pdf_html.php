<div id="woo_order_items_html">
    <?php
    require_once __DIR__ . '/order_pdf_style.php';
    require_once __DIR__ . '/order_pdf_html_header.php';

    $items_count = count($order->get_items());

    $burger_items = '';
    $other_items = '';

    $counter = 1;

    foreach ($order->get_items() as $item_id => $item) {
        ob_start();

        $item_category = 'other-item';
        
        $meta_data = $item->get_all_formatted_meta_data('');

        $product_id = $item->get_product_id();

        $categories = get_the_terms($product_id, 'product_cat');
        
        foreach ($categories as $key => $cat) {
            if ($cat->slug == 'burger') {
                $item_category = ' burger';
                break;
            }
        }

        $all_metadata = $item->get_meta_data();

        if (($counter > 1 && trim($item_category) == 'burger') || ($counter == 7) || ($counter > 7 && $counter % 7 == 0)) {
            echo '<div class="print-page-break"></div>';
            include __DIR__ . '/order_pdf_html_header.php';
        }
    ?>
        <div class="order-item <?php echo $item_category; ?>">
            <table class="items-table" width="100%">
                <tr class="item-info">
                    <td class="item-name" width="55%">
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


                        <?php
                        // Composite Product meta data
                        // $wooco_items = WPCleverWooco::get_items($item->get_meta('wooco_ids'));

                        woo_op_order_composite_meta($item_id, $item);

                        ?>
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
        </div>
    <?php
        // $counter < $items_count

        if (trim($item_category) == 'burger') {
            $burger_items .= ob_get_clean();
        } else {
            $other_items .= ob_get_clean();
        }

        $counter++;
    }

    echo $other_items;

    echo $burger_items;
    ?>
</div>