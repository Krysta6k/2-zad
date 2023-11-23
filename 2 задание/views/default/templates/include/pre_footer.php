</main>

<!-- Modals -->
<?
  require_once $results['templates'] . "/include/faq-modal.php";
  require_once $results['templates'] . "/include/refund-modal.php";
  require_once $results['templates'] . "/include/order-modal.php";
  /*require_once $results['templates'] . "/include/video-modal.php";*/ 
  require_once $results['templates'] . "/include/detail-modal.php";
?>

<footer class="footer mt-auto">
  <div class="container-fluid">
    <div class="footer-top">
      <a class="logo" href="<?= SITE_URL ?>"><img class="lazy" data-src="/img/logo.png" alt="<?= $results['information']->title ?>" /></a>

      <a class="footer-top__item" href="tel:+79617231717"><div class="icon"><? require_once ROOT . "/img/icons/cellphone.svg";  ?></div>
        <div class="text">+7 961 723 17 17</div>
      </a>

      <a class="footer-top__item" href="tel:+73843761717">
        <div class="icon"><? require_once ROOT . "/img/icons/phone.svg";  ?></div>
        <div class="text">+7 (3843) 761 717</div>
      </a>

      <a class="footer-top__item" href="<?= 'mailto:' . $results['information']->email ?>">
        <div class="icon"><? require_once ROOT . "/img/icons/mail.svg";  ?></div>
        <div class="text"><?= $results['information']->email ?></div>
      </a>

      <div class="footer-top__item social-list">
        <a class="social-list__item" href="https://vk.com/kino_im_kino" target="_blank"><? require_once ROOT . "/img/icons/socials/vk.svg";  ?></a>
      </div>
    </div>

    <div class="footer-bottom">
      <div class="info">
        <div class="copyright">&copy; Кино им. Кино | <?= date('Y') ?></div>
        
        <a class="policy" href="#">Политика конфиденциальности</a>
      </div>

      <a href="//devstarter.ru?utm_source=<?= $_SERVER['SERVER_NAME'] ?>" target="_blank" title="Разработка и поддержка проекта" class="developer">
        <img data-src="/img/badge.svg" class="lazy">
      </a>
    </div>
  </div>
</footer>

<script type="text/javascript" defer src="<?= '/' . VIEW . DS . TEMPLATE . '/assets/js/all.js' ?>"></script>