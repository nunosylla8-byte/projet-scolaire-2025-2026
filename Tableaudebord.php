<?php
require_once 'ensemble.php';

// =====================================================
// DONNÉES CALCULÉES EN PHP
// =====================================================
$nbGarcons = nbParSexe("M");
$nbFilles  = nbParSexe("F");

// Les 6 cartes : [valeur, titre, icône, bordure au survol, style de l'icône]
$cartes = [
    [count($_SESSION["Filieres"]),  "Filières",  "fi-rr-graduation-cap", "hover:border-[#38BDF8]/40", "bg-[#0284C7]/20 text-[#38BDF8] border-[#0284C7]/30"],
    [count($_SESSION["Niveaux"]),   "Niveaux",   "fi-rr-stats",          "hover:border-[#10B981]/40", "bg-[#059669]/20 text-[#34D399] border-[#059669]/30"],
    [count($_SESSION["Classes"]),   "Classes",   "fi-rr-bank",           "hover:border-[#F59E0B]/40", "bg-[#D97706]/20 text-[#FBBF24] border-[#D97706]/30"],
    [count($_SESSION["Etudiants"]), "Étudiants", "fi-rr-users-alt",      "hover:border-[#6366F1]/40", "bg-[#4F46E5]/20 text-[#818CF8] border-[#4F46E5]/30"],
    [$nbGarcons,                    "Garçons",   "fi-rr-user",           "hover:border-[#0284C7]/40", "bg-[#0369A1]/30 text-[#38BDF8] border-[#0284C7]/40"],
    [$nbFilles,                     "Filles",    "fi-rr-portrait",       "hover:border-[#DB2777]/40", "bg-[#BE185D]/30 text-[#F472B6] border-[#DB2777]/40"],
];

// 5 derniers étudiants (le plus récent en premier)
$derniers = array_slice(array_reverse($_SESSION["Etudiants"]), 0, 5);

// Classes les plus peuplées : tableau associatif code => effectif, trié du plus grand au plus petit
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

    <!-- ==================== CONTENU PRINCIPAL ==================== -->
    <main class="flex-1 min-w-0 lg:ml-64 p-4 pt-20 sm:p-6 sm:pt-20 lg:p-8">

        <header class="w-full flex flex-wrap justify-between items-center gap-2 text-xs font-semibold mb-6">
            <div class="flex items-center gap-2 text-slate-500">
                <span class="hover:underline cursor-pointer">École 221</span>
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
            <p class="text-sm text-slate-500 font-semibold mt-1">Vue d'ensemble de l'établissement École 221</p>
        </div>

        <!-- 6 cartes sombres.
             Effet zoom en CSS : le conteneur (group) est immobile et écoute la souris,
             c'est la carte à l'intérieur qui grossit. -->
        <div class="w-full px-2 sm:px-6 lg:px-8 py-3 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
            <?php foreach ($cartes as [$valeur, $titre, $icone, $survol, $styleIcone]): ?>
                <div class="relative group hover:z-10">
                    <div class="bg-[#1E293B] p-5 rounded-3xl border border-[#334155]/50 shadow-lg flex items-center gap-4 <?= $survol ?> transition-transform duration-700 ease-in-out md:group-hover:scale-[1.15]">
                        <div class="w-12 h-12 rounded-2xl border flex items-center justify-center text-xl <?= $styleIcone ?>">
                            <i class="fi <?= e($icone) ?>"></i>
                        </div>
                        <div>
                            <p class="text-3xl font-black text-[#F8FAFC] font-mono tracking-tight"><?= $valeur ?></p>
                            <p class="text-xs font-bold text-[#94A3B8] uppercase tracking-wider"><?= e($titre) ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- ==================== SECTION BASSE ==================== -->
        <div class="grid grid-cols-12 gap-6 mt-7">

            <!-- Cadre gauche : 5 derniers étudiants -->
            <div class="col-span-12 xl:col-span-7 bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                <h2 class="text-base font-extrabold text-slate-900 mb-4">5 derniers étudiants inscrits</h2>

                <div class="overflow-x-auto"><table class="w-full min-w-[420px] text-left border-collapse">
                    <thead>
                        <tr class="bg-[#F0F4F9] text-[11px] font-extrabold text-slate-500 uppercase tracking-wider">
                            <th class="py-2.5 px-3 rounded-l-xl">MATRICULE</th>
                            <th class="py-2.5 px-3">NOM</th>
                            <th class="py-2.5 px-3">PRÉNOM</th>
                            <th class="py-2.5 px-3 rounded-r-xl text-center">SEXE</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">

                        <?php if (empty($derniers)): ?>
                            <tr><td colspan="4" class="py-4 text-center text-slate-400 font-semibold">Aucun étudiant inscrit pour le moment.</td></tr>
                        <?php endif; ?>

                        <?php foreach ($derniers as $et): ?>
                            <tr>
                                <td class="py-3 px-3 font-semibold text-slate-500"><?= e($et["matricule"]) ?></td>
                                <td class="py-3 px-3 font-extrabold text-slate-900 uppercase"><?= e($et["nom"]) ?></td>
                                <td class="py-3 px-3 font-extrabold text-slate-900"><?= e($et["prenom"]) ?></td>
                                <td class="py-3 px-3 text-center">
                                    <?php if ($et["sexe"] === "M"): ?>
                                        <span class="w-6 h-6 rounded-full bg-sky-100 text-sky-600 font-extrabold text-[11px] inline-flex items-center justify-center">M</span>
                                    <?php elseif ($et["sexe"] === "F"): ?>
                                        <span class="w-6 h-6 rounded-full bg-pink-100 text-pink-600 font-extrabold text-[11px] inline-flex items-center justify-center">F</span>
                                    <?php else: ?>
                                        <span class="text-slate-300">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                    </tbody>
                </table></div>
            </div>

            <!-- Cadre droit : classes les plus peuplées -->
            <div class="col-span-12 xl:col-span-5 bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                <h2 class="text-base font-extrabold text-slate-900 mb-5">Classes les plus peuplées</h2>

                <div class="space-y-4 text-xs">

                    <?php if (empty($topClasses)): ?>
                        <p class="text-center text-slate-400 font-semibold py-4">Aucune classe enregistrée.</p>
                    <?php endif; ?>

                    <?php $rang = 0; ?>
                    <?php foreach ($topClasses as $codeClasse => $nb): ?>
                        <?php
                            $rang++;
                            $classe   = trouver($_SESSION["Classes"], "code", $codeClasse);
                            $filiere  = trouver($_SESSION["Filieres"], "code", $classe["filiere"]);
                            $capacite = (int)$classe["capacite"];
                            $pct      = $capacite > 0 ? round($nb / $capacite * 100) : 0;
                            $largeur  = min(100, $pct);
                        ?>
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="w-5 h-5 shrink-0 bg-slate-900 text-white rounded-full flex items-center justify-center font-extrabold text-[10px]"><?= $rang ?></span>
                                    <span class="font-extrabold text-slate-900 truncate"><?= e($classe["nom"]) ?></span>
                                </div>
                                <span class="text-slate-400 font-bold text-[11px] whitespace-nowrap"><?= $nb ?>/<?= $capacite > 0 ? $capacite : "—" ?></span>
                            </div>
                            <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                                <div class="bg-slate-900 h-full transition-all duration-500" style="width: <?= $largeur ?>%"></div>
                            </div>
                            <p class="text-[10px] text-slate-400 mt-1 font-bold"><?= e($filiere["nom"] ?? "—") ?> · <?= $pct ?>% de capacité</p>
                        </div>
                    <?php endforeach; ?>

                </div>
            </div>

        </div>
    </main>
</body>
</html>