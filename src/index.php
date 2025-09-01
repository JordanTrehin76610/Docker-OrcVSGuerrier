<?php 
require_once'assets/class/Character.php';
require_once'assets/class/Guerrier.php';
require_once'assets/class/Orc.php';

session_start();

$btnParis = true;

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

    if(!isset($_COOKIE['monnaie'])){
        if (empty($_COOKIE['monnaie'])) {
            // ResetMonnaie();
        }
    }

    if(isset($_POST['parieGuerrier'])) {
        parieGuerrier();
    }   

    if(isset($_POST['ResetMonnaie'])) {
        ResetMonnaie();
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

if(empty($_SESSION['guerrier']) || empty($_SESSION['orc']) || empty($_SESSION['commence']) || $_SESSION['finish'] != false) {
    $btnCombat = false;
} else {
    $btnCombat = true;
    $btnParis = false;
}



//NOUVELLE PARTIE
function nouvellePartie() {
    session_unset();
    session_destroy();
}



function ResetMonnaie() {
    setcookie('monnaie', 200);
}



function parieGuerrier() {
    if ($_POST['montantPariGuerrier'] > $_COOKIE['monnaie']) {
        $_SESSION["parisGuerrierErreur"] = "Vous n'avez pas assez de monnaie pour ce pari.";
    }  else if ($_POST['montantPariGuerrier'] <= 0 ) {
        $_SESSION["parisGuerrierErreur"] = "Veuillez entrer une valeur supérieur à zéro.";
    } else {
        $_SESSION["parisGuerrierMonaie"] = $_POST['montantPariGuerrier'];
        $_SESSION['montantPariGuerrier'] = $_POST['montantPariGuerrier'];
        $_SESSION['parisGuerrier'] = true;
        $monnaie = $_COOKIE['monnaie'] - $_POST['montantPariGuerrier'];
        setcookie('monnaie', $monnaie);
        $_SESSION["parisGuerrierErreur"] = "Paris rentrer !";
    }
}

function parieOrc() {
    if ($_POST['montantParisOrc'] > $_COOKIE['monnaie']) {
        $_SESSION["parisOrcErreur"] = "Vous n'avez pas assez de monnaie pour ce pari.";
    }  else if ($_POST['montantParisOrc'] <= 0 ) {
        $_SESSION["parisOrcErreur"] = "Veuillez entrer une valeur supérieur à zéro.";
    } else {
        $_SESSION["parisOrcMonaie"] = $_POST['montantParisOrc'];
        $_SESSION['montantParisOrc'] = $_POST['montantParisOrc'];
        $_SESSION['parisOrc'] = true;
        $monnaie = $_COOKIE['monnaie'] - $_POST['montantParisOrc'];
        setcookie('monnaie', $monnaie);
        $_SESSION["parisOrcErreur"] = "Paris rentrer !";
    }
}



//CREATION DE PERSO
function creationGuerrier() {
    $guerrier = new Guerrier(1600, 400, "Hallebarde", 200, "Bouclier", 150);
    $_SESSION['guerrier'] = $guerrier;
    $_SESSION['guerrierPV'] = $guerrier->getHealth();
    $_SESSION['guerrierMANA'] = $guerrier->getMana();
    $_SESSION['guerrierFORCE'] = $guerrier->getWeaponDamage();
    $_SESSION['guerrierDEFENSE'] = $guerrier->getShieldAbsorbtion();
    $_SESSION['guerrierIMG'] = "assets/img/guerrier.png";
    $btnCreationGuerrier = false;
    $_SESSION['finish'] = false;
    $_POST['action'] = "<b>Un guerrier est apparu</b>";
}

function creationOrc() {
    $orc = new Orc(1400, 300, 100, 500);
    $_SESSION['orc'] = $orc;
    $_SESSION['orcPV'] = $orc->getHealth();
    $_SESSION['orcMANA'] = $orc->getMana();
    $_SESSION['orcFORCE'] = $orc->getDamageMin() . " - " . $orc->getDamageMax();
    $_SESSION['orcIMG'] = "assets/img/orc.png";
    $btnCreationOrc = false;
    $_SESSION['finish'] = false;
    $_POST['action'] = "<b>Un orc est apparu</b>";
}



//ORDRE DE JEU
function commence() {
    $commence = false;
    while ($commence !== true) {
        $commenceGuerrier = rand(1,6);
        $commenceOrc = rand(1,6);
        $btnParis = false;
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
    if($_SESSION['orc']->getHealth() <= 0 || $_SESSION['guerrier']->getHealth() <= 0) { //Condition de victoire

        if($_SESSION['orc']->getHealth() <= 0) { //Si le guerrier gagne

            $_POST['action'] =  "<b>VICTOIRE DU GUERRIER</b>";
            $_SESSION['orcIMG'] = "assets/img/orcMort.png";
            if (isset($_SESSION['parisGuerrier']) && $_SESSION['parisGuerrier'] == true) {
                $monnaie = ($_SESSION['montantPariGuerrier']*2) + $_COOKIE['monnaie'];
                setcookie('monnaie', $monnaie);
            }

        } else if ($_SESSION['guerrier']->getHealth() <= 0) { //Si l'orc gagne

            $_POST['action'] =  "<b>VICTOIRE DE L'ORC</b>";
            $_SESSION['guerrierIMG'] = "assets/img/guerrierMort.png";

            if (isset($_SESSION['parisOrc']) && $_SESSION['parisOrc'] == true) {
                $monnaie = ($_SESSION['montantParisOrc']*2) + $_COOKIE['monnaie'];
                setcookie('monnaie', $monnaie);
            }

        }

    } else if ($_SESSION['commence'] == "guerrier") { //DEBUT DE L'ATTAQUE DU GUERRIER

        $alea = rand(1, 2);
        $soigne = $_SESSION['guerrier']->magicUse();
        if ($alea == 2 && ($_SESSION['guerrier']->getMana() > $soigne)) {

            $_SESSION['guerrier']->setHealth(($_SESSION['guerrier']->getHealth()) + $soigne);
            $_SESSION['guerrier']->setMana($_SESSION['guerrier']->getMana() - $soigne);
            if ($_SESSION['guerrier']->getMana() < 0) {
                $_SESSION['guerrier']->setMana(0);
            }
            $_POST['couleurMANAGuerrier'] = "danger";
            $_POST['couleurPVGuerrier'] = "success";
            $_SESSION['guerrierPV'] = $_SESSION['guerrier']->getHealth();
            $_SESSION['guerrierMANA'] = $_SESSION['guerrier']->getMana();
            $_POST['action'] =  "<b>Le guerrier utilise sa mana et se soigne de " . $soigne . " PV !</b>";
            $_SESSION['guerrierIMG'] = "assets/img/guerrierSoin.png";
            $_SESSION['commence'] = "orc";

        } else {

        // Le guerrier attaque
        $degat = $_SESSION['guerrier']->attack();
        $_POST['action'] =  "<b>Le guerrier attaque et inflige " . $degat . " de dégat !</b>";
        $aleaMANA = rand(10, 60);
        if (($_SESSION['guerrier']->getMana() + $aleaMANA) < 400) {
            $_SESSION['guerrier']->setMana($_SESSION['guerrier']->getMana() + $aleaMANA);
            $_POST['couleurMANAGuerrier'] = "success";
            $_SESSION['guerrierMANA'] = $_SESSION['guerrier']->getMana();
        } else if ($_SESSION['guerrier']->getMana() == 400) {
            $_SESSION['guerrier']->setMana(400);
        } else {
            $_SESSION['guerrier']->setMana(400);
            $_POST['couleurMANAGuerrier'] = "success";
            $_SESSION['guerrierMANA'] = $_SESSION['guerrier']->getMana();
        }

        // L'orc perd de la vie
        $_SESSION['orc']->setHealth(($_SESSION['orc']->getHealth()) - $degat);
        $_SESSION['orcIMG'] = "assets/img/orcDamage.png";
        $_POST['couleurPVOrc'] = "danger";
        $_SESSION['orcPV'] = $_SESSION['orc']->getHealth();

        // On active la seconde partie pour le spectacle
        $_POST['btnCombat'] = "Continue";
        $_SESSION['commence'] = "consequence guerrier";

    }}  else if ($_SESSION['commence'] == "consequence guerrier") { //LA SUITE DE L'ATTAQUE DU GUERRIER

        // Prochain tour possible
        $_SESSION['guerrierIMG'] = "assets/img/guerrier.png";
        $_POST['couleurPVOrc'] = "light";
        $_SESSION['couleurMANAGuerrier'] = "light";
        $_SESSION['orcIMG'] = "assets/img/orc.png";
        $_POST['action'] =  "<b>L'orc n'a plus que " . $_SESSION['orcPV'] . " de point de vie.</b>";
        $_POST['btnCombat'] = "Tour de l'orc";
        $_SESSION['commence'] = "orc";

    }  else if ($_SESSION['commence'] == "orc") { //DEBUT DE L'ATTAQUE DE L'ORC

        $alea = rand(1, 2);
        $_SESSION['guerrierIMG'] = "assets/img/guerrier.png";
        if ($alea == 2 && ($_SESSION['orc']->getMana() > 200)) {

            $degat = $_SESSION['orc']->useMagic();
            $_SESSION['orcIMG'] = "assets/img/orcFORT.png";
            $_SESSION['degatOrc'] = $degat;
            $_SESSION['orcMANA'] = $_SESSION['orc']->getMana();
            $_POST['couleurMANAOrc'] = "danger";
            $_POST['action'] =  "<b>L'orc utilise sa mana, devient super fort et inflige " . $degat . " de dégat !</b>";
            $_POST['btnCombat'] = "Continue";
            $_SESSION['commence'] = "suite orc";

        } else {

        $degat = $_SESSION['orc']->attack();
        $_SESSION['degatOrc'] = $degat;
        $_POST['action'] =  "<b>L'orc attaque et inflige " . $degat . " de dégat !</b>";
        $aleaMANA = rand(10, 60);
        if (($_SESSION['orc']->getMana() + $aleaMANA) < 300) {
            $_SESSION['orc']->setMana($_SESSION['orc']->getMana() + $aleaMANA);
            $_POST['couleurMANAOrc'] = "success";
            $_SESSION['orcMANA'] = $_SESSION['orc']->getMana();
        } else if ($_SESSION['orc']->getMana() == 300) {
            $_SESSION['orc']->setMana(300);
        } else {
            $_SESSION['orc']->setMana(300);
            $_POST['couleurMANAOrc'] = "success";
            $_SESSION['orcMANA'] = $_SESSION['orc']->getMana();
        }
        $_POST['btnCombat'] = "Continue";
        $_SESSION['commence'] = "suite orc";

    }} else if ($_SESSION['commence'] == "suite orc") { //LA SUITE DE L'ATTAQUE DE L'ORC

        $degatFinale = $_SESSION['guerrier']->getDamage($_SESSION['degatOrc']);
        $_SESSION['orcIMG'] = "assets/img/orc.png";
        $_POST['couleurMANAOrc'] = "light";
        $_SESSION['guerrierIMG'] = "assets/img/guerrierDamage.png";
        $_POST['couleurPVGuerrier'] = "danger";
        $_SESSION['guerrierPV'] = $_SESSION['guerrier']->getHealth();
        $_POST['action'] =  "<b>Le bouclier absorbe " . $_SESSION['guerrier']->getShieldAbsorbtion() . " de dégat, le guerrier ne subit que " . $degatFinale . " de dégats !</b>";
        $_POST['btnCombat'] = "Continue";
        $_SESSION['commence'] = "fin orc";

    } else if ($_SESSION['commence'] == "fin orc") { //FIN DE L'ATTAQUE DE L'ORC

        $_POST['couleurPVGuerrier'] = "light";
        $_SESSION['guerrierIMG'] = "assets/img/guerrier.png";
        $_POST['action'] =  "<b>Le guerrier n'a plus que " . $_SESSION['guerrier']->getHealth() . " de point de vie.</b>";
        $_POST['btnCombat'] = "Tour du guerrier";
        $_SESSION['commence'] = "guerrier";

    }
}


// var_dump($_POST);
// var_dump($_SESSION);
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


<body class="texte">

    <div class="container text-center">
        <div class="row">
            <div class="col-10">
                <h1 class="text-start">Guerrier VS Orc</h1>
            </div>
            <div class="col-1 d-flex align-items-center">
                <form method="post">
                    <button type="submit" class="btn btn-primary mx-3" value="ResetMonnaie"
                        name="ResetMonnaie">Reset</button>
                </form>
                <p class="mt-3"><?= $_COOKIE['monnaie'] ?? 0 ?></p>
                <img src="assets/img/monnaie.png" alt="Sousous" class="monnais">
            </div>
        </div>
    </div>


    <!-- Terrain et bouton -->
    <div class="container text-center">
        <div class="row border cadre mb-2">
            <div class="row terrain">
                <div class="col">
                    <img src="<?= $_SESSION['guerrierIMG'] ?? "assets/img/PasEncoreLa.png" ?>" alt="Jolie guerrier"
                        class="perso">
                </div>
                <div class="col">
                    <img src="<?= $_SESSION['orcIMG'] ?? "assets/img/PasEncoreLa.png" ?>" alt="Méchant et pas bo orc"
                        class="perso">
                </div>
            </div>
            <div class="row">
                <div class="col-1 d-flex ms-5">
                    <img src="assets/img/vie.png" alt="vie" class="logo">
                    <p class="text-<?= $_POST['couleurPVGuerrier'] ?? "light"?>"> <?= $_SESSION['guerrierPV'] ?? ""?>
                    </p>
                </div>
                <div class="col-1 d-flex">
                    <img src="assets/img/mana.png" alt="mana" class="logo">
                    <p class="text-<?= $_POST['couleurMANAGuerrier'] ?? "light"?>">
                        <?= $_SESSION['guerrierMANA'] ?? ""?> </p>
                </div>
                <div class="col-1 d-flex">
                    <img src="assets/img/arme.png" alt="force" class="logo">
                    <p class="text-light"> <?= $_SESSION['guerrierFORCE'] ?? ""?>
                    </p>
                </div>
                <div class="col-1 d-flex">
                    <img src="assets/img/bouclier.png" alt="defense" class="logo">
                    <p class="text-light"> <?= $_SESSION['guerrierDEFENSE'] ?? ""?> </p>
                </div>
                <div class="col-3"></div>
                <div class="col-1 d-flex ms-5">
                    <img src="assets/img/vie.png" alt="vie" class="logo">
                    <p class="text-<?= $_POST['couleurPVOrc'] ?? "light"?>"> <?= $_SESSION['orcPV'] ?? ""?> </p>
                </div>
                <div class="col-1 d-flex">
                    <img src="assets/img/mana.png" alt="mana" class="logo">
                    <p class="text-<?= $_POST['couleurMANAOrc'] ?? "light"?>"> <?= $_SESSION['orcMANA'] ?? ""?> </p>
                </div>
                <div class="col-2 d-flex">
                    <img src="assets/img/arme.png" alt="force" class="logo">
                    <p class="text-light"> <?= $_SESSION['orcFORCE'] ?? ""?> </p>
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



    <div class="container text-center mt-2">
        <div class="row">
            <div class="col">
                <form action="" method="post">
                    <span
                        class="ms-2 text-danger fst-italic fw-light"><?= $_SESSION["parisGuerrierErreur"] ?? '' ?></span>
                    <input type="number" class="w-25" name="montantPariGuerrier"
                        value="<?= $_SESSION["parisGuerrierMonaie"] ?? "" ?>">
                    <?php if ($btnParis !== false) { ?>
                    <button type="submit" class="btn btn-primary" value="parieGuerrier" name="parieGuerrier">
                        PARIE !
                    </button>
                    <?php } ?>
                </form>
            </div>
            <div class="col">
                <!-- Bouton nouvelle partie -->
                <div class="text-center mt-2">
                    <form method="post">
                        <button type="submit" class="btn btn-primary" value="nouvellePartie"
                            name="nouvellePartie">Nouveau
                            jeu</button>
                    </form>
                </div>
            </div>
            <div class="col">
                <form action="" method="post">
                    <span
                        class="ms-2 text-danger fst-italic fw-light"><?= $_SESSION["parisOrcErreur"] ?? '' ?></span>
                    <input type="number" class="w-25" name="montantParisOrc"
                        value="<?= $_SESSION["parisOrcMonaie"] ?? "" ?>">
                    <?php if ($btnParis !== false) { ?>
                    <button type="submit" class="btn btn-primary" value="parieOrc" name="parieOrc">
                        PARIE !
                    </button>
                    <?php } ?>
                </form>
            </div>
        </div>
    </div>


    <!-- Bouton combat -->
    <?php if ($btnCombat !== false) { ?>
    <div class="text-center mt-2">
        <form method="post">
            <button type="submit" class="btn btn-danger" value="combat"
                name="combat"><?= $_POST['btnCombat'] ?? 'Combat !' ?></button>
        </form>
    </div>
    <?php } ?>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
    </script>
</body>


</html>