<?php 
require_once'assets/class/Character.php';
require_once'assets/class/Guerrier.php';
require_once'assets/class/Orc.php';

session_start();



//APPELLE TOUTE LES FONCTION
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if(isset($_POST['creationGuerrier'])) {
        creationGuerrier();
    }

    
    if(isset($_POST['creationOrc'])) {
        creationOrc();
    }
    
    if(isset($_SESSION['orc']) && isset($_SESSION['guerrier']) && isset($_POST['commence']) ) {
        commence();
    }

    if(isset($_SESSION['orc']) && isset($_SESSION['guerrier']) && isset($_SESSION['commence']) && isset($_POST['combat']) ) {
        combat();
    }

    if(isset($_POST['nouvellePartie'])) {
        nouvellePartie();
    }
    
}



// CACHE LES BOUTONS QUI DEVIENNENT INUTILE
if(empty($_SESSION['guerrier'])) {
    $btnCreationGuerrier = true;
} else {
    $btnCreationGuerrier = false;
}

if(empty($_SESSION['orc'])) {
    $btnCreationOrc = true;
} else {
    $btnCreationOrc = false;
}

if(empty($_SESSION['commence'])) {
    $btnCommence = true;
} else {
    $btnCommence = false;
}



//NOUVELLE PARTIE
function nouvellePartie() {
    session_unset();
    session_destroy();
}



//CREATION DE PERSO
function creationGuerrier() {
    $guerrier = new Guerrier(2000, 500, "Hallebarde", 250, "Bouclier", 600);
    $_SESSION['guerrier'] = $guerrier;
    $_SESSION['guerrierPV'] = $guerrier->getHealth();
    $_SESSION['guerrierMANA'] = $guerrier->getMana();
    $btnCreationGuerrier = false;
    $_POST['action'] = "<b>Un guerrier est apparu</b>";
}

function creationOrc() {
    $orc = new Orc(1500, 200, 100, 400);
    $_SESSION['orc'] = $orc;
    $_SESSION['orcPV'] = $orc->getHealth();
    $_SESSION['orcMANA'] = $orc->getMana();
    $btnCreationOrc = false;
    $_POST['action'] = "<b>Un orc est apparu</b>";
}



//ORDRE DE JEU
function commence() {
    $commence = false;
    while ($commence !== true) {
        $commenceGuerrier = rand(1,6);
        $commenceOrc = rand(1,6);
        if ($commenceGuerrier > $commenceOrc) {
            $_SESSION['commence'] = "guerrier";
            $_POST['action'] = "<b>Le guerrier commence le combat !</b>";
            $commence = true;
            $btnCommence = false;
        } else if ($commenceOrc > $commenceGuerrier) {
            $_SESSION['commence'] = "orc";
            $_POST['action'] =  "<b>L'orc commence le combat !</b>";
            $commence = true;
            $btnCommence = false;
        }
    }
}



// COMBAT
function combat() {
    if ($_SESSION['commence'] == "guerrier") {

        // Le guerrier attaque
        $degat = $_SESSION['guerrier']->attack();
        $_POST['action'] =  "<b>Le guerrier attaque et inflige " . $degat . " de dégat !</b>";

        // L'orc perd de la vie
        $_SESSION['orc']->setHealth(($_SESSION['orc']->getHealth()) - $degat);
        $_POST['couleurPVOrc'] = "danger";
        $_SESSION['orcPV'] = $_SESSION['orc']->getHealth();

        sleep(2);

        // Prochain tour possible
        $_POST['couleurPVOrc'] = "dark";
        $_POST['action'] =  "<b>L'orc n'a plus que " . $_SESSION['orcPV'] . " de point de vie.</b>";
        $_SESSION['commence'] = "tour de orc";
    }

    if ($_SESSION['commence'] == "orc") {
        $degat = $_SESSION['orc']->attack();
        $_POST['action'] =  "<b>L'orc attaque et inflige " . $degat . " de dégat !</b>";
        $_SESSION['commence'] = "tour de guerrier";
    }
}


var_dump($_POST);
var_dump($_SESSION);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orc VS Guerrier</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/style.css">
</head>


<body>

    <h1 class="text-center">Orc VS Guerrier</h1>

    <!-- Terrain et bouton -->
    <div class="container text-center">
        <div class="row border cadre mb-2">
            <div class="row terrain">
                <div class="col">
                    <img src="" alt="Jolie guerrier" id="Guerrier">
                </div>
                <div class="col">
                    <img src="" alt="Méchant et pas bo orc" id="Orc">
                </div>
            </div>
            <div class="row">
                <div class="col-1 d-flex ms-5">
                    <p>PV:</p>
                    <p> <?= $_SESSION['guerrierPV'] ?? ""?> </p>
                </div>
                <div class="col-1 d-flex">
                    <p>MANA:</p>
                    <p> <?= $_SESSION['guerrierMANA'] ?? ""?> </p>
                </div>
                <div class="col-7"></div>
                <div class="col-1 d-flex ms-5">
                    <p>PV:</p>
                    <p class="text-<?= $_POST['couleurPVOrc'] ?? "dark"?>"> <?= $_SESSION['orcPV'] ?? ""?> </p>
                </div>
                <div class="col-1 d-flex">
                    <p>MANA:</p>
                    <p> <?= $_SESSION['orcMANA'] ?? ""?> </p>
                </div>
            </div>
        </div>
        <div class="row">
            <p> <?= $_POST['action'] ?? '<em>Les futurs actions seront inscrites ici</em>' ?></p>
        </div>
        <div class="row">
            <div class="col">
                <?php if ($btnCreationGuerrier !== false) { ?>
                <form method="post">
                    <button type="submit" class="btn btn-primary" value="creationGuerrier" name="creationGuerrier"
                        style="display: inline;" id="btnCreationGuerrier">Création du Guerrier</button>
                </form>
                <?php } ?>
            </div>
            <div class="col">
                <?php if ($btnCommence !== false) { ?>
                <form method="post">
                    <button type="submit" class="btn btn-primary" value="commence" name="commence"
                        style="display: inline;">Qui commence
                        ?</button>
                </form>
                <?php } ?>
            </div>
            <div class="col">
                <?php if ($btnCreationOrc !== false) { ?>
                <form method="post">
                    <button type="submit" class="btn btn-primary" value="creationOrc" name="creationOrc"
                        style="display: inline;">Création de
                        l'Orc</button>
                </form>
                <?php } ?>
            </div>
        </div>
    </div>


    <!-- Bouton nouvelle partie -->
    <div class="text-center">
        <form method="post">
            <button type="submit" class="btn btn-primary" value="nouvellePartie" name="nouvellePartie">Nouveau
                jeu</button>
        </form>
    </div>


    <!-- Bouton combat -->
    <div class="text-center">
        <form method="post">
            <button type="submit" class="btn btn-danger" value="combat" name="combat">COMBAT !</button>
        </form>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
    </script>
</body>


</html>