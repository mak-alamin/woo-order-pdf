<?php
/*
Plugin Name: Woo Order PDF
Description: Adds a Generate PDF button to print orders in the WooCommerce admin.
Version: 1.1.1
Author: Mak Alamin
*/

if (!defined("ABSPATH")) {
    wp_die("Access denied!");
}

// Enqueue necessary scripts and styles in admin
add_action('admin_enqueue_scripts', 'enqueue_pdf_button_scripts');
function enqueue_pdf_button_scripts($hook)
{
    wp_enqueue_style('woo_op_admin_main', plugins_url('css/admin-main.css',  __FILE__) , null, time(), false);

    wp_enqueue_script('woo_op_js', plugin_dir_url(__FILE__) . 'js/pdf-button-script.js', array('jquery'), time(), true);

    wp_localize_script('woo_op_js', 'wooOrderPdfData', array(
        'ajaxurl' => admin_url('admin-ajax.php')
    ));
}

// Add custom metabox to display order items on WooCommerce orders page
add_action('add_meta_boxes', 'woo_op_add_order_items_metabox');
function woo_op_add_order_items_metabox()
{
    add_meta_box(
        'order_print_button_metabox',
        'Order PDF',
        'woo_op_order_print_metabox_content',
        'shop_order',
        'side',
        'high'
    );

    add_meta_box(
        'order_items_metabox',
        'Order Items',
        'woo_op_order_items_metabox_content',
        'shop_order',
        'normal',
        'low'
    );
}

function woo_op_order_print_metabox_content($post)
{
    echo '<p><button type="button" id="generate-pdf-button" class="button generate-pdf-button" data-order_id="' . $post->ID . '">Bon drucken</button></p>';
}

function woo_op_order_composite_meta($order_item_id, $order_item)
{
    if (($ids = $order_item->get_meta('wooco_ids')) && (is_admin() || (WPCleverWooco::get_setting('hide_component', 'no') === 'yes_text') || (WPCleverWooco::get_setting('hide_component', 'no') === 'yes_list'))) {
        if ($items = WPCleverWooco::get_items($ids)) {

            $product_id = $order_item->get_product_id();

            $components    = get_post_meta($product_id, 'wooco_components', true);

            if (WPCleverWooco::get_setting('hide_component', 'no') === 'yes_list') {
                $items_str = [];

                foreach ($items as $item) {
                    if ((WPCleverWooco::get_setting('hide_component_name', 'yes') === 'no') && !empty($item['component'])) {
                        $items_str[] = apply_filters('wooco_order_component_product_name', '<li>' . $item['component'] . ': ' . $item['qty'] . ' × ' . get_the_title($item['id']) . '</li>', $item);
                    } else {
                        $items_str[] = apply_filters('wooco_order_component_product_name', '<li>' . $item['qty'] . ' × ' . get_the_title($item['id']) . '</li>', $item);
                    }
                }

                $items_str = apply_filters('wooco_order_component_product_names', '<ul>' . implode('', $items_str) . '</ul>', $items);
            } else {
                $items_str = [];

                foreach ($items as $item) {
                    $component_name = isset($components[$item['key']]) ? $components[$item['key']]['name'] : '';

                    if ((WPCleverWooco::get_setting('hide_component_name', 'yes') === 'no') && !empty($item['component'])) {

                        $items_str[] = '<strong>' . $component_name . ': </strong>' . $item['qty'] . ' × ' . get_the_title($item['id']);
                    } else {

                        $items_str[] = '<strong>' . $component_name . ': </strong>' . apply_filters('wooco_order_component_product_name', $item['qty'] . ' × ' . get_the_title($item['id']), $item);
                    }
                }

                $items_str =  implode('<br/> ', $items_str);
            }

            return apply_filters('wooco_before_order_itemmeta_composite', '<div class="wooco-itemmeta-composite">' . sprintf(WPCleverWooco::localization('cart_components_s', esc_html__('Components: %s', 'wpc-composite-products')), $items_str) . '</div>', $order_item_id, $order_item);
        }
    }

    if (is_admin() && ($parent_id = $order_item->get_meta('wooco_parent_id'))) {
        if (($component = $order_item->get_meta('wooco_component')) && !empty($component)) {
            return apply_filters('wooco_before_order_itemmeta_component', '<div class="wooco-itemmeta-component">' . sprintf(WPCleverWooco::localization('cart_composite_s', esc_html__('Composite: %s', 'wpc-composite-products')), get_the_title($parent_id) . apply_filters('wooco_name_separator', ' &rarr; ') . $component) . '</div>', $order_item_id, $order_item);
        } else {
            return apply_filters('wooco_before_order_itemmeta_component', '<div class="wooco-itemmeta-component">' . sprintf(WPCleverWooco::localization('cart_composite_s', esc_html__('Composite: %s', 'wpc-composite-products')), get_the_title($parent_id)) . '</div>', $order_item_id, $order_item);
        }
    }
}

function woo_op_order_items_metabox_content($post)
{
    // Display order items
    echo woo_op_generate_order_items_for_pdf($post->ID);
}

function woo_op_generate_order_items_for_pdf($order_id)
{
    $order = wc_get_order(intval($order_id));

    ob_start();

    if (!empty($order)) {
        require_once __DIR__ . '/order_pdf_html.php';
        echo '<iframe id="woo_order_pdf_frame"> </iframe>';
    } else {
        echo 'Order not found.';
    }

    return ob_get_clean();
}

// Add Print PDF button to Orders action column
add_filter('woocommerce_admin_order_actions', 'woo_op_add_print_pdf_action_button', 100, 2);
function woo_op_add_print_pdf_action_button($actions, $order)
{
    // Set the action button
    $actions['print_pdf'] = array(
        'url'       =>  admin_url('admin-ajax.php?order_id=' . $order->get_id()),
        'name'      => __('Drucken', 'woocommerce'),
        'action'    => 'print_pdf',
    
    );

    return $actions;
}

// Add Iframe in WooCommerce orders page
add_action('admin_footer', 'woo_op_add_iframe_to_order_table');
function woo_op_add_iframe_to_order_table()
{
    global $pagenow;

    // Check if it's the WooCommerce orders page
    if ($pagenow === 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] === 'shop_order') {
    ?>
        <div id="order_items_print_pdf"></div>
<?php
    }
}

add_action('wp_ajax_woo_op_get_print_html_for_action_print', 'woo_op_get_print_html_for_action_print');

function woo_op_get_print_html_for_action_print()
{
    $order_id = isset($_REQUEST['order_id']) ? $_REQUEST['order_id'] : 0;

    $html = woo_op_generate_order_items_for_pdf($order_id);

    wp_send_json($html);
}
