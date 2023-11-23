<? require_once($results['templates'] . "/include/header.php"); ?>

<section class="breadcrumbs py-2 d-none d-lg-block">
	<div class="container-fluid">
		<?
		Breadcrumb::add(SITE_URL, ucfirst(Lang::get('titles', 'Home')));
		Breadcrumb::add(SITE_URL . DS .  substr($GLOBALS['router']->getMethodPrefix(), 0, -1), Lang::get('titles', 'Cabinet'));
		Breadcrumb::add(SITE_URL . DS .  substr($GLOBALS['router']->getMethodPrefix(), 0, -1) . DS . $GLOBALS['router']->getController(), $results['controller_info']->title ?: ucfirst(str_replace('_', ' ', $GLOBALS['router']->getController())));
		echo Breadcrumb::out();
		?>
	</div>
</section>

<section class="py-4">
	<div class="container-fluid">
		<div class="mb-4 d-flex align-items-center">
			<h1 class="text-body fw-bold h2 mb-0"><?= $results['controller_info']->title ?: ucfirst(str_replace('_', ' ', $GLOBALS['router']->getController())) ?> <span class="badge ms-3 text-bg-warning text-light"><?= $results['total_users'] ?><span></h1>
			<a href="#" role="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvas" data-prefix="<?= $GLOBALS['router']->getMethodPrefix() ? '/' . substr($GLOBALS['router']->getMethodPrefix(), 0, -1) . '/' : '/' ?>" data-controller="<?= $GLOBALS['router']->getController() ?>" data-action="add" data-header="<?= Lang::get('headers', 'Add user') ?>" data-params="<?= htmlspecialchars(json_encode(['redirect' => $_SERVER['REQUEST_URI']])) ?>" class="btn btn-danger lh-sm ms-auto shadow-none bg-gradient rounded-4 rounded-md-5 py-2 offcanvas_btn"><i class="fa-solid fa-plus"></i> <span class="d-none d-md-inline-block ms-2"><?= Lang::get('btns', 'Add') ?></span></a>
		</div>

		<? require_once($results['templates'] . "/include/btns_block.php"); ?>

		<div class="bg-light p-3 mb-4 pt-0 rounded">
			<div class="table-responsive-comment">
				<table class="table table-hover border-light mb-0 w-100 mw-100 users">
					<thead>
						<tr>
							<th scope="col" width="60">#</th>
							<th scope="col" class="sorting"><?= Lang::get('titles', 'Title') ?></th>
							<th scope="col" class="sorting" width="170"><?= Lang::get('titles', 'Phone') ?></th>
							<th scope="col" class="d-none d-md-table-cell sorting" width="170"><?= Lang::get('titles', 'Birthday') ?></th>
							<th scope="col" class="d-none d-md-table-cell sorting" width="250"><?= Lang::get('titles', 'Usergroup') ?></th>
							<? if ($results['session_user']->usergroup <= 2) : ?>
							<th scope="col" width="50">&nbsp;</th>
							<? endif; ?>
						</tr>
					</thead>
					<tbody>
						<? foreach ($results['users'] as $key => $item) : ?>
							<tr>
								<td>
									<span class="indicator bg-<?= ($item->status == 1) ? 'success' : 'danger' ?>"></span>
									<?= str_pad($item->id, 4, '0', STR_PAD_LEFT) ?>
								</td>
								<td>
									<a href="/cabinet/user/profile/<?= base64_decode($item->username) ?>" target="_blank" class="text-decoration-none text-body d-flex align-items-center <?= $item->online == 0 ? 'user_offline' : 'user_online' ?>" rel="tooltip" title="<?= $item->surname . ' ' . $item->name ?>">
										<img src="/img/loader.svg" data-src="<?= json_decode($item->img)->thumb ?: '/img/user/default.png' ?>" alt="<?= $item->surname . ' ' . $item->name ?>" class="rounded me-2 lazy bg-light" width="34" height="34" style="object-fit: cover" />
										<span class="d-inline-block text-truncate" style="max-width: 250px;"><?= ($item) ? $item->surname . '&nbsp;' . $item->name : 'Не указан' ?></span>
									</a>
								</td>
								<td><a href="<?= 'tel:' . preg_replace('/[\-\()\ ]/', '', $item->phone) ?>" class="text-black text-decoration-none me-auto sp-line-1"><?= str_replace('+7', '8', App::phone_format($item->phone)) ?></a></td>
								<td class="d-none d-md-table-cell"><?= $item->birthday ? App::date_format($item->birthday) : '' ?></td>
								<td class="d-none d-md-table-cell"><? foreach ($results['usergroups'] as $usergroup) if ($usergroup->id == $item->usergroup) echo $usergroup->title ?></td>
								<td>
									<div class="dropdown text-center">
										<a class="btn border-0 p-0" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa-solid fa-bars"></i></a>

										<ul class="dropdown-menu shadow border-0 rounded-0">
											<li><a class="dropdown-item" href="<?= '/cabinet/user/profile/' . base64_decode($item->username) ?>"><?= Lang::get('btns', 'View') ?></a></li>

											<? if ($results['session_user']->usergroup <= 2) : ?>
												<li><a class="dropdown-item offcanvas_btn" href="#" role="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvas" data-prefix="<?= $GLOBALS['router']->getMethodPrefix() ? '/' . substr($GLOBALS['router']->getMethodPrefix(), 0, -1) . '/' : '/' ?>" data-controller="<?= $GLOBALS['router']->getController() ?>" data-action="edit" data-header="<?= Lang::get('headers', 'Edit ' . $GLOBALS['router']->getController()) ?>" data-params="<?= htmlspecialchars(json_encode(['id' => (int) $item->id, 'redirect' => $_SERVER['REQUEST_URI']])) ?>"><?= Lang::get('btns', 'Edit') ?></a></li>
												<li><a class="dropdown-item delete" href="#" data-controller="<?= $GLOBALS['router']->getController() ?>" data-id="<?= $item->id ?>" data-message="<?= Lang::get('messages', 'Are you sure') . '?' ?>" data-redirect="<?= base64_encode($_SERVER['REQUEST_URI']) ?>"><?= Lang::get('btns', 'Delete') ?></a></li>
											<? endif; ?>
										</ul>
									</div>
								</td>
							</tr>
						<? endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>

		<?
			$pagination = new Pagination($results['total_users'], $_GET['page'] ?: 0, '?page=');
			echo (string) $pagination->get();
		?>
	</div>
</section>

<? require_once($results['templates'] . '/include/pre_footer.php'); ?>
<? require_once($results['templates'] . '/include/footer.php'); ?>