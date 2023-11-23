<?php
require_once(ROOT . '/vendor/autoload.php');

class Pagination
{
  /**
   * 
   * Ссылок навигации на страницу
   * 
   */
  private $max = 4;

  /**
   * 
   * Ключ для GET, в который пишется номер страницы
   * 
   */
  private $index = 'page';

  /**
   * 
   * Текущая страница
   * 
   */
  private $current_page;

  /**
   * 
   * Общее количество записей
   * 
   */
  private $total;

  /**
   * 
   * Записей на страницу
   * 
   */
  private $limit;

  private $amount;

  /**
   * Запуск необходимых данных для навигации
   * @param integer $total - общее количество записей
   * @param integer $limit - количество записей на страницу
   * 
   * @return
   */
  public function __construct($total, $currentPage, $index)
  {
    # Устанавливаем общее количество записей
    $this->total = $total;

    # Устанавливаем количество записей на страницу

    if ($GLOBALS['router']->getMethodPrefix() == 'admin_')
      $this->limit = $_COOKIE['admin_items_per_page'] ?: ADMIN_ITEMS_PER_PAGE;
    else
      $this->limit = $_COOKIE['items_per_page'] ?: DEFAULT_ITEMS_PER_PAGE;

    # Устанавливаем ключ в url
    $this->index = $index;

    # Устанавливаем количество страниц
    $this->amount = $this->amount();

    # Устанавливаем номер текущей страницы
    $this->setCurrentPage($currentPage);
  }

  /**
   *  Для вывода ссылок
   * 
   *  HTML-код со ссылками навигации
   */
  public function get()
  {
    # Для записи ссылок
    $links = null;

    # Получаем ограничения для цикла
    $limits = $this->limits();

    $html = '<nav><ul class="articles__pagination pagination">';
    # Генерируем ссылки
    for ($page = $limits[0]; $page <= $limits[1]; $page++) {
      # Если текущая это текущая страница, ссылки нет и добавляется класс active
      if ($page == $this->current_page) {
        $links .= '<li class=""><a class="pagination__btn active" href="#">' . $page . '</a></li>';
      } else {
        # Иначе генерируем ссылку
        $links .= $this->generateHtml($page);
      }
    }

    # Если ссылки создались
    if (!is_null($links)) {
      # Если текущая страница не первая
      if ($this->current_page > 1)
        # Создаём ссылку "На предыдущую"
        $links = $this->generateHtml($this->current_page - 1, "<svg width=\"8\" height=\"12\" viewBox=\"0 0 8 12\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\">\n    <path d=\"M6.5 11L1.5 6L6.5 1\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>\n</svg>\n") . $links;

      # Если текущая страница не первая
      if ($this->current_page < $this->amount)
        # Создаём ссылку "На следующую"
        $links .= $this->generateHtml($this->current_page + 1, "<svg width=\"8\" height=\"12\" viewBox=\"0 0 8 12\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\">\n    <path d=\"M1.5 1L6.5 6L1.5 11\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" />\n</svg>\n");
    }

    $html .= $links . '</ul></nav>';

    # Возвращаем html
    return $html;
  }

  /**
   * Для генерации HTML-кода ссылки
   * @param integer $page - номер страницы
   * 
   * @return
   */
  private function generateHtml($page, $text = null)
  {
    # Если текст ссылки не указан
    if (!$text)
      # Указываем, что текст - цифра страницы
      $text = $page;

    $currentURI = rtrim($_SERVER['REQUEST_URI'], '/') . '/';
    $currentURI = preg_replace('~?page=[0-9]+~', '', $currentURI);
    # Формируем HTML код ссылки и возвращаем
    return
      '<li class=""><a class="pagination__btn" href="' . $currentURI . $this->index . $page . '">' . $text . '</a></li>';
  }

  /**
   *  Для получения, откуда стартовать
   * 
   * @return массив с началом и концом отсчёта
   */
  private function limits()
  {
    # Вычисляем ссылки слева (чтобы активная ссылка была посередине)
    $left = $this->current_page - round($this->max / 2);

    # Вычисляем начало отсчёта
    $start = $left > 0 ? $left : 1;

    # Если впереди есть как минимум $this->max страниц
    if ($start + $this->max <= $this->amount)
      # Назначаем конец цикла вперёд на $this->max страниц или просто на минимум
      $end = $start > 1 ? $start + $this->max : $this->max;
    else {
      # Конец - общее количество страниц
      $end = $this->amount;

      # Начало - минус $this->max от конца
      $start = $this->amount - $this->max > 0 ? $this->amount - $this->max : 1;
    }

    # Возвращаем
    return
      array($start, $end);
  }

  /**
   * Для установки текущей страницы
   * 
   * @return
   */
  private function setCurrentPage($currentPage)
  {
    # Получаем номер страницы
    $this->current_page = $currentPage;

    # Если текущая страница боле нуля
    if ($this->current_page > 0) {
      # Если текунщая страница меньше общего количества страниц
      if ($this->current_page > $this->amount)
        # Устанавливаем страницу на последнюю
        $this->current_page = $this->amount;
    } else
      # Устанавливаем страницу на первую
      $this->current_page = 1;
  }

  /**
   * Для получеия общего числа страниц
   * 
   * @return число страниц
   */
  private function amount()
  {
    # Делим и возвращаем
    return ceil($this->total / $this->limit);
  }
}
