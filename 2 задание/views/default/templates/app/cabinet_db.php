<? require_once($results['templates'] . "/include/header.php"); ?>

<section class="setup pt-3 pb-4">
  <div class="container">
    <form action="<?= $results['form']['db']['action'] ?>" method="post" enctype="multipart/form-data">
      <h1 class="text-muted mb-3 fw-bold">Database config</h1>

      <div class="form-group bg-white shadow-sm rounded p-3 mb-3">
        <div class="row">
          <div class="col-12 col-md-6">
            <label class="text-muted mb-2">Host</label>
            <input class="form-control mb-3" type="text" name="db[host]" placeholder="Ex: localhost" value="<?= $results['db']['host'] ?: '' ?>" required>
          </div>
          <div class="col-12 col-md-6">
            <label class="text-muted mb-2">Driver</label>
            <select name="db[driver]" class="form-control mb-3" required>
              <option value="mysql" <?= $results['db']['driver'] == 'mysql' ? 'selected' : '' ?>>MySQL</option>
              <option value="pgsql" <?= $results['db']['driver'] == 'pgsql' ? 'selected' : '' ?>>pgSQL</option>
              <option value="sqlite" <?= $results['db']['driver'] == 'sqlite' ? 'selected' : '' ?>>SQLite</option>
              <option value="oracle" <?= $results['db']['driver'] == 'oracle' ? 'selected' : '' ?>>Oracle</option>
              <option value="sqlsrv" <?= $results['db']['driver'] == 'sqlsrv' ? 'selected' : '' ?> disabled>Microsoft SQL</option>
            </select>
          </div>
          <div class="col-12 col-md-6">
            <label class="text-muted mb-2"><?= Lang::get('titles', 'Name') ?></label>
            <input class="form-control mb-3" type="text" name="db[database]" placeholder="" value="<?= $results['db']['database'] ?: '' ?>" required>
          </div>
          <div class="col-12 col-md-6">
            <label class="text-muted mb-2">Username</label>
            <input class="form-control mb-3" type="text" name="db[username]" placeholder="" value="<?= $results['db']['username'] ?: '' ?>" required>
          </div>
          <div class="col-12 col-md-6">
            <label class="text-muted mb-2">Password</label>
            <input class="form-control mb-3" type="password" name="db[password]" placeholder="" value="" <?= empty($results['db']['password']) ? 'required' : '' ?>>
          </div>
          <div class="col-12 col-md-6">
            <label class="text-muted mb-2">Charset</label>
            <input class="form-control mb-3" type="text" name="db[charset]" placeholder="" value="<?= $results['db']['charset'] ?: 'utf8mb4' ?>" required>
          </div>
          <div class="col-12 col-md-6">
            <label class="text-muted mb-2">Collation</label>
            <input class="form-control mb-3" type="text" name="db[collation]" placeholder="" value="<?= $results['db']['collation'] ?: 'utf8mb4_unicode_ci' ?>">
          </div>
          <div class="col-12 col-md-6">
            <label class="text-muted mb-2">Database prefix</label>
            <input class="form-control mb-3" type="text" name="db[prefix]" placeholder="" value="<?= $results['db']['prefix'] ?: '' ?>">
          </div>
          <div class="col-12 col-md-6">
            <label class="text-muted mb-2">Cache directory</label>
            <input class="form-control mb-3" type="text" name="db[cachedir]" placeholder="Ex: /cache/sql/" value="<?= $results['db']['cachedir'] ?: '' ?>">
          </div>
        </div>
      </div>

      <input type="submit" name="<?= $results['form']['db']['btn']['name'] ?>" class="btn btn-sm btn-outline-primary" value="<?= $results['form']['db']['btn']['val'] ?: 'Setup' ?>">
    </form>
  </div>
</section>
<? require_once($results['templates'] . "/include/pre_footer.php"); ?>
<script type="text/javascript">
  $(document).ready(function() {
    // Password
    $('#password_show').on('click', function(e) {
      e.preventDefault();

      $('#admin_pass').attr('type', (_, attr) => attr == 'password' ? 'text' : 'password');
    });

    $('#password_generate').on('click', function(e) {
      e.preventDefault();

      $.post('/app/generate_password', {
        generate: 'get strong'
      }, function(data) {
        let res = JSON.parse(data);
        $('#admin_pass').val(res.strong_pass).attr('type', 'text');
      });
    });
  });
</script>
<? require_once($results['templates'] . "/include/footer.php"); ?>