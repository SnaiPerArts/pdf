<?php
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
                $html ="<html lang='en'><head><meta charset='utf-8'><title>PDF to HTML Slider</title>
				<link rel='stylesheet' href='assets/slider.css' type='text/css'>
<script src='assets/jquery.min.js'></script>	<script src='assets/flexslider-min.js'></script>
<style>body{background:#f2f2f2 no-repeat center top;margin:0}.flex-container{position: relative;
margin: 0 auto; padding: 20px;}</style>
<meta name='robots' content='noindex,follow' />
</head>
<body><div class='flex-container'><div class='flexslider'><ul class='slides'>";
            foreach ($files as $elem) {
                $html .= "<li><img src='image/".$elem."'></li>";
            }
                $html .="</ul></div></div>
<script>
$(document).ready(function () {
	$('.flexslider').flexslider({
		animation: 'fade',
		controlsContainer: '.flexslider'
	});
});
</script>
</body></html>";

                $fp = fopen("slider/".$dir_img."/index.html", "w");
                fwrite($fp, $html);
                fclose($fp);

                $pathimage = realpath("slider/".$dir_img."/image");
                $pathassets = realpath("assets");
                $zip_name = "slider/".$dir_img."/".$dir_img.".zip";

                $zip = new ZipArchive();
                $zip->open($zip_name, ZIPARCHIVE::CREATE);
                $zip->addFile("slider/".$dir_img."/index.html", "index.html");
                $imag = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($pathimage),
                    RecursiveIteratorIterator::LEAVES_ONLY
                );
            foreach ($imag as $name => $file) {
                if (!$file->isDir()) {
                    $fileimag = $file->getRealPath();
                    $relativeimag = substr($fileimag, strlen($pathimage) + 1);
                    $zip->addFile($fileimag, "image/".$relativeimag);
                }
            }
                $asset = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($pathassets),
                    RecursiveIteratorIterator::LEAVES_ONLY
                );
            foreach ($asset as $name => $file) {
                if (!$file->isDir()) {
                    $fileasset = $file->getRealPath();
                    $relativeasset = substr($fileasset, strlen($pathassets) + 1);
                    $zip->addFile($fileasset, "assets/".$relativeasset);
                }
            }
                $zip->close();
            if (file_exists($zip_name)) {
                header('Content-type: application/zip');
                header('Content-Disposition: attachment; filename="'.$dir_img.".zip".'"');
                readfile($zip_name);
                unlink($zip_name);
                unlink("slider/".$dir_img."/index.html");
            }
        }
    }
}
