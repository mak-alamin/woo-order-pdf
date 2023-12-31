// const { jsPDF } = window.jspdf;
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

  document.getElementById('woo_order_pdf_frame').style.visibility = 'hidden';
}

var pdfButton = document.getElementById("generate-pdf-button");

pdfButton.addEventListener("click", function (e) {
  e.preventDefault();

  var order_id = e.target.dataset.order_id;

  printDiv("woo_order_items_html", order_id);

  // printDiv("order_line_items");
});
