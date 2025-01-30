<?php
// error ko lagi
error_reporting(E_ALL);
ini_set('display_errors', '1');

$serverName = "";
$userName = "";
$password = ""; 

$conn = mysqli_connect($serverName, $userName, $password);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$createDatabase = "CREATE DATABASE IF NOT EXISTS movieapp";
if (!mysqli_query($conn, $createDatabase)) {
    die("Error creating database: " . mysqli_error($conn));
}


mysqli_select_db($conn,"movieapp" );

$createTable = "CREATE TABLE IF NOT EXISTS movies (
    search_term VARCHAR(255) NOT NULL,
    title VARCHAR(255) NOT NULL,
    release_year INT,
    poster VARCHAR(255),
    details TEXT
);";

if (!mysqli_query($conn, $createTable)) {
    die("Error creating table: " . mysqli_error($conn));
}

if (isset($_GET["q"])) {
    $movieTitle = trim(mysqli_real_escape_string($conn, $_GET["q"]));
} else {
    $movieTitle = "Shawshank redemption";
}

$selectAllData = "SELECT * FROM movies WHERE search_term = '$movieTitle' OR title = '$movieTitle'";
$result = mysqli_query($conn, $selectAllData);
if (mysqli_num_rows($result) > 0) {
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    header('Content-Type: application/json');
    echo json_encode($rows);
} else {
    $apiKey = "";
    $url = "https://www.omdbapi.com/?apikey=$apiKey&t=$movieTitle";
    
    $response = @file_get_contents($url);

    if ($response === false) {
        echo json_encode(["error" => "failed fetching data"]);
        exit();
    }

    $data = json_decode($response, true);

    if (isset($data)) {
        $title = mysqli_real_escape_string($conn, $data['Title']);
        $release_year = intval($data['Year']);
        $poster = mysqli_real_escape_string($conn, $data['Poster']);
        $details = mysqli_real_escape_string($conn, $data['Plot']);

        $insertData = "INSERT INTO movies (search_term, title, release_year, poster, details) 
        VALUES ('$movieTitle', '$title', '$release_year', '$poster', '$details')";

        if (mysqli_query($conn, $insertData)) {
            $selectAllData = "SELECT * FROM movies WHERE search_term = '$movieTitle'";
            $result = mysqli_query($conn, $selectAllData);
            $rows = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $rows[] = $row;
            }
            header('Content-Type: application/json');
            echo json_encode($rows);
        } else {
            echo json_encode(["error" => ":" . mysqli_error($conn)]);
        }
    } else {
        echo json_encode(["error" => "error response"]);
    }
}

mysqli_close($conn);
?>

