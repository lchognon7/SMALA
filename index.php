<?php
include('src/php/connectDB.php');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- Title -->
    <meta name="description" content="Home description.">
    <title>LA SMALA</title>
    <meta charset="UTF-8"/>
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
    </nav>
    <div class="nav_overlay"></div>
    <main id="main_connexion">
        <?php
        try {
            $pdo = new PDO(DB_DRIVER . ":host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_LOGIN, DB_PASS, DB_OPTIONS);
            $requete = "SELECT * FROM `user`;";
            $prepare = $pdo->prepare($requete);
            $prepare->execute();
            $res = $prepare->rowCount();
            $resultat = $prepare->fetchAll();
            if($res == 0){
                ?>
                <h1>Bienvenue sur ta SMALA</h1>
                <p>Afin de gérer ta nouvelle SMALA, tu vas devoir créer ton compte administrateur. Renseigne le formulaire suivant :</p>
                <form action="index.php" method="POST">
                    <label for="user_pseudo_creation_admin">Choisis ton pseudonyme</label>
                    <input type="text" name="user_pseudo_creation_admin" required>
                    <label for="user_mail_creation_admin">Ton adresse email</label>
                    <input type="email" name="user_mail_creation_admin" required>
                    <label for="user_mdp_creation_admin">Ton mot de passe</label>
                    <input type="password" name="user_mdp_creation_admin" pattern="^(?=.*[$@%*^&\#+_-.!;])(?=.*[A-Z])(?=.*\d)(?=.*[a-z]).{8,}$" title="Il doit faire au moins 8 caractères et contenir au moins une minuscule, une majuscule, un chiffre, un caractère spécial." required>
                    <input type="submit" value="Créer mon compte" name="btn_submit_creation_admin">
                </form>
                <?php
                //Création du premier admin
                if(isset($_POST['btn_submit_creation_admin'])){
                    $user_pseudo_creation_admin = $_POST['user_pseudo_creation_admin'];
                    $user_mail_creation_admin = $_POST['user_mail_creation_admin'];
                    $user_mdp_creation_admin = $_POST['user_mdp_creation_admin'];
                    try {
                    $pdo = new PDO(DB_DRIVER . ":host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_LOGIN, DB_PASS, DB_OPTIONS);
                    $requete = "INSERT INTO `user` (`user_pseudo`, `user_mail`, `user_mdp`, `user_role`)
                                VALUES (:user_pseudo, :user_mail, :user_mdp, :user_role);";
                    $prepare = $pdo->prepare($requete);
                    $prepare->execute(array(
                        ':user_pseudo' => $user_pseudo_creation_admin,
                        ':user_mail' => $user_mail_creation_admin,
                        ':user_mdp' => $user_mdp_creation_admin,
                        ':user_role' => 1
                    ));
                    $res = $prepare->rowCount();

                    if ($res == 1) {
                        try {
                        $pdo = new PDO(DB_DRIVER . ":host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_LOGIN, DB_PASS, DB_OPTIONS);
                        $requete = "SELECT * FROM `user`;";
                        $prepare = $pdo->prepare($requete);
                        $prepare->execute();
                        $res = $prepare->rowCount();
                        $resultat = $prepare->fetch();
                        $_SESSION['user_role'] = $resultat['user_role'];
                        $_SESSION['user_id'] = $resultat['user_id'];
                        $_SESSION['user_pseudo'] = $resultat['user_pseudo'];
                        $_SESSION['user_mail'] = $resultat['user_mail'];
                        header('Location: admin.php');
                        } catch (PDOException $e) {
                        exit("❌🙀❌ OOPS :\n" . $e->getMessage());
                        }
                    }
                    } catch (PDOException $e) {
                    exit("❌🙀❌ OOPS :\n" . $e->getMessage());
                    echo "<p>Une erreur s'est produite, veuillez contacter le webmaster</p>";
                    }
                }
            } else {
                ?>
                <h1>Bienvenue sur ta SMALA</h1>
                <p>Indentifie toi pour aller sur ton espace personnel</p>
                <form action="index.php" method="POST">
                    <label for="user_mail_connexion">Ton adresse email</label>
                    <input type="email" name="user_mail_connexion" required>
                    <label for="user_mdp_connexion">Ton mot de passe</label>
                    <input type="password" name="user_mdp_connexion" required>
                    <input type="submit" value="Me connecter !" name="btn_submit_connexion">
                </form>
                <?php
                //Système de connexion
                if(isset($_POST['btn_submit_connexion'])){
                    $user_mail_connexion = $_POST['user_mail_connexion'];
                    $user_mdp_connexion = $_POST['user_mdp_connexion'];
                    try {
                        $pdo = new PDO(DB_DRIVER . ":host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_LOGIN, DB_PASS, DB_OPTIONS);
                        $requete = "SELECT * FROM user WHERE user_mail = :user_mail;";
                        $prepare = $pdo->prepare($requete);
                        $prepare->execute(array(
                        ':user_mail' => $user_mail_connexion
                        ));
                        $res = $prepare->rowCount();
                        $resultat = $prepare->fetch();
                        if($resultat['user_mdp'] == $user_mdp_connexion){
                            if($resultat['user_role'] == 0){
                                $_SESSION['user_role'] = $resultat['user_role'];
                                $_SESSION['user_id'] = $resultat['user_id'];
                                $_SESSION['user_pseudo'] = $resultat['user_pseudo'];
                                $_SESSION['user_mail'] = $resultat['user_mail'];
                                header('Location: actu.php');
                            } else {
                                $_SESSION['user_role'] = $resultat['user_role'];
                                $_SESSION['user_id'] = $resultat['user_id'];
                                $_SESSION['user_pseudo'] = $resultat['user_pseudo'];
                                $_SESSION['user_mail'] = $resultat['user_mail'];
                                header('Location: admin.php');
                            }
                        } else {
                            echo("<p>Email ou mot de passe erroné</p>");
                        }
                    } catch (PDOException $e) {
                        exit("❌🙀❌ OOPS :\n" . $e->getMessage());
                    }
                }
            }
        } catch (PDOException $e) {
            exit("❌🙀❌ OOPS :\n" . $e->getMessage());
        }
        ?>
    </main>
    <script src="src/js/app.js"></script>
</body>
</html>