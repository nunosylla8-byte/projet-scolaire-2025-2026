<?php
require_once 'ensemble.php';

$erreurs  = [];
$reouvrir = null;

// Couleurs proposées pour le badge : [nom affiché, fond, texte]
$COULEURS = [
    ["Bleu",   "bg-sky-100",     "text-sky-600"],
    ["Vert",   "bg-emerald-100", "text-emerald-600"],
    ["Violet", "bg-violet-100",  "text-violet-600"],
    ["Orange", "bg-orange-100",  "text-orange-600"],
    ["Rose",   "bg-pink-100",    "text-pink-600"],
    ["Jaune",  "bg-yellow-100",  "text-yellow-700"],
    ["Rouge",  "bg-red-100",     "text-red-600"],
];

// =====================================================
// 1. TRAITEMENT DES FORMULAIRES (POST)
// =====================================================
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $operation = $_POST["operation"] ?? "";
    $idOrigine = trim($_POST["id_origine"] ?? "");

    // ---------- ANNULER LA DERNIÈRE SUPPRESSION (bouton du toast) ----------
    if ($operation === "annuler") {
        if (annulerSuppression("Niveaux")) {
            flash("Suppression annulée");
        }
        rediriger("Niveaux.php");
    }

    // ---------- SUPPRIMER ----------
    if ($operation === "supprimer") {
        $code = trim($_POST["code"] ?? "");
        $i = trouverIndex($_SESSION["Niveaux"], "code", $code);

        if ($i < 0) {
            flash("Niveau introuvable", "erreur");
        } elseif (nbClassesDeNiveau($code) > 0) {
            // Règle de gestion : on ne supprime pas un niveau qui contient des classes
            flash("Impossible : ce niveau contient encore des classes", "erreur");
        } else {
            $libelle = $_SESSION["Niveaux"][$i]["libelle"];
            supprimerAvecAnnulation("Niveaux", $i);
            flash("« " . $libelle . " » a été supprimé", "succes", true);
        }
        rediriger("Niveaux.php");
    }

    // ---------- AJOUTER / MODIFIER ----------
    $couleur = (int)($_POST["couleur"] ?? 0);
    if ($couleur < 0 || $couleur >= count($COULEURS)) {
        $couleur = 0;
    }

    $donnees = [
        "code"    => strtoupper(trim($_POST["code"] ?? "")),
        "libelle" => trim($_POST["libelle"] ?? ""),
        "couleur" => $couleur,
    ];

    if ($donnees["code"] === "")    { $erreurs["code"]    = "Ce champ est obligatoire"; }
    if ($donnees["libelle"] === "") { $erreurs["libelle"] = "Ce champ est obligatoire"; }

    $indexOrigine  = ($operation === "modifier") ? trouverIndex($_SESSION["Niveaux"], "code", $idOrigine) : -1;
    $indexExistant = trouverIndex($_SESSION["Niveaux"], "code", $donnees["code"]);

    if (!isset($erreurs["code"]) && $indexExistant >= 0 && $indexExistant !== $indexOrigine) {
        $erreurs["code"] = "Ce code existe déjà";
    }

    if (empty($erreurs)) {
        $niveau = [
            "code"       => $donnees["code"],
            "libelle"    => $donnees["libelle"],
            "bg_badge"   => $COULEURS[$couleur][1],
            "text_badge" => $COULEURS[$couleur][2],
        ];

        if ($operation === "modifier") {
            if ($indexOrigine < 0) {
                flash("Niveau introuvable", "erreur");
            } else {
                $ancienCode = $_SESSION["Niveaux"][$indexOrigine]["code"];
                $_SESSION["Niveaux"][$indexOrigine] = $niveau;

                // Si le code change, on met à jour les classes qui l'utilisent
                if (strcasecmp($ancienCode, $niveau["code"]) !== 0) {
                    foreach ($_SESSION["Classes"] as $k => $classe) {
                        if (strcasecmp($classe["niveau"], $ancienCode) === 0) {
                            $_SESSION["Classes"][$k]["niveau"] = $niveau["code"];
                        }
                    }
                }
                flash("« " . $niveau["libelle"] . " » a été modifié");
            }
        } else {
            $_SESSION["Niveaux"][] = $niveau;
            flash("« " . $niveau["libelle"] . " » a été ajouté");
        }
        rediriger("Niveaux.php");
    }

    $reouvrir = [
        "donnees" => $donnees + ["id_origine" => $idOrigine],
        "modif"   => ($operation === "modifier"),
    ];
}

// =====================================================
// 2. DONNÉES À AFFICHER
// =====================================================
$niveaux = $_SESSION["Niveaux"];
$total   = count($niveaux);
$flash   = lireFlash();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Niveaux - École 221</title>

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
                <span class="font-extrabold text-slate-800">Niveaux</span>
            </div>
            <div class="flex items-center gap-2 text-slate-500 font-bold">
                <span>Année universitaire 2025–2026</span>
                <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full"></span>
            </div>
        </header>

        <!-- Titre + bouton "Nouveau niveau" -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Niveaux</h1>
                <p class="text-sm text-slate-500 font-semibold mt-1">
                    <?= $total ?> <?= $total > 1 ? "niveaux enregistrés" : "niveau enregistré" ?>
                </p>
            </div>
            <button type="button" data-ouvrir-drawer class="bg-[#0B1728] hover:bg-[#1A283B] text-white px-5 py-3 rounded-xl font-bold text-sm flex items-center justify-center gap-2 transition shadow-sm">
                <i class="fi fi-rr-plus text-xs"></i>
                <span>Nouveau niveau</span>
            </button>
        </div>

        <!-- Grille des cartes -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-5">

            <?php if (empty($niveaux)): ?>
                <p class="col-span-full text-center text-slate-400 font-bold py-16">Aucun niveau enregistré</p>
            <?php endif; ?>

            <?php foreach ($niveaux as $n): ?>
                <?php
                    $nb = nbClassesDeNiveau($n["code"]);

                    // Position de la couleur dans la liste (pour pré-sélectionner le choix en modification)
                    $indexCouleur = 0;
                    foreach ($COULEURS as $k => $c) {
                        if ($c[1] === $n["bg_badge"]) { $indexCouleur = $k; }
                    }
                    $item = $n + ["couleur" => $indexCouleur];
                ?>
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex flex-col items-center justify-center text-center">
                    <div class="w-16 h-16 rounded-2xl <?= e($n["bg_badge"]) ?> <?= e($n["text_badge"]) ?> flex items-center justify-center font-black text-xl mb-4">
                        <?= e($n["code"]) ?>
                    </div>
                    <h2 class="text-lg font-extrabold text-slate-900 mb-1"><?= e($n["libelle"]) ?></h2>
                    <p class="text-xs font-semibold text-slate-400"><?= $nb ?> <?= $nb > 1 ? "classes" : "classe" ?></p>

                    <div class="mt-4 pt-3 border-t border-slate-100 w-full flex justify-center gap-4">
                        <button type="button" data-item="<?= e(json_encode($item, JSON_UNESCAPED_UNICODE)) ?>" class="btn-modif text-slate-400 hover:text-sky-600 transition" title="Modifier">
                            <i class="fi fi-rr-pencil text-base pointer-events-none"></i>
                        </button>

                        <form method="POST" action="Niveaux.php" class="form-suppr" data-message="Voulez-vous supprimer le niveau « <?= e($n["libelle"]) ?> » ?">
                            <input type="hidden" name="operation" value="supprimer">
                            <input type="hidden" name="code" value="<?= e($n["code"]) ?>">
                            <button type="submit" class="text-slate-400 hover:text-red-600 transition" title="Supprimer">
                                <i class="fi fi-rr-trash text-base pointer-events-none"></i>
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>
    </main>

    <!-- ==================== DRAWER ==================== -->
    <aside id="drawer"
           data-cle="code"
           data-titre-ajout="Nouveau niveau"
           data-titre-modif="Modifier le niveau"
           class="fixed top-0 right-0 h-full w-full max-w-md bg-white text-black z-[60] translate-x-full transition-transform duration-300 flex flex-col">

        <div class="flex items-center gap-4 p-4 border-b">
            <button type="button" data-fermer-drawer class="text-slate-500 hover:text-black" title="Fermer">
                <i class="fi fi-rr-cross-small text-2xl"></i>
            </button>
            <h2 id="titre-drawer" class="text-xl font-extrabold">Nouveau niveau</h2>
        </div>

        <form id="formulaire" method="POST" action="Niveaux.php" novalidate class="flex flex-col flex-1 min-h-0">
            <input type="hidden" name="operation" value="ajouter">
            <input type="hidden" name="id_origine" value="">

            <div class="flex-1 overflow-y-auto p-4 space-y-4">

                <div>
                    <label class="block text-sm font-bold mb-1">Code (ex : L1, M2)</label>
                    <input name="code" type="text" class="w-full border rounded-lg px-3 py-2" />
                    <p class="erreur <?= isset($erreurs["code"]) ? "" : "hidden" ?> text-sm text-red-600 mt-1"><?= e($erreurs["code"] ?? "") ?></p>
                </div>

                <div>
                    <label class="block text-sm font-bold mb-1">Libellé (ex : Licence 1)</label>
                    <input name="libelle" type="text" class="w-full border rounded-lg px-3 py-2" />
                    <p class="erreur <?= isset($erreurs["libelle"]) ? "" : "hidden" ?> text-sm text-red-600 mt-1"><?= e($erreurs["libelle"] ?? "") ?></p>
                </div>

                <div>
                    <label class="block text-sm font-bold mb-1">Couleur du badge</label>
                    <select name="couleur" class="w-full border rounded-lg px-3 py-2">
                        <?php foreach ($COULEURS as $k => $c): ?>
                            <option value="<?= $k ?>"><?= e($c[0]) ?></option>
                        <?php endforeach; ?>
                    </select>
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