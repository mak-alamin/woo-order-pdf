<style>
    .first_pdf_page h2 {
        color: #F61E61;
    }

    .first_pdf_page .header_speisen {
        display: none
    }

    /* style="visibility: hidden;" */
    #woo_order_items_html .pdf_heading,
    .pdf_heading {
        font-size: 22px;
        font-weight: 600;
        text-align: center;
        padding: 50px 0 20px;
    }

    #woo_order_items_html .order-item,
    .order-item {
        padding: 0px;
        padding-top: 50px;
    }

    #woo_order_items_html .order-item.other-item,
    .order-item.other-item {
        padding-top: 20px;
    }

    #woo_order_items_html .order-item table,
    .order-item table,
    .items-table {
        padding-top: 0px;
    }

    .header-table {
        padding-top: 30px;
    }

    #woo_order_items_html .item-heading th,
    .item-heading th {
        border-bottom: 1px solid #eaeaea;
        padding-bottom: 5px;
    }

    #woo_order_items_html .item-info>td,
    .item-info>td {
        padding-top: 10px;
    }

    #woo_order_items_html table th,
    #woo_order_items_html .order-item th,
    .order-item th,
    .items-table th,
    .header-table th {
        text-align: left;
    }

    #woo_order_items_html .order-item h2,
    .order-item h2 {
        padding: 0;
        font-size: 20px;
        font-weight: 500;
    }

    #woo_order_items_html .display_meta p,
    .display_meta p {
        margin: 0;
    }

    #woo_order_items_html .display_meta th,
    .display_meta th,
    #woo_order_items_html .display_meta td,
    .display_meta td {
        color: #888897;
    }

    #woo_order_pdf_frame {
        width: 100%;
        height: 1px;
        visibility: hidden !important;
    }

    iframe#woo_order_pdf_frame {
        border: 0 !important;
    }

    @media print {
        .print-page-break {
            page-break-before: always;
        }

        @page {
            size: auto;
            margin: 0mm;
        }

        body {
            margin: 0 1.6cm 1.6cm 1.6cm;
        }
    }
</style>