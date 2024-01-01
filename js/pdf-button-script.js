function printDiv(divId, orderId = 0) {
  var originalTitle = document.title;
  
  document.title = "Rechnung für Bestellung#" + orderId;

  document.getElementById("woo_order_pdf_frame").style.visibility = "visible";

  document.getElementById(
    "woo_order_pdf_frame"
  ).contentWindow.document.body.innerHTML =
    document.getElementById(divId).innerHTML;

  var wspFrame = document.getElementById("woo_order_pdf_frame").contentWindow;

  wspFrame.focus();
  wspFrame.print();
  //   window.print();

  document.title = originalTitle;

  document.getElementById("woo_order_pdf_frame").style.visibility = "hidden";
}

var wooOrderPrintBtn = document.getElementById("generate-pdf-button");

if (wooOrderPrintBtn) {
  wooOrderPrintBtn.addEventListener("click", function (e) {
    e.preventDefault();

    var order_id = e.target.dataset.order_id;

    printDiv("woo_order_items_html", order_id);

    // printDiv("order_line_items");
  });
}

jQuery(document).on(
  "click",
  ".wc-action-button-print_pdf.print_pdf",
  function (e) {
    e.preventDefault();

    const urlParams = new URLSearchParams(new URL(e.target).search);
    const order_id = urlParams.get("order_id");

    jQuery.ajax({
      method: "GET",
      url: wooOrderPdfData.ajaxurl,
      data: {
        action: "woo_op_get_print_html_for_action_print",
        order_id: order_id,
      },
      success: function (res) {
        console.log(res);
        if (res) {
          jQuery("#order_items_print_pdf").html(res);

          printDiv("woo_order_items_html", order_id);
        }
      },
      error: function (err) {
        console.log(err);
      },
    });
  }
);
