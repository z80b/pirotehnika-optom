<?php
	$group_id = "161148829"; // ID группы
	$topic_id = "37012519"; // ID обсуждения
	$count = 100; // Количество комментариев, которое будет выведено
	$extended = 1; // Будут ли загружены профили
	$need_likes = 1; // Будут ли загружены лайки
	$sort = "desc"; // Отображаем с начала(asc) или конца(desc)
	$version = "5.4"; // Версия VK API (На текущий момент менять не нужно)
	$access_token ="8e6a899a8e6a899a8e6a899a708e0fd2c888e6a8e6a899ad532c43f8ecc59b80efe1d02";

 
	$page = file_get_contents("https://api.vk.com/method/board.getComments?" . "group_id=" . $group_id . "&topic_id=" . $topic_id . "&access_token=" . $access_token . "&count=" . $count . "&extended=" . $extended . "&need_likes=" . $need_likes . "&sort=" . $sort . "&v=" . $version);
 //$json = json_decode($page, true);
//echo $json;
	echo  $page;
//var_dump($json);
// Создаем новый класс Coor:






	/* Если не срабатывает php код и страница с JSON пустая, то потребуется закомментировать $page и echo $page и раскомментировать код ниже */

	// $page = "https://api.vk.com/method/board.getComments?" . "group_id=" . $group_id . "&topic_id=" . $topic_id . "&count=" . $count . "&extended=" . $extended . "&need_likes=" . $need_likes . "&sort=" . $sort . "&v=" . $version";

	// function file_get_contents_curl($url) {
  //   $ch = curl_init();
  //   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  //   curl_setopt($ch, CURLOPT_URL, $url);
  //   $data = curl_exec($ch);
  //   curl_close($ch);
  //   return $data;
	// }
	//
	// echo file_get_contents_curl($page);
?>
