<? require_once($results['templates'] . "/include/header.php"); ?>

<section class="auth h-100">
  <div class="container h-100">
    <div class="auth__column form-column d-flex justify-content-center flex-columns h-100 align-items-center">
      <div class="auth-form w-100" style="max-width:520px;text-align:center">
        <h3 class="auth-form__title mb-4">Восстановление пароля</h3>
        <p class="auth-form__text">Введите свой номер телефона и мы отправим вам временный пароль для восстановлению доступа.</p>

        <form class="form" action="<?= $results['form']['action'] ?>" method="post">
          <input type="hidden" name="redirect" value="<?= base64_encode('/cabinet') ?>">

          <div class="auth-form">
            <input class="form__field form-control mb-3" name="user[phone]" type="phone" required id="phone" placeholder="Ваш номер телефона">

            <button class="btn btn-primary mb-3 auth-form__button login-btn" type="submit" name="<?= $results['form']['btn'] ?>">Сброс пароля</button>

            <div class="login-with">
              <div class="login-with__title">Войти с помощью</div>
              <div class="login-with__buttons-row mb-3">
                <a href="/user/vk_login" class="btn btn-light btn-sm login-with__item"><img src="/img/socials/vk.svg" alt=""></a>
                <a href="/user/yandex_login" class="btn btn-light btn-sm login-with__item"><img src="/img/socials/yandex.svg" alt=""></a>
                <?/* <a href="/user/esia_login" class="btn btn-light btn-sm login-with__item"><img src="/img/socials/gos-uslugi.svg" alt=""></a> */?>
              </div>
            </div>

            <div class="auth-btn-wrapper">
              <a href="/user/login" class="create-account-btn text-muted d-block">Я вспомнил пароль</a>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>

<? require_once($results['templates'] . "/include/pre_footer.php"); ?>
<? require_once($results['templates'] . "/include/footer.php"); ?>