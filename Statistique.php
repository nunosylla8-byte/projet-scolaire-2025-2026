<?php
require_once 'ensemble.php';

// =====================================================
// OUTILS
// =====================================================

// Pourcentage sans division par zéro
function pourcentage($partie, $total) {
    return $total > 0 ? round($partie / $total * 100) : 0;
}

// Une ligne avec barre : largeur de l'étiquette (classe Tailwind), nom, valeur, maximum, couleur, texte à droite
// La barre la plus longue = 100 %. Minimum 8 % pour que le chiffre reste lisible.
function ligneBarre($largeur, $nom, $valeur, $max, $couleur, $droite = "") {
    $w = $max > 0 ? max(8, round($valeur / $max * 100)) : 8;

    $html  = '<div class="flex items-center justify-between gap-2 sm:gap-4">';
    $html .= '<span class="' . $largeur . ' text-slate-800 truncate shrink-0" title="' . e($nom) . '">' . e($nom) . '</span>';
    $html .= '<div class="flex-1 min-w-0 bg-slate-100 h-8 rounded-xl overflow-hidden">';
    $html .= '<div class="h-full rounded-xl flex items-center px-3 text-white" style="width:' . $w . '%; background:' . $couleur . '">' . $valeur . '</div>';
    $html .= '</div>';
    if ($droite !== "") {
        $html .= '<span class="text-slate-400 text-[10px] sm:text-[11px] font-bold whitespace-nowrap">' . e($droite) . '</span>';
    }
    $html .= '</div>';
    return $html;
}

$COULEURS_FILIERES = ["#0B1728", "#1E40AF", "#2563EB", "#3B82F6", "#60A5FA", "#93C5FD"];
$COULEURS_NIVEAUX  = ["#7C3AED", "#8B5CF6", "#A78BFA", "#C4B5FD", "#DDD6FE", "#EDE9FE"];

// =====================================================
// CALCULS DES STATISTIQUES (en PHP)
// =====================================================
$nbFilieres  = count($_SESSION["Filieres"]);
$nbNiveaux   = count($_SESSION["Niveaux"]);
$nbClasses   = count($_SESSION["Classes"]);
$total       = count($_SESSION["Etudiants"]);
$garcons     = nbParSexe("M");
$filles      = nbParSexe("F");

$pm = pourcentage($garcons, $total);
$pf = $total > 0 ? 100 - $pm : 0;          // la somme fait toujours 100 %

// Étudiants par filière
$parFiliere = [];
foreach ($_SESSION["Filieres"] as $f) {
    $parFiliere[] = ["nom" => $f["nom"], "nb" => nbEtudiantsDeFiliere($f["code"])];
}
$maxF = 0;
foreach ($parFiliere as $x) { $maxF = max($maxF, $x["nb"]); }

// Étudiants par niveau
$parNiveau = [];
foreach ($_SESSION["Niveaux"] as $n) {
    $parNiveau[] = ["nom" => $n["libelle"], "nb" => nbEtudiantsDeNiveau($n["code"])];
}
$maxN = 0;
foreach ($parNiveau as $x) { $maxN = max($maxN, $x["nb"]); }

// Effectifs par classe, la plus peuplée en premier
$parClasse = [];
foreach ($_SESSION["Classes"] as $c) {
    $parClasse[] = ["nom" => $c["nom"], "nb" => nbEtudiantsDeClasse($c["code"]), "capacite" => (int)$c["capacite"]];
}
usort($parClasse, function ($a, $b) { return $b["nb"] <=> $a["nb"]; });

$maxC = 0;
foreach ($parClasse as $x) { $maxC = max($maxC, $x["nb"]); }

$top = $parClasse[0] ?? null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques - École 221</title>

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
    <main class="flex-1 min-w-0 lg:ml-64 p-4 pt-20 sm:p-6 sm:pt-20 lg:p-8">

        <header class="w-full flex flex-wrap justify-between items-center gap-2 text-xs font-semibold mb-6">
            <div class="flex items-center gap-2 text-slate-500">
                <span class="hover:underline cursor-pointer">École 221</span>
                <span>/</span>
                <span class="font-extrabold text-slate-800">Statistiques</span>
            </div>
            <div class="flex items-center gap-2 text-slate-500 font-bold">
                <span>Année universitaire 2025–2026</span>
                <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full"></span>
            </div>
        </header>

        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Statistiques</h1>
            <p class="text-sm text-slate-500 font-semibold mt-1">Indicateurs clés de l'établissement</p>
        </div>

        <!-- 6 cartes -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">

            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 font-black flex items-center justify-center text-2xl shadow-inner"><?= $nbFilieres ?></div>
                <div>
                    <span class="text-base font-bold text-slate-700 block">Filières</span>
                    <span class="text-xs font-semibold text-slate-400">Formations disponibles</span>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 font-black flex items-center justify-center text-2xl shadow-inner"><?= $nbNiveaux ?></div>
                <div>
                    <span class="text-base font-bold text-slate-700 block">Niveaux</span>
                    <span class="text-xs font-semibold text-slate-400">Cursus de Licence &amp; Master</span>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 font-black flex items-center justify-center text-2xl shadow-inner"><?= $nbClasses ?></div>
                <div>
                    <span class="text-base font-bold text-slate-700 block">Classes</span>
                    <span class="text-xs font-semibold text-slate-400">Groupes de cours</span>
                </div>
            </div>

            <div class="bg-[#0B1728] p-6 rounded-2xl text-white shadow-md flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl bg-[#1A283B] text-white font-black flex items-center justify-center text-3xl"><?= $total ?></div>
                <div>
                    <span class="text-lg font-extrabold text-white block">Total étudiants</span>
                    <span class="text-xs font-semibold text-slate-400">Inscrits cette année</span>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl bg-sky-50 text-sky-600 font-black flex items-center justify-center text-2xl shadow-inner"><?= $garcons ?></div>
                <div>
                    <span class="text-base font-bold text-slate-700 block">Garçons</span>
                    <span class="text-xs font-semibold text-slate-400"><?= pourcentage($garcons, $total) ?>% de l'effectif</span>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl bg-pink-50 text-pink-600 font-black flex items-center justify-center text-2xl shadow-inner"><?= $filles ?></div>
                <div>
                    <span class="text-base font-bold text-slate-700 block">Filles</span>
                    <span class="text-xs font-semibold text-slate-400"><?= pourcentage($filles, $total) ?>% de l'effectif</span>
                </div>
            </div>
        </div>

        <!-- Répartition par sexe -->
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm mb-8">
            <h2 class="text-base font-extrabold text-slate-900">Répartition par sexe</h2>
            <p class="text-xs text-slate-400 font-semibold mb-4">Parité au sein de l'établissement</p>

            <div class="w-full h-7 rounded-xl overflow-hidden flex font-extrabold text-[11px] text-white bg-slate-100">
                <div class="bg-[#2B5292] h-full flex items-center justify-center" style="width:<?= $pm ?>%"><?= $pm > 0 ? $pm . "%" : "" ?></div>
                <div class="bg-[#C22975] h-full flex items-center justify-center" style="width:<?= $pf ?>%"><?= $pf > 0 ? $pf . "%" : "" ?></div>
            </div>

            <div class="flex gap-6 mt-4 text-xs font-bold text-slate-600">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-[#2B5292]"></span>
                    <span>Garçons (<?= $garcons ?>)</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-[#C22975]"></span>
                    <span>Filles (<?= $filles ?>)</span>
                </div>
            </div>
        </div>

        <!-- Filières & Niveaux -->
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 xl:gap-8 mb-8">

            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
                <h2 class="text-base font-extrabold text-slate-900">Étudiants par filière</h2>
                <p class="text-xs text-slate-400 font-semibold mb-6">Répartition des effectifs</p>
                <div class="space-y-4 text-xs font-extrabold">
                    <?php if (empty($parFiliere)): ?>
                        <p class="text-center text-slate-400 font-bold py-6">Aucune filière enregistrée</p>
                    <?php endif; ?>
                    <?php foreach ($parFiliere as $i => $x): ?>
                        <?= ligneBarre("w-20 sm:w-28", $x["nom"], $x["nb"], $maxF, $COULEURS_FILIERES[$i % count($COULEURS_FILIERES)]) ?>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
                <h2 class="text-base font-extrabold text-slate-900">Étudiants par niveau</h2>
                <p class="text-xs text-slate-400 font-semibold mb-6">Répartition par année d'études</p>
                <div class="space-y-4 text-xs font-extrabold">
                    <?php if (empty($parNiveau)): ?>
                        <p class="text-center text-slate-400 font-bold py-6">Aucun niveau enregistré</p>
                    <?php endif; ?>
                    <?php foreach ($parNiveau as $i => $x): ?>
                        <?= ligneBarre("w-16 sm:w-24", $x["nom"], $x["nb"], $maxN, $COULEURS_NIVEAUX[$i % count($COULEURS_NIVEAUX)]) ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Effectifs par classe -->
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3 mb-6">
                <div>
                    <h2 class="text-base font-extrabold text-slate-900">Effectifs par classe</h2>
                    <p class="text-xs text-slate-400 font-semibold">Taux de remplissage des classes</p>
                </div>
                <div class="sm:text-right">
                    <span class="text-[10px] text-slate-400 font-extrabold block uppercase tracking-wider">Classe la plus peuplée</span>
                    <?php if ($top && $top["nb"] > 0): ?>
                        <span class="text-sm font-black text-slate-900 block"><?= e($top["nom"]) ?></span>
                        <span class="text-xs font-bold text-amber-500 block"><?= $top["nb"] ?> étudiant<?= $top["nb"] > 1 ? "s" : "" ?></span>
                    <?php else: ?>
                        <span class="text-sm font-black text-slate-900 block">—</span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="space-y-4 text-xs font-extrabold">
                <?php if (empty($parClasse)): ?>
                    <p class="text-center text-slate-400 font-bold py-6">Aucune classe enregistrée</p>
                <?php endif; ?>
                <?php foreach ($parClasse as $x): ?>
                    <?php
                        $droite = $x["capacite"] > 0
                            ? $x["nb"] . "/" . $x["capacite"] . " (" . pourcentage($x["nb"], $x["capacite"]) . "%)"
                            : $x["nb"] . "/—";
                    ?>
                    <?= ligneBarre("w-24 sm:w-36", $x["nom"], $x["nb"], $maxC, "#1E3A8A", $droite) ?>
                <?php endforeach; ?>
            </div>
        </div>
    </main>
</body>
</html>