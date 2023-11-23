<!DOCTYPE html>
<html lang="en">

<head>
<title>Title</title>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<!-- Bootstrap CSS v5.2.1 -->
	<link href="<?= TEMPLATE . '/assets/css/bootstrap.min.css?v=' . strtotime(date('Y-m-d')) ?>" rel="stylesheet" type="text/css" />
	<script src="<?= TEMPLATE . '/assets/js/jquery.min.js' ?>"></script>
	<link rel="stylesheet" href="views/default/assets/css/style2.css?v=<?= strtotime(date('Y-m-dhis'))?>">
</head>

<body>
    <header class="header">
        <div class="container-fluid header__container">
            <div class="row header__row">
                <div class="col-3 logo__col">
                    <div class="header__img-wraper">
                        <img src="img\logo.svg" alt="">
                    </div>
                    <h1 class="header__title">Доставка еды</h1>
                </div>

                <div class="col-7 phone__col">
                    <a class="header__phone" href="tel:88000000000">8 800 000 00 00</a>
                </div>

                <div class="col-2 buttons__col">
                    <a href="#" class="header__button">Вход</a>|<a href="#" class="header__button">Регистрация</a>
                </div>
            </div>
        </div>
    </header>

    <div class="navigation">
        <div class="container-fluid navigation__container">

            <div class="row navigation__row">
                <nav class="col-8 navigation__menu">
					<?php foreach ($results['categories'] as $categorie): ?>
						<a href="#" class="navigation__link"><?php echo $categorie->name ?></a>
                    <?php endforeach; ?>	
                </nav>

                <div class="col-4 navigation__basket__col">
                    <div class="navigation__basket">Корзина</div>
                </div>
            </div>
        </div>
    </div>

    <div class="slider">
        <div class="container-fluid slider__container">
            <div id="carouselExampleIndicators" class="carousel slide">
                <div class="carousel-indicators">
                    <div class="button__wraper">
                        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0"
                            class="active" aria-current="true" aria-label="Slide 1"></button>
                    </div>
                    <div class="button__wraper">
                        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0"
                            class="active" aria-current="true" aria-label="Slide 1"></button>
                    </div>
                    <div class="button__wraper">
                        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0"
                            class="active" aria-current="true" aria-label="Slide 1"></button>
                    </div>
                </div>

                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="img\Mask Group.png" class="d-block w-100" alt="...">
                    </div>
                    <div class="carousel-item">
                        <img src="img\pizza2.png" class="d-block w-100" alt="...">
                    </div>
                    <div class="carousel-item">
                        <img src="img\pizza3.png" class="d-block w-100" alt="...">
                    </div>
                </div>
                
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators"
                    data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators"
                    data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
    </div>
    
    <?php foreach ($results['categories'] as $categorie):?>
        <section class="dishes">
            <div class="container-fluid dishes__container">
                <h2><?php echo $categorie->name; ?></h2>

                <div class="row dishes__row">
                    
                        <div class="col-12 dishes__col">
                        <?php 
                        foreach ($results['goods'] as $item):
                            if($item->categori_id == $categorie->idcategories):
                        ?>
                            <div class="dish">
                                <div class="dish__frame">
                                    <img class="dishes__img" src="<?php echo($item->img) ?>" alt="">
                                </div>

                                <p class="dish__name"><?php echo($item->name); ?></p>
                                <p class="dish__description"><?php echo($item->description); ?></p>
                                
                                <div class="dish__cost">
                                    <p class="dish__rubcost"><?php echo($item->price);?></p>
                                    <p class="dish__grcost"><?php echo($item->weight);?></p>
                                </div>

                                <div class="dishes__basket">В корзину</div>
                            </div>
                            <?php 
                                endif;  
                                endforeach;
                            ?>
                        </div>
                        
                </div>
            </div>
        </section>
    <?php endforeach;?>

    <section class="deliveryinfo">
        <div class="container-fluid deliveryinfo__container">
            <h3>Условия доставки</h3>
            <p class="deliveryinfo__paragraph1">Самовывоз из офиса интернет-магазина<br> Минимальная сумма заказа
                отсутствует. Эта услуга
                бесплатна. <br>
                Cвой заказ
                можно получить в офисе интернет-магазина каждый день с с 9:00 до 21:00. <br>
                по адресу: Челюскинцев ул, дом 15
            </p>
            <p class="deliveryinfo__paragraph2">Доставка курьерской службой Наш курьер доставит заказ по указанному
                адресу с 10:00 до 21:00.
                После
                предварительного звонка оператора курьер дополнительно свяжется для предупреждения о выезде по адресу
                доставки (ориентировочно за 1 час). Стоимость доставки 200 руб. при сумме заказа менее 2000 руб.
            </p>

            <p class="deliveryinfo__paragraph3">при сумме заказа менее 2000 руб. При сумме заказа более 2000 руб.
                доставка осуществляется
                бесплатно
            </p>

            <p class="deliveryinfo__paragraph4">Мы можем предложить доставку в день заказа или в любой последующий день
                с 10:00 до 21:00. Срочная
                доставка
                может быть осуществлена в любое удобное время в интервале 1 час, но не ранее, чем через 3 часа
                после
                оформления заказа. В случае опоздания курьера - доставка за наш счет!</p>
        </div>


    </section>

    <div class="sliderreviews">
        <div class="container-fluid sliderreviews__container">
            <h4>Отзывы наших клиентов</h4>
            <div class="reviews">
                <?php foreach ($results['reviews'] as $rew):?>
                    <div class="review__wraper">
                    <div class="review__personnameanddata">
                    <p class="review__personname"><?php echo$rew->name ?></p>
                    <p class="review__data"><?php echo$rew->data ?></p>
                    </div>
                    <img src="img\stars.png" alt="">
                    <p class="review__text"><?php echo$rew->text ?></p>
                    </div>
                <?php endforeach;?>
        </div>
            

        </div>


    </div>
    <section class="address">
        <div class="container-fluid address__container">
            <div class="row address__row">
                <div class="col-6 adress__col">
                    <h4>Доставка еды </h4>
                    <p class="address__top">Россия, г. Новокузнецк,<br>
                        ул. Красноармейская, 65</p>

                    <p class="address__mid">8 800 000 00 00 <br>
                        E-mail: pf_zodchiy_granit@mail.ru</p>

                    <p class="address__bot">Время работы: Пн. - пт: с 9:00 до 18:00,<br>
                        сб.: с 10:00 до 15:00, вс.: выходной </p>
                </div>
                <div class="col-6 map__col">
                    <div class="address__map">
                        <script type="text/javascript" charset="utf-8" async
                            src="https://api-maps.yandex.ru/services/constructor/1.0/js/?um=constructor%3A0ab3403184df898ffc3edc8412f8eccd8f385c1ddff2a37e6d1097372a06826d&amp;width=558&amp;height=360&amp;lang=ru_RU&amp;scroll=true"></script>
                    </div>
                </div>

            </div>



        </div>

    </section>

    <footer class="footer">
        <div class="container-fluid footer__container">
            <div class="row footer__row">
                <div class="col-4 footer__text__col">
                    <p class="footer__text">Доставка еды</p>
                </div>
                <div class="col-4 footer__imgs__col">
                    <div class="footer__imgs ">
                        <img src="img\VK_white.png" alt="">
                        <img src="img\OK_white.png" alt="">
                        <img src="img\Facebook_white.png" alt="">
                        <img src="img\Instagram_white.png" alt="">
                    </div>
                </div>
                <div class="col-4 footer__number__col">
                    <p class="footer__number">8 800 000 00 00</p>
                </div>

            </div>




        </div>
    </footer>





                            




    <script src="<?= TEMPLATE . '/assets/js/bootstrap.bundle.min.js' ?>"></script>
</body>

</html>