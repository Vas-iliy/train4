<?php
$connection = new PDO('mysql:host=localhost; dbname=train6; charset=utf8', 'root', 'root');

//для массовой загрузки файлов
if (isset($_POST['go'])) {
    $files = array();
    $diff = count($_FILES['file']) - count($_FILES['file'], COUNT_RECURSIVE);
    if ($diff == 0) {
        $files = array($_FILES['file']);
    } else {
        foreach($_FILES['file'] as $k => $l) {
            foreach($l as $i => $v) {
                $files[$i][$k] = $v;
            }
        }
    }
    foreach ($files as $file) {

        $fileName = strval($file['name']);
        $fileType = strval($file['name']);
        $fileTmp_name = strval($file['tmp_name']);
        $fileError = strval($file['error']);
        $fileSize = strval($file['size']);
        $fileExtension = strtolower(end(explode('.', $fileName)));

        if (count(explode('.', $fileName))>2) {
            for ($i=0; $i == count(explode('.', $fileName))-2; $i++) {
                $fileName .= explode('.', $fileName)[$i] . '.';
            }
        }
        else {
            $fileName = explode('.', $fileName)[0];
        }

        $fileName = preg_replace('/[0-9]/' , '', $fileName);

        $fileExtensionArr = ['jpg', 'jpeg', 'png'];

        if (in_array($fileExtension, $fileExtensionArr)) {
            if ($fileSize < 5000000) {
                if ($fileError == 0) {
                    $connection->query("INSERT INTO images (name, extension) VALUES ('$fileName', '$fileExtension')");

                    $lastId = $connection->query("SELECT MAX(id) FROM images");
                    $lastId = $lastId->fetchAll();
                    $lastId = $lastId[0][0];

                    $fileNameNew = $lastId . $fileName . '.' . $fileExtension;
                    $fileDestination = 'uploads/' . $fileNameNew;
                    move_uploaded_file($fileTmp_name, $fileDestination);


                } else {
                    echo 'Что-то пошло не так :(';
                }
            } else {
                echo 'Слишком большой размер файла';
            }
        } else {
            echo 'Неверный тип файла';
        }
    }

}

/*echo "<pre>";
var_dump($_FILES);
echo "</pre>";*/

/*if ($_POST) {
    header("Location:index.php");
}*/

$data = $connection->query("SELECT * FROM images");

echo "<div>";
foreach ($data as $img) {
    $image = "uploads/" . $img['id'] . $img['name'] . '.' . $img['extension'];
    if (file_exists($image)) {
        echo "<div>";
        echo "<img width='300' src='$image'>";
        echo "<form method='post'> <input type='submit' name='delete" . $img['id'] . "' value='Удалить'></form>";
        echo "<div>";
    }

    $delete = "delete" . $img['id'];
    if (isset($_POST[$delete])) {
        $deleteImg = $img['id'];
        $connection->query("DELETE FROM images WHERE id = '$deleteImg'");

        if (file_exists($image)) {
            unlink($image);
        }
    }
}

echo "</div>";

?>



<form method="post", enctype="multipart/form-data">
    <input type="file" multiple name="file[]"  required>
    <input type="submit" value="Добавить" name="go">
</form>
