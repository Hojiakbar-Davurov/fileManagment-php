<?php include 'filesLogic.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="MyCss.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Fayl CRUD</title>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="search">
            <form action="index.php">
                <input width="100px" class="forInput" type="search" id="mySearch" name="search"
                       placeholder="Search the site..." size="30">
                <button>izlash</button>
            </form>
        </div>
        <div class="upload">
            <form action="index.php" method="post" enctype="multipart/form-data">
                <h3>Fayl yuklash</h3>
                <input class="forInput" type="file" name="myfile"> <br>
                <button type="submit" name="yuklash">yuklash</button>
            </form>
            <br>
            <form action="index.php">
                <input class="asc" type="submit" name="order" value="ASC">
                <input class="asc" type="submit" name="order" value="DESC">
            </form>

        </div>

        <form action="index.php">
            <div class="filter1">
                <h3>Filter</h3>
                <input type="checkbox" id="size1" name="size1">
                <label for="size1">1-MB kichik</label><br>
                <input type="checkbox" id="size2" name="size2">
                <label for="size2">1-MB ~ 2-MB</label><br>
                <input type="checkbox" id="size3" name="size3">
                <label for="size3">3-MB katta</label><br>
            </div>

            <div class="filter2">
                <input class="forInput" type="date" name="fromDate">
                <input class="forInput" type="date" name="untilDate">
            </div>
            <div class="filter2">
                <select class="forInput" name="type" id="type">
                    <option value="">Fayl turini tanlang...</option>
                    <option value="pdf">pdf</option>
                    <option value="jp">image</option>
                    <option value="xls">exel</option>
                </select>
                <button>Izlash</button>
            </div>
        </form>
    </div>

    <table id="customers">
        <thead>
        <tr>
            <th width="50%">Name</th>
            <th width="10%">Type</th>
            <th width="10%">Size</th>
            <th width="20%">Date</th>
            <th width="3%"></th>
            <th width="3%"></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($files as $file): ?>
            <tr>

                <td><?php
                    echo $file['name'];
                    ?>
                </td>

                <td><?php
                    echo $file['type']; ?>
                </td>

                <td><?php echo floor($file['size'] / 1024) . ' KB'; ?></td>

                <td><?php echo $file['createdAt']; ?></td>

                <td>
                    <a class="fa fa-download" href="index.php?download_id=<?php echo $file['id'] ?>"></a>
                </td>
                <td>
                    <a class="fa fa-trash" href="index.php?delete_id=<?php echo $file['id'] ?>"></a>
                </td>
            </tr>
        <?php endforeach; ?>

        </tbody>
    </table>
</div>
</div>


</body>
</html>