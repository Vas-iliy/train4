<?php
$connection = new PDO('mysql:host=localhost; dbname=train6; charset=utf8', 'root', 'root');

if (isset($_POST['go'])) {
    $fileName = $_FILES['file']['name'];
    $fileType = $_FILES['file']['type'];
    $fileTmp_name = $_FILES['file']['tmp_name'];
    $fileError = $_FILES['file']['error'];
    $fileSize = $_FILES['file']['size'];

    $fileExtension = strtolower(end(explode('.', $fileName)));

    if (count(explode('.', $fileName))>2) {
        $n = count(explode('.', $fileName))-2;
        for ($i=0; $i==$n; $i++) {
            $fileName .= explode('.', $fileName)[$i] . '.';
        }
    } else {
        $fileName = explode('.', $fileName)[0];
    }

    $fileName = preg_replace('/[0-9]/', '', $fileName);
    $fileExtensionArr = ['jpg', 'jpeg', 'png'];

    if (in_array($fileExtension, $fileExtensionArr)) {
        if ($fileSize<5000000) {
            if ($fileError == 0) {
                $connection->query("INSERT INTO images (name, extension) VALUES ('$fileName', '$fileExtension')");

                $lastId = $connection->query("SELECT MAX(id) FROM images");
                $lastId = $lastId->fetch();
                $lastId = $lastId[0];

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

if ($_POST) {
    header("Location:index.php");
}

$data = $connection->query("SELECT * FROM images");

echo "<div>";
foreach ($data as $img) {
    $image = "uploads/" . $img['id'] . $img['name'] . '.' . $img['extension'];
    if (file_exists($image)) {
        echo "<div>";
        echo "<img width='300' src='$image'>";
        echo "<form method='post'> <input type='submit' name='delete". $img['id'] ."' value='Удалить'></form>";
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
    <input type="file" name="file"  required>
    <input type="submit" value="Добавить" name="go">
</form>
