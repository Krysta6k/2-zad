<? require_once($results['templates'] . "/include/header.php"); ?>

<section class="setup pt-3 pb-4">
  <div class="container">
    <form action="<?= $results['form']['app']['action'] ?>" method="post" enctype="multipart/form-data">
      <input type="hidden" name="app[id]" value="<?= $results['app']->id ?: 1 ?>">
      <h1 class="text-muted mb-3 fw-bold">Application</h1>

      <h5 class="mb-3"><?= Lang::get('titles', 'Image') ?></h5>

      <div class="form-group bg-light rounded p-3 mb-3">
        <div class="img preview embed-responsive mb-3">
          <label for="upload" rel="tooltip" title="Выбрать изображение" class="holder bg-white rounded text-center d-flex h-100 w-100 justify-content-center align-items-center" style="cursor: pointer;background: url(<?= $results['app']->img ?>);aspect-ratio: 16/9;background-repeat: no-repeat;background-position: center;">
            <h3><i class="fa fa-upload" aria-hidden="true"></i></h3>
          </label>

          <input id="upload" class="file-upload__input d-none" type="file" name="image" onchange="readURL('previewImg')" accept=".jpg, .jpeg, .png, .svg, .webp, .ico">
        </div>

        <input type="hidden" name="app[img]" class="form-control mb-3" autocomplete="off" value="<?= $results['app']->img ?>">

        <label class="text-muted mb-3"><?= $results['lang']['titles']['alt_tag'] ?: 'Alt tag' ?></label>
        <input type="text" name="app[alt_tag]" class="form-control mb-3" autocomplete="off" value="<?= $results['app']->alt_tag ?>">
      </div>
  
      <h5 class="mb-3"><?= Lang::get('titles', 'General') ?></h5>

      <div class="form-group bg-light rounded p-3 mb-3">
        <div class="row">
          <div class="col-12 col-md-12">
            <label class="text-muted mb-2">Website name</label>
            <input class="form-control mb-3" type="text" name="app[title]" value="<?= $results['app']->title ?: '' ?>" required>
          </div>
          <div class="col-12 col-md-12">
            <label class="text-muted mb-2">Website tagline</label>
            <input class="form-control mb-3" type="text" name="app[tagline]" value="<?= $results['app']->tagline ?: '' ?>">
          </div>
          <div class="col-12 col-md-12">
            <div class="mb-3">
              <label class="text-muted mb-2">Website description</label>
              <textarea class="wr-short bg-white" name="app[description]"><?= $results['app']->description ?: '' ?></textarea>
            </div>
          </div>
          <div class="col-12 col-md-6">
            <label class="text-muted mb-2"><?= Lang::get('titles', 'Phone') ?></label>
            <input class="form-control mb-3" type="text" name="app[phone]" value="<?= $results['app']->phone ?: '' ?>">
          </div>
          <div class="col-12 col-md-6">
            <label class="text-muted mb-2"><?= Lang::get('titles', 'Email') ?></label>
            <input class="form-control mb-3" type="email" name="app[email]" value="<?= $results['app']->email ?: '' ?>">
          </div>
          <div class="col-12 col-md-6">
            <label class="text-muted mb-2">Region</label>
            <select name="app[region_id]" class="form-control mb-3 select2" required>
              <? foreach ($results['regions'] as $region) : ?>
                <option value="<?= $region->id ?>" <?= ($region->id == $results['app']->region_id) ? 'selected' : '' ?>><?= $region->title ?></option>
              <? endforeach; ?>
            </select>
          </div>
          <div class="col-12 col-md-6">
            <label class="text-muted mb-2">City</label>
            <select name="app[city_id]" class="form-control mb-3 select2" data-controller="cities" data-param_name="region_id" data-param_val="<?= $results['app']->region_id ?>" required>
              <? foreach ($results['cities'] as $city) : ?>
                <option value="<?= $city->id ?>" <?= ($city->id == $results['app']->city_id) ? 'selected' : '' ?>><?= $city->title ?></option>
              <? endforeach; ?>
            </select>
          </div>
          <div class="col-12 col-md-12">
            <label class="text-muted mb-2">Address</label>
            <input class="form-control mb-3" type="text" name="app[address]" value="<?= $results['app']->address ?: '' ?>">
          </div>
          <div class="col-12 col-md-12">
            <label class="text-muted mb-2"><?= $results['lang']['seo']['title_SEO'] ?: 'Title for SEO' ?></label>
            <input class="form-control mb-3" type="text" name="app[seo_title]" value="<?= $results['app']->seo_title ?: '' ?>">
          </div>
          <div class="col-12 col-md-12">
            <label class="text-muted mb-2"><?= $results['lang']['seo']['description_SEO'] ?: 'Description for SEO' ?></label>
            <textarea class="form-control mb-3" name="app[seo_description]"><?= $results['app']->seo_description ?: '' ?></textarea>
          </div>
          <div class="col-12 col-md-12">
            <label class="text-muted mb-2"><?= $results['lang']['seo']['keywords_SEO'] ?: 'Keywords for SEO' ?></label>
            <input class="form-control mb-3" type="text" name="app[seo_keywords]" value="<?= $results['app']->seo_keywords ?: '' ?>">
          </div>

          <div class="col-12 col-md-6">
            <div class="mb-3">
              <div class="form-check">
                <input class="form-check-input" type="radio" name="app[robots_index]" id="indexRadio1" value="index" <?= ($results['app']->robots_index == 'index') ? 'checked' : '' ?>>
                <label class="form-check-label" for="indexRadio1">
                  Index
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="app[robots_index]" id="indexRadio2" value="noindex" <?= ($results['app']->robots_index == 'noindex') ? 'checked' : '' ?>>
                <label class="form-check-label" for="indexRadio2">
                  No index
                </label>
              </div>
            </div>
          </div>
          <div class="col-12 col-md-6">
            <div class="mb-3">
              <div class="form-check">
                <input class="form-check-input" type="radio" name="app[robots_follow]" id="followRadio1" value="follow" <?= ($results['app']->robots_follow == 'follow') ? 'checked' : '' ?>>
                <label class="form-check-label" for="followRadio1">
                  Follow
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="app[robots_follow]" id="followRadio2" value="nofollow" <?= ($results['app']->robots_follow == 'nofollow') ? 'checked' : '' ?>>
                <label class="form-check-label" for="followRadio2">
                  No follow
                </label>
              </div>
            </div>
          </div>

          <div class="col-12 col-md-6">
            <label class="text-muted mb-2">Lang</label>
            <input class="form-control mb-3" type="text" name="app[lang_code]" value="<?= $results['app']->lang_code ?: '' ?>">
          </div>
          <div class="col-12 col-md-6">
            <label class="text-muted mb-2">Token</label>
            <input class="form-control mb-3" type="text" name="app[token]" value="<?= $results['app']->token ?: '' ?>">
          </div>
        </div>
      </div>

      <input type="submit" name="<?= $results['form']['app']['btn']['name'] ?>" class="btn btn-sm btn-primary" value="<?= $results['form']['app']['btn']['val'] ?: 'Setup' ?>">
    </form>
  </div>
</section>

<? require_once($results['templates'] . '/include/pre_footer.php'); ?>
<? require_once($results['templates'] . '/include/trumbowyg.php'); ?>
<? require_once($results['templates'] . '/include/footer.php'); ?>