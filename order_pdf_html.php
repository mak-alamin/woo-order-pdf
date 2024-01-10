<div id="woo_order_items_html">
    <?php
    require_once __DIR__ . '/order_pdf_style.php';

    ob_start();

    require_once __DIR__ . '/order_pdf_html_header.php';

    $pdf_page_header = ob_get_clean();

    $items_count = count($order->get_items());

    $burger_items = '';
    $other_items = '';

    $counter = 1;

    // echo "Order Items: " . count($order->get_items());

    $pdf_page_no = 1;

    foreach ($order->get_items() as $item_id => $item) {
        $item_html = '';

        $item_category = 'other-item';

        $meta_data = $item->get_all_formatted_meta_data('');

        $product_id = $item->get_product_id();

        $categories = get_the_terms($product_id, 'product_cat');

        foreach ($categories as $key => $cat) {
            // echo "Cat: " . $cat->slug;
            if ($cat->slug == 'burger') {
                $item_category = 'burger';
                break;
            }
        }

        $all_metadata = $item->get_meta_data();

        // $item_html .=  ", Pdf page: " . $pdf_page_no;

        if ($counter == 1) {
            $item_html .= '<div class="first_pdf_page">';
            $item_html .= $pdf_page_header;
        }

        // Logic for page break
        if (($counter == 7) || ($counter > 6 &&  ($counter / 6) > $pdf_page_no) || ($counter > 1 && $item_category == 'burger')) {

            if ($counter > 6 &&  ($counter / 6) > $pdf_page_no) {
                $pdf_page_no++;
            }

            if ($counter == 7 || ($counter < 7 && $item_category == 'burger')) {
                $item_html .= '</div>'; // .first_pdf_page ends
            }

            $item_html .=  '<div class="print-page-break"></div>';

            $item_html .= $pdf_page_header;
        }

        // Item Name column
        $item_html .= '<div class="order-item ' . $item_category . '">';

        $item_html .= '<table class="items-table" width="100%">';

        $item_html .= '<tr class="item-info">';

        $item_html .= '<td class="item-name" width="55%">';

        $item_html .= '<h2>' . $item->get_name() . '- (' . $item->get_quantity() . ') </h2>';

        // Composite Product meta data
        // $wooco_items = WPCleverWooco::get_items($item->get_meta('wooco_ids'));

        $item_html .= woo_op_order_composite_meta($item_id, $item);


        if (!empty($meta_data)) {
            $item_html .= '<div class="display-meta">';
            foreach ($meta_data as $meta_id => $meta) {
                // $item_html .= '<p><strong>' . $meta->display_key  . ': </strong><span>' . $meta->display_value . '</span></p>';

                $item_html .= '<p><strong>' . $meta->key  . ': </strong><span>' . $meta->value . '</span></p>';
            }

            $item_html .= '</div>';
        }

        $item_html .= '</td>';

        // Item Cost Column
        $item_html .= '<td class="item-cost" width="15%">';

        $item_html .=  wc_price($order->get_item_subtotal($item, false, true), array('currency' => $order->get_currency()));

        $item_html .= '</td>';

        // Item Quantity Column
        $item_html .= '<td class="item-quantity" width="15%">';

        $item_html .= '<small class="times">&times;</small> ' . esc_html($item->get_quantity());

        $refunded_qty = -1 * $order->get_qty_refunded_for_item($item_id);

        if ($refunded_qty) {
            $item_html .=  '<small class="refunded">' . esc_html($refunded_qty * -1) . '</small>';
        }
        $item_html .= '</td>';

        // Item Line Cost Column
        $item_html .= '<td class="line-cost" width="15%">';

        $item_html .=  wc_price($item->get_total(), array('currency' => $order->get_currency()));

        if ($item->get_subtotal() !== $item->get_total()) {
            /* translators: %s: discount amount */
            $item_html .=  '<span class="wc-order-item-discount">' . sprintf(esc_html__('%s discount', 'woocommerce'), wc_price(wc_format_decimal($item->get_subtotal() - $item->get_total(), ''), array('currency' => $order->get_currency()))) . '</span>';
        }

        $refunded = -1 * $order->get_total_refunded_for_item($item_id);

        if ($refunded) {
            $item_html .= '<small class="refunded">' . wc_price($refunded, array('currency' => $order->get_currency())) . '</small>';
        }

        $item_html .= '</td> </tr> </table>';

        $item_html .= '</div>'; // order-item ends

        if ($item_category == 'burger') {
            $burger_items .= $item_html;
        } else {
            $other_items .= $item_html;
        }

        $counter++;
    }

    echo $other_items;

    echo $burger_items;
    ?>
</div>
<iframe id="woo_order_pdf_frame"> </iframe>