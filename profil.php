<?php
include('src/php/connectDB.php');
if (!isset($_SESSION['user_role'])) {
    header('Location: index.php');
}
//On récupère les données img dans la BDD pour l'affichage qui suivra dans la page
$pdo = new PDO(DB_DRIVER . ":host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_LOGIN, DB_PASS, DB_OPTIONS);
$requete = "SELECT * FROM `img` WHERE img_user_id = :img_user_id;";
$prepare = $pdo->prepare($requete);
$prepare->execute(array(
    ':img_user_id' => $_SESSION['user_id']
));
$res = $prepare->rowCount();
$image = $prepare->fetchAll();

//Formulaire de suppression d'image
if(isset($_POST['img_supr'])) {
    $img_id = $_POST['img_id'];
    $img_url = $_POST['img_url'];
    unlink($img_url);
    try {
        $pdo = new PDO(DB_DRIVER . ":host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_LOGIN, DB_PASS, DB_OPTIONS);
        $requete = "DELETE FROM `img` WHERE `img_id` = :img_id;";
        $prepare = $pdo->prepare($requete);
        $prepare->execute(array(
            ':img_id' => $img_id
        ));
        header("Location: profil.php");
    } catch (PDOException $e) {
        exit("❌🙀❌ OOPS :\n" . $e->getMessage());
    }
}
if(isset($_POST['img_mod'])){
    $img_id = $_POST['img_id'];
    $img_titre = $_POST['img_titre_mod'];
    try {
        $pdo = new PDO(DB_DRIVER . ":host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_LOGIN, DB_PASS, DB_OPTIONS);
        $requete = "UPDATE `img` SET `img_titre` = :img_titre WHERE `img_id` = :img_id;";
        $prepare = $pdo->prepare($requete);
        $prepare->execute(array(
          ':img_id' => $img_id,
          ':img_titre' => $img_titre
        ));
        $res = $prepare->rowCount();
    
        if ($res == 1) {
          header("Location: profil.php");
        }
      } catch (PDOException $e) {
        exit("❌🙀❌ OOPS :\n" . $e->getMessage());
      }
}
if (isset($_POST['profil_mod'])) {
    $user_id = $_SESSION['user_id'];
    $user_pseudo = $_POST['user_pseudo_profil_mod'];
    $user_mail = $_POST['user_mail_profil_mod'];
    $user_mdp = $_POST['user_mdp_profil_mod'];
    
    try {
        $pdo = new PDO(DB_DRIVER . ":host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_LOGIN, DB_PASS, DB_OPTIONS);
        $requete = "UPDATE `user` SET `user_pseudo` = :user_pseudo, `user_mail` = :user_mail, `user_mdp` = :user_mdp WHERE `user_id` = :user_id;";
        $prepare = $pdo->prepare($requete);
        $prepare->execute(array(
            ':user_id' => $user_id,
            ':user_pseudo' => $user_pseudo,
            ':user_mail' => $user_mail,
            ':user_mdp' => $user_mdp
        ));
        $res = $prepare->rowCount();
        if ($res == 1) {
            $_SESSION['user_mail'] = $user_mail;
            $_SESSION['user_pseudo'] = $user_pseudo;
            header("Location: profil.php");
        }
    } catch (PDOException $e) {
        exit("❌🙀❌ OOPS :\n" . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <!-- Title -->
    <meta name="description" content="Home description.">
    <title>MA SMALA</title>
    <meta charset="UTF-8" />
    <!-- Robots -->
    <meta name="robots" content="index, follow">
    <!-- Device -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="format-detection" content="telephone=no">
    <!-- Links -->
    <?php echo '<link rel="stylesheet" href="src/css/style.css?' . filemtime('src/css/style.css') . '" />'; ?>
</head>

<body>
    <nav>
        <h1>SMALA</h1>
        <div id="btn_menu">
            <input type="checkbox" />
            <span></span>
            <span></span>
            <span></span>
            <ul id="menu">
                <a href="actu.php">
                    <li>Fil d'actualité</li>
                </a>
                <a href="profil.php">
                    <li>Mon Profil</li>
                </a>
                <?php
                if ($_SESSION['user_role'] == 1) {
                ?>
                    <a href="admin.php">
                        <li>Page admin</li>
                    </a>
                <?php
                }
                ?>
                <a href="src/php/logout.php">
                    <li>Déconnexion</li>
                </a>
            </ul>
        </div>
    </nav>
    <main id="main_profil">
        <h2>Profil de <span><?php echo ($_SESSION['user_pseudo']); ?></span></h2>
        <form action="profil.php" method="POST">
            <h3>Modifier mes informations</h3>
            <label for="user_mail_profil_mod">Adresse email</label>
            <input type="email" name="user_mail_profil_mod" value="<?php echo ($_SESSION['user_mail']); ?>" required>
            <label for="user_pseudo_profil_mod">Pseudonyme</label>
            <input type="text" name="user_pseudo_profil_mod" value="<?php echo ($_SESSION['user_pseudo']); ?>" required>
            <label for="user_mdp_profil_mod">Mot de passe</label>
            <input type="password" name="user_mdp_profil_mod" required>
            <input type="submit" name="profil_mod" value="Modifier">
        </form>
        <div id="container_div_img_mod">
            <p>Vous avez upload <?php echo ($res); ?> image(s)</p>
            <?php
            foreach ($image as $key => $value) {
            ?>
                <div class="div_img_mod">
                    <img src="<?php echo ($value['img_url']); ?>" alt="<?php echo ($value['img_titre']); ?>">
                    <form action="profil.php" method="POST">
                        <input type="text" name="img_titre_mod" value="<?php echo ($value['img_titre']); ?>">
                        <input type="hidden" name="img_id" value="<?php echo ($value['img_id']); ?>">
                        <input type="hidden" name="img_url" value="<?php echo ($value['img_url']); ?>">
                        <input type="submit" name="img_mod" value="Modifier">
                        <input type="submit" name="img_supr" value="Supprimer">
                    </form>
                </div>
            <?php
            }
            ?>
        </div>
    </main>
    <script src="src/js/app.js"></script>
</body>

</html>