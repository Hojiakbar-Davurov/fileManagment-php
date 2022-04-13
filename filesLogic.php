<?php
// connect to the database
$conn = mysqli_connect('localhost', 'root', 'root', 'phpcrud');

$sql = "SELECT * FROM files Order BY createdAt desc ";

if (isset($_GET['order'])) {
    if ($_GET['order'] == "ASC") {
        $sql = "SELECT * FROM files Order BY name asc ";
    } else {
        $sql = "SELECT * FROM files Order BY name desc ";
    }
}

$result = mysqli_query($conn, $sql);

// Falyni yuklash
if (isset($_POST['yuklash'])) {
    // fayl nomini olish
    $indexFileType = strrpos($_FILES['myfile']['name'], '.');
    $filename = substr($_FILES['myfile']['name'], 0, $indexFileType);

    // fayl turini aniqlash
    $fileType = pathinfo($_FILES['myfile']['name'], PATHINFO_EXTENSION);

    // xotiraga saqlash uchun manzil
    $filePathOfMemory = 'uploads/' . $_FILES['myfile']['name'];

    // fayl o'lchamini olish
    $size = $_FILES['myfile']['size'];
    $file = $_FILES['myfile']['tmp_name'];


    if (!in_array($fileType, ['pdf', 'jpg', 'xls', 'jpeg'])) {
        echo "fayl quyidagi formatda bo'lishi kerak: .jpg, .jpeg, .pdf yoki .xls";
    } elseif ($_FILES['myfile']['size'] > 5 * 1024 * 1024) { // file shouldn't be larger than 1Megabyte
        echo "fayl hajmi 5 - MB dan kichik bo'lishi kerak !";
    } else {
        if (move_uploaded_file($file, $filePathOfMemory)) {
            $sql = "INSERT INTO files (name, size, type) VALUES ('$filename', '$size', '$fileType')";
            if (mysqli_query($conn, $sql)) {
                header("Location: index.php");
            }
        } else {
            echo "Failed to upload file.";
        }
    }
}

// Faylni download qilish
if (isset($_GET['download_id'])) {
    $id = $_GET['download_id'];

    // Faylni olib kelish
    $sql = "SELECT * FROM files WHERE id=$id";
    $result = mysqli_query($conn, $sql);

    $file = mysqli_fetch_assoc($result);
    $filepath = 'uploads/' . $file['name'];

    if (file_exists($filepath)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($filepath));
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize('uploads/' . $file['name']));

        //This part of code prevents files from being corrupted after download
        ob_clean();
        flush();

        readfile('uploads/' . $file['name']);

        // Now update downloads count
        $newCount = $file['downloads'] + 1;
        $updateQuery = "UPDATE files SET downloads=$newCount WHERE id=$id";
        mysqli_query($conn, $updateQuery);
        exit;
    }
}

// Faylni o'chirish
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];

    $sql = "DELETE FROM files WHERE id=$id";
    if (mysqli_query($conn, $sql)) {
        header("Location: index.php");
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
    mysqli_close($conn);
}

// Faylni nomi bo'yicha qidirish
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $sql = "SELECT * FROM files WHERE name LIKE '%$search%'";

    $result = mysqli_query($conn, $sql);
}

// Faylni filter qilish
if (isset($_GET['type'])) {
    $sql = "SELECT * FROM files WHERE";

    // Fayl turi bo'yicha
    $type = $_GET['type'];
    $sql .= " type LIKE '%$type%'";

    // Faylning boshlang'ich sanasi bo'yicha
    $fromDate = $_GET['fromDate'];
    if ($fromDate != null) {
        $sql .= " AND createdAt >= CAST('$fromDate' as Date)";
    }

    // Faylning oxirgi sanasi bo'yicha
    $untilDate = $_GET['untilDate'];
    if ($untilDate != null) {
        $sql .= " AND createdAt <= CAST('$untilDate' as Date)";
    }

    $size1 = $_GET['size1'];
    $size2 = $_GET['size2'];
    $size3 = $_GET['size3'];
    // 1-MB dan kichigi
    if ($size1 == "on") {
        $sql .= " AND size < 1 * 1024 * 1024";
    }

    // 1-MB ~ 2-MB dan kichigi
    if ($size2 == "on") {
        if ($size1 == "on") {
            $sql .= " OR (size > 1 * 1024 * 1024 AND size < 2 * 1024 * 1024)";
        } else
            $sql .= " AND (size > 1 * 1024 * 1024 AND size < 2 * 1024 * 1024)";
    }

    // 3-MB dan kattasi
    if ($size3 == "on") {
        if (($size1 == "on" || $size2 == "on") ||
            ($size1 == "on" && $size2 == "on")) {
            $sql .= " OR size > 3 * 1024 * 1024";
        } else
            $sql .= " AND size > 3 * 1024 * 1024";

    }

    $result = mysqli_query($conn, $sql);
}

$files = mysqli_fetch_all($result, MYSQLI_ASSOC);