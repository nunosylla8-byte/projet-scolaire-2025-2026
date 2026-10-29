<?php
require_once 'ensemble.php';

$erreurs  = [];
$reouvrir = null;

// =====================================================
// 1. TRAITEMENT DES FORMULAIRES (POST)
// =====================================================
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $operation = $_POST["operation"] ?? "";
    $idOrigine = trim($_POST["id_origine"] ?? "");

    // ---------- ANNULER LA DERNIÈRE SUPPRESSION (bouton du toast) ----------
    if ($operation === "annuler") {
        if (annulerSuppression("Classes")) {
            flash("Suppression annulée");
        }
        rediriger("Classes.php");
    }

    // ---------- SUPPRIMER ----------
    if ($operation === "supprimer") {
        $code = trim($_POST["code"] ?? "");
        $i = trouverIndex($_SESSION["Classes"], "code", $code);

        if ($i < 0) {
            flash("Classe introuvable", "erreur");
        } elseif (nbEtudiantsDeClasse($code) > 0) {
            // Règle de gestion : on ne supprime pas une classe qui contient des étudiants
            flash("Impossible : cette classe contient encore des étudiants", "erreur");
        } else {
            $nom = $_SESSION["Classes"][$i]["nom"];
            supprimerAvecAnnulation("Classes", $i);
            flash("« " . $nom . " » a été supprimée", "succes", true);
        }
        rediriger("Classes.php");
    }

    // ---------- AJOUTER / MODIFIER ----------
    $donnees = [
        "nom"      => trim($_POST["nom"] ?? ""),
        "code"     => strtoupper(trim($_POST["code"] ?? "")),
        "filiere"  => trim($_POST["filiere"] ?? ""),
        "niveau"   => trim($_POST["niveau"] ?? ""),
        "capacite" => trim($_POST["capacite"] ?? ""),
    ];

    if ($donnees["nom"] === "")  { $erreurs["nom"]  = "Ce champ est obligatoire"; }
    if ($donnees["code"] === "") { $erreurs["code"] = "Ce champ est obligatoire"; }

    // La filière et le niveau doivent exister vraiment
    if ($donnees["filiere"] === "" || trouverIndex($_SESSION["Filieres"], "code", $donnees["filiere"]) < 0) {
        $erreurs["filiere"] = "Choisissez une filière";
    }
    if ($donnees["niveau"] === "" || trouverIndex($_SESSION["Niveaux"], "code", $donnees["niveau"]) < 0) {
        $erreurs["niveau"] = "Choisissez un niveau";
    }

    // La capacité doit être un entier supérieur ou égal à 1
    $capacite = filter_var($donnees["capacite"], FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
    if ($capacite === false) {
        $erreurs["capacite"] = "Entrez un nombre entier supérieur à 0";
    }

    // Code déjà utilisé par une AUTRE classe ?
    $indexOrigine  = ($operation === "modifier") ? trouverIndex($_SESSION["Classes"], "code", $idOrigine) : -1;
    $indexExistant = trouverIndex($_SESSION["Classes"], "code", $donnees["code"]);

    if (!isset($erreurs["code"]) && $indexExistant >= 0 && $indexExistant !== $indexOrigine) {
        $erreurs["code"] = "Ce code existe déjà";
    }

    // En modification : la capacité ne peut pas être inférieure à l'effectif actuel
    if ($operation === "modifier" && $indexOrigine >= 0 && $capacite !== false) {
        $effectif = nbEtudiantsDeClasse($_SESSION["Classes"][$indexOrigine]["code"]);
        if ($capacite < $effectif) {
            $erreurs["capacite"] = "Cette classe a déjà " . $effectif . " étudiant(s)";
        }
    }

    if (empty($erreurs)) {
        $classe = [
            "code"     => $donnees["code"],
            "nom"      => $donnees["nom"],
            "filiere"  => $donnees["filiere"],
            "niveau"   => $donnees["niveau"],
            "capacite" => $capacite,
        ];

        if ($operation === "modifier") {
            if ($indexOrigine < 0) {
                flash("Classe introuvable", "erreur");
            } else {
                $ancienCode = $_SESSION["Classes"][$indexOrigine]["code"];
                $_SESSION["Classes"][$indexOrigine] = $classe;

                // Si le code change, on met à jour les étudiants de cette classe
                if (strcasecmp($ancienCode, $classe["code"]) !== 0) {
                    foreach ($_SESSION["Etudiants"] as $k => $etudiant) {
                        if (strcasecmp($etudiant["classe"], $ancienCode) === 0) {
                            $_SESSION["Etudiants"][$k]["classe"] = $classe["code"];
                        }
                    }
                }
                flash("« " . $classe["nom"] . " » a été modifiée");
            }
        } else {
            $_SESSION["Classes"][] = $classe;
            flash("« " . $classe["nom"] . " » a été ajoutée");
        }
        rediriger("Classes.php");
    }

    $reouvrir = [
        "donnees" => $donnees + ["id_origine" => $idOrigine],
        "modif"   => ($operation === "modifier"),
    ];
}

// =====================================================
// 2. DONNÉES À AFFICHER
// =====================================================
$classes  = $_SESSION["Classes"];
$filieres = $_SESSION["Filieres"];
$niveaux  = $_SESSION["Niveaux"];
$total    = count($classes);
$flash    = lireFlash();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classes - École 221</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.1.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>

    <style>
        body { font-family: 'Nunito', sans-serif; }
    </style>
</head>
<body class="bg-[#F0F4F9] text-gray-800 flex min-h-screen">

    <?php include 'sidebar.php'; ?>

    <main class="flex-1 min-w-0 lg:ml-64 p-4 pt-20 sm:p-6 sm:pt-20 lg:p-8">

        <header class="w-full flex flex-wrap justify-between items-center gap-2 text-xs font-semibold mb-6">
            <div class="flex items-center gap-2 text-slate-500">
                <span class="hover:underline cursor-pointer">École 221</span>
                <span>/</span>
                <span class="font-extrabold text-slate-800">Classes</span>
            </div>
            <div class="flex items-center gap-2 text-slate-500 font-bold">
                <span>Année universitaire 2025–2026</span>
                <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full"></span>
            </div>
        </header>

        <!-- Titre + bouton "Nouvelle classe" -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Classes</h1>
                <p class="text-sm text-slate-500 font-semibold mt-1">
                    <?= $total ?> <?= $total > 1 ? "classes enregistrées" : "classe enregistrée" ?>
                </p>
            </div>
            <button type="button" data-ouvrir-drawer class="bg-[#0B1728] hover:bg-[#1A283B] text-white px-5 py-3 rounded-xl font-bold text-sm flex items-center justify-center gap-2 transition shadow-sm">
                <i class="fi fi-rr-plus text-xs"></i>
                <span>Nouvelle classe</span>
            </button>
        </div>

        <!-- Grille des cartes -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-6">

            <?php if (empty($classes)): ?>
                <p class="col-span-full text-center text-slate-400 font-bold py-16">Aucune classe enregistrée</p>
            <?php endif; ?>

            <?php foreach ($classes as $c): ?>
                <?php
                    // Informations liées, retrouvées grâce aux codes
                    $filiere  = trouver($_SESSION["Filieres"], "code", $c["filiere"]);
                    $capacite = (int)$c["capacite"];
                    $nb       = nbEtudiantsDeClasse($c["code"]);
                    $pct      = $capacite > 0 ? min(100, round($nb / $capacite * 100)) : 0;
                ?>
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start mb-1 gap-2">
                            <h2 class="text-lg font-extrabold text-slate-900 leading-snug"><?= e($c["nom"]) ?></h2>
                            <span class="bg-[#0B1728] text-white font-black text-xs px-2.5 py-1 rounded-xl"><?= e($c["niveau"]) ?></span>
                        </div>

                        <div class="flex justify-between items-center mb-4">
                            <p class="text-xs text-slate-400 font-bold uppercase tracking-wider"><?= e($c["code"]) ?></p>
                            <div class="whitespace-nowrap flex items-center">
                                <button type="button" data-item="<?= e(json_encode($c, JSON_UNESCAPED_UNICODE)) ?>" class="btn-modif text-slate-400 hover:text-sky-600 transition mr-3" title="Modifier">
                                    <i class="fi fi-rr-pencil text-base pointer-events-none"></i>
                                </button>

                                <form method="POST" action="Classes.php" class="form-suppr inline" data-message="Voulez-vous supprimer la classe « <?= e($c["nom"]) ?> » ?">
                                    <input type="hidden" name="operation" value="supprimer">
                                    <input type="hidden" name="code" value="<?= e($c["code"]) ?>">
                                    <button type="submit" class="text-slate-400 hover:text-red-600 transition" title="Supprimer">
                                        <i class="fi fi-rr-trash text-base pointer-events-none"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="space-y-1 text-xs font-semibold mb-6">
                            <p class="text-slate-400">
                                Filière <span class="text-slate-900 font-extrabold ml-1"><?= e($filiere["nom"] ?? "—") ?></span>
                            </p>
                            <p class="text-slate-400">
                                Capacité <span class="text-slate-900 font-extrabold ml-1"><?= $capacite ?> places</span>
                            </p>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between items-center text-xs font-bold mb-1.5">
                            <span class="text-slate-500"><?= $nb ?> <?= $nb > 1 ? "étudiants" : "étudiant" ?></span>
                            <span class="text-slate-500"><?= $pct ?>%</span>
                        </div>
                        <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-[#0B1728] h-full rounded-full" style="width: <?= $pct ?>%;"></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>
    </main>

    <!-- ==================== DRAWER ==================== -->
    <aside id="drawer"
           data-cle="code"
           data-titre-ajout="Nouvelle classe"
           data-titre-modif="Modifier la classe"
           class="fixed top-0 right-0 h-full w-full max-w-md bg-white text-black z-[60] translate-x-full transition-transform duration-300 flex flex-col">

        <div class="flex items-center gap-4 p-4 border-b">
            <button type="button" data-fermer-drawer class="text-slate-500 hover:text-black" title="Fermer">
                <i class="fi fi-rr-cross-small text-2xl"></i>
            </button>
            <h2 id="titre-drawer" class="text-xl font-extrabold">Nouvelle classe</h2>
        </div>

        <form id="formulaire" method="POST" action="Classes.php" novalidate class="flex flex-col flex-1 min-h-0">
            <input type="hidden" name="operation" value="ajouter">
            <input type="hidden" name="id_origine" value="">

            <div class="flex-1 overflow-y-auto p-4 space-y-4">

                <div>
                    <label class="block text-sm font-bold mb-1">Nom de la classe</label>
                    <input name="nom" type="text" class="w-full border rounded-lg px-3 py-2" />
                    <p class="erreur <?= isset($erreurs["nom"]) ? "" : "hidden" ?> text-sm text-red-600 mt-1"><?= e($erreurs["nom"] ?? "") ?></p>
                </div>

                <div>
                    <label class="block text-sm font-bold mb-1">Code</label>
                    <input name="code" type="text" class="w-full border rounded-lg px-3 py-2" />
                    <p class="erreur <?= isset($erreurs["code"]) ? "" : "hidden" ?> text-sm text-red-600 mt-1"><?= e($erreurs["code"] ?? "") ?></p>
                </div>

                <div>
                    <label class="block text-sm font-bold mb-1">Filière</label>
                    <select name="filiere" class="w-full border rounded-lg px-3 py-2 bg-white">
                        <option value="">-- Choisir une filière --</option>
                        <?php foreach ($filieres as $f): ?>
                            <option value="<?= e($f["code"]) ?>"><?= e($f["nom"]) ?> (<?= e($f["code"]) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                    <p class="erreur <?= isset($erreurs["filiere"]) ? "" : "hidden" ?> text-sm text-red-600 mt-1"><?= e($erreurs["filiere"] ?? "") ?></p>
                </div>

                <div>
                    <label class="block text-sm font-bold mb-1">Niveau</label>
                    <select name="niveau" class="w-full border rounded-lg px-3 py-2 bg-white">
                        <option value="">-- Choisir un niveau --</option>
                        <?php foreach ($niveaux as $n): ?>
                            <option value="<?= e($n["code"]) ?>"><?= e($n["libelle"]) ?> (<?= e($n["code"]) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                    <p class="erreur <?= isset($erreurs["niveau"]) ? "" : "hidden" ?> text-sm text-red-600 mt-1"><?= e($erreurs["niveau"] ?? "") ?></p>
                </div>

                <div>
                    <label class="block text-sm font-bold mb-1">Capacité (places)</label>
                    <input name="capacite" type="number" min="1" class="w-full border rounded-lg px-3 py-2" />
                    <p class="erreur <?= isset($erreurs["capacite"]) ? "" : "hidden" ?> text-sm text-red-600 mt-1"><?= e($erreurs["capacite"] ?? "") ?></p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-4 p-4 border-t">
                <button type="button" data-fermer-drawer class="text-gray-600 font-bold">Annuler</button>
                <button id="btn-valider" type="submit" class="bg-[#0B1728] text-white font-bold px-6 py-2 rounded-lg">AJOUTER</button>
            </div>
        </form>
    </aside>

    <?php include 'ui.php'; ?>
</body>
</html>