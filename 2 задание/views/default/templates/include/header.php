<!DOCTYPE html>
<html lang="ru" class="h-100">

<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<meta name="keywords" content="<?= preg_replace('/\"([^\"]*)\"/ismU', '&laquo;$1&raquo;', $results['seo']['keywords']) ?>">
	<meta name="description" content="<?= preg_replace('/\"([^\"]*)\"/ismU', '&laquo;$1&raquo;', $results['seo']['description']) ?>">
	<meta name="author" content="DevStarter Technology">
	<meta name="csrf_token" content="<?= $_SESSION['csrf_token'] ?>">
	<title><?= preg_replace('/\"([^\"]*)\"/ismU', '&laquo;$1&raquo;', $results['seo']['title'] ?: $results['information']->title) ?></title>
	<link rel="icon" type="image/x-icon" href="/favicon.ico">

	<meta property="og:locale" content="<?= $_COOKIE['language'] ?: DEFAULT_LANG	?>" />
	<meta property="og:title" content="<?= preg_replace('/\"([^\"]*)\"/ismU', '&laquo;$1&raquo;', $results['seo']['title'] ?: $results['information']->title) ?>">
	<meta property="og:site_name" content="<?= preg_replace('/\"([^\"]*)\"/ismU', '&laquo;$1&raquo;', $results['seo']['title'] ?: $results['information']->title) ?>">
	<meta property="og:url" content="<?= SITE_URL . $_SERVER["REQUEST_URI"]; ?>">
	<meta property="og:description" content="<?= preg_replace('/\"([^\"]*)\"/ismU', '&laquo;$1&raquo;', $results['seo']['description'] ?: $results['information']->description) ?>">
	<meta property="og:type" content="website">
	<meta property="og:image" content="<?= SITE_URL . $results['template'] . $results['seo']['img'] . '?' . strtotime(date('Y-m-d H:i')) ?>" />
	<meta property="og:image:secure_url" content="<?= SITE_URL . $results['template'] . $results['seo']['img'] . '?' . strtotime(date('Y-m-d H:i')) ?>" />
	<meta property="og:image:type" content="image/jpeg" />
	<meta property="og:image:width" content="400" />
	<meta property="og:image:height" content="300" />

	<link rel="icon" type="image/x-icon" href="/img/favicons/favicon.ico" />
	<link rel="apple-touch-icon" sizes="180x180" href="/img/favicons/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/img/favicons/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="192x192" href="/img/favicons/android-chrome-192x192.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/img/favicons/favicon-16x16.png">
	<link rel="manifest" href="/img/favicons/site.webmanifest">
	<link rel="mask-icon" href="/img/favicons/safari-pinned-tab.svg" color="#00a4e6">
	<link rel="shortcut icon" href="/img/favicons/favicon.ico">
	<meta name="msapplication-TileColor" content="#00a4e6">
	<meta name="msapplication-TileImage" content="/img/favicons/mstile-144x144.png">
	<meta name="msapplication-config" content="/img/favicons/browserconfig.xml">
	<meta name="theme-color" content="#ffffff">
	<link href="<?= '/' . VIEW . DS . TEMPLATE . '/assets/css/style.min.css?v=' . strtotime(date('Y-m-d:his')) ?>" rel="stylesheet" type="text/css" />
	<script src="<?= '/' . VIEW . DS . TEMPLATE . '/assets/js/jquery.min.js' ?>"></script>
	<meta name="robots" content="<?= ($results['seo']['robots']['i'] ?: 'noindex') . ',' . ($results['seo']['robots']['f'] ?: 'nofollow') ?>">
	<link rel="canonical" href="<?= SITE_URL . $_SERVER['REQUEST_URI'] ?>" />
</head>

<body class="d-flex flex-column h-100">
	<main class="flex-shrink-0">
		<header class="header">
			<div class="collapse collapse-menu" id="collapseExample">
				<div class="navbar d-block">
					<div class="flex-wrapper mb-3">
						<a class="collapse-toggler" data-bs-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample"><? include $_SERVER['DOCUMENT_ROOT'] . "/img/icons/burger.svg";  ?></a>

						<a class="logo" href="<?= SITE_URL ?>">
							<?
							if (!empty($results['information']->img)) :
								echo '<img src="' . ($results['information']->img ?: '/img/logo.svg') . '" alt="' . ($results['information']->seo_title ?: $results['information']->title) . '" height="35">';
							else :
								echo $results['information']->title;
							endif;
							?>
						</a>
					</div>

					<div class="collapse-bottom">
						<a class="collapse-menu__item" href="/">Главная</a>
						<button class="collapse-menu__item btn-link" type="button" data-bs-toggle="modal" data-bs-target="#refundModal">Возвраты</button>
						<button class="collapse-menu__item btn-link" type="button" data-bs-toggle="modal" data-bs-target="#faqModal">FAQ</button>
					</div>
				</div>
			</div>

			<nav class="navbar">
				<a class="collapse-toggler" data-bs-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample"><? include $_SERVER['DOCUMENT_ROOT'] . "/img/icons/times.svg";  ?></a>

				<a class="logo" href="<?= SITE_URL ?>">
					<?
					if (!empty($results['information']->img)) :
						echo '<img src="' . ($results['information']->img ?: '/img/logo.svg') . '" alt="' . ($results['information']->seo_title ?: $results['information']->title) . '" height="35">';
					else :
						echo $results['information']->title;
					endif;
					?>
				</a>
			</nav>
		</header>