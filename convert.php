<?php
header("Content-type: text/html; charset=windows-1251");
if ($_FILES['userfile']['tmp_name'] == '') {
    echo "Выберите файл!";
} else {
    $uploaddir = $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'pdf'.DIRECTORY_SEPARATOR;
    $uploadfile = $uploaddir . basename($_FILES['userfile']['name']);
    move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile);
    if (substr(strrchr($_FILES['userfile']['name'], '.'), 1) != 'pdf') {
        echo "Выберите PDF файл!";
    } else {
        $pdf_file = $uploadfile;
        $im = new imagick($pdf_file);
        $im ->writeimage("file.jpg");
        $size = getimagesize("file.jpg");
        @unlink("file.jpg");
        if ($size[0] > $size[1]) {
            $orient="g";
        } else {
            $orient="v";
        }
        $pages=$im->getiteratorindex() +1;
        $size = round(filesize($pdf_file)/1024/1024, 2);
        if ($size>50 or $pages>20 or $orient=='g') {
            $out = "Файл не соответствует требованиям:\n Размер файла не более 50 Мб \n 
			Страниц не более 20 \n Ориентация портретная(вертикальная)";
            echo $out;
        } else {
            date_default_timezone_set('Europe/Moscow');
            $filename = substr(md5(microtime()), 0, 10);
            ;
            mkdir("slider/".$filename);
            mkdir("slider/".$filename."/image");
            $i=0;
            foreach ($im as $_img) {
                $i++;
                $_img->setResolution(300, 300);
                $_img->setImageFormat('jpeg');
                $_img->writeimage('slider/'.$filename.'/image/image-'.$i.'.jpg');
            }
            $im->destroy();
            $f = fopen("slide.txt", "a+");
            $localtime = date("Y-m-d H:i:s");
            fwrite($f, $filename."|".$localtime."\n");
            fclose($f);
            echo $filename;
            exit();
        }
    }
}
