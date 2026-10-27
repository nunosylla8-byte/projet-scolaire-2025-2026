<?php
require_once 'ensemble.php';

// =====================================================
// DONNÉES CALCULÉES EN PHP
// =====================================================
$nbFilieres  = count($_SESSION["Filieres"]);
$nbNiveaux   = count($_SESSION["Niveaux"]);
$nbClasses   = count($_SESSION["Classes"]);
$nbEtudiants = count($_SESSION["Etudiants"]);
$nbGarcons   = nbParSexe("M");
$nbFilles    = nbParSexe("F");

// [titre, valeur, icône, couleurs, lien]
$cartes = [
    ["Filières",   $nbFilieres,  "fi-rr-graduation-cap", "bg-sky-100 text-sky-600",         "Filieres.php"],
    ["Niveaux",    $nbNiveaux,   "fi-rr-stats",          "bg-emerald-100 text-emerald-600", "Niveaux.php"],
    ["Classes",    $nbClasses,   "fi-rr-bank",           "bg-amber-100 text-amber-600",     "Classes.php"],
    ["Étudiants",  $nbEtudiants, "fi-rr-users-alt",      "bg-purple-100 text-purple-600",   "Etudiants.php"],
    ["Garçons",    $nbGarcons,   "fi-rr-user",           "bg-blue-100 text-blue-600",       "Etudiants.php?sexe=M"],
    ["Filles",     $nbFilles,    "fi-rr-user",           "bg-pink-100 text-pink-600",       "Etudiants.php?sexe=F"],
];

// Les 5 derniers étudiants enregistrés : on inverse la liste puis on garde 5 éléments
$derniers = array_slice(array_reverse($_SESSION["Etudiants"]), 0, 5);

// Classes ayant le plus d'étudiants : tableau associatif code => effectif, trié du plus grand au plus petit
$effectifs = [];
foreach ($_SESSION["Classes"] as $classe) {
    $effectifs[$classe["code"]] = nbEtudiantsDeClasse($classe["code"]);
}
arsort($effectifs);
$topClasses = array_slice($effectifs, 0, 5, true);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - École 221</title>

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
                <span class="font-extrabold text-slate-800">Tableau de bord</span>
            </div>
            <div class="flex items-center gap-2 text-slate-500 font-bold">
                <span>Année universitaire 2025–2026</span>
                <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full"></span>
            </div>
        </header>

        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Tableau de bord</h1>
            <p class="text-sm text-slate-500 font-semibold mt-1">Vue d'ensemble de l'établissement</p>
        </div>

        <!-- Les 6 cartes -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <?php foreach ($cartes as [$titre, $valeur, $icone, $couleurs, $lien]): ?>
                <a href="<?= e($lien) ?>" class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex items-center justify-between hover:shadow-md transition">
                    <div>
                        <p class="text-sm font-bold text-slate-500"><?= e($titre) ?></p>
                        <p class="text-4xl font-black text-slate-900 mt-1"><?= $valeur ?></p>
                    </div>
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center <?= e($couleurs) ?>">
                        <i class="fi <?= e($icone) ?> text-2xl"></i>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

            <!-- 5 derniers étudiants -->
            <div class="xl:col-span-2 bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="flex justify-between items-center px-6 pt-6 pb-4">
                    <h2 class="text-lg font-extrabold text-slate-900">Derniers étudiants enregistrés</h2>
                    <a href="Etudiants.php" class="text-sm font-bold text-sky-600 hover:underline">Voir tout</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-[#F8FAFC] text-slate-500 text-xs uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-3 font-extrabold">Matricule</th>
                                <th class="px-6 py-3 font-extrabold">Nom</th>
                                <th class="px-6 py-3 font-extrabold">Prénom</th>
                                <th class="px-6 py-3 font-extrabold">Sexe</th>
                                <th class="px-6 py-3 font-extrabold">Classe</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php if (empty($derniers)): ?>
                                <tr><td colspan="5" class="px-6 py-10 text-center text-slate-400 font-bold">Aucun étudiant enregistré</td></tr>
                            <?php endif; ?>

                            <?php foreach ($derniers as $et): ?>
                                <?php $infos = infosEtudiant($et); ?>
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-6 py-3 font-bold text-slate-700"><?= e($et["matricule"]) ?></td>
                                    <td class="px-6 py-3 font-extrabold text-slate-900"><?= e($et["nom"]) ?></td>
                                    <td class="px-6 py-3 font-semibold text-slate-700"><?= e($et["prenom"]) ?></td>
                                    <td class="px-6 py-3">
                                        <?php if ($et["sexe"] === "M"): ?>
                                            <span class="bg-sky-100 text-sky-600 font-black text-xs px-3 py-1 rounded-full">M</span>
                                        <?php else: ?>
                                            <span class="bg-pink-100 text-pink-600 font-black text-xs px-3 py-1 rounded-full">F</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-3 font-semibold text-slate-700"><?= e($infos["classe"]) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Classes ayant le plus d'étudiants -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">
                <h2 class="text-lg font-extrabold text-slate-900 mb-5">Classes les plus peuplées</h2>

                <?php if (empty($topClasses)): ?>
                    <p class="text-slate-400 font-bold text-sm">Aucune classe enregistrée</p>
                <?php endif; ?>

                <div class="space-y-5">
                    <?php foreach ($topClasses as $codeClasse => $effectif): ?>
                        <?php
                            $classe   = trouver($_SESSION["Classes"], "code", $codeClasse);
                            $capacite = (int)$classe["capacite"];
                            $pct      = $capacite > 0 ? min(100, round($effectif / $capacite * 100)) : 0;
                        ?>
                        <div>
                            <div class="flex justify-between text-sm font-bold mb-1.5">
                                <span class="text-slate-800"><?= e($classe["nom"]) ?></span>
                                <span class="text-slate-500"><?= $effectif ?> / <?= $capacite ?></span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2">
                                <div class="bg-[#EAB308] h-2 rounded-full" style="width: <?= $pct ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
    </main>
</body>
</html>