// Удаление элемента
$("body").on("click", ".delete", function () {
	if (confirm($(this).data("message"))) {
		$.ajax({
			url: "/" + $(this).data("controller") + "/delete",
			method: "post",
			dataType: "json",
			data: {
				id: $(this).data("id"),
				redirect: $(this).data("redirect") ? $(this).data("redirect") : window.location.href,
				delete: true,
			},
			success: function (callback) {
				makeToast(".toasts-place", callback.type.toLowerCase(), callback.type, callback.message);
			},
		});

		$(this).closest(".list-group-item, tr").remove(); 
	}

	return false;
});

// Заполнение offcanvas данными
$(".offcanvas_btn").on("click", function (e) {
	offcanvas($(this).data("prefix"), $(this).data("controller"), $(this).data("action"), $(this).data("header"), $(this).data("params"));
});

function offcanvas(prefix = null, controller, action, header, params) {
	$("#offcanvas .offcanvas-title").empty().html(header);
	$.get(prefix + controller + "/" + action + "?" + $.param(params), function (data) {
		$("#offcanvas .offcanvas-body").html(data);

		document.getElementById("offcanvas").addEventListener("show.bs.offcanvas", async (event) => {
			$("#offcanvas .offcanvas-body .select2").select2({
				theme: "bootstrap-5",
				width: "auto",
				dropdownParent: document.querySelector(".offcanvas-body form") != null ? ".offcanvas-body form" : null,
			});
		});
	});
}

// Маска телефона
$("input[type='phone']").mask("+7 (999) 999 9999");

// Прокрутка таблиц
if ($(window).width() <= 1080) $(".table-responsive-comment").addClass("table-responsive");
	else $(".table-responsive-comment").removeClass("table-responsive");

// Политика куки
$(".agree .custom-control-input").on("click", function () {
	if ($(this).prop("checked") == true) {
		$(".btn-agree").removeClass("disabled");
		$(".btn-agree").removeAttr("disabled");
	} else if ($(this).prop("checked") == false) {
		$(".btn-agree").addClass("disabled");
		$(".btn-agree").addAttr("disabled");
	}
});

// Поиск
$("#search").on("click", function (e) {
	e.preventDefault();
	$("#search_field").toggleClass("d-none");
});

// Кликабельные ссылки в меню
$(".navbar .nav-link").on("click", function (e) {
	e.preventDefault();
	window.location.href = $(this).attr("href");
});

$("#items_per_page").on("change", function (e) {
	$.post("/app/cookie", { title: "items_per_page", val: $(this).val() }, function (data) {
		location.reload();
	});
});

// Показать пароль
$("body").on("click", "#password_show", function () {
	$(".password").attr("type", (_, attr) => (attr == "password" ? "text" : "password"));
});

// Сгенерировать пароль
$("body").on("click", "#password_generate", function () {
	$.post("/app/generate_password", { generate: "get strong" }, function (data) {
		let res = JSON.parse(data);
		$(".password").val(res.strong_pass).attr("type", "text");
	});
});

// Возврат к верху страницы
function getOverHere(elem, offset = 0, speed = 400) {
	$("html, body").animate(
		{
			scrollTop: $(elem).offset().top + offset,
		},
		speed
	);
}

//Вывод тостов
function makeToast(where, type, title = null, text, delay = 5000) {
	toastr.options.progressBar = true;
	switch (type.toLowerCase()) {
		case "error":
			toastr.error(text, title);
			break;
		case "success":
			toastr.success(text, title);
			break;
		case "warning":
			toastr.warning(text, title);
			break;
		case "info":
			toastr.info(text, title);
			break;
	}
}

// Заливка фона изображение на превью
function readURL(imageUrl, controller_name, ratio) {
	let input = event.target;

	if (input.files && input.files[0]) {
		let reader = new FileReader();

		reader.onload = function (e) {
			if (controller_name == "articles") {
				$(".jcrop-preview").css("background-image", "url(" + e.target.result + ")");
				// $('input[name="data[img]"]').val(e.target.result);
			} else {
				$(".holder")
					.css("background-image", "url(" + e.target.result + ")")
					.addClass("load");
				$('input[name="codeimg"]').val(e.target.result);
			}

			if (ratio) {
				setTimeout(() => {
					$('input[name="' + imageUrl + 'Width"]').val($("#" + imageUrl).width());
					$('input[name="' + imageUrl + 'Height"]').val($("#" + imageUrl).height());
				}, 1000);
				if (imageUrl == "gallery_item") {
					$("#upload_gallery_form").find(".btn-submit").addClass("show");
				} else {
					$("#" + imageUrl).imgAreaSelect({
						handles: true,
						aspectRatio: ratio,
						onSelectEnd: function (img, selection) {
							$('input[name="' + imageUrl + '-x"]').val((selection.x1 * 100) / $('input[name="' + imageUrl + 'Width"]').val());
							$('input[name="' + imageUrl + '-y"]').val((selection.y1 * 100) / $('input[name="' + imageUrl + 'Height"]').val());
							$('input[name="' + imageUrl + '-w"]').val((selection.width * 100) / $('input[name="' + imageUrl + 'Width"]').val());
							$('input[name="' + imageUrl + '-h"]').val((selection.height * 100) / $('input[name="' + imageUrl + 'Height"]').val());
						},
					});
				}
			}
		};

		reader.readAsDataURL(input.files[0]);
	}
}

// Форматирование цен
function moneyFormat(n) {
	return parseFloat(n)
		.toFixed(1)
		.replace(/(\d)(?=(\d{3})+\.)/g, "$1 ")
		.replace(".", ",");
}

// // Получение сегодняшней даты 
// function addZero(i) {
// 	if (i < 10) i = "0" + i;
// 	return i;
// }

// function GetTodayDate() {
// 	var d = new Date();

// 	var dd = addZero(d.getDate());
// 	var mm = d.getMonth() + 1;
// 	var yyyy = d.getFullYear();

// 	var currentDate= yyyy + "-" + addZero(mm) + "-" + dd;

// 	return currentDate;
// }

// // Инициализация Яндекс карты
// function yandex_map_init(lat, lon) {
// 	myMap = new ymaps.Map("map", {
// 		center: [lat, lon],
// 		zoom: 14,
// 		controls: ["smallMapDefaultSet"],
// 	});

// 	myMap.controls.remove("zoomControl");
// 	myMap.controls.remove("searchControl");
// 	myMap.controls.remove("typeSelector");
// 	myMap.controls.remove("fullscreenControl");
// 	myMap.controls.remove("routeButtonControl");
// 	myMap.controls.remove("trafficControl");
// 	myMap.controls.remove("geolocationControl");
// 	myMap.controls.remove("rulerControl");

// 	myMap.geoObjects.add(new ymaps.Placemark([lat, lon]));
// }

// // Чекбоксы таблиц
// function table_checkbox_click() {
// 	let id = $(this).data("id");

// 	if ($(this).is(":checked", true)) {
// 		if (isAllChecked) {
// 			var index = unchecked_items.indexOf(id);
// 			if (index > -1) {
// 				unchecked_items.splice(index, 1);
// 			}
// 		} else {
// 			checked_items.push(id);
// 		}
// 	} else {
// 		if (isAllChecked) {
// 			unchecked_items.push(id);
// 		} else {
// 			var index = checked_items.indexOf(id);
// 			if (index > -1) {
// 				checked_items.splice(index, 1);
// 			}
// 		}
// 	}

// 	if (checked_items.length > 0 || isAllChecked) {
// 		$(".table-responsive .btn-group").removeClass("d-none");
// 	} else {
// 		$(".table-responsive .btn-group").addClass("d-none");
// 	}
// }

// // Чекбоксы таблиц (выбрать все)
// function table_all_checkbox_click() {
// 	if ($(this).is(":checked", true)) {
// 		isAllChecked = true;
// 		checked_items.length = 0;
// 	} else {
// 		isAllChecked = false;
// 		unchecked_items.length = 0;
// 	}
// }

// Поиск обязательных полей
function find_required() {
	var requireds = document.querySelectorAll("[required]");

	requireds.forEach((item) => {
		var label = item.closest("div").querySelector("label");

		if (label != null) label.innerHTML += '&nbsp;<span class="text-danger">*</span>';
	});
}