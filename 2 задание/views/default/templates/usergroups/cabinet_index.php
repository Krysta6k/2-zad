<? require_once($results['templates'] . "/include/header.php"); ?>

<section class="setup pt-3 pb-4">
  <div class="container">
    <? require_once($results['templates'] . "/include/btns_block.php"); ?>

    <div class="form-group bg-white shadow-sm rounded p-3 mb-3">
      <div class="table-responsive">
        <table data-order='[[ 0, "asc" ]]' data-page-length='25' class="table table-sm">
          <thead>
            <tr>
              <th>#</th>
              <th><?= Lang::get('titles', 'Title') ?></th>
              <th><?= Lang::get('titles', 'Description') ?></th>
              <th><?= Lang::get('titles', 'Actions') ?></th>
            </tr>
          </thead>
          <tbody>
            <? foreach ($results['usergroups'] as $key => $item) : ?>
              <tr>
                <td><?= $item->id ?></td>
                <td><?= $item->title ?></td>
                <td><?= $item->description ?></td>
                <td>
                  <a href="#" class="edit_usergroup btn btn-sm btn-outline-secondary" data-id="<?= $item->id ?>" data-bs-toggle="modal" data-bs-target="#edit_usergroup_modal"><i class="fa fa-pencil"></i></a>
                  <a href="#" class="delete btn btn-sm btn-outline-danger" data-controller="<?= $GLOBALS['router']->getController() ?>" data-id="<?= $item->id ?>"><i class="fa fa-trash"></i></a>
                </td>
              </tr>
            <? endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <div class="modal fade" id="add_usergroup_modal" tabindex="-1" aria-labelledby="add_usergroup_modal_label" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
          <form action="/usergroups/add" method="post">
            <div class="modal-header">
              <h5 class="modal-title" id="add_usergroup_modal_label"><?= Lang::get('titles', 'Add') ?> script</h5>
              <button type="button" class="btn btn-sm p-0" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
              <input type="hidden" name="redirect" value="<?= $_SERVER['REQUEST_URI'] ?>">

              <div class="row">
                <div class="col-12 col-md-12">
                  <label class="text-muted mb-2"><?= Lang::get('titles', 'Title') ?></label>
                  <input type="text" name="data[title]" class="form-control mb-3" autocomplete="off" required>
                </div>

                <div class="col-12 col-md-12">
                  <div class="mb-3">
                    <label class="text-muted mb-2"><?= Lang::get('titles', 'Description') ?></label>
                    <textarea name="data[description]" class="form-control" autocomplete="off"></textarea>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" name="add" class="btn btn-primary"><?= Lang::get('btns', 'Add') ?></button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="modal fade" id="edit_usergroup_modal" tabindex="-1" aria-labelledby="edit_usergroup_modal_label" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
          <form action="/usergroups/edit" method="post">
            <div class="modal-header">
              <h5 class="modal-title" id="edit_usergroup_modal_label">Edit script</h5>
              <button type="button" class="btn btn-sm p-0" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
              <input type="hidden" name="data[id]">
              <input type="hidden" name="redirect" value="<?= $_SERVER['REQUEST_URI'] ?>">

              <div class="row">
                <div class="col-12 col-md-12">
                  <label class="text-muted mb-2"><?= Lang::get('titles', 'Title') ?></label>
                  <input type="text" name="data[title]" class="form-control mb-3" autocomplete="off" required>
                </div>

                <div class="col-12 col-md-12">
                  <div class="mb-3">
                    <label class="text-muted mb-2"><?= Lang::get('titles', 'Description') ?></label>
                    <textarea name="data[description]" class="form-control" autocomplete="off"></textarea>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" name="edit" class="btn btn-primary"><?= $results['lang']['btns']['edit'] ?: 'Edit' ?></button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

<? require_once($results['templates'] . "/include/pre_footer.php"); ?>
<? require_once($results['templates'] . "/include/datatables.php"); ?>
<script type="text/javascript">
  $(document).ready(function() {
    $('.edit_usergroup').on('click', function(e) {
      e.preventDefault();

      $.ajax({
        url: '/usergroups/get',
        method: 'post',
        dataType: 'json',
        data: {
          'id': $(this).data('id'),
          'get': true
        },
        success: function(callback) {
          $('#edit_usergroup_modal input[name="data[id]"]').val(callback.data.id);
          $('#edit_usergroup_modal input[name="data[title]"]').val(callback.data.title);
          $('#edit_usergroup_modal textarea[name="data[description]"]').html(callback.data.description);
        }
      });
    });
  });
</script>
<? require_once($results['templates'] . "/include/footer.php"); ?>