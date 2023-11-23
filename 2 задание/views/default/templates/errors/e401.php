<? require_once($results['templates'] . "/include/header.php"); ?>

<div class="section__wrapper">
  <div class="row justify-content-center">
    <div class="col-lg-6 col-xl-4 mb-4">
      <div class="error-text-box">
        <svg viewBox="0 0 600 200">
          <symbol id="s-text">
            <text text-anchor="middle" x="50%" y="50%" dy=".35em">401!</text>
          </symbol>
          <use class="text" xlink:href="#s-text"></use>
          <use class="text" xlink:href="#s-text"></use>
          <use class="text" xlink:href="#s-text"></use>
          <use class="text" xlink:href="#s-text"></use>
          <use class="text" xlink:href="#s-text"></use>
        </svg>
      </div>
      <div class="text-center">
        <h3 class="mt-0 mb-2">Упс! У вас нет разрешения на просмотр данного файла или ресурса.</h3>
        <p class="text-muted mb-3">
          Похоже, вы свернули не туда. Не волнуйтесь... это случается с лучшими из нас.
        </p>
      </div>
    </div>
  </div>
</div>

<?
require_once($results['templates'] . '/include/pre_footer.php');
require_once($results['templates'] . '/include/footer.php');
?>