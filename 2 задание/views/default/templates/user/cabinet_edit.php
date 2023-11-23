<form action="/user/edit" method="post" enctype="multipart/form-data">
  <input type="hidden" name="user[id]" value="<?= $results['user']->id ?>">
  <input type="hidden" name="redirect" value="<?= base64_encode($_GET['redirect']) ?>">

  <p class="h6"><?= Lang::get('titles', 'Image') ?></p>
	<div class="form-group mb-3">
    <div class="img preview embed-responsive mb-3">
      <label for="upload" rel="tooltip" title="Выбрать изображение" 
        class="holder bg-light border-0 rounded text-center d-flex h-100 w-100 justify-content-center align-items-center" 
        style="cursor: pointer; aspect-ratio: 1/1; background-repeat: no-repeat; background-size: cover; background-position: center; background-image: url(<?= json_decode($results['user']->img)->fullsize ?>)"
      >
        <h3><i class="fa fa-upload" aria-hidden="true"></i></h3>
      </label>
      <input id="upload" class="file-upload__input d-none" type="file" name="image" onchange="readURL('previewImg')" accept=".jpg, .jpeg, .png, .svg, .webp, .ico">
    </div>

    <input type="hidden" name="user[img]" class="form-control mb-3" autocomplete="off" value="<?= htmlspecialchars($results['user']->img) ?>">
  </div>

  <p class="h6"><?= Lang::get('titles', 'General') ?></p>
  <div class="form-group mb-3">
    <label class="text-muted mb-2"><?= Lang::get('titles', 'Name') ?></label>
    <input type="text" name="user[name]" class="form-control" autocomplete="off" value="<?= $results['user']->name ?>" required>
  </div>

  <div class="form-group mb-3">
    <label class="text-muted mb-2"><?= Lang::get('titles', 'Surname') ?></label>
    <input type="text" name="user[surname]" class="name form-control" autocomplete="off" value="<?= $results['user']->surname ?>" required>
  </div>

  <div class="form-group mb-3">
    <label class="text-muted mb-2"><?= Lang::get('titles', 'Patronymic') ?></label>
    <input type="text" name="user[patronymic]" class="form-control" autocomplete="off" value="<?= $results['user']->patronymic ?>">
  </div>

  <div class="form-group mb-3">
    <label class="text-muted mb-2"><?= Lang::get('titles', 'Sex') ?></label>
    <select name="user[sex]" class="form-select ">
      <option value="0" <?= ($results['user']->sex == 0) ? 'selected' : '' ?>><?= Lang::get('titles', 'Not specified') ?></option>
      <option value="1" <?= ($results['user']->sex == 1) ? 'selected' : '' ?>><?= Lang::get('titles', 'Male') ?></option>
      <option value="2" <?= ($results['user']->sex == 2) ? 'selected' : '' ?>><?= Lang::get('titles', 'Female') ?></option>
    </select>
  </div>

  <div class="form-group mb-3">
    <label class="text-muted mb-2"><?= Lang::get('titles', 'Login') ?></label>
    <input type="text" name="user[username]" class="alias form-control" autocomplete="off" value="<?= base64_decode($results['user']->username) ?>" required>
  </div>

  <div class="form-group mb-3">
    <label class="text-muted mb-2"><?= Lang::get('titles', 'Phone') ?></label>
    <input type="phone" name="user[phone]" class="form-control" autocomplete="off" value="<?= $results['user']->phone ?>" required>
  </div>

  <div class="form-group mb-3">
    <label class="text-muted mb-2"><?= Lang::get('titles', 'Email') ?></label>
    <input type="email" name="user[email]" class="form-control" autocomplete="off" value="<?= $results['user']->email ?>" required>
  </div>

  <div class="form-group mb-3">
    <label class="text-muted mb-2"><?= Lang::get('titles', 'Birthday') ?></label>
    <input type="date" name="user[birthday]" class="form-control" value="<?= $results['user']->birthday ?>" autocomplete="off">
  </div>

  <div class="form-group mb-3">
    <label class="text-muted mb-2"><?= Lang::get('titles', 'About') ?></label>
    <textarea name="user[about]" class="form-control" autocomplete="off"><?= $results['user']->about ?></textarea>
  </div>

	<div class="form-group mb-3">
		<label class="text-muted mb-2"><?= Lang::get('titles', 'Usergroup') ?></label>
		<select name="user[usergroup]" class="form-control mb-3" required>
			<? foreach ($results['users_groups'] as $usergroup) : ?>
				<option value="<?= $usergroup->id ?>" <?= ($usergroup->id == $results['user']->usergroup) ? 'selected' : '' ?>><?= $usergroup->title ?></option>
			<? endforeach; ?>
		</select>
	</div>

	<div class="form-group">
		<label class="text-muted mb-2"><?= Lang::get('titles', 'Registration date') ?></label>
		<input type="datetime-local" name="user[registration_date]" class="form-control mb-3" min="<?= str_replace(' ', 'T', date($results['user']->registration_date)) ?>" value="<?= str_replace(' ', 'T', date($results['user']->registration_date)) ?>" autocomplete="off" required readonly>
	</div>

	<div class="form-group">
		<label class="text-muted mb-2"><?= Lang::get('titles', 'Token') ?></label>
		<input type="text" name="user[code]" class="form-control mb-3" autocomplete="off" value="<?= $results['user']->code ?>" required readonly>
	</div>

	<div class="form-group mb-3">
		<label class="text-muted mb-2"><?= Lang::get('titles', 'Status') ?></label>

		<div class="form-check">
			<input class="form-check-input" type="radio" name="user[status]" id="statusRadio1" value="1" <?= ($results['user']->status == 1) ? 'checked' : '' ?>>
			<label class="form-check-label" for="statusRadio1"><?= $results['lang']['statuses']['active'] ?: 'Active' ?></label>
		</div>
		<div class="form-check">
			<input class="form-check-input" type="radio" name="user[status]" id="statusRadio2" value="0" <?= ($results['user']->status == 0) ? 'checked' : '' ?>>
			<label class="form-check-label" for="statusRadio2"><?= $results['lang']['statuses']['deactive'] ?: 'Deactive' ?></label>
		</div>
	</div>

  <div class="form-group mb-3">
    <label class="text-muted mb-2"><?= Lang::get('titles', 'Password') ?></label>
    <div class="input-group mb-2">
      <input type="password" name="user[password]" class="password form-control">
      <button class="btn btn-outline-secondary" id="password_show" type="button"><?= Lang::get('titles', 'Show') ?></button>
    </div>
    <a href="#" id="password_generate"><?= Lang::get('titles', 'Generate') ?></a>
  </div>

  <button type="submit" name="edit" class="btn btn-danger lh-sm py-2 px-5 rounded-5 my-4 w-100"><?= Lang::get('btns', 'Save') ?></button>
</form>

<script type="text/javascript">
  $("input[type='phone']").mask("+7 (999) 999 9999");
  find_required();

  $('.select2').select2({theme: 'bootstrap-5', dropdownParent: "#offcanvas-body form"});
</script>