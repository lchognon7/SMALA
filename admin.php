<?php
include('src/php/connectDB.php');
if ($_SESSION['user_role'] !=  1) {
    header('Location: index.php');
}
//On récupère les données img dans la BDD pour l'affichage qui suivra dans la page
$pdo = new PDO(DB_DRIVER . ":host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_LOGIN, DB_PASS, DB_OPTIONS);
$requete = "SELECT * FROM `user`";
$prepare = $pdo->prepare($requete);
$prepare->execute();
$user = $prepare->fetchAll();
$requete = "SELECT img_url, img_titre, img_id, user_pseudo FROM `img`
FULL JOIN `user` ON `user_id` = `img_user_id`;";
$prepare = $pdo->prepare($requete);
$prepare->execute();
$img = $prepare->fetchAll();
$img = array_reverse($img);
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
    <div class="nav_overlay"></div>
    <main id="main_admin">
        <h2>Ajouter un utilisateur</h2>
        <form action="admin.php" method='POST'>
            <label for="pseudo">Pseudo</label>
            <input type="text" name='pseudo' required>
            <label for="mail">Adresse e-mail</label>
            <input type="email" name='mail' required>
            <label for="mdp">Mot de passe</label>
            <input type="text" name='mdp' required>
            <label for="role">Type de compte</label>
            <select name="role" id="role" required>
                <option value="0">Utilisateur</option>
                <option value="1">Administrateur</option>
            </select>
            <input type="submit" name='submit' value='créer'>
            <?php

        try {

            $connexion = new PDO(DB_DRIVER . ":host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_LOGIN, DB_PASS, DB_OPTIONS);

            if (isset($_POST['submit'])) {
                $user_pseudo = $_POST['pseudo'];
                $user_mail = $_POST['mail'];
                $user_mdp = $_POST['mdp'];
                $user_role = $_POST['role'];

                $requete = "INSERT INTO `user` (`user_pseudo`, `user_mail`, `user_mdp`, `user_role`)
                VALUES (:user_pseudo, :user_mail, :user_mdp, :user_role);";
                $prepare = $connexion->prepare($requete);
                $prepare->execute(array(
                    ":user_pseudo" => $user_pseudo,
                    ":user_mail" => $user_mail,
                    ":user_mdp" => $user_mdp,
                    ":user_role" => $user_role
                ));
                $resultat = $prepare->rowCount();

                if ($resultat == 1) {
                    echo "<p>Le nouveau compte " . htmlspecialchars($user_pseudo) . " a bien été créé.</p>";
                } else {
                    echo "<p>Une erreur s'est produite. Le nouveau compte " . htmlspecialchars($user_pseudo) . " n'a pas pu être créé.</p>";
                }
            }
        } catch (PDOException $e) {
            exit("🚫" . $e->getMessage());
        }
        ?>
        </form>
        <h2>Modification et suppression d'un utilisateur</h2>
        <div id="container_main_admin">
            <div id="search_bar">
                <p>Entrez le nom de l'utilisateur</p>
                <input type="text" id="search_input">
            </div>
            <div id="user_container_main_admin">
                <?php
                    foreach($user as $key => $value){
                        ?>
                        <div class="<?php echo($value['user_pseudo']);?>">
                            <form action="admin.php" method="POST">
                                <p><?php echo($value['user_pseudo']);?></p>
                                <label for="user_pseudo">Pseudo</label>
                                <input type="text" name="user_pseudo" id="user_pseudo" value="<?php echo($value['user_pseudo']);?>" required>
                                <label for="user_mail">Email</label>
                                <input type="text" name="user_mail" id="user_mail" value="<?php echo($value['user_mail']);?>" required>
                                <label for="user_mdp">Mot de passe</label>
                                <input type="text" name="user_mdp" id="user_mdp" value="<?php echo($value['user_mdp']);?>" required>
                                <label for="user_role">Rôle</label>
                                <select name="user_role" id="user_role" required>
                                    <option value="0">Utilisateur</option>
                                    <option value="1">Administrateur</option>
                                </select>
                                <input type="submit" name="user_mod_admin" value="modifier">
                                <input type="submit" name="user_supr_admin" value="supprimer">
                            </form>
                        </div>
                        <?php
                    }
                ?>
            </div>
        </div>
        <h2>Modification et suppression d'une image</h2>
        <div id="container_img_main_admin">
            <div id="img_search_bar">
                <p>Entrez le nom de l'utilisateur</p>
                <input type="text" id="img_search_input">
            </div>
            <div id="img_container_main_admin">
                <?php
                    foreach($img as $key => $value){
                        ?>
                        <div class="<?php echo($value['img_titre']);?>">
                            <form action="admin.php" method="POST">
                                <p><?php echo($value['img_titre']);?> - <span><?php echo($value['user_pseudo']);?></span></p>
                                <img src="<?php echo($value['img_url']);?>" alt="<?php echo($value['img_titre']);?>">
                                <label for="img_titre">Titre</label>
                                <input type="text" name="img_titre" value="<?php echo($value['img_titre']);?>" required>
                                <input type="hidden" name="img_id" value="<?php echo($value['img_id']);?>" required>
                                <input type="submit" name="img_mod" value="modifier">
                                <input type="submit" name="img_supr" value="supprimer">
                            </form>
                        </div>
                        <?php
                    }
                ?>
            </div>
        </div>
    </main>
    <script src="src/js/app.js"></script>
</body>

</html>