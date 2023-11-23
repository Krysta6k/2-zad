<? require_once($results['templates'] . "/include/header.php"); ?>

<section class="setup pt-3 pb-4">
  <div class="container">
    <h1 class="text-muted mb-3 fw-bold">Отчет</h1>

    <div class="table-responsive">
      <table class="table table-bordered border-primary text-light align-middle">
        <thead>
          <th>#</th>
          <th>Kinoplan</th>
          <th>Сбер</th>
          <th>Фильм</th>
          <th>Оформлен</th>
          <th>Сеанс</th>
          <th>Стоимость</th>
          <th>Покупатель</th>
        </thead>
        <tbody>
          <? foreach($results['orders'] as $order): ?>
            <tr>
              <td><?= $order->id ?></td>
              <td><?= $order->kinoplan_id ?></td>
              <td>
                <? 
                  if($order->payment == 1) {
                    $status = [
                      'text' => 'Оплачен',
                      'color' => 'success'
                    ];
                  } else {
                    $status = [
                      'text' => 'Не оплачен',
                      'color' => 'danger'
                    ];
                  }
                ?>
                <span class="badge bg-<?= $status['color'] ?> mb-2"><?= $status['text'] ?></span>
                <div class="d-block"><?= $order->sber_id ?></div>
              </td>
              <td>
                <small class="text-muted d-block"><?= $order->performanceId ?></small>
                <?= $order->film ?>
              </td>
              <td><?= App::date_format($order->order_date, 'd.m.y H:i') ?></td>
              <td><?= App::date_format($order->visit_date, 'd.m.y H:i') ?></td>
              <td><?= $order->price ?></td>
              <td><? foreach($results['users'] as $user) if($user->id == $order->user_id) echo $user->name . ' ' . $user->surname . '<br>' . $user->email ?></td>
            </tr>
          <? endforeach; ?>
        </tbody>
      </table>
    </div>

    <div class="page-control d-flex mb-3">
      <div class="items-per-page me-auto">
        <select name="limit" id="items_per_page" class="form-select shadow-none border-0 text-muted">
          <? foreach (ITEMS_PER_PAGE_LIST as $limit_item) : ?>
            <option value="<?= $limit_item ?>" <?= ($_COOKIE['items_per_page'] ?: DEFAULT_ITEMS_PER_PAGE) == $limit_item ? 'selected' : '' ?>><?= $limit_item ?></option>
          <? endforeach; ?>
        </select>
      </div>

      <div class="pagination-block">
        <?
          if ($results['total'] > ($_COOKIE['items_per_page'] ?: DEFAULT_ITEMS_PER_PAGE)):
            $pagination = new Pagination($results['total'], $_GET['page'] ?: 0, '?page=');
            echo (string) $pagination->get(); 
          endif;
        ?>
      </div>
    </div>
  </div>
</section>

<? require_once($results['templates'] . "/include/pre_footer.php"); ?>
<script type="text/javascript">
  $("#items_per_page").on("change", function (e) {
    $.post("/app/cookie", { title: "items_per_page", val: $(this).val() }, function (data) {
      location.reload();
    });
  });
</script>
<? require_once($results['templates'] . "/include/footer.php"); ?>