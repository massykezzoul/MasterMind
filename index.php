<?php
session_start();
//require 'MasterMind.php';
//require 'Players.php';
//include("MasterMind.php");

/*
    Variables POST utilisé:
    $_POST['nom'] : contient le nom du joueur définie seulement lors de la première tantative
    $_POST['essai'] : contient le dernier essai saisie
    $_POST['save'] : si définit alors sauvgarder la partie en cours (partie en cours est dans $_SESSION[])
    $_POST['load'] : si définit le joueur veut charger une partie.
        $_POST['loadName'] : contient le nom de la sauvgarde à charger (le nom du joueur). est définit seulement si $_POST['load'] est définit
    $_POST['exit'] : si définit le jouer veut quitter 

    Variables SESSION utilisé :
    $_SESSION['nom'] : contient le nom du joueur définie seulement lors de la première tantative
    $_SESSION['essai'] : contient le dernier essai saisie // obligatoire dans le cas oû $_POST['essai'] disparait après un recharger manuelle de la page par l'user
    $_SESSION['jeu'] : contient le jeu en cours (une instance de la classe MasterMind (Pensé à unserialize() avant utilisation);

    Cookie définie :
    $_COOKIE['sauvgardes'] => Contient un objet de la classe Players (Pensé à unserialize() avant utilisation)
*/
/*
spl_autoload_register(function ($class_name) {
    include_once $class_name . '.php';
});
*/
require_once('classes.inc.php');
/* annulé le jeu */
if (isset($_POST['exit'])) {
    unset($_SESSION);
    unset($_POST);
    session_destroy();
}

/* Chargement d'une sauvgarde */
if (isset($_POST['load']) && isset($_POST['loadName']) && isset($_COOKIE['sauvgardes'])) { // if cliqué sur boutton chargé
    $players = unserialize($_COOKIE['sauvgardes']); // chargement des sauvgardes
    $player = $players->getPlayerWithName($_POST['loadName']);
    if ($player != null) {
        /* Chargement réussi, sauvgarder le joueur et le jeu dans $_SESSION */
        $_SESSION['nom'] = $player->getName();
        $_SESSION['essai'] = $player->getJeu()->getEssais()[$player->getJeu()->getNbEssai()-1]->getEssai(); // faut simplifier cette chose
        $_SESSION['jeu'] = serialize($player->getJeu());
    }
    unset($player);
    unset($players);
}

/* Sauvgarde */
if (isset($_POST['save']) && isset($_SESSION['nom']) && isset($_SESSION['jeu']) && isset($_SESSION['essai'])) {
    $tempSauvgarde = time() + 24*36000; // une journée
    /* regarder si une sauvgarde existe déja */
    if (isset($_COOKIE['sauvgardes'])) 
        $saves = unserialize($_COOKIE['sauvgardes']);
    else
        $saves = new Players();
    $saves->addPlayer(new Player($_SESSION['nom'],unserialize($_SESSION['jeu'])));
    setcookie('sauvgardes', serialize($saves) , $tempSauvgarde);
    /* Indispensable pour affiché la sauvgarde imédiatement dans la page d'acceuil (sans actualisé) */
    $_COOKIE['sauvgardes'] = serialize($saves);

    unset($_SESSION);
    unset($_POST);
    session_destroy();
}

?>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Mastermind</title>
    <meta type="title">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="centerBox">
        <?php
        if (isset($_POST['nom']) || isset($_SESSION['nom'])) { // Jeu en cours
            ?>
            <h1>Mastermind</h1>

            <?php
                if (!isset($_SESSION['jeu']) || isset($_POST['nom'])) {
                    // Nouveau jeu
                    if (isset($_POST['nom']))
                        $_SESSION['nom'] = $_POST['nom']; // Stocker le nom du joueur
                    if (!isset($_SESSION['jeu'])) {
                        $jeu = new MasterMind(4);
                        $_SESSION['jeu'] = serialize($jeu); // stocker l'etat du jeu
                    }

                    ?>
                <form action="" method="POST" class="blueBorder">
                    <label id="essai">Tentez votre chance : </label>
                    <input type="text" name="essai" id="essai" />
                    <span class="submit">
                        <input type="submit" value="OK">
                        <input type="submit" name="exit" value="Exit">
                    </span>
                </form>
                <?php

                    } else if (isset($_SESSION['nom']) && isset($_SESSION['jeu']) && (isset($_SESSION['essai']) || isset($_POST['essai']))) {
                        // Continuer le jeu - recuperer les données
                        $jeu = unserialize($_SESSION['jeu']);
                        if (isset($_POST['essai']))    
                            $_SESSION['essai'] = $_POST['essai'];
                        $essaiUser = $_SESSION['essai'];
                        $couple = $jeu->test($essaiUser);
                        if ($couple->getX() == -1 || $couple->getY() == -1) // ERREUR DE SAISI
                            $erreur = true;
                        else 
                            $erreur = false;

                        $_SESSION['jeu'] = serialize($jeu);

                        if ($jeu->isGagner()) {
                            ?>
                    <div class="greenBorder textCenter">
                        <p>
                            Exacte le chiffre étais bien <?php echo $essaiUser; ?>.
                            Bien joué <?php echo $_SESSION['nom'];?>. vous avez réussi en <?php echo $jeu->getNbEssai(); ?> coups.<br/>
                            Liste des coups joué : <br/>
                            <ul>
                                <?php
                                    foreach ($jeu->getEssais() as $key) {
                                        echo '<li>' . $key->getEssai() . ' : Bien placés : '. $key->getBp() . ',Mal placés :' . $key->getMp() . '</li>';
                                    }
                                    /* ------- Ajouter ici la suppression de la sauvgarde -------- */
                                    unset($_SESSION);
                                    unset($_POST);
                                    session_destroy();
                                ?>
                            </ul>
                            <button onclick="javascript:window.location.reload()">Rejouer</button>
                        </p>
                    </div>
                <?php
                        } else {
                            if ($erreur) { // Gérer l'erreur
                                ?> 
                                <p class="redBorder">Erreur de saisie : '<?php echo $essaiUser; ?>' n'est pas un nombre acceptable.</p>
                                <?php
                            }
                            
                            if ($jeu->getNbEssai() != 0) {
                                $essai =  $jeu->getEssais()[$jeu->getNbEssai()-1];
                                $bp = $essai->getBp();
                                $mp = $essai->getMp();
                            }

                            ?>
                    <form action="" method="POST" class="redBorder">
                        <?php  if ($jeu->getNbEssai() != 0) { ?>
                        <p>"<?php echo $essai->getEssai(); ?>" Erroné, Bien placés : <?php echo $bp;?> Mal placé : <?php echo $mp;?></p>
                        <?php } ?>
                        <label id="essai">ReTentez votre chance : </label>
                        <input type="text" name="essai" id="essai" />
                        <span class="submit">
                            <input type="submit" value="OK">
                            <input type="submit" name="exit" value="Exit">
                            <input type="submit" name="save" value="Save">
                        </span>
                    </form>
            <?php
                    }
                } else {
                    echo 'ERROR<br>';
                    echo 'SESSION : <br>';
                    print_r($_SESSION);
                    echo 'POST : <br>';
                    print_r($_POST);
                }
            } else {
                // Premiere visite
                //session_destroy();
                ?>
            <h1>Bienvenu au jeu du Mastermind</h1>
            <p><strong>Règles du jeu</strong> : Le jeu crèe un code aléatoire caché à 4 chiffres différents. Le joueur tente à chaque coup une
                combinaison de 4 chiffres différents et le jeu lui répond en indiquant le nombre de chiffres bien placés et le nombre
                de chiffres mal placés.</p>

            <form action="" method="post" class="blueBorder">
                <label for="nom">Votre nom : </label>
                <input type="text" name="nom" id="nom" />
                <input type="submit" name="play" value="Jouer nouveau jeu !" />
            <!--<?php if (isset($_COOKIE['jeu']) && isset($_COOKIE['nom'])) {?><input type="submit" name="load" value="Charger"><?php } ?> -->
            </form>
        <?php
        if (isset($_COOKIE['sauvgardes'])) {
            // TODO une boucle pour afficher tout les jeu sauvgardé
            ?>
            <hr>
            <h2>Jeu sauvgardé</h2>
            <form id="load" action="" method="POST">
            <?php
                $players = unserialize($_COOKIE['sauvgardes']);
                for ($i=0; $i<$players->getSize();$i++ ) {
                    $name = $players->getPlayer($i)->getName();
            ?>
            <p> 
                <input type="radio" name ="loadName" id="<?php echo $name; ?>" value="<?php echo $name; ?>">
                <label for="<?php echo $name; ?>"> <?php echo $name; ?></label><br>
            </p>
            <?php
                }
            ?>
            <span class="submit">
                <input type="submit" value="Charger" name="load">
                <input type="submit" value="Supprimer" name="delete">
            </span>
            </form>
            <?php
        }
        }
        ?>
        <div class="author">
            <p>
                Created By <a href="https://github.com/massykezzoul">Massili Kezzoul</a> &copy; Sept. 2019
            </p>
        </div>
    </div>
</body>

</html>