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
        $str= explode("|", $nal);
        $razn =floor(( strtotime(date("Y-m-d H:i:s")) - strtotime($str[1]) )/60);
        if ($razn>30) {
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
                echo "<html lang='en'>
<head>
	<meta charset='utf-8'>
	<title>PDF to HTML Slider</title>
	<link rel='stylesheet' href='assets/slider.css' type='text/css'>
	<script src='assets/jquery.min.js'></script>
	<script src='assets/flexslider-min.js'></script>
<style>body{background:#f2f2f2 no-repeat center top;margin:0}.flex-container{position: relative;margin: 0 auto; 
padding: 20px;}a.knopka {  color: #fff;text-decoration: none;user-select: none;background: rgb(212,75,56);
padding: .7em 1.5em;outline: none;} .down{margin-top:70px;}</style>
<meta name='robots' content='noindex,follow' />
</head>
<body>

<div class='flex-container'>
	<div class='flexslider'>
		<ul class='slides'>";
            foreach ($files as $elem) {
                echo "<li><img src='slider/".$dir_img."/image/".$elem."'></li>";
            }
                echo"</ul>
</div></div>
<div class='down'><center><a href='http://pdf.youedu.ru/index.html' class='knopka'>Главная страница</a>
&nbsp;&nbsp;<a href='http://pdf.youedu.ru/download.php?sl=".$dir_img."' class='knopka'>Скачать</a></center></div>
<script>
$(document).ready(function () {
	$('.flexslider').flexslider({
		animation: 'fade',
		controlsContainer: '.flexslider'
	});
});
</script>
</body>
</html>";
        }
    }
}
