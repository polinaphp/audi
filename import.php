<?php
/** @var PDO $pdo */
$pdo = require $_SERVER['DOCUMENT_ROOT'] . '/db.php';
$brands = $pdo->prepare("INSERT INTO brands (id, name, url, bold, done) VALUES(?, ?, ?, ?, ?)"); //подготовленный запрос для вставки данных в таблицу
$models = $pdo->prepare("INSERT INTO models (brands_id, name, url, hasPanorama, done) VALUES(?, ?, ?, ?, ?)");
$generations = $pdo->prepare("INSERT INTO generations (model_id, src, src2x, url, title , generationInfo, isNewAuto, isComingSoon, frameTypes, group_name, group_salug, group_short) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$complectations = $pdo->prepare("INSERT INTO complectations (generation_id, name, url, group_name) VALUES(?, ?, ?, ?)");

$content = file_get_contents('Audi.json'); //читает содержимое базы и сохраняет его в переменную
$array = json_decode($content, true); // преобразует строки в массив

$bold= $array['bold'] ? 1 : 0; // если истинно присвается значение 1 иначе 0

 $brands->execute([$array['id'], $array['name'], $array['url'], $bold, $array['done']]);// подготовленный запрос для того что бы вставить данные о бренде
$brand_id =$pdo ->lastInsertId(); // ид послдней записи в таблице бренд
if(!$brand_id) {
    die("Ошибка добавления данных"); //если условие выполняется то работа завершается и сообщает о ошибке
}
foreach ($array['models'] as $item) {  //перебор массив моделей
    $hasPanorama = $item['hasPanorama'] ? 1 : 0; //преобразование знечений
    $done = $item['done'] ? 1 : 0; //преобразование знечений
    $models->execute([$brand_id, $item['name'], $item['url'], $hasPanorama, $done]); //вставляет данные о модели в таблицу
    $model_id = $pdo -> lastInsertId(); // ид последней вставленной модели


     foreach ($item['generations'] as $generation) {
         $isNewAuto = $generation['isNewAuto'] ? 1 : 0;
         $isComingSoon = $generation['isComingSoon'] ? 1 : 0;


         $generations->execute([$model_id, $generation['src'], $generation['src2x'], $generation['url'], $generation['title'], $generation['generationInfo'], $isNewAuto, $isComingSoon, $generation['frameTypes'], $generation['group'], $generation['groupSalug'], $generation['groupShort']]);
         $generation_id = $pdo -> lastInsertId();
         foreach ($generation['complectations'] as $complectation) {

             $complectations->execute([$generation_id, $complectation['name'], $complectation['url'], $complectation['group']]); //вставка данных в таблицу комплектации
//var_dump(($complectations));
         }
     }
}
