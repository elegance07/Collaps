<?php

/*

 * @ https://kino-xa.ru

 * @  Магазин раскодированных модулей. Скоро будет работать

 */

if (!defined("DATALIFEENGINE") || !defined("LOGGED_IN")) {

    header("HTTP/1.1 403 Forbidden");

    header("Location: ../../");

    exit("Hacking attempt!");

}

if (!empty($_POST["q_search"]) || $_POST["kp_id"]) {

    require_once DLEPlugins::Check(ENGINE_DIR . "/modules/collaps.php");

    exit;

}

$__name = "COLLAPS";

$__descr = "Автонаполнение сайта DLE по базе Collaps";

$config_mod = unserialize(file_get_contents(ENGINE_DIR . "/data/" . $mod . ".config"));

if (!$config_mod) {

    $config_mod = array();

}

if ($action) {

    if (!$user_group[$member_id["user_group"]]["admin_addnews"]) {

        exit("{\"success\":false,\"message\":\"" . $lang["index_denied"] . "\"}");

    }

    if ($action == "config") {

        $config_mod = is_array($_POST["config"]) ? $_POST["config"] : array();

        file_put_contents(ENGINE_DIR . "/data/" . $mod . ".config", serialize($config_mod));

        exit("{\"success\":true,\"message\":\"Настройки успешно сохранены\"}");

    }

    if ($action == "sections") {

        $row = $db->super_query("SELECT id, name FROM " . PREFIX . "_admin_sections WHERE name = '" . $mod . "'");

        if ($row) {

            $db->query("DELETE FROM " . PREFIX . "_admin_sections WHERE name = '" . $mod . "'");

            exit("{\"success\":true,\"message\":\"Модуль убран из меню «Сторонние модули»\"}");

        }

        $db->query("INSERT IGNORE INTO " . PREFIX . "_admin_sections (name, title, descr, icon, allow_groups) VALUES ('" . $mod . "', '" . $__name . "', '" . $descr . "', '', '1')");

        exit("{\"success\":true,\"message\":\"Модуль добавлен в меню «Сторонние модули»\"}");

    }

}

if (!$user_group[$member_id["user_group"]]["admin_addnews"]) {

    msg("error", $lang["index_denied"], $lang["index_denied"]);

}

$tags_arr = array("poster" => "Постер", "title_ru" => "Название", "title_en" => "Оригинальное название", "year" => "Год выхода", "description" => "Описание", "countries" => "Страны", "genres" => "Жанры", "actors" => "Актеры", "actors_dubl" => "Актеры дубляжа", "directors" => "Режиссеры", "iframe_url" => "Плеер", "trailer" => "Трейлер", "kinopoisk_id" => "ID Kinopoisk", "imdb_id" => "ID IMDB", "world_art_id" => "ID WORLD ART", "translator" => "Перевод", "quality" => "Качество видео", "last_season" => "Последний сезон", "last_episode" => "Последний эпизод", "rating_kp" => "Kinopoisk рейтинг", "rating_imdb" => "IMDB рейтинг", "rating_world_art" => "WORLD ART рейтинг", "rate_mpaa" => "Рейтинг материала по шкале MPAA", "premiere_ru" => "Премьера в россии", "premiere_world" => "Мировая премьера", "video_type" => "Тип видео (фильм, сериал...)", "collection" => "Подборки", "parts" => "id Фильмов из Франшизы", "time" => "Продолжительность", "trivia" => "Знаете ли Вы...", "age" => "Возрастное ограничение", "budget" => "Бюджет", "slogan" => "Слоган", "fees_rus" => "Кассовые сборы в РФ", "fees_use" => "Кассовые сборы в США", "fees_world" => "Кассовые сборы в мире", "design" => "Художники", "editor" => "Монтажеры", "operator" => "Операторы", "producer" => "Продюсеры", "screenwriter" => "Сценаристы", "serial_status" => "Статус сериала", "id" => "id Collaps", ":instream_ads" => "Наличие рекламы: да / нет");

$country_arr = array("Австралия", "Австрия", "Азербайджан", "Албания", "Алжир", "Американское Самоа", "Ангилья", "Англия", "Ангола", "Андорра", "Антигуа и Барбуда", "Аргентина", "Армения", "Аруба", "Афганистан", "Багамы", "Бангладеш", "Барбадос", "Бахрейн", "Бейкер", "Белиз", "Белоруссия", "Бельгия", "Бенилюкс", "Бенин", "Болгария", "Боливия", "Бонэйр", "Бопутатсвана", "Босния и Герцеговина", "Ботсвана", "Бразилия", "Бруней", "Буркина-Фасо", "Бурунди", "Бутан", "Вануату", "Ватикан", "Великобритания", "Венгрия", "Венда", "Венесуэла", "Вьетнам", "Габон", "Гаити", "Гайана", "Гамбия", "Гана", "Гватемала", "Гвинея", "Гвинея-Бисау", "Германия", "Гернси", "Гибралтар", "Гондурас", "Гонконг", "Сомали", "Гренада", "Греция", "Грузия", "Гуам", "Дания", "Конго", "Косово", "Джибути", "Джонстон", "Джубаленд", "Доминика", "Доминикана", "Египет", "Замбия", "Зимбабве", "Израиль", "Имамат Оман", "Индия", "Индонезия", "Иордания", "Ирак", "Иран", "Ирландия", "Исландия", "Испания", "Италия", "Йемен", "Султанат Касири", "Кабо-Верде", "Казахстан", "Камбоджа", "Камерун", "Канада", "Катар", "Кашубия", "Кенедугу", "Кения", "Киргизия", "Кирибати", "Китай", "Колумбия", "Коморы", "Конго", "Корея Северная", "Корея Южная", "Нидерланды", "Конго", "Коста-Рика", "Куба", "Кувейт", "Кюрасао", "Лаос", "Латвия", "Лесото", "Либерия", "Ливан", "Ливия", "Литва", "Лихтенштейн", "Люксембург", "Маврикий", "Мавритания", "Мадагаскар", "Малави", "Малайзия", "Мали", "Мальдивы", "Мальта", "Марокко", "Мартиазо", "Мексика", "Мидуэй", "Мозамбик", "Молдавия", "Молдова", "Монако", "Монголия", "Монтсеррат", "Мьянма", "Намибия", "Науру", "Непал", "Нигер", "Нигерия", "Нидерланды", "Никарагуа", "Ниуэ", "Новая Зеландия", "Новая Каледония", "Норвегия", "Остров Норфолк", "ОАЭ", "Оман", "Пакистан", "Палау", "Панама", "Парагвай", "Перу", "Польша", "Португалия", "Пуэрто Рико", "Ангилья", "Закистан", "Кипр", "Логон", "Россия", "Руанда", "Румыния", "Сальвадор", "Самоа", "Сан-Марино", "Саудовская Аравия", "Северная Ирландия", "Северная Македония", "Сейшельские Острова ", "Сенегал", "Сент-Люсия", "Сербия", "Силенд", "Сингапур", "Синт-Мартен", "Синт-Эстатиус", "Сирия", "Сискей", "Словакия", "Словения", "Соломоновы Острова", "Сомали", "Сомалиленд", "Судан", "Суринам", "СССР", "США", "Сьерра-Леоне", "Таджикистан", "Таиланд", "Тайвань", "Танзания", "Того", "Токелау", "Тонга", "Торо", "Транскей", "Тринидад", "Тобаго", "Тувалу", "Тунис", "Туркмения", "Турция", "Уганда", "Узбекистан", "Украина", "Уругвай", "Уэйк", "Уэльс", "ФШМ", "Фиджи", "Филиппины", "Финляндия", "Фландренсис", "Фолклендские острова", "Франция", "Французская Полинезия", "Хауленд", "Хиршабелле", "Хорватия", "Центральноафриканская Республика", "Чад", "Черногория", "Чехия", "Чили", "Швейцария", "Швеция", "Шотландия", "Шри-Ланка", "Эквадор", "Экваториальная Гвинея", "Эритрея", "Эсватини", "Эстония", "Эфиопия", "Южная Георгия", "ЮАР", "Южный Судан", "Ямайка", "Япония");

$main = "";

foreach (array("title" => "Заголовок новости", "short_story" => "Краткое описание", "full_story" => "Полное описание", "metatitle" => "Метатег Title", "descr" => "Метатег Description", "keywords" => "Метатег Keywords", "tags" => "Теги новости", "alt_name" => "ЧПУ новости", "api_token" => "Токен Collaps", "lickey" => "Лицензионный ключ") as $name => $title) {

    $main .= "<tr><td class=\"col-xs-6 col-sm-6 col-md-7 white-line\"><h6 class=\"media-heading text-semibold\">" . $title . "</h6><span></span></td><td class=\"col-xs-6 col-sm-6 col-md-7 white-line\"><input name=\"config[" . $name . "]\" class=\"form-control\" value=\"" . (isset($config_mod[$name]) ? $config_mod[$name] : "") . "\"></td></tr>";

}

$add_film = makecheckbox("config[add_film]", $config_mod["add_film"]);

$add_serial = makecheckbox("config[add_serial]", $config_mod["add_serial"]);

$add_mult = makecheckbox("config[add_mult]", $config_mod["add_mult"]);

$add_multserial = makecheckbox("config[add_multserial]", $config_mod["add_multserial"]);

$add_anime = makecheckbox("config[add_anime]", $config_mod["add_anime"]);

$add_animeserial = makecheckbox("config[add_animeserial]", $config_mod["add_animeserial"]);

$main .= "<tr><td class=\"col-xs-6 col-sm-6 col-md-7 white-line\"><h6 class=\"media-heading text-semibold\">Добавлять на сайт</h6></td><td class=\"col-xs-6 col-sm-6 col-md-7 white-line\">" . "<div style=\"margin-bottom:10px\">" . $add_film . " Фильмы</div>" . "<div style=\"margin-bottom:10px\">" . $add_serial . " Сериалы</div>" . "<div style=\"margin-bottom:10px\">" . $add_mult . " Мультфильмы</div>" . "<div style=\"margin-bottom:10px\">" . $add_multserial . " Мульт-сериалы</div>" . "<div style=\"margin-bottom:10px\">" . $add_anime . " Аниме</div>" . "<div style=\"margin-bottom:10px\">" . $add_animeserial . " Аниме-сериалы</div>" . "</td></tr>";

$main .= "<tr><td class=\"col-xs-6 col-sm-6 col-md-7 white-line\"><h6 class=\"media-heading text-semibold\">Черный список</h6><span>Укажите список ID Кинопоиска(каждый с новой строки)</span></td><td class=\"col-xs-6 col-sm-6 col-md-7 white-line\"><textarea name=\"config[blacklist]\" style=\"height:200px;\" class=\"form-control\">" . (isset($config_mod["blacklist"]) ? $config_mod["blacklist"] : "") . "</textarea></td></tr>";

$first_new = makecheckbox("config[first_new]", $config_mod["first_new"]);

$main .= "<tr>\r\n      <td class=\"col-xs-6 col-sm-6 col-md-7 white-line\">\r\n        <h6 class=\"media-heading text-semibold\">Добавлять сначала новинки:</h6>\r\n        <span class=\"text-muted text-size-small hidden-xs\">если включено, то сначала будут добавлятся новые фильмы (по годам), иначе так как находятся в базе. по порядку</span>\r\n      </td>\r\n      <td class=\"col-xs-6 col-sm-6 col-md-7 white-line\">" . $first_new . "</td>\r\n    </tr>";

$allow_country = $config_mod["allow_country"] ? $config_mod["allow_country"] : array();

foreach ($country_arr as $value) {

    if (in_array(mb_strtolower($value, "utf-8"), $allow_country)) {

        $country_select[] = "<option value=\"" . mb_strtolower($value, "utf-8") . "\" selected>" . $value . "</option>";

    } else {

        $country_select[] = "<option value=\"" . mb_strtolower($value, "utf-8") . "\">" . $value . "</option>";

    }

}

$allow_year = $config_mod["allow_year"] ? $config_mod["allow_year"] : array();

for ($i = 1920; $i < 2020 + date("Y"); $i++) {

    if (in_array($i, $allow_year)) {

        $year_select[] = "<option value=\"" . $i . "\" selected>" . $i . "</option>";

    } else {

        $year_select[] = "<option value=\"" . $i . "\">" . $i . "</option>";

    }

}

$main .= "<tr>\r\n      <td class=\"col-xs-6 col-sm-6 col-md-7 white-line\">\r\n        <h6 class=\"media-heading text-semibold\">Допустимые страны:</h6>\r\n        <span class=\"text-muted text-size-small hidden-xs\">будут добавляться публикации только определенных стран. если ничего не выбрано - то все</span>\r\n      </td>\r\n      <td class=\"col-xs-6 col-sm-6 col-md-7 white-line\"><select data-placeholder=\"Выберите страну\" name=\"config[allow_country][]\" class=\"categoryselect\" multiple=\"\" style=\"width: 100%; max-width: 350px;\">" . implode("", $country_select) . "</select></td>\r\n    </tr>";

$main .= "<tr>\r\n      <td class=\"col-xs-6 col-sm-6 col-md-7 white-line\">\r\n        <h6 class=\"media-heading text-semibold\">Допустимые года:</h6>\r\n        <span class=\"text-muted text-size-small hidden-xs\">будут добавляться публикации только определенных годов. если ничего не выбрано - то все</span>\r\n      </td>\r\n      <td class=\"col-xs-6 col-sm-6 col-md-7 white-line\"><select data-placeholder=\"Выберите год\" name=\"config[allow_year][]\" class=\"categoryselect\" multiple=\"\" style=\"width: 100%; max-width: 350px;\">" . implode("", $year_select) . "</select></td>\r\n    </tr>";

$go_moder = makecheckbox("config[go_moder]", $config_mod["go_moder"]);

$conditionEmptyDescr = makecheckbox("config[go_moder_empty_descr]", $config_mod["go_moder_empty_descr"]);

$conditionEmptyPoster = makecheckbox("config[go_moder_empty_poster]", $config_mod["go_moder_empty_poster"]);

$update_news_date = makecheckbox("config[update_news_date]", $config_mod["update_news_date"]);

$disable_index = makecheckbox("config[disable_index]", $config_mod["disable_index"]);

$enable_ads = makecheckbox("config[enable_ads]", $config_mod["enable_ads"]);

$upload_poster = makecheckbox("config[upload_poster]", $config_mod["upload_poster"]);

$main .= "<tr>\r\n      <td class=\"col-xs-6 col-sm-6 col-md-7 white-line\">\r\n        <h6 class=\"media-heading text-semibold\">Загружать постер на сайт:</h6>\r\n        <span class=\"text-muted text-size-small hidden-xs\">если включено то постер будет загружатся вам на сервер. иначе будет ссылка на сторонний ресурс</span>\r\n      </td>\r\n      <td class=\"col-xs-6 col-sm-6 col-md-7 white-line\">" . $upload_poster . "</td>\r\n    </tr>";

$main .= "<tr>\r\n    <td class=\"col-xs-6 col-sm-6 col-md-7 white-line\">\r\n        <h6 class=\"media-heading text-semibold\">\r\n            Сначала отправлять на модерацию:\r\n        </h6>\r\n        <h6 class=\"media-heading text-semibold\">\r\n            На модерацию при отсутствии описания:\r\n        </h6>\r\n        <h6 class=\"media-heading text-semibold\">\r\n            На модерацию при отсутствии постера:\r\n        </h6>\r\n        <h6 class=\"media-heading text-semibold\">\r\n            Добавлять с вшитой рекламой:\r\n        </h6>        \r\n    </td>\r\n    <td class=\"col-xs-6 col-sm-6 col-md-7 white-line\">\r\n        <div style=\"margin-bottom: 7px\">\r\n            " . $go_moder . "\r\n        </div>\r\n        <div style=\"margin-bottom: 7px\">\r\n            " . $conditionEmptyDescr . "\r\n        </div>\r\n        <div style=\"margin-bottom: 7px\">\r\n            " . $conditionEmptyPoster . "\r\n        </div>\r\n        <div>" . $enable_ads . "</div>\r\n    </td>\r\n</tr>\r\n<tr>\r\n    <td class=\"col-xs-6 col-sm-6 col-md-7 white-line\">\r\n        <h6 class=\"media-heading text-semibold\">\r\n            Поднимать новость при обновлении:\r\n        </h6>\r\n        <h6 class=\"media-heading text-semibold\">\r\n            Запретить индексацию страницы для поисковиков:\r\n        </h6>\r\n    </td>\r\n    <td class=\"col-xs-6 col-sm-6 col-md-7 white-line\">\r\n        <div style=\"margin-bottom: 7px\">\r\n            " . $update_news_date . "\r\n        </div>\r\n        <div style=\"margin-bottom: 7px\">\r\n            " . $disable_index . "\r\n        </div>\r\n    </td>\r\n</tr>";

$xfields = "";

foreach (xfieldsload() as $xfield) {

    $options = "";

    foreach ($tags_arr as $key => $value) {

        if ($xfield[3] == "yesorno" && substr($key, 0, 1) == ":" || in_array($xfield[3], array("text", "textarea", "htmljs", "select")) && substr($key, 0, 1) != ":" || in_array($xfield[3], array("image", "imagegalery")) && $key == "image") {

            $key = trim($key, ":");

            if ($key == "image" && in_array($xfield[3], array("image", "imagegalery"))) {

                $key = "xf-" . $key;

            }

            if (in_array($config_mod["xfields"][$xfield[0]], array("{" . $key . "}", "{xf-" . $key . "}"))) {

                $selected = " selected";

            } else {

                $selected = "";

            }

            $options .= "<option value=\"{" . $key . "}\"" . $selected . ">" . $value . "</option>";

        }

    }

    if ($options != "") {

        $xfields .= "<tr><td class=\"col-xs-6 col-sm-6 col-md-7 white-line\"><h6 class=\"media-heading text-semibold\">" . $xfield[1] . "</h6><span>Дополнительное поле [" . $xfield[0] . "]</span></td><td class=\"col-xs-6 col-sm-6 col-md-7 white-line\"><select name=\"config[xfields][" . $xfield[0] . "]\" style=\"width:100%;max-width:350px;\" class=\"uniform\"><option value=\" \">--- не выбрано ---</option>" . $options . "</select></td></tr>";

    }

}

$genre_arr = array("сериал", "фильм", "зарубежный", "русский", "арт-хаус", "дорама", "аниме", "аниме сериал", "биография", "боевик", "блокбастер", "вестерн", "военный", "детектив", "детский", "документальный", "драма", "игра", "исторический", "комедия", "концерт", "короткометражка", "полнометражный", "криминал", "мелодрама", "мистический", "музыка", "мультфильм", "мультсериал", "мюзикл", "новости", "путешествия", "приключения", "развлекательный", "реальное ТВ", "семейный", "спортивный", "ток-шоу", "триллер", "ужасы", "фантастика", "фильм-нуар", "фэнтези", "церемония", "эротика", "США", "Россия", "Украина", "Белоруссия", "Корея Южная", "Япония", "Франция", "Китай", "Германия", "СССР", "Турция", "Великобритания", "Индия", "Гаити", "Пуэрто Рико", "Пакистан", "Панама", "Буркина-Фасо", "Мьянма", "Монголия", "Египет", "Иордания", "Конго", "Молдова", "Нигерия", "Гана", "Албания", "Косово", "Словакия", "Армения", "Монако", "Судан", "Кения");

for ($i = date("Y") + 4; 1920 <= $i; $i--) {

    $genre_arr[] = $i;

}

$cats = "";

foreach ($cat_info as $cat) {

    $options = "";

    foreach ($genre_arr as $genre) {

        $options .= "<option value=\"" . $genre . "\"" . (in_array($genre, $config_mod["category"][$cat["id"]]) ? " selected" : "") . ">" . $genre . "</option>";

    }

    $cat_id = $cat["parentid"];

    $name = $cat["name"];

    while ($cat_id) {

        $name = $cat_info[$cat_id]["name"] . " / " . $name;

        $cat_id = $cat_info[$cat_id]["parentid"];

    }

    $cats .= "<tr><td class=\"col-xs-6 col-sm-6 col-md-7 white-line\"><h6 class=\"media-heading text-semibold\">" . $name . "</h6><span>категория [ID:" . $cat["id"] . "]</span></td><td class=\"col-xs-6 col-sm-6 col-md-7 white-line\"><select name=\"config[category][" . $cat["id"] . "][]\" style=\"width:100%;max-width:350px;\" class=\"categoryselect\" multiple>" . $options . "</select></td></tr>";

}

$tags = "";

foreach ($tags_arr as $key => $value) {

    $tags .= "<tr><td class=\"col-xs-6 col-sm-6 col-md-7 white-line\"><h6 class=\"media-heading text-semibold\">" . $value . "</h6><span></span></td><td class=\"col-xs-6 col-sm-6 col-md-7 white-line\">[if_" . $key . "] {" . $key . "} [/if_" . $key . "]<br>[ifnot_" . $key . "] данных нету [/ifnot_" . $key . "]</td></tr>";

}

echoheader($__name, $__descr);

echo "<div class=\"row\">\r\n\t<div class=\"col-md-12\">\r\n\t\t<div class=\"";

echo 12 <= $config["version_id"] ? "panel" : "box";

echo "\">\r\n\t\t    <div class=\"";

echo 12 <= $config["version_id"] ? "panel-heading" : "box-header";

echo "\">\r\n\t\t\t\t<ul class=\"nav nav-tabs ";

echo 12 <= $config["version_id"] ? "nav-tabs-solid" : "nav-tabs-left";

echo "\">\r\n\t\t\t\t\t<li class=\"active\">\r\n\t\t\t\t\t\t<a href=\"#main\" data-toggle=\"tab\">\r\n\t\t\t\t\t\t\t<i class=\"icon-cog\"></i> \r\n\t\t\t\t\t\t\tЗаголовок и метатеги\r\n\t\t\t\t\t\t</a>\r\n\t\t\t\t\t</li>\t\t\r\n\r\n\t\t\t\t\t<li>\r\n\t\t\t\t\t\t<a href=\"#xfields\" data-toggle=\"tab\">\r\n\t\t\t\t\t\t\t<i class=\"icon-cog\"></i> \r\n\t\t\t\t\t\t\tДополнительные поля\r\n\t\t\t\t\t\t</a>\r\n\t\t\t\t\t</li>\t\r\n\r\n\t\t\t\t\t<li>\r\n\t\t\t\t\t\t<a href=\"#cats\" data-toggle=\"tab\">\r\n\t\t\t\t\t\t\t<i class=\"icon-cog\"></i> \r\n\t\t\t\t\t\t\tКатегории\r\n\t\t\t\t\t\t</a>\r\n\t\t\t\t\t</li>\t\r\n\r\n\t\t\t\t\t<li>\r\n\t\t\t\t\t\t<a href=\"#tags\" data-toggle=\"tab\">\r\n\t\t\t\t\t\t\t<i class=\"icon-cog\"></i> \r\n\t\t\t\t\t\t\tТеги\r\n\t\t\t\t\t\t</a>\r\n\t\t\t\t\t</li>\t\t\t\t\r\n\t\t\t\t</ul>\r\n\t\t\t</div>\r\n\r\n\t\t\t<form id=\"config\">\r\n\t            <div class=\"box-content\">\r\n\t                <div class=\"tab-content\">  \t                  \r\n\t\t                <div class=\"tab-pane active\" id=\"main\">\r\n\t                    \t<table class=\"table table-normal\">\r\n\t\t\t\t\t\t\t    <tbody>\r\n\t\t\t\t\t\t\t\t\t";

echo $main;

echo "\t\t\t\t\t\t\t    </tbody>\r\n\t\t\t\t\t\t\t</table>\t\t\t\t\t\t\t\r\n\t                    </div>\r\n\r\n\t                    <div class=\"tab-pane\" id=\"xfields\">\r\n\t                    \t<table class=\"table table-normal\">\r\n\t\t\t\t\t\t\t    <tbody>\r\n\t\t\t\t\t\t\t\t\t";

echo $xfields;

echo "\t\t\t\t\t\t\t    </tbody>\r\n\t\t\t\t\t\t\t</table>\t\t\t\t\t\t\t\r\n\t                    </div>\r\n\r\n\t                    <div class=\"tab-pane\" id=\"cats\">\r\n\t                    \t<table class=\"table table-normal\">\r\n\t\t\t\t\t\t\t    <tbody>\r\n\t\t\t\t\t\t\t\t\t";

echo $cats;

echo "\t\t\t\t\t\t\t    </tbody>\r\n\t\t\t\t\t\t\t</table>\t\t\t\t\t\t\t\r\n\t                    </div>\r\n\r\n\t                    <div class=\"tab-pane\" id=\"tags\">\r\n\t                    \t<div style=\"padding: 25px;text-align: center;font-size: 14px\">Список тегов для вывода информации в заголовок, метатеги и теги новости. Используйте на вкладке «Заголовок и метатеги».</div>\r\n\t                    \t<table class=\"table table-normal\">\r\n\t\t\t\t\t\t\t    <tbody>\r\n\t\t\t\t\t\t\t\t\t";

echo $tags;

echo "\t\t\t\t\t\t\t    </tbody>\r\n\t\t\t\t\t\t\t</table>\t\t\t\t\t\t\t\r\n\t                    </div>\r\n\t                </div>\r\n\r\n\t                <div class=\"";

echo 12 <= $config["version_id"] ? "panel-footer" : "box-footer";

echo " padded\">\r\n\t\t\t\t\t\t<input onclick=\"save_config(); return false;\" class=\"btn ";

echo 12 <= $config["version_id"] ? "bg-teal" : "btn-green";

echo "\" type=\"submit\" value=\"Сохранить настройки\">\r\n\r\n\t\t\t\t\t\t<input onclick=\"switch_sections(); return false\" class=\"btn ";

echo 12 <= $config["version_id"] ? "btn-bluebg-primary-600" : "btn-blue";

echo "\" style=\"float: right;\" type=\"button\" value=\"Ссылка в меню «Сторонние модули» (вкл/выкл)\">\r\n\t\t\t\t\t</div>\r\n\t            </div>\r\n\t        </form>\r\n        </div>\r\n    </div>\r\n</div>\r\n\r\n<script type=\"text/javascript\">\r\nfunction save_config() {\t\r\n\t\$.post('";

echo $config["admin_path"];

echo "?mod=";

echo $mod;

echo "&action=config', \$('#config').serialize(), function(data){ \r\n\t\tdata = JSON.parse(data);\r\n\t\tDLEalert( data.message, '";

echo $__name;

echo "' );\r\n\t});  \r\n}\r\n\r\nfunction switch_sections() {\t\r\n\t\$.get('";

echo $config["admin_path"];

echo "?mod=";

echo $mod;

echo "&action=sections', null, function(data){\r\n\t\tdata = JSON.parse(data);\r\n\t\tDLEalert( data.message, '";

echo $__name;

echo "' );\r\n\t});  \r\n}\r\n\r\n\$('.categoryselect').chosen({no_results_text: 'Ничего не найдено'});\r\n</script>\r\n\r\n\r\n";

echofooter();

echo "  ";

function makeCheckBox($name, $selected)

{

    $selected = $selected ? "checked" : "";

    return "<input class=\"switch\" type=\"checkbox\" name=\"" . $name . "\" value=\"1\" " . $selected . ">";

}

function makeDropDown($options, $name, $selected)

{

    $output = "<select class=\"uniform\" style=\"min-width:100px;\" name=\"" . $name . "\">\r\n";

    foreach ($options as $value => $description) {

        $output .= "<option value=\"" . $value . "\"";

        if ($selected == $value) {

            $output .= " selected ";

        }

        $output .= ">" . $description . "</option>\n";

    }

    $output .= "</select>";

    return $output;

}

?>
