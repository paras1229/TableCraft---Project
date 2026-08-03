<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_login();
$pageTitle = 'Export Guide | TableCraft';
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="row justify-content-center">
  <div class="col-lg-9">
    <div class="card">
      <div class="card-body p-4 p-lg-5">
        <h2 class="fw-bold mb-3">Export Guide</h2>
        <p class="small-muted">This project supports three download types from the table view:</p>
        <div class="row g-3">
          <div class="col-md-4"><div class="p-3 border rounded-4 h-100"><div class="fw-semibold mb-2">PDF</div><div class="small-muted">Uses html2canvas + jsPDF in the browser to create a printable PDF file.</div></div></div>
          <div class="col-md-4"><div class="p-3 border rounded-4 h-100"><div class="fw-semibold mb-2">PNG</div><div class="small-muted">Exports the rendered table as a high-resolution image.</div></div></div>
          <div class="col-md-4"><div class="p-3 border rounded-4 h-100"><div class="fw-semibold mb-2">DOC</div><div class="small-muted">Downloads the table as a Word-compatible document.</div></div></div>
        </div>
        <hr class="my-4">
        <ol class="small-muted mb-0">
          <li>Open a saved table from dashboard.</li>
          <li>Click PDF, PNG or DOC buttons.</li>
          <li>Save the downloaded file in your project demo folder.</li>
        </ol>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
