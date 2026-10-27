<?php
require_once 'ensemble.php';

$erreurs  = [];     // messages d'erreur par champ
$reouvrir = null;   // pour rouvrir le drawer si le formulaire est invalide

// =====================================================
// 1. TRAITEMENT DES FORMULAIRES (POST)
// =====================================================
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $operation = $_POST["operation"] ?? "";
    $idOrigine = trim($_POST["id_origine"] ?? "");   // code de la filière avant modification

    // ---------- SUPPRIMER ----------
    if ($operation === "supprimer") {
        $code = trim($_POST["code"] ?? "");
        $i = trouverIndex($_SESSION["Filieres"], "code", $code);

        if ($i < 0) {
            flash("Filière introuvable", "erreur");
        } elseif (nbClassesDeFiliere($code) > 0) {
            // Règle de gestion : on ne supprime pas une filière qui contient des classes
            flash("Impossible : cette filière contient encore des classes", "erreur");
        } else {
            $nom = $_SESSION["Filieres"][$i]["nom"];
            array_splice($_SESSION["Filieres"], $i, 1);
            flash("« " . $nom . " » a été supprimée");
        }
        rediriger("Filieres.php");
    }

    // ---------- AJOUTER / MODIFIER ----------
    $donnees = [
        "code"        => strtoupper(trim($_POST["code"] ?? "")),
        "nom"         => trim($_POST["nom"] ?? ""),
        "responsable" => trim($_POST["responsable"] ?? ""),   // optionnel
        "description" => trim($_POST["description"] ?? ""),   // optionnelle
    ];

    // Champs obligatoires : code et nom seulement
    if ($donnees["code"] === "") { $erreurs["code"] = "Ce champ est obligatoire"; }
    if ($donnees["nom"] === "")  { $erreurs["nom"]  = "Ce champ est obligatoire"; }

    // Code déjà utilisé par une AUTRE filière ?
    $indexOrigine = ($operation === "modifier") ? trouverIndex($_SESSION["Filieres"], "code", $idOrigine) : -1;
    $indexExistant = trouverIndex($_SESSION["Filieres"], "code", $donnees["code"]);

    if (!isset($erreurs["code"]) && $indexExistant >= 0 && $indexExistant !== $indexOrigine) {
        $erreurs["code"] = "Ce code existe déjà";
    }

    if (empty($erreurs)) {
        if ($operation === "modifier") {
            if ($indexOrigine < 0) {
                flash("Filière introuvable", "erreur");
            } else {
                $ancienCode = $_SESSION["Filieres"][$indexOrigine]["code"];
                $_SESSION["Filieres"][$indexOrigine] = $donnees;

                // Si le code change, on met à jour les classes qui l'utilisent
                if (strcasecmp($ancienCode, $donnees["code"]) !== 0) {
                    foreach ($_SESSION["Classes"] as $k => $classe) {
                        if (strcasecmp($classe["filiere"], $ancienCode) === 0) {
                            $_SESSION["Classes"][$k]["filiere"] = $donnees["code"];
                        }
                    }
                }
                flash("« " . $donnees["nom"] . " » a été modifiée");
            }
        } else {
            $_SESSION["Filieres"][] = $donnees;
            flash("« " . $donnees["nom"] . " » a été ajoutée");
        }
        rediriger("Filieres.php");   // évite le renvoi du formulaire si on actualise la page
    }

    // Erreurs : on rouvrira le drawer avec ce que l'utilisateur a saisi
    $reouvrir = [
        "donnees" => $donnees + ["id_origine" => $idOrigine],
        "modif"   => ($operation === "modifier"),
    ];
}

// =====================================================
// 2. DONNÉES À AFFICHER
// =====================================================
$filieres = $_SESSION["Filieres"];
$total    = count($filieres);
$flash    = lireFlash();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filières - École 221</title>

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

    <!-- ==================== CONTENU PRINCIPAL ==================== -->
    <main class="flex-1 ml-64 p-8">

        <header class="w-full flex justify-between items-center text-xs font-semibold mb-6">
            <div class="flex items-center gap-2 text-slate-500">
                <span>École 221</span>
                <span>/</span>
                <span class="font-extrabold text-slate-800">Filières</span>
            </div>
            <div class="flex items-center gap-2 text-slate-500 font-bold">
                <span>Année universitaire 2025–2026</span>
                <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full"></span>
            </div>
        </header>

        <!-- Titre + bouton "Nouvelle filière" -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Filières</h1>
                <p class="text-sm text-slate-500 font-semibold mt-1">
                    <?= $total ?> <?= $total > 1 ? "filières enregistrées" : "filière enregistrée" ?>
                </p>
            </div>
            <button type="button" data-ouvrir-drawer class="bg-[#0B1728] hover:bg-[#1A283B] text-white px-5 py-3 rounded-xl font-bold text-sm flex items-center gap-2 transition shadow-sm">
                <i class="fi fi-rr-plus text-xs"></i>
                <span>Nouvelle filière</span>
            </button>
        </div>

        <!-- Grille des cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

            <?php if (empty($filieres)): ?>
                <p class="col-span-full text-center text-slate-400 font-bold py-16">Aucune filière enregistrée</p>
            <?php endif; ?>

            <?php foreach ($filieres as $f): ?>
                <?php $nb = nbEtudiantsDeFiliere($f["code"]); ?>
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-center mb-5 gap-2">
                            <span class="bg-[#0B1728] text-white font-black text-xs px-3.5 py-2 rounded-xl tracking-wider uppercase"><?= e($f["code"]) ?></span>
                            <span class="bg-[#F0F5FD] text-[#475569] font-bold text-xs px-3 py-1 rounded-full">
                                <?= $nb ?> <?= $nb > 1 ? "étudiants" : "étudiant" ?>
                            </span>
                        </div>

                        <h2 class="text-xl font-extrabold text-slate-900 leading-snug"><?= e($f["nom"]) ?></h2>

                        <?php if ($f["responsable"] !== ""): ?>
                            <div class="flex items-center gap-1.5 mt-1 text-sm font-bold text-[#EAB308]">
                                <i class="fi fi-rr-user text-blue-500 text-xs"></i>
                                <span><?= e($f["responsable"]) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div>
                        <?php if ($f["description"] !== ""): ?>
                            <p class="text-sm text-slate-400 font-semibold leading-relaxed mt-4"><?= e($f["description"]) ?></p>
                        <?php endif; ?>

                        <div class="flex justify-end items-center mt-4 pt-3 border-t border-slate-100">
                            <!-- Modifier : ouvre le drawer rempli avec les données de la filière -->
                            <button type="button" data-item="<?= e(json_encode($f, JSON_UNESCAPED_UNICODE)) ?>" class="btn-modif text-slate-400 hover:text-sky-600 transition mr-3" title="Modifier">
                                <i class="fi fi-rr-pencil text-base pointer-events-none"></i>
                            </button>

                            <!-- Supprimer : formulaire POST, le modal demande la confirmation -->
                            <form method="POST" action="Filieres.php" class="form-suppr" data-message="Voulez-vous supprimer la filière « <?= e($f["nom"]) ?> » ?">
                                <input type="hidden" name="operation" value="supprimer">
                                <input type="hidden" name="code" value="<?= e($f["code"]) ?>">
                                <button type="submit" class="text-slate-400 hover:text-red-600 transition" title="Supprimer">
                                    <i class="fi fi-rr-trash text-base pointer-events-none"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>
    </main>

    <!-- ==================== DRAWER (formulaire ajout / modification) ==================== -->
    <aside id="drawer"
           data-cle="code"
           data-titre-ajout="Nouvelle filière"
           data-titre-modif="Modifier la filière"
           class="fixed top-0 right-0 h-full w-full max-w-md bg-white text-black z-[60] translate-x-full transition-transform duration-300 flex flex-col">

        <div class="flex items-center gap-4 p-4 border-b">
            <button type="button" data-fermer-drawer class="text-slate-500 hover:text-black" title="Fermer">
                <i class="fi fi-rr-cross-small text-2xl"></i>
            </button>
            <h2 id="titre-drawer" class="text-xl font-extrabold">Nouvelle filière</h2>
        </div>

        <form id="formulaire" method="POST" action="Filieres.php" novalidate class="flex flex-col flex-1 min-h-0">
            <input type="hidden" name="operation" value="ajouter">
            <input type="hidden" name="id_origine" value="">

            <div class="flex-1 overflow-y-auto p-4 space-y-4">

                <div>
                    <label class="block text-sm font-bold mb-1">Code</label>
                    <input name="code" type="text" class="w-full border rounded-lg px-3 py-2" />
                    <p class="erreur <?= isset($erreurs["code"]) ? "" : "hidden" ?> text-sm text-red-600 mt-1"><?= e($erreurs["code"] ?? "") ?></p>
                </div>

                <div>
                    <label class="block text-sm font-bold mb-1">Nom</label>
                    <input name="nom" type="text" class="w-full border rounded-lg px-3 py-2" />
                    <p class="erreur <?= isset($erreurs["nom"]) ? "" : "hidden" ?> text-sm text-red-600 mt-1"><?= e($erreurs["nom"] ?? "") ?></p>
                </div>

                <div>
                    <label class="block text-sm font-bold mb-1">Responsable <span class="text-slate-400 font-semibold">(optionnel)</span></label>
                    <input name="responsable" type="text" class="w-full border rounded-lg px-3 py-2" />
                </div>

                <div>
                    <label class="block text-sm font-bold mb-1">Description <span class="text-slate-400 font-semibold">(optionnelle)</span></label>
                    <textarea name="description" rows="4" class="w-full border rounded-lg px-3 py-2"></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-4 p-4 border-t">
                <button type="button" data-fermer-drawer class="text-gray-600 font-bold">Annuler</button>
                <button id="btn-valider" type="submit" class="bg-[#0B1728] text-white font-bold px-6 py-2 rounded-lg">AJOUTER</button>
            </div>
        </form>
    </aside>

    <!-- Toast + modal + logique d'affichage du drawer (toujours après le drawer) -->
    <?php include 'ui.php'; ?>
</body>
</html>
