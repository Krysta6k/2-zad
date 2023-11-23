<? foreach (App::get_scripts() as $script) echo $script->code; ?>

<div aria-live="polite" aria-atomic="true" class="toasts-place position-fixed p-2" style="top:4rem;right: 1rem; z-index:1200">
	<script type="text/javascript">
		<? if (isset($_SESSION['notify'])) : ?>
			$(document).ready(function() {
				<? foreach ($_SESSION['notify'] as $toast) : ?>
					makeToast('.toasts-place', '<?= $toast['type'] ?>', '<?= $toast['title'] ?>', '<?= $toast['text'] ?>', 5000)
				<? endforeach;
				$_SESSION['notify'] = null; ?>
			});
		<? endif; ?>
	</script>
</div>

<? if ($_COOKIE['policy'] != 'yes') : ?>
	<div class="cookie_policy" role="alert" aria-live="assertive" aria-atomic="true">
		<div class="toast-body">
			<div class="toast-body__text">Продолжая пользоваться этим сайтом, вы соглашаетесь на использование cookie и обработку данных в соответствии с Политикой сайта в области обработки и защиты персональных данных.</div>
			<button type="button" class="btn btn-blue close-toast-btn" data-bs-dismiss="toast">Принять</button>
		</div>
	</div>

	<script type="text/javascript">
		$(document).ready(function() {
			$(".cookie_policy .close-toast-btn").on("click", function (e) {
				$('.cookie_policy').fadeOut();

				$.post("/app/cookie", { title: "policy", val: 'yes' }, function (data) {
					$('.cookie_policy .close-toast-btn, .cookie_policy').fadeOut()
				});
			})
		});
	</script>
<? endif; ?>
</body>
</html>