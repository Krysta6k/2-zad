<? require_once($results['templates'] . "/include/header.php"); ?>

<div class="section__wrapper">
  <div class="row justify-content-center">
    <div class="col-lg-6 col-xl-4 mb-4">
      <div class="error-text-box">
        <svg viewBox="0 0 600 200">
          <symbol id="s-text">
            <text text-anchor="middle" x="50%" y="50%" dy=".35em">404!</text>
          </symbol>
          <use class="text" xlink:href="#s-text"></use>
          <use class="text" xlink:href="#s-text"></use>
          <use class="text" xlink:href="#s-text"></use>
          <use class="text" xlink:href="#s-text"></use>
          <use class="text" xlink:href="#s-text"></use>
        </svg>
      </div>
      <div class="text-center">
        <h3 class="mt-0 mb-2">Упс! Страница не найдена</h3>
        <p class="text-muted mb-3">
          Похоже, вы ошиблись. Не волнуйтесь ...
          Это случается с лучшими из нас. Вы можете проверить свое интернет-соединение.
        </p>
      </div>
    </div>
  </div>
</div>

<?
require_once($results['templates'] . '/include/pre_footer.php');
require_once($results['templates'] . '/include/footer.php');
?>