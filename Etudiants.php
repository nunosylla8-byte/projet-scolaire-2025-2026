<?php
require_once 'ensemble.php';

$erreurs  = [];
$reouvrir = null;

// =====================================================
// 1. TRAITEMENT DES FORMULAIRES (POST)
// =====================================================
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $operation = $_POST["operation"] ?? "";
    $idOrigine = trim($_POST["id_origine"] ?? "");   // matricule avant modification

    // ---------- ANNULER LA DERNIÈRE SUPPRESSION (bouton du toast) ----------
    if ($operation === "annuler") {
        if (annulerSuppression("Etudiants")) {
            flash("Suppression annulée");
        }
        rediriger("Etudiants.php");
    }

    // ---------- SUPPRIMER ----------
    if ($operation === "supprimer") {
        $matricule = trim($_POST["matricule"] ?? "");
        $i = trouverIndex($_SESSION["Etudiants"], "matricule", $matricule);

        if ($i < 0) {
            flash("Étudiant introuvable", "erreur");
        } else {
            $nomComplet = $_SESSION["Etudiants"][$i]["prenom"] . " " . $_SESSION["Etudiants"][$i]["nom"];
            supprimerAvecAnnulation("Etudiants", $i);
            flash($nomComplet . " a été supprimé(e)", "succes", true);
        }
        rediriger("Etudiants.php");
    }

    // ---------- AJOUTER / MODIFIER ----------
    $donnees = [
        "matricule"      => trim($_POST["matricule"] ?? ""),
        "nom"            => trim($_POST["nom"] ?? ""),
        "prenom"         => trim($_POST["prenom"] ?? ""),
        "date_naissance" => trim($_POST["date_naissance"] ?? ""),
        "sexe"           => trim($_POST["sexe"] ?? ""),
        "telephone"      => trim($_POST["telephone"] ?? ""),
        "adresse"        => trim($_POST["adresse"] ?? ""),
        "classe"         => trim($_POST["classe"] ?? ""),
    ];

    // Champs obligatoires
    foreach (["matricule", "nom", "prenom", "date_naissance", "sexe", "telephone", "adresse", "classe"] as $champ) {
        if ($donnees[$champ] === "") {
            $erreurs[$champ] = "Ce champ est obligatoire";
        }
    }

    // Sexe : M ou F uniquement
    if (!isset($erreurs["sexe"]) && $donnees["sexe"] !== "M" && $donnees["sexe"] !== "F") {
        $erreurs["sexe"] = "Valeur invalide";
    }

    // Date de naissance : date réelle (AAAA-MM-JJ) et pas dans le futur
    if (!isset($erreurs["date_naissance"])) {
        $p = explode("-", $donnees["date_naissance"]);
        if (count($p) !== 3 || !checkdate((int)$p[1], (int)$p[2], (int)$p[0])) {
            $erreurs["date_naissance"] = "Date invalide";
        } elseif ($donnees["date_naissance"] > date("Y-m-d")) {
            $erreurs["date_naissance"] = "La date ne peut pas être dans le futur";
        }
    }

    // Téléphone : chiffres, espaces et + ; entre 9 et 13 chiffres
    if (!isset($erreurs["telephone"])) {
        $chiffres = preg_replace('/\D/', '', $donnees["telephone"]);
        if (!preg_match('/^[0-9+ ]+$/', $donnees["telephone"]) || strlen($chiffres) < 9 || strlen($chiffres) > 13) {
            $erreurs["telephone"] = "Numéro invalide (9 chiffres minimum)";
        }
    }

    // Matricule unique (en ignorant l'étudiant qu'on modifie)
    $indexOrigine  = ($operation === "modifier") ? trouverIndex($_SESSION["Etudiants"], "matricule", $idOrigine) : -1;
    $indexExistant = trouverIndex($_SESSION["Etudiants"], "matricule", $donnees["matricule"]);

    if (!isset($erreurs["matricule"]) && $indexExistant >= 0 && $indexExistant !== $indexOrigine) {
        $erreurs["matricule"] = "Ce matricule existe déjà";
    }

    // Classe : elle doit exister et ne pas être pleine
    if (!isset($erreurs["classe"])) {
        $classe = trouver($_SESSION["Classes"], "code", $donnees["classe"]);

        if ($classe === null) {
            $erreurs["classe"] = "Classe introuvable";
        } else {
            // En modification, si l'étudiant est déjà dans cette classe, il est déjà compté dans l'effectif
            $dejaDedans = ($indexOrigine >= 0
                && strcasecmp($_SESSION["Etudiants"][$indexOrigine]["classe"], $classe["code"]) === 0);

            if (!$dejaDedans && nbEtudiantsDeClasse($classe["code"]) >= $classe["capacite"]) {
                $erreurs["classe"] = "Cette classe est pleine (" . $classe["capacite"] . " places)";
            }
        }
    }

    if (empty($erreurs)) {
        $nomComplet = $donnees["prenom"] . " " . $donnees["nom"];

        if ($operation === "modifier") {
            if ($indexOrigine < 0) {
                flash("Étudiant introuvable", "erreur");
            } else {
                $_SESSION["Etudiants"][$indexOrigine] = $donnees;
                flash($nomComplet . " a été modifié(e)");
            }
        } else {
            $_SESSION["Etudiants"][] = $donnees;
            flash($nomComplet . " a été ajouté(e)");
        }
        rediriger("Etudiants.php");
    }

    $reouvrir = [
        "donnees" => $donnees + ["id_origine" => $idOrigine],
        "modif"   => ($operation === "modifier"),
    ];
}

// =====================================================
// 2. RECHERCHE ET FILTRE (GET)
// =====================================================
$q     = trim($_GET["q"] ?? "");
$sexeActif = trim($_GET["sexe"] ?? "");   // "" = Tous, "M", "F"

$liste = [];
foreach ($_SESSION["Etudiants"] as $etudiant) {
    if ($sexeActif !== "" && $etudiant["sexe"] !== $sexeActif) { continue; }
    if ($q !== "") {
        $texte = $etudiant["nom"] . " " . $etudiant["prenom"] . " " . $etudiant["matricule"];
        if (!contient($texte, $q)) { continue; }
    }
    $liste[] = $etudiant;
}

// =====================================================
// 3. DONNÉES À AFFICHER
// =====================================================
$classes = $_SESSION["Classes"];
$total   = count($_SESSION["Etudiants"]);
$flash   = lireFlash();

// Matricule proposé pour un nouvel étudiant : le plus grand numéro + 1
$max = 0;
foreach ($_SESSION["Etudiants"] as $etudiant) {
    if (preg_match('/^221-2025-(\d+)$/', $etudiant["matricule"], $m)) {
        $max = max($max, (int)$m[1]);
    }
}
$prochainMatricule = "221-2025-" . str_pad($max + 1, 3, "0", STR_PAD_LEFT);

// Styles des boutons Tous / Garçons / Filles
$styleActif   = "bg-[#0B1728] text-white px-5 py-2.5 rounded-xl";
$styleInactif = "bg-[#F1F5F9] text-slate-500 hover:bg-slate-200 px-5 py-2.5 rounded-xl transition";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Étudiants - École 221</title>

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
                <span class="font-extrabold text-slate-800">Étudiants</span>
            </div>
            <div class="flex items-center gap-2 text-slate-500 font-bold">
                <span>Année universitaire 2025–2026</span>
                <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full"></span>
            </div>
        </header>

        <!-- Titre + bouton "Nouvel étudiant" -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Étudiants</h1>
                <p class="text-sm text-slate-500 font-semibold mt-1"><?= $total ?> étudiants enregistrés</p>
            </div>
            <button type="button" data-ouvrir-drawer class="bg-[#0B1728] hover:bg-[#1A283B] text-white px-5 py-3 rounded-xl font-bold text-sm flex items-center justify-center gap-2 transition shadow-sm">
                <i class="fi fi-rr-plus text-xs"></i>
                <span>Nouvel étudiant</span>
            </button>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-4 sm:p-6">

            <!-- Recherche + filtres : formulaire GET (Entrée pour rechercher) -->
            <form method="GET" action="Etudiants.php" class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-6">
                <!-- Bouton caché : fait que la touche Entrée garde le filtre actuel -->
                <button type="submit" class="hidden" tabindex="-1" aria-hidden="true"></button>
                <input type="hidden" name="sexe" value="<?= e($sexeActif) ?>">

                <div class="w-full sm:w-2/3">
                    <input name="q" type="text" value="<?= e($q) ?>" placeholder="Rechercher par nom, prénom ou matricule..." autocomplete="off" class="w-full bg-[#F8FAFC] border border-slate-200 text-xs font-bold text-slate-700 placeholder-slate-400 px-4 py-3 rounded-xl focus:outline-none focus:border-slate-400 transition">
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto justify-start sm:justify-end text-xs font-bold">
                    <button type="submit" name="sexe" value=""  class="<?= $sexeActif === ""  ? $styleActif : $styleInactif ?>">Tous</button>
                    <button type="submit" name="sexe" value="M" class="<?= $sexeActif === "M" ? $styleActif : $styleInactif ?>">Garçons</button>
                    <button type="submit" name="sexe" value="F" class="<?= $sexeActif === "F" ? $styleActif : $styleInactif ?>">Filles</button>
                </div>
            </form>

            <!-- Tableau -->
            <div class="overflow-x-auto">
                <table class="w-full min-w-[720px] text-left border-collapse">
                    <thead>
                        <tr class="text-[11px] font-black text-slate-400 uppercase tracking-wider border-b border-slate-100">
                            <th class="py-3 px-4">MATRICULE</th>
                            <th class="py-3 px-4">NOM</th>
                            <th class="py-3 px-4">PRÉNOM</th>
                            <th class="py-3 px-4 text-center">SEXE</th>
                            <th class="py-3 px-4">CLASSE</th>
                            <th class="py-3 px-4">FILIÈRE</th>
                            <th class="py-3 px-4">NIVEAU</th>
                            <th class="py-3 px-4 text-center">ACTION</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs font-bold text-slate-700">

                        <?php if (empty($liste)): ?>
                            <tr><td colspan="8" class="py-10 text-center text-slate-400 font-bold">Aucun étudiant trouvé</td></tr>
                        <?php endif; ?>

                        <?php foreach ($liste as $et): ?>
                            <?php $infos = infosEtudiant($et); ?>
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-4 px-4 font-bold text-slate-500"><?= e($et["matricule"]) ?></td>
                                <td class="py-4 px-4 font-black text-slate-900"><?= e($et["nom"]) ?></td>
                                <td class="py-4 px-4 font-bold text-slate-700"><?= e($et["prenom"]) ?></td>
                                <td class="py-4 px-4 text-center">
                                    <?php if ($et["sexe"] === "M"): ?>
                                        <span class="w-6 h-6 rounded-full bg-sky-100 text-sky-600 font-extrabold text-[11px] inline-flex items-center justify-center">M</span>
                                    <?php elseif ($et["sexe"] === "F"): ?>
                                        <span class="w-6 h-6 rounded-full bg-pink-100 text-pink-600 font-extrabold text-[11px] inline-flex items-center justify-center">F</span>
                                    <?php else: ?>
                                        <span class="text-slate-300">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4 px-4 font-bold text-slate-700"><?= e($infos["classe"]) ?></td>
                                <td class="py-4 px-4">
                                    <span class="bg-slate-100 text-slate-700 font-extrabold text-[10px] px-2.5 py-1 rounded-md"><?= e($infos["filiere"]) ?></span>
                                </td>
                                <td class="py-4 px-4 font-extrabold text-slate-500"><?= e($infos["niveau"]) ?></td>
                                <td class="py-4 px-4 text-center whitespace-nowrap">
                                    <button type="button" data-item="<?= e(json_encode($et, JSON_UNESCAPED_UNICODE)) ?>" class="btn-modif text-slate-400 hover:text-sky-600 transition mr-3" title="Modifier">
                                        <i class="fi fi-rr-pencil text-base pointer-events-none"></i>
                                    </button>

                                    <form method="POST" action="Etudiants.php" class="form-suppr inline" data-message="Voulez-vous supprimer l'étudiant « <?= e($et["prenom"] . " " . $et["nom"]) ?> » ?">
                                        <input type="hidden" name="operation" value="supprimer">
                                        <input type="hidden" name="matricule" value="<?= e($et["matricule"]) ?>">
                                        <button type="submit" class="text-slate-400 hover:text-red-600 transition" title="Supprimer">
                                            <i class="fi fi-rr-trash text-base pointer-events-none"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                    </tbody>
                </table>
            </div>

            <!-- Compteur sous le tableau -->
            <div class="mt-6 text-xs font-bold text-slate-400">
                <?= count($liste) ?> résultats sur <?= $total ?> étudiants
            </div>
        </div>
    </main>

    <!-- ==================== DRAWER ==================== -->
    <aside id="drawer"
           data-cle="matricule"
           data-titre-ajout="Nouvel étudiant"
           data-titre-modif="Modifier l'étudiant"
           class="fixed top-0 right-0 h-full w-full max-w-lg bg-white text-black z-[60] translate-x-full transition-transform duration-300 flex flex-col">

        <div class="flex items-center gap-4 p-4 border-b">
            <button type="button" data-fermer-drawer class="text-slate-500 hover:text-black" title="Fermer">
                <i class="fi fi-rr-cross-small text-2xl"></i>
            </button>
            <h2 id="titre-drawer" class="text-xl font-extrabold">Nouvel étudiant</h2>
        </div>

        <form id="formulaire" method="POST" action="Etudiants.php" novalidate class="flex flex-col flex-1 min-h-0">
            <input type="hidden" name="operation" value="ajouter">
            <input type="hidden" name="id_origine" value="">

            <div class="flex-1 overflow-y-auto p-4 space-y-4">

                <div>
                    <label class="block text-sm font-bold mb-1">Matricule</label>
                    <input name="matricule" type="text" value="<?= e($prochainMatricule) ?>" class="w-full border rounded-lg px-3 py-2" />
                    <p class="erreur <?= isset($erreurs["matricule"]) ? "" : "hidden" ?> text-sm text-red-600 mt-1"><?= e($erreurs["matricule"] ?? "") ?></p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold mb-1">Nom</label>
                        <input name="nom" type="text" class="w-full border rounded-lg px-3 py-2" />
                        <p class="erreur <?= isset($erreurs["nom"]) ? "" : "hidden" ?> text-sm text-red-600 mt-1"><?= e($erreurs["nom"] ?? "") ?></p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-1">Prénom</label>
                        <input name="prenom" type="text" class="w-full border rounded-lg px-3 py-2" />
                        <p class="erreur <?= isset($erreurs["prenom"]) ? "" : "hidden" ?> text-sm text-red-600 mt-1"><?= e($erreurs["prenom"] ?? "") ?></p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold mb-1">Date de naissance</label>
                        <input name="date_naissance" type="date" max="<?= date("Y-m-d") ?>" class="w-full border rounded-lg px-3 py-2" />
                        <p class="erreur <?= isset($erreurs["date_naissance"]) ? "" : "hidden" ?> text-sm text-red-600 mt-1"><?= e($erreurs["date_naissance"] ?? "") ?></p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-1">Sexe</label>
                        <select name="sexe" class="w-full border rounded-lg px-3 py-2 bg-white">
                            <option value="">-- Choisir --</option>
                            <option value="M">Masculin</option>
                            <option value="F">Féminin</option>
                        </select>
                        <p class="erreur <?= isset($erreurs["sexe"]) ? "" : "hidden" ?> text-sm text-red-600 mt-1"><?= e($erreurs["sexe"] ?? "") ?></p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold mb-1">Téléphone</label>
                    <input name="telephone" type="text" placeholder="77 123 45 67" class="w-full border rounded-lg px-3 py-2" />
                    <p class="erreur <?= isset($erreurs["telephone"]) ? "" : "hidden" ?> text-sm text-red-600 mt-1"><?= e($erreurs["telephone"] ?? "") ?></p>
                </div>

                <div>
                    <label class="block text-sm font-bold mb-1">Adresse</label>
                    <input name="adresse" type="text" class="w-full border rounded-lg px-3 py-2" />
                    <p class="erreur <?= isset($erreurs["adresse"]) ? "" : "hidden" ?> text-sm text-red-600 mt-1"><?= e($erreurs["adresse"] ?? "") ?></p>
                </div>

                <div>
                    <label class="block text-sm font-bold mb-1">Classe</label>
                    <select name="classe" class="w-full border rounded-lg px-3 py-2 bg-white">
                        <option value="">-- Choisir une classe --</option>
                        <?php foreach ($classes as $c): ?>
                            <option value="<?= e($c["code"]) ?>"><?= e($c["nom"]) ?> (<?= nbEtudiantsDeClasse($c["code"]) ?>/<?= (int)$c["capacite"] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                    <p class="text-xs text-slate-400 font-semibold mt-1">La filière et le niveau sont déduits de la classe.</p>
                    <p class="erreur <?= isset($erreurs["classe"]) ? "" : "hidden" ?> text-sm text-red-600 mt-1"><?= e($erreurs["classe"] ?? "") ?></p>
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