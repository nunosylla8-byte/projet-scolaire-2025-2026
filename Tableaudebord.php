<?php

require_once 'ensemble.php';

if (!isset($_SESSION["Etudiants"])) {
    $_SESSION["Etudiants"] = [];
}

if (isset($_POST['ajout_etudiant'])) {
    $Newetudiant = [
        "matricule" => $_POST['matricule'],
        "nom"       => $_POST['nom'],
        "prenom"    => $_POST['prenom'],
        "sexe"      => $_POST['sexe'],
    ];
    $_SESSION["Etudiants"][] = $Newetudiant;
} 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - École 221</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts: Nunito -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- CDN Flaticon Icons -->
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.1.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>

    <style>
        body { font-family: 'Nunito', sans-serif; }
    </style>
</head>
<body class="bg-[#F0F4F9] text-gray-800 flex min-h-screen">

   <!-- ==================== SIDEBAR GAUCHE ==================== -->
    <aside class="w-64 bg-[#0B1728] text-white flex flex-col justify-between p-4 min-h-screen fixed left-0 top-0 z-10">
        <div>
            <!-- Header Sidebar -->
            <div class="mb-8 px-3 pt-2">
                <h1 class="text-2xl font-black tracking-tight text-white">École 221</h1>
                <p class="text-xs text-slate-400 font-semibold mt-0.5">Gestion Scolaire</p>
            </div>
           
            <!-- Navigation -->
            <nav class="space-y-2">
                <a href="Classes.php" class="flex items-center gap-3.5 px-4 py-3 bg-[#1A283B] text-[#EAB308] font-bold text-sm rounded-xl border-l-4 border-[#EAB308] shadow-sm">
                    <i class="fi fi-rr-apps text-base"></i>
                    <span>Tableau de bord</span>
                </a>

                <a href="Filieres.php" class="flex items-center gap-3.5 px-4 py-3 text-slate-300 hover:bg-[#1A283B] hover:text-white font-bold text-sm rounded-xl transition">
                    <i class="fi fi-rr-graduation-cap text-base"></i>
                    <span>Filières</span>
                </a>

                <a href="Niveaux.php" class="flex items-center gap-3.5 px-4 py-3 text-slate-300 hover:bg-[#1A283B] hover:text-white font-bold text-sm rounded-xl transition">
                    <i class="fi fi-rr-stats text-base"></i>
                    <span>Niveaux</span>
                </a>

                <!-- Élément Classes Actif -->
                <a href="Classe.php" class="flex items-center gap-3.5 px-4 py-3 text-slate-300 hover:bg-[#1A283B] hover:text-white font-bold text-sm rounded-xl transition">
                    <i class="fi fi-rr-bank text-base"></i>
                    <span>Classes</span>
                </a>

                <a href="Etudiants.php" class="flex items-center gap-3.5 px-4 py-3 text-slate-300 hover:bg-[#1A283B] hover:text-white font-bold text-sm rounded-xl transition">
                    <i class="fi fi-rr-users-alt text-base"></i>
                    <span>Étudiants</span>
                </a>

                <a href="Statistique.php" class="flex items-center gap-3.5 px-4 py-3 text-slate-300 hover:bg-[#1A283B] hover:text-white font-bold text-sm rounded-xl transition">
                    <i class="fi fi-rr-chart-histogram text-base"></i>
                    <span>Statistiques</span>
                </a>
            </nav>
        </div>

        <!-- Profile Utilisateur Bas -->
        <div class="flex items-center gap-3 px-2 pt-4 border-t border-slate-800">
            <div class="w-10 h-10 rounded-full bg-[#EAB308] text-[#0B1728] flex items-center justify-center font-black text-sm">
                S
            </div>
            <div>
                <p class="text-sm font-bold text-white leading-tight">Secrétariat</p>
                <p class="text-xs text-slate-400 font-semibold">Administrateur</p>
            </div>
        </div>
    </aside>

    <!-- ==================== CONTENU PRINCIPAL ==================== -->
    <main class="flex-1 ml-64 p-8">
        
        <!-- Header supérieur -->
        <header class="w-full flex justify-between items-center text-xs font-semibold mb-6">
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

        <!-- Titre principal de la page -->
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Tableau de bord</h1>
            <p class="text-sm text-slate-500 font-semibold mt-1">Vue d'ensemble de l'établissement École 221</p>
        </div>

      <!-- Grille des 6 Cards (Version Sombre Éléguante) -->
<div class="w-full px-8 py-3 grid grid-cols-3 gap-5">
    
    <!-- Card 1 : Filières -->
    <div class="bg-[#1E293B] p-5 rounded-3xl border border-[#334155]/50 shadow-lg flex items-center gap-4 hover:border-[#38BDF8]/40 transition-all duration-200">
        <div class="w-12 h-12 rounded-2xl bg-[#0284C7]/20 text-[#38BDF8] border border-[#0284C7]/30 flex items-center justify-center text-xl">
            <i class="fi fi-rr-graduation-cap"></i>
        </div>
        <div>
            <p class="text-3xl font-black text-[#F8FAFC] font-mono tracking-tight">4</p>
            <p class="text-xs font-bold text-[#94A3B8] uppercase tracking-wider">Filières</p>
        </div>
    </div>

    <!-- Card 2 : Niveaux -->
    <div class="bg-[#1E293B] p-5 rounded-3xl border border-[#334155]/50 shadow-lg flex items-center gap-4 hover:border-[#10B981]/40 transition-all duration-200">
        <div class="w-12 h-12 rounded-2xl bg-[#059669]/20 text-[#34D399] border border-[#059669]/30 flex items-center justify-center text-xl">
            <i class="fi fi-rr-stats"></i>
        </div>
        <div>
            <p class="text-3xl font-black text-[#F8FAFC] font-mono tracking-tight">5</p>
            <p class="text-xs font-bold text-[#94A3B8] uppercase tracking-wider">Niveaux</p>
        </div>
    </div>

    <!-- Card 3 : Classes -->
    <div class="bg-[#1E293B] p-5 rounded-3xl border border-[#334155]/50 shadow-lg flex items-center gap-4 hover:border-[#F59E0B]/40 transition-all duration-200">
        <div class="w-12 h-12 rounded-2xl bg-[#D97706]/20 text-[#FBBF24] border border-[#D97706]/30 flex items-center justify-center text-xl">
            <i class="fi fi-rr-bank"></i>
        </div>
        <div>
            <p class="text-3xl font-black text-[#F8FAFC] font-mono tracking-tight">7</p>
            <p class="text-xs font-bold text-[#94A3B8] uppercase tracking-wider">Classes</p>
        </div>
    </div>

    <!-- Card 4 : Étudiants -->
    <div class="bg-[#1E293B] p-5 rounded-3xl border border-[#334155]/50 shadow-lg flex items-center gap-4 hover:border-[#6366F1]/40 transition-all duration-200">
        <div class="w-12 h-12 rounded-2xl bg-[#4F46E5]/20 text-[#818CF8] border border-[#4F46E5]/30 flex items-center justify-center text-xl">
            <i class="fi fi-rr-users-alt"></i>
        </div>
        <div>
            <p class="text-3xl font-black text-[#F8FAFC] font-mono tracking-tight">12</p>
            <p class="text-xs font-bold text-[#94A3B8] uppercase tracking-wider">Étudiants</p>
        </div>
    </div>

    <!-- Card 5 : Garçons -->
    <div class="bg-[#1E293B] p-5 rounded-3xl border border-[#334155]/50 shadow-lg flex items-center gap-4 hover:border-[#0284C7]/40 transition-all duration-200">
        <div class="w-12 h-12 rounded-2xl bg-[#0369A1]/30 text-[#38BDF8] border border-[#0284C7]/40 flex items-center justify-center text-xl">
            <i class="fi fi-rr-user"></i>
        </div>
        <div>
            <p class="text-3xl font-black text-[#F8FAFC] font-mono tracking-tight">6</p>
            <p class="text-xs font-bold text-[#94A3B8] uppercase tracking-wider">Garçons</p>
        </div>
    </div>

    <!-- Card 6 : Filles -->
    <div class="bg-[#1E293B] p-5 rounded-3xl border border-[#334155]/50 shadow-lg flex items-center gap-4 hover:border-[#DB2777]/40 transition-all duration-200">
        <div class="w-12 h-12 rounded-2xl bg-[#BE185D]/30 text-[#F472B6] border border-[#DB2777]/40 flex items-center justify-center text-xl">
            <i class="fi fi-rr-portrait"></i>
        </div>
        <div>
            <p class="text-3xl font-black text-[#F8FAFC] font-mono tracking-tight">6</p>
            <p class="text-xs font-bold text-[#94A3B8] uppercase tracking-wider">Filles</p>
        </div>
    </div>

</div>
        <!-- ==================== SECTION BASSE ==================== -->
        <div class="grid grid-cols-12 gap-6 mt-7">
            
           <!-- Cadre Gauche : Tableau des étudiants -->
<div class="col-span-12 lg:col-span-7 bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
    <h2 class="text-base font-extrabold text-slate-900 mb-4">5 derniers étudiants inscrits</h2>
    
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-[#F0F4F9] text-[11px] font-extrabold text-slate-500 uppercase tracking-wider">
                <th class="py-2.5 px-3 rounded-l-xl">MATRICULE</th>
                <th class="py-2.5 px-3">NOM</th>
                <th class="py-2.5 px-3">PRÉNOM</th>
                <th class="py-2.5 px-3 rounded-r-xl text-center">SEXE</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-xs">
            <?php if (empty($_SESSION["Etudiants"])): ?>
                <tr>
                    <td colspan="4" class="py-4 text-center text-slate-400 font-semibold">
                        Aucun étudiant inscrit pour le moment.
                    </td>
                </tr>
            <?php else: ?>
                <?php 
                    $total = count($_SESSION["Etudiants"]);
                    $debut = max(0, $total - 5);
                ?>
                <?php for ($i = $total - 1; $i >= $debut; $i--): ?>
                    <?php $Etudiant = $_SESSION["Etudiants"][$i]; ?>
                    <tr>
                        <td class="py-3 px-3 font-semibold text-slate-500"><?= htmlspecialchars($Etudiant['matricule'] ?? '') ?></td>
                        <td class="py-3 px-3 font-extrabold text-slate-900 uppercase"><?= htmlspecialchars($Etudiant['nom'] ?? '') ?></td>
                        <td class="py-3 px-3 font-extrabold text-slate-900"><?= htmlspecialchars($Etudiant['prenom'] ?? '') ?></td>
                        <td class="py-4 px-4 text-center">
                                <?php if ($Etudiant['sexe'] === 'M'): ?>
                                    <span class="w-6 h-6 rounded-full bg-sky-100 text-sky-600 font-extrabold text-[11px] inline-flex items-center justify-center">M</span>
                                <?php else: ?>
                                    <span class="w-6 h-6 rounded-full bg-pink-100 text-pink-600 font-extrabold text-[11px] inline-flex items-center justify-center">F</span>
                                <?php endif; ?>
                            </td>
                    </tr>
                <?php endfor; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
            <!-- Cadre Droit : Classes les plus peuplées -->
            <div class="col-span-12 lg:col-span-5 bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                <h2 class="text-base font-extrabold text-slate-900 mb-5">Classes les plus peuplées</h2>

                <div class="space-y-4 text-xs">
                    <!-- Classe 1 -->
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <div class="flex items-center gap-2">
                                <span class="w-5 h-5 bg-slate-900 text-white rounded-full flex items-center justify-center font-extrabold text-[10px]">1</span>
                                <span class="font-extrabold text-slate-900">INFO L1 Groupe A</span>
                            </div>
                            <span class="text-slate-400 font-bold text-[11px]">3/35</span>
                        </div>
                        <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-slate-900 h-full w-[9%]"></div>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1 font-bold">Informatique · 9% de capacité</p>
                    </div>

                    <!-- Classe 2 -->
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <div class="flex items-center gap-2">
                                <span class="w-5 h-5 bg-slate-900 text-white rounded-full flex items-center justify-center font-extrabold text-[10px]">2</span>
                                <span class="font-extrabold text-slate-900">INFO L2 Groupe A</span>
                            </div>
                            <span class="text-slate-400 font-bold text-[11px]">2/30</span>
                        </div>
                        <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-slate-900 h-full w-[7%]"></div>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1 font-bold">Informatique · 7% de capacité</p>
                    </div>

                    <!-- Classe 3 -->
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <div class="flex items-center gap-2">
                                <span class="w-5 h-5 bg-slate-900 text-white rounded-full flex items-center justify-center font-extrabold text-[10px]">3</span>
                                <span class="font-extrabold text-slate-900">GES L1 Groupe A</span>
                            </div>
                            <span class="text-slate-400 font-bold text-[11px]">2/40</span>
                        </div>
                        <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-slate-900 h-full w-[5%]"></div>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1 font-bold">Gestion · 5% de capacité</p>
                    </div>

                    <!-- Classe 4 -->
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <div class="flex items-center gap-2">
                                <span class="w-5 h-5 bg-slate-900 text-white rounded-full flex items-center justify-center font-extrabold text-[10px]">4</span>
                                <span class="font-extrabold text-slate-900">MKT L1 Groupe A</span>
                            </div>
                            <span class="text-slate-400 font-bold text-[11px]">2/30</span>
                        </div>
                        <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-slate-900 h-full w-[7%]"></div>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1 font-bold">Marketing · 7% de capacité</p>
                    </div>

                    <!-- Classe 5 -->
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <div class="flex items-center gap-2">
                                <span class="w-5 h-5 bg-slate-900 text-white rounded-full flex items-center justify-center font-extrabold text-[10px]">5</span>
                                <span class="font-extrabold text-slate-900">INFO L3 Groupe A</span>
                            </div>
                            <span class="text-slate-400 font-bold text-[11px]">1/25</span>
                        </div>
                        <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-slate-900 h-full w-[4%] font-bold"></div>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1 font-bold">Informatique · 4% de capacité</p>
                    </div>
                </div>
            </div>

        </div>
    </main>

</body>
</html>