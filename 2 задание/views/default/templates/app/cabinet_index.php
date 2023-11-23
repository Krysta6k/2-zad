<? require_once($results['templates'] . "/include/header.php"); ?>

<section class="setup pt-3 pb-4">
  <div class="container">
    <h1 class="text-muted mb-3 fw-bold">Application</h1>

    <div class="row">
      <div class="col-6 col-md-3">
        <div class="item bg-white shadow-sm rounded p-3 mb-3 d-flex justify-content-center align-items-center position-relative" style="aspect-ratio:16/9">
          <a href="/cabinet/app/config" class="text-black stretched-link">Config</a>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="item bg-white shadow-sm rounded p-3 mb-3 d-flex justify-content-center align-items-center position-relative" style="aspect-ratio:16/9">
          <a href="/cabinet/app/db" class="text-black stretched-link">Database</a>
        </div>
      </div>
      <!-- <div class="col-6 col-md-3">
        <div class="item bg-white shadow-sm rounded p-3 mb-3 d-flex justify-content-center align-items-center position-relative" style="aspect-ratio:16/9">
          <a href="#" class="text-black stretched-link">Extensions</a>
        </div>
      </div> -->
    </div>
  </div>
</section>

<? require_once($results['templates'] . "/include/pre_footer.php"); ?>
<? require_once($results['templates'] . "/include/footer.php"); ?>