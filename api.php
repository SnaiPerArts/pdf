<?php
header("Content-type: text/html; charset=windows-1251");
date_default_timezone_set('Europe/Moscow');
$dir_img = $_GET['sl'];
if ($dir_img =='' or strlen($dir_img) != 10) {
    header('Location: index.html');
} else {
    $contents = file_get_contents("slide.txt");
    $pattern = "/^.*$dir_img.*$/m";
    $nal='';
    if (preg_match_all($pattern, $contents, $matches)) {
        $nal = implode(" ", $matches[0]);
    }
    if ($nal =='') {
        header('Location: index.html');
    } else {
        $greet = function ($dir, $sort = 0) {
            $list = scandir($dir, $sort);
            if (!$list) {
                return false;
            }
            if ($sort == 0) {
                unset($list[0], $list[1]);
            } else {
                unset($list[count($list)-1], $list[count($list)-1]);
            }
            return $list;
        };
        $dir = "slider/".$dir_img."/image";
        $files = $greet($dir);
        natcasesort($files);

        $data['slider'] = $dir_img;

        foreach ($files as $elem) {
                $data['image'][] = "http://".$_SERVER['SERVER_NAME']."/slider/".$dir_img."/image/".$elem;
        }
    }
}
echo json_encode($data);
