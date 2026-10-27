<?php
require_once 'ensemble.php';

$erreurs  = [];
$reouvrir = null;

// Couleurs proposées automatiquement aux nouveaux niveaux
$palette = [
    ["bg-sky-100",     "text-sky-600"],
    ["bg-emerald-100", "text-emerald-600"],
    ["bg-amber-100",   "text-amber-600"],
    ["bg-purple-100",  "text-purple-600"],
    ["bg-rose-100",    "text-rose-600"],
    ["bg-teal-100",    "text-teal-600"],
    ["bg-indigo-100",  "text-indigo-600"],
];

// =====================================================
// 1. TRAITEMENT DES FORMULAIRES (POST)
// =====================================================
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $operation = $_POST["operation"] ?? "";
    $idOrigine = trim($_POST["id_origine"] ?? "");

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
            array_splice($_SESSION["Niveaux"], $i, 1);
            flash("« " . $libelle . " » a été supprimé");
        }
        rediriger("Niveaux.php");
    }

    // ---------- AJOUTER / MODIFIER ----------
    $donnees = [
        "code"    => strtoupper(trim($_POST["code"] ?? "")),
        "libelle" => trim($_POST["libelle"] ?? ""),
    ];

    if ($donnees["code"] === "")    { $erreurs["code"]    = "Ce champ est obligatoire"; }
    if ($donnees["libelle"] === "") { $erreurs["libelle"] = "Ce champ est obligatoire"; }

    $indexOrigine  = ($operation === "modifier") ? trouverIndex($_SESSION["Niveaux"], "code", $idOrigine) : -1;
    $indexExistant = trouverIndex($_SESSION["Niveaux"], "code", $donnees["code"]);

    if (!isset($erreurs["code"]) && $indexExistant >= 0 && $indexExistant !== $indexOrigine) {
        $erreurs["code"] = "Ce code existe déjà";
    }

    if (empty($erreurs)) {
        if ($operation === "modifier") {
            if ($indexOrigine < 0) {
                flash("Niveau introuvable", "erreur");
            } else {
                $ancien     = $_SESSION["Niveaux"][$indexOrigine];
                $ancienCode = $ancien["code"];

                // On garde les couleurs du niveau, on change seulement code et libellé
                $_SESSION["Niveaux"][$indexOrigine] = [
                    "code"       => $donnees["code"],
                    "libelle"    => $donnees["libelle"],
                    "bg_badge"   => $ancien["bg_badge"],
                    "text_badge" => $ancien["text_badge"],
                ];

                // Si le code change, on met à jour les classes qui l'utilisent
                if (strcasecmp($ancienCode, $donnees["code"]) !== 0) {
                    foreach ($_SESSION["Classes"] as $k => $classe) {
                        if (strcasecmp($classe["niveau"], $ancienCode) === 0) {
                            $_SESSION["Classes"][$k]["niveau"] = $donnees["code"];
                        }
                    }
                }
                flash("« " . $donnees["libelle"] . " » a été modifié");
            }
        } else {
            // Couleur choisie selon le nombre de niveaux déjà créés
            $couleur = $palette[count($_SESSION["Niveaux"]) % count($palette)];
            $_SESSION["Niveaux"][] = [
                "code"       => $donnees["code"],
                "libelle"    => $donnees["libelle"],
                "bg_badge"   => $couleur[0],
                "text_badge" => $couleur[1],
            ];
            flash("« " . $donnees["libelle"] . " » a été ajouté");
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

    <main class="flex-1 ml-64 p-8">

        <header class="w-full flex justify-between items-center text-xs font-semibold mb-6">
            <div class="flex items-center gap-2 text-slate-500">
                <span>École 221</span>
                <span>/</span>
                <span class="font-extrabold text-slate-800">Niveaux</span>
            </div>
            <div class="flex items-center gap-2 text-slate-500 font-bold">
                <span>Année universitaire 2025–2026</span>
                <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full"></span>
            </div>
        </header>

        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Niveaux</h1>
                <p class="text-sm text-slate-500 font-semibold mt-1">
                    <?= $total ?> <?= $total > 1 ? "niveaux enregistrés" : "niveau enregistré" ?>
                </p>
            </div>
            <button type="button" data-ouvrir-drawer class="bg-[#0B1728] hover:bg-[#1A283B] text-white px-5 py-3 rounded-xl font-bold text-sm flex items-center gap-2 transition shadow-sm">
                <i class="fi fi-rr-plus text-xs"></i>
                <span>Nouveau niveau</span>
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

            <?php if (empty($niveaux)): ?>
                <p class="col-span-full text-center text-slate-400 font-bold py-16">Aucun niveau enregistré</p>
            <?php endif; ?>

            <?php foreach ($niveaux as $n): ?>
                <?php
                    $nbClasses   = nbClassesDeNiveau($n["code"]);
                    $nbEtudiants = nbEtudiantsDeNiveau($n["code"]);
                ?>
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-center mb-5 gap-2">
                            <span class="<?= e($n["bg_badge"]) ?> <?= e($n["text_badge"]) ?> font-black text-sm px-4 py-2 rounded-xl tracking-wider uppercase"><?= e($n["code"]) ?></span>
                            <span class="bg-[#F0F5FD] text-[#475569] font-bold text-xs px-3 py-1 rounded-full">
                                <?= $nbClasses ?> <?= $nbClasses > 1 ? "classes" : "classe" ?>
                            </span>
                        </div>

                        <h2 class="text-xl font-extrabold text-slate-900 leading-snug"><?= e($n["libelle"]) ?></h2>
                        <p class="text-sm text-slate-400 font-semibold mt-1">
                            <?= $nbEtudiants ?> <?= $nbEtudiants > 1 ? "étudiants" : "étudiant" ?>
                        </p>
                    </div>

                    <div class="flex justify-end items-center mt-4 pt-3 border-t border-slate-100">
                        <button type="button" data-item="<?= e(json_encode($n, JSON_UNESCAPED_UNICODE)) ?>" class="btn-modif text-slate-400 hover:text-sky-600 transition mr-3" title="Modifier">
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
                    <label class="block text-sm font-bold mb-1">Code <span class="text-slate-400 font-semibold">(ex : L1, M2)</span></label>
                    <input name="code" type="text" class="w-full border rounded-lg px-3 py-2" />
                    <p class="erreur <?= isset($erreurs["code"]) ? "" : "hidden" ?> text-sm text-red-600 mt-1"><?= e($erreurs["code"] ?? "") ?></p>
                </div>

                <div>
                    <label class="block text-sm font-bold mb-1">Libellé <span class="text-slate-400 font-semibold">(ex : Licence 1)</span></label>
                    <input name="libelle" type="text" class="w-full border rounded-lg px-3 py-2" />
                    <p class="erreur <?= isset($erreurs["libelle"]) ? "" : "hidden" ?> text-sm text-red-600 mt-1"><?= e($erreurs["libelle"] ?? "") ?></p>
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