<?php
include('connectDB.php');
$target_dir = "upload/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

// On vérifie si c'est bien une image
if(isset($_POST["submit"])) {
  $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
  if($check !== false) {
    echo "<p>Le fichier est une image - " . $check["mime"] . ".</p>";
    $uploadOk = 1;
  } else {
    echo "<p>Le fichier n'est pas une image.</p>";
    $uploadOk = 0;
  }
} else {
    header('Location: ../../actu.php');
}

// On vérifie si l'image existe déjà
if (file_exists($target_file)) {
  echo "<p>Le nom de l'image est déjà pris.</p>";
  $uploadOk = 0;
}

// On vérifie que l'image ne soit pas trop lourde
if ($_FILES["fileToUpload"]["size"] > 1000000) {
  echo "<p>Le poids de l'image est trop élevé, il ne doit pas dépasser 1Mo.</p>";
  $uploadOk = 0;
}

// On vérifie si le format de l'image est bien en jpg / png / jpeg/ gif
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" ) {
  echo "<p>Seul les JPG, JPEG, PNG et GIF sont acceptés.</p>";
  $uploadOk = 0;
}

// On vérifie si toutes les conditions sont remplies, cf $uploadOk == 1
if ($uploadOk == 0) {
  echo "<p>Votre image n'a pas été upload.</p>";
  echo"<a href='../../actu.php'>Retour à l'accueil</a>";
// Si tout va bien on upload l'image
} else {
  if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
    echo "<p>Le fichier ". htmlspecialchars( basename( $_FILES["fileToUpload"]["name"])). " a bien été upload.</p>";
    //Usine à gaz SQL pour lier le tout dans la DB
    $img_titre = $_POST['img_titre'];
    $img_url = "src/php/upload/".$_FILES["fileToUpload"]["name"];
    $img_user_id = $_SESSION['user_id'];
    try {
      $pdo = new PDO(DB_DRIVER . ":host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_LOGIN, DB_PASS, DB_OPTIONS);
      $requete = "INSERT INTO `img` (`img_url`, `img_titre`, `img_user_id`)
                  VALUES (:img_url, :img_titre, :img_user_id);";
      $prepare = $pdo->prepare($requete);
      $prepare->execute(array(
        ':img_url' => $img_url,
        ':img_titre' => $img_titre,
        ':img_user_id' => $img_user_id
      ));
      $res = $prepare->rowCount();
  
      if ($res == 1) {
        header("Location: ../../actu.php");
      }
    } catch (PDOException $e) {
      exit("❌🙀❌ OOPS :\n" . $e->getMessage());
    }
    //Fin de l'usine à gaz
  } else {
    echo "<p>Une erreur inconnue est survenue. Veuillez contacter le webmaster.</p>";
  }
}
?>