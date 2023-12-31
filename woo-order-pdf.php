<?php
/*
Plugin Name: Woo Order PDF
Description: Adds a Generate PDF button in WooCommerce order details page in the admin.
Version: 1.0
Author: Mak Alamin
*/

if (!defined("ABSPATH")) {
    wp_die("Access denied!");
}

define("WOO_ORDER_PDF_JS", plugin_dir_url(__FILE__) . 'js/pdf-button-script.js');

// Enqueue necessary scripts and styles in admin
add_action('admin_enqueue_scripts', 'enqueue_pdf_button_scripts');
function enqueue_pdf_button_scripts($hook)
{
?>
    <style>
        #woo_order_pdf_frame {
            width: 100%;
            height: 1px;
            visibility: hidden;
        }
    </style>
<?php
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
    echo '<p><button type="button" id="generate-pdf-button" class="button generate-pdf-button" data-order_id="' . $post->ID . '">Print PDF</button></p>';

    echo '<script src="' . WOO_ORDER_PDF_JS . '"></script>';
}

function woo_op_order_items_metabox_content($post)
{
    // Get the order object
    $order = wc_get_order($post->ID);

    // Display order items
    if ($order) {
        require __DIR__ . '/order_pdf_html.php';

        echo '<iframe id="woo_order_pdf_frame"> </iframe>';
    } else {
        echo 'Order not found.';
    }
}
