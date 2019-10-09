<?php
session_start();
//require 'MasterMind.php';
include("MasterMind.php");

if (isset($_POST['exit'])) { // annulé le jeu
    unset($_SESSION);
    unset($_POST);
    session_destroy();
}

if (isset($_POST['load'])) { // if cliqué sur boutton chargé
    if (isset($_COOKIE['nom']) && !isset($_SESSION['nom'])) { // 
        $_SESSION['nom'] = $_COOKIE['nom'];
        if (isset($_COOKIE['jeu']) && !isset($_SESSION['jeu']) && isset($_COOKIE['essai']) && !isset($_SESSION['essai'])) {
           $_SESSION['jeu'] = $_COOKIE['jeu'];
           $_SESSION['essai'] = $_COOKIE['essai'];
        }
    } else {
        echo 'Aucun chargement possible <br>';
    }
}
    
if (isset($_POST['save'])) {
    if (isset($_SESSION['nom'])) {
        setcookie('nom', $_SESSION['nom'] , time() + 24*3600);
        $_COOKIE['nom'] = $_SESSION['nom'];
        if (isset($_SESSION['jeu']) && isset($_SESSION['essai'])) {
            setcookie('jeu', $_SESSION['jeu'] , time() + 24*3600);
            setcookie('essai', $_SESSION['essai'] , time() + 24*3600);
            $_COOKIE['jeu'] = $_SESSION['jeu'];
            $_COOKIE['essai'] = $_SESSION['essai'];
        }
    }
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
                    <input type="submit" value="OK">
                    <input type="submit" name="exit" value="Exit">
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
                        <input type="submit" value="OK">
                        <input type="submit" name="exit" value="Exit">
                        <input type="submit" name="save" value="Save">
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
            <?php if (isset($_COOKIE['jeu']) && isset($_COOKIE['nom'])) { ?><input type="submit" name="load" value="Charger"><?php } ?>
            </form>
        <?php
        if (isset($_COOKIE['jeu']) && isset($_COOKIE['nom'])) { // il y'a une sauvgarde dans un cookie
            // TODO une boucle pour afficher tout les jeu sauvgardé 
            ?>
            <hr>
            <h2>Jeu sauvgardé</h2>
            <p> 
                <strong>Nom : </strong> <?php echo $_COOKIE['nom']; ?>
            </p>
            <?php
        }
        }
        ?>
        <div class="author">
            <p>
                Created By Massili Kezzoul &copy; Sept. 2019
            </p>
        </div>
    </div>
</body>

</html>