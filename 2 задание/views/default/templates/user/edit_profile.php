<form action="/user/edit" method="post" enctype="multipart/form-data">
  <input type="hidden" name="user[id]" value="<?= $results['session_user']->id ?>">
  <input type="hidden" name="redirect" value="<?= base64_encode($_GET['redirect']) ?>">

  <p class="h6"><?= Lang::get('titles', 'Image') ?></p>
  <div class="form-group mb-3">
    <div class="img preview embed-responsive mb-3">
      <label for="upload" rel="tooltip" title="Выбрать изображение" class="holder bg-light border-0 rounded text-center d-flex h-100 w-100 justify-content-center align-items-center" style="cursor: pointer; aspect-ratio: 1/1; background-repeat: no-repeat; background-size: cover; background-image: url(<?= json_decode($results['session_user']->img)->fullsize ?>)">
        <h3><i class="fa fa-upload" aria-hidden="true"></i></h3>
      </label>
      <input id="upload" class="file-upload__input d-none" type="file" name="image" onchange="readURL('previewImg')" accept=".jpg, .jpeg, .png, .svg, .webp, .ico">
    </div>

    <input type="hidden" name="user[img]" class="form-control mb-3" autocomplete="off" value="<?= htmlspecialchars($results['session_user']->img) ?>">
  </div>

  <p class="h6"><?= Lang::get('titles', 'General') ?></p>
  <div class="form-group mb-3">
    <label class="text-muted mb-2"><?= Lang::get('titles', 'Name') ?></label>
    <input type="text" name="user[name]" class="form-control" autocomplete="off" value="<?= $results['session_user']->name ?>" required>
  </div>

  <div class="form-group mb-3">
    <label class="text-muted mb-2"><?= Lang::get('titles', 'Surname') ?></label>
    <input type="text" name="user[surname]" class="name form-control" autocomplete="off" value="<?= $results['session_user']->surname ?>" required>
  </div>

  <div class="form-group mb-3">
    <label class="text-muted mb-2"><?= Lang::get('titles', 'Patronymic') ?></label>
    <input type="text" name="user[patronymic]" class="form-control" autocomplete="off" value="<?= $results['session_user']->patronymic ?>">
  </div>

  <div class="form-group mb-3">
    <label class="text-muted mb-2"><?= Lang::get('titles', 'Sex') ?></label>
    <select name="user[sex]" class="form-select ">
      <option value="0" <?= ($results['session_user']->sex == 0) ? 'selected' : '' ?>><?= Lang::get('titles', 'Not specified') ?></option>
      <option value="1" <?= ($results['session_user']->sex == 1) ? 'selected' : '' ?>><?= Lang::get('titles', 'Male') ?></option>
      <option value="2" <?= ($results['session_user']->sex == 2) ? 'selected' : '' ?>><?= Lang::get('titles', 'Female') ?></option>
    </select>
  </div>

  <div class="form-group mb-3">
    <label class="text-muted mb-2"><?= Lang::get('titles', 'Login') ?></label>
    <input type="text" name="user[username]" class="alias form-control" autocomplete="off" value="<?= base64_decode($results['session_user']->username) ?>" required>
  </div>

  <div class="form-group mb-3">
    <label class="text-muted mb-2"><?= Lang::get('titles', 'Phone') ?></label>
    <input type="phone" name="user[phone]" class="form-control" autocomplete="off" value="<?= $results['session_user']->phone ?>" required>
  </div>

  <div class="form-group mb-3">
    <label class="text-muted mb-2"><?= Lang::get('titles', 'Email') ?></label>
    <input type="email" name="user[email]" class="form-control" autocomplete="off" value="<?= $results['session_user']->email ?>" required>
  </div>

  <div class="form-group mb-3">
    <label class="text-muted mb-2"><?= Lang::get('titles', 'Birthday') ?></label>
    <input type="date" name="user[birthday]" class="form-control" value="<?= $results['session_user']->birthday ?>" autocomplete="off">
  </div>

  <div class="form-group mb-3">
    <label class="text-muted mb-2"><?= Lang::get('titles', 'About') ?></label>
    <textarea name="user[about]" class="form-control" autocomplete="off"><?= $results['session_user']->about ?></textarea>
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