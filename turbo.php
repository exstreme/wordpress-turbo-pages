<?php
header('Content-Type: text/html; charset=utf-8');

/* Config */
$siteurl="https://site.ru"; // Здесь меняем на свой сайт
$title = "My site"; // Название сайта
$description = "My description"; // Описание сайта
$email = "mymail@site.ru"; // Автор и почта
$author = "Name";
/* End of config */


// указываем, что нам нужен минимум от WP
// Можно попытаться оптимизировать, вместо get_permalink($item->ID) использовать $siteurl.'/'.$item->post_name ,
// если будут прямые ссылки и не будет родителей
//define('SHORTINIT', true);
//define('WP_DEBUG',true);

// подгружаем среду WordPress
require_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php' );

// Глобальные переменные $wp, $wp_query, $wp_the_query не установлены...
global $wpdb;
$list = $wpdb->get_results("SELECT `post_content`, `post_title`,`post_date_gmt`,`ID` 
							FROM $wpdb->posts
							WHERE `post_status` IN ('publish', 'inherit') AND `post_type` IN ('page','post')", 'OBJECT');

if(!$list) exit();

$xml='<?xml version="1.0" encoding="utf-8"?>
<rss
    xmlns:yandex="http://news.yandex.ru"
    xmlns:media="http://search.yahoo.com/mrss/"
    xmlns:turbo="http://turbo.yandex.ru"
    version="2.0">
	<channel>
		<title>'.$title.'</title>
		<description><![CDATA['.$description.']]></description>
		<link>'.$siteurl.'/</link>
		<lastBuildDate>'.date(DATE_ATOM).'</lastBuildDate>
		<language>ru-ru</language>
		<managingEditor>'.$email.' ('.$author.')</managingEditor>';
foreach($list as $item) {
    $xml.='
			<item turbo="true">
			<title>'.$item->post_title.'</title>
			<link>'.get_permalink($item->ID).'</link>
			<turbo:content><![CDATA['.htmlspecialchars_decode($item->post_content);
    $xml.='
			<p>Для просмотра и добавления комментариев посетите полную версию сайта по ссылке ниже!</p>]]></turbo:content>
			<author>'.$author.'</author>
			<pubDate>'.$item->post_date_gmt.'</pubDate>
		</item>';
}
$xml.='</channel>
</rss>';
//echo $xml;
if (file_put_contents($_SERVER['DOCUMENT_ROOT'].'/turbo.xml', $xml))
{
    echo "XML файл сгенерирован";
}
else
{
    echo "Неизвестная ошибка";
}
?>