<?php
$connectionException = false;

require(dirname(__FILE__)."/env.php");
require(dirname(__FILE__)."/libs.php");


$loader = new \Twig\Loader\FilesystemLoader(dirname(__FILE__)."/twig");
$twig = new \Twig\Environment($loader);
$result_msg = "";
$query="";
$result = "";

if(array_key_exists("delete", $_POST)){
    $_SESSION["uploaded_files"] = [];
    $response = getResponse('GET', 'delete?user_id=' . $_SESSION["logged_user_email"]);
}



if(isset($_FILES["files"])) {
 $uploads_dir = '/uploads';
  $files = [];
  foreach ($_FILES["files"]["name"] as $key => $name) {
    $path = $_FILES["files"]["tmp_name"][$key];
    $name = basename($name);
    $files[] = [
        'name' => 'file', // Use 'file[]' to indicate an array of files
        'contents' => file_get_contents($path),
        'filename' => $name,
    ];
    
  }
  getResponse('POST', 'upload?user_id=' . $_SESSION["logged_user_email"], null, $files);
}

if(isset($_POST["query"])) {
  $query = $_POST["query"];
  $body = json_encode(["query" => $query]);
  $response = getResponse('POST', 'query?user_id=' . $_SESSION["logged_user_email"], $body);

  $comments = "";
  if(property_exists($response, 'result')){
    if($response->comments != "") {
      $comments = $response->comments;
    }
    $result = $response->result;
  } else {
    $result = $response;
  }
}

// Récupération de la liste de documents présents côté api
$response = getResponse('GET', 'documents?user_id=' . $_SESSION["logged_user_email"]);
$comments = "";
if(property_exists($response, 'documents')){
  if($response->comments != "") {
    $comments = $response->comments;
  }
  $_SESSION["uploaded_files"] = $response->documents;
} else {
  $result = $response;
}

$page = 'index.html';
if($connectionException) {
  $page = 'connection_error.html';
}

echo $twig->render($page, [
'usermail' => $_SESSION["logged_user_email"],
'result_msg' => $result_msg,
'uploaded_files' => $_SESSION["uploaded_files"],
'query' => $query,
'comments' => $comments,
'result' => $result
]);