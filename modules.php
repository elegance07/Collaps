<?php

/*

 * @ https://kino-xa.ru

 Магазин раскодированных модулей. Скоро будет работать

 */

require_once DLEPlugins::Check(ENGINE_DIR . "/modules/collaps.func.php");

$config_mod = unserialize(file_get_contents(ENGINE_DIR . "/data/collaps.config"));

if (!$config_mod) {

    $config_mod = array();

}

$hostName = strtolower(substr(getenv("HTTP_HOST"), 0, 4)) == "www." ? substr(getenv("HTTP_HOST"), 4) : getenv("HTTP_HOST");

$ads_xf = preg_replace("#[\\{\\}]#", "", $config_mod["xfields"]["instream_ads"]);

if ($_POST["q_search"]) {

    if (1 < $member_id["user_group"]) {

        exit("{\"error\": \"for admin user only\"}");

    }

    $q_search = trim($_POST["q_search"]);

    if (preg_match("/[^0-9]/", $q_search)) {

        $res = request("https://api.themoviedb.org/3/search/movie?api_key=" . $config_mod["api_token"] . "&query=" . urlencode($q_search));

    } else {

        $res = request("https://api.themoviedb.org/3/search/tv?api_key=" . $config_mod["api_token"] . "&query=" . urlencode($q_search));

    }

    $res = json_decode($res, true);

    if (!count($res["results"])) {

        exit("{\"error\": \"Sonuç yok\"}");

    }

    if (!empty($res["message"])) {

        exit($res["message"]);

    }

    foreach ($res["results"] as $value) {

        $data[] = "<div class=\"collaps_item\" data-id=\"" . $value["kinopoisk_id"] . "\"><span class=\"kp-id\" title=\"kinopoisk id\">" . $value["kinopoisk_id"] . "</span> <span title=\"Çekmek için tıklayın\">" . $value["title"] . "</span></div>";

    }

    echo json_encode(array("ok" => true, "result" => implode("\n", $data)));

    exit;

} else {

    if ($_POST["kp_id"] && $_POST["action"] == "parse") {

        $res = request("http://api.themoviedb.org/3/movie/".intval($_POST["kp_id"]."?api_key=" .$config_mod["api_token"]));

        $res = json_decode($res, true);

        if (!empty($res["message"])) {

            exit($res["message"]);

        }

        if (($premier = strtotime($res["premier"])) !== false && $res["premier"]) {

            $res["premier"] = date("j", $premier) . " " . $langdate[date("F", $premier)] . " " . date("Y", $premier);

        }

        if (($premier_rus = strtotime($res["premier_rus"])) !== false && $res["premier_rus"]) {

            $res["premier_rus"] = date("j", $premier_rus) . " " . $langdate[date("F", $premier_rus)] . " " . date("Y", $premier_rus);

        }

        $request = array();

        $genres = $res["genres"];

        $ex_genres = array();

        if ($res["type"] == "series") {

            $ex_genres[] = "сериал";

            $request["video_type"] = "сериал";

        } else {

            if ($res["type"] == "film") {

                $ex_genres[] = "фильм";

                $request["video_type"] = "фильм";

            } else {

                if ($res["type"] == "anime-film") {

                    $ex_genres[] = "аниме";

                    $request["video_type"] = "аниме";

                } else {

                    if ($res["type"] == "cartoon") {

                        $ex_genres[] = "мультфильм";

                        $request["video_type"] = "мультфильм";

                    } else {

                        if ($res["type"] == "cartoon-series") {

                            $ex_genres[] = "мультсериал";

                            $request["video_type"] = "мультсериал";

                        } else {

                            if ($res["type"] == "anime-series") {

                                $ex_genres[] = "аниме сериал";

                                $request["video_type"] = "аниме сериал";

                            }

                        }

                    }

                }

            }

        }

        $cats = array();

        $genres = fixGenres((array) $res["genres"]);

        $inter = array_merge($ex_genres, $genres, (array) $res["country"], array($res["year"]));

        foreach ($config_mod["category"] as $cat_id => $values) {

            $f = true;

            foreach ($values as $value) {

                if (!in_array($value, $inter)) {

                    $f = false;

                    break;

                }

            }

            if ($f) {

                $cats[] = $cat_id;

            }

        }

        $config_mod["category"] = implode(",", $cats);

        $request["title_ru"] = $res["title"];

        $request["title_en"] = !empty($res["original_title"]) ? $res["original_title"] : "";

        $request["year"] = $res["year"];

        $request["description"] = html_entity_decode($res["description"]);

        $request["countries"] = implode(", ", $res["country"]);

        $request["genres"] = implode(", ", $res["genres"]);

        $request["actors"] = implode(", ", $res["actors"]);

        $request["actors_dubl"] = implode(", ", $res["actors_dubl"]);

        $request["directors"] = implode(", ", $res["director"]);

        $request["collection"] = implode(", ", $res["collection"]);

        $request["parts"] = implode(", ", $res["parts"]);

        $request["iframe_url"] = $res["iframe_url"];

        $request["quality"] = $res["quality"];

        $request["budget"] = $res["budget"];

        $request["slogan"] = $res["slogan"];

        $request["trivia"] = $res["Знаете ли вы…"];

        $request["fees_rus"] = $res["fees_rus"];

        $request["fees_use"] = $res["fees_use"];

        $request["fees_world"] = $res["fees_world"];

        $request["design"] = $res["design"];

        $request["editor"] = $res["editor"];

        $request["operator"] = $res["operator"];

        $request["producer"] = $res["producer"];

        $request["id"] = $res["id"];

        $request["screenwriter"] = $res["screenwriter"];

        $request["translator"] = !empty($res["voiceActing"]) ? implode(", ", $res["voiceActing"]) : "";

        $request["premiere_ru"] = $res["premier_rus"];

        $request["premiere_world"] = $res["premier"];

        $request["rating_kp"] = $res["kinopoisk"];

        $request["rating_imdb"] = $res["imdb"];

        $request["rating_world_art"] = $res["world_art"];

        $request["rate_mpaa"] = $res["rate_mpaa"];

        $request["kinopoisk_id"] = $res["kinopoisk_id"];

        $request["imdb_id"] = $res["imdb_id"];

        $request["world_art_id"] = $res["world_art_id"];

        $request["age"] = $res["age"];

        $request["time"] = $res["time"];

        $request["trailer"] = count($res["trailers"]) ? $res["trailers"][0]["iframe_url"] : "";

        $request["instream_ads"] = $res["ads"] == "" ? "" : 1;

        if ($request["title_en"] == $request["title_ru"]) {

            $request["title_en"] = "";

        }

        $is_serial = false;

        if (in_array($res["type"], array("series", "anime-series", "cartoon-series"))) {

            $seasons = array();

            foreach ($res["seasons"] as $s) {

                $seasons[] = $s["season"];

            }

            rsort($seasons);

            $season = $seasons[0];

            $episodes = array();

            $arr = 1 < count($seasons) ? $seasons[0] - 1 : 0;

            foreach ($res["seasons"][$arr]["episodes"] as $e) {

                if ($e["iframe_url"] == "") {

                    continue;

                }

                $episodes[] = $e["episode"];

            }

            rsort($episodes);

            $episode = explode("-", $episodes[0]);

            rsort($episode);

            $episode = $episode[0];

            $request["last_season"] = 0 < intval($season) ? intval($season) : "";

            $request["last_episode"] = 0 < intval($episode) ? intval($episode) : "";

            $is_serial = true;

        }

        if ($is_serial) {

            $request["serial_status"] = $serial_statuses[$res["serial_status"]];

        }

        if ($config_mod["upload_poster"]) {

            $poster_file = $_POST["kp_id"] . "_" . time();

            $poster = request($res["poster"], ROOT_DIR . "/uploads/posts/" . FOLDER_PREFIX . "/" . $poster_file);

            if ($poster) {

                $request["poster"] = str_replace(ROOT_DIR . "/", $config["http_home_url"], $poster);

                $poster = str_replace(ROOT_DIR . "/uploads/posts/", "", $poster);

            }

        } else {

            $request["poster"] = $res["poster"];

        }

        $news_id = intval($_POST["news_id"]);

        $author = isset($_POST["author"]) ? $_POST["author"] : $member_id["name"];

        $author = $db->safesql($author);

        $old_poster = !empty($_POST["poster"]) ? $_POST["poster"] : "";

        $images_row = $db->super_query("SELECT * FROM " . PREFIX . "_images WHERE news_id='" . $news_id . "' AND author='" . $author . "'");

        if ($old_poster && preg_match("#/uploads/posts/(\\d{4}-\\d{2}/[^/]+)#i", $old_poster, $find) && $images_row) {

            @unlink(ROOT_DIR . "/uploads/posts/" . $find[1]);

            $new_images = preg_replace("#((^|\\|\\|\\|)?" . $find[1] . "(\$|\\|\\|\\|))#", "", $images_row["images"]);

            $new_images = $poster ? $new_images ? $new_images . "|||" . $poster : $poster : $new_images;

            $db->query("UPDATE " . PREFIX . "_images SET images='" . $new_images . "' WHERE id='" . $images_row["id"] . "'");

        }

        if ($poster && !$images_row) {

            $db->query("INSERT INTO " . PREFIX . "_images (images,news_id,author,date) VALUES('" . $poster . "','" . $news_id . "','" . $author . "','" . $_TIME . "')");

        }

        $compile = template($config_mod, $request);

        if ($news_id) {

            $compile["meta_title"] = $compile["metatitle"];

            unset($compile["short_story"]);

            unset($compile["full_story"]);

        }

        unset($compile["api_token"]);

        unset($compile["allow_year"]);

        unset($compile["metatitle"]);

        unset($compile["lickey"]);

        unset($compile["api_token"]);

        echo json_encode(array("ok" => true, "result" => $compile));

        exit;

    } else {

        echo "\r\n\r\n\r\n\r\n<script type=\"text/javascript\">\r\n\$(function(){\r\n\tvar regexp = /id=(\\d+)/;\r\n\tvar href = window.location.href;\r\n\tvar news_id = regexp.test(href) ? href.match(regexp)[1] : 0;\r\n\tvar author = \$(\"input[name='old_author']\").val();\r\n\tvar ads_xf = '";

        echo $ads_xf;

        echo "';\r\n\t\$('#related_news').before('<div id=\"parser_btns\"></div>');\r\n    \$('#parser_btns').append('<button id=\"parse_start\" class=\"visible-lg-inline-block btn bg-info-800 btn-sm btn-raised legitRipple\">Arama ve Çek</button>&nbsp;');\r\n    \$(document).on(\"click\", \"#parse_start\", function(){\r\n        var btn = \$(this);\r\n        btn.attr(\"disabled\", \"disabled\").text('Yükleniyor...');\r\n        var q_search = \$('#title').val();\r\n        \$.post(\"?mod=collaps\", {q_search:q_search,news_id:news_id,author:author}, function(data){\r\n            if(data.error) {\r\n            \tGrowl.info({\r\n            \t    title: 'Bilgi',\r\n            \t    text: data.error\r\n            \t});\r\n            }\r\n            if(data.ok){\r\n\t\t\t\t\$(\"#collaps_results\").remove();\r\n                \$(\"body\").append(\"<div id='collaps_results' title='Arama Sonuçları' style='display:none'>\"+data.result+\"</div>\");\r\n                var b = {};\r\n                b[\"Kapat\"] = function() {\r\n                    \$(this).dialog(\"close\")\r\n                };\r\n                \$(\"#collaps_results\").dialog({\r\n                    autoOpen: 1,\r\n                    width: 500,\r\n                    height: 310,\r\n                    resizable: !1,\r\n                    buttons: b\r\n                });                \r\n            }\r\n            btn.removeAttr(\"disabled\").text('Arama ve Çekme');\r\n        },\"json\");\r\n        return false;\r\n    })\r\n    .on(\"click\", \".collaps_item\", function(){\r\n ShowLoading('');\r\n    \t\$(\"#collaps_results\").remove();\r\n        var t = \$(this), kp_id = t.data('id'), media_type = t.data('media_type'),poster = \$('xf_poster').val() || \$('[name=\"xfield[poster]\"]').val();\r\n        \$.post(\"?mod=collaps\", {action: 'parse',media_type:media_type, kp_id: kp_id,news_id:news_id,author:author, poster:poster}, function(data){\r\n            if(data.error) {\r\n            \tGrowl.info({\r\n            \t    title: 'Bilgi',\r\n            \t    text: data.error\r\n            \t});\r\n            }\r\n            if(data.result){\r\n                \$.each(data.result, function(key,val) {\r\n                \t\$('[name=\"'+key+'\"]').val(val);\r\n                \tif(\$.inArray(key, ['tags', 'keywords'] )>-1) {\r\n                \t\t\$('[name=\"'+key+'\"]').tokenfield('setTokens', val);\r\n                \t}\r\n                \tif(\$('#'+key).attr('data-rel')=='links'){\r\n                \t\t\$('#'+key).tokenfield('setTokens', val);\r\n                \t}\r\n                \tif ( key == \"short_story\" || key == \"full_story\" ) {\r\n                \t\tif (typeof \$.fn.froalaEditor != 'undefined') {\r\n                \t\t\t\$('#' + key).froalaEditor('html.set', val);\r\n                \t\t} else \$('#' + key);\r\n                \t}\r\n                });\r\n                \$.each(data.result['xfields'], function(key,val) {\r\n                \tif(\$('#xf_'+key).attr('data-rel')=='links'){\r\n                \t\t\$('#xf_'+key).tokenfield('setTokens', val);\r\n                \t} else {\r\n                \t\t\$('#xf_'+key).val(val)\r\n                \t}\r\n                });\r\n                var cats = data.result['category'].split(',');\r\n                var opt = \$('#category option');\r\n                opt.each(function(indx, element){\r\n                    \$(this).removeAttr('selected');\r\n                });\r\n                for (var i = 0; i < cats.length; i++) {\r\n                    opt.each(function(indx, element){\r\n\t                    var o = \$(this).val();\r\n\t                    if ( \$.trim(o) == \$.trim(cats[i] ) ) {\$(this).prop(\"selected\", true);}\r\n                    });\r\n                }\r\n                \$('#category').trigger(\"chosen:updated\");\r\n                if (!ads_xf) return;\r\n                var ads = \$('[name=\"xfield['+ads_xf+']\"]'), is_ads = ads.prop('checked');  \r\n                if (data.result['xfields'][ads_xf] == '1') {\r\n                \tif (!is_ads) ads.trigger('click');\r\n                } else {\r\n                \tif (is_ads) ads.trigger('click');\r\n                }\r\n            }\r\n        },\"json\");\r\n        return false;\r\n    });   \r\n});\r\n</script>\r\n\r\n\r\n<style>\r\n#collaps_results {\r\n\tcounter-reset: li;\r\n}\r\n.collaps_item {\r\n    cursor: pointer;\r\n    font-size: 15px;\r\n    margin-bottom: 4px;\r\n    text-overflow: ellipsis;\r\n    overflow: hidden;\r\n    white-space: nowrap;\r\n}\r\n.collaps_item:hover {\r\n\tcolor: yellowgreen;\r\n}\r\n.collaps_item .kp-id {\r\n    font-size: 12px;\r\n    color: cadetblue;\r\n    min-width: 50px;\r\n    display: inline-block;\r\n}\r\n.collaps_item:before {\r\n    content: counter(li);\r\n    display: inline-block;\r\n    height: 28px;\r\n    width: 28px;\r\n    border: 3px solid yellowgreen;\r\n    margin-right: 8px;\r\n    counter-increment: li;\r\n    text-align: center;\r\n    border-radius: 20px;\r\n    line-height: 1.7;\r\n    font-size: 12px;\r\n}\r\n</style>";

    }

}

?>
