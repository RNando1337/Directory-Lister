<?php

if (array_key_exists("dir", $_GET) && empty($_GET['dir'])) {
    header("Location:" . $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"] . "/", false, 302);
} elseif (empty($_SERVER['QUERY_STRING'])) {
    header("Location:" . $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"] . "?dir=/", false, 302);
}


if (preg_match("/(?:(\.\.))/", $_GET['dir']))
    die("Unauthorized");

$directory = (@$_GET['dir'] != "") ? $_GET['dir'] : "/";

$pathFile = explode("?dir=", $_SERVER["REQUEST_URI"]);

$currentDir = __DIR__ . $directory . "/";

$listDir = scandir($currentDir);

if ($directory == "/") {
    $files = array_diff($listDir, [".", "..", "index.php"]);
} else {
    $files = array_diff($listDir, [".", "index.php"]);
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Directory Lister</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" integrity="sha512-+4zCK9k+qNFUR5X+cKL9EIR+ZOhtIloNl9GIKS57V1MyNsYpYcUrUeQc9vNfzsWfV28IaLL3i96P9sdNyeRssA==" crossorigin="anonymous" />
</head>

<body>
    <div class="container">
        <br>
        <h1 class="text-center">Directory Lister</h1>
        <br>
        <br>
        <table class="table table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Size</th>
                    <th>Last Modified</th>
                </tr>
            </thead>
            <tbody>
                <?php

                foreach ($files as $file) {
                    $filetype = filetype($currentDir . $file);
                    $filesize = filesize($currentDir . $file);
                    $filetime = date("F d Y H:i:s.", filemtime($currentDir . $file));
                    $dirPath = ($directory == "/") ? $file : "/$file";

                    if ($file == "..") {
                        $realPath = array_diff(explode("/", $pathFile[1]), [""]);
                        $realPath = array_diff($realPath, [$realPath[sizeof($realPath)]]);
                        $dirPath = implode("/", $realPath);
                    } else {
                        $dirPath = substr($pathFile[1], 1) . $dirPath;
                    }

                    if ($filetype == "dir") {
                        $file = ($file != "..") ? $file . " /" : $file;
                        $filetype = "Folder";
                        $icon = ($file != "..") ? "fa-folder" : "fa-arrow-left";
                        $href = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . $pathFile[0] . "?dir=/" . $dirPath;
                    } else {
                        $filetype = "File";
                        $icon = "fa-file";
                        $href = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . substr($pathFile[0], 0, -1) . $pathFile[1] . "/" . $file;
                    }

                    echo "<tr>
                            <td><a class='text-info text-decoration-none' href='$href'><i class='fas $icon text-secondary'></i>&nbsp;&nbsp;&nbsp;" . ucfirst($file) . "</a></td>
                            <td>$filetype</td>
                            <td>$filesize</td>
                            <td>$filetime</td>
                        </tr>";
                }


                ?>
            </tbody>
        </table>
    </div>
</body>

</html>