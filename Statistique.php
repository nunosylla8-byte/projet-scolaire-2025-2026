<?php
session_start();

require_once 'ensemble.php';

// Initialisation si la session est vide
if (!isset($_SESSION["Etudiants"])) {
    $_SESSION["Etudiants"] = [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques - École 221</title>
    
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
                <a href="Classes.php" class="flex items-center gap-3.5 px-4 py-3 text-slate-300 hover:bg-[#1A283B] hover:text-white font-bold text-sm rounded-xl transition">
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

                <a href="Classe.php" class="flex items-center gap-3.5 px-4 py-3 text-slate-300 hover:bg-[#1A283B] hover:text-white font-bold text-sm rounded-xl transition">
                    <i class="fi fi-rr-bank text-base"></i>
                    <span>Classes</span>
                </a>

                <a href="Etudiants.php" class="flex items-center gap-3.5 px-4 py-3 text-slate-300 hover:bg-[#1A283B] hover:text-white font-bold text-sm rounded-xl transition">
                    <i class="fi fi-rr-users-alt text-base"></i>
                    <span>Étudiants</span>
                </a>

                <!-- Élément Statistiques Actif -->
                <a href="Statistique.php" class="flex items-center gap-3.5 px-4 py-3 bg-[#1A283B] text-[#EAB308] font-bold text-sm rounded-xl border-l-4 border-[#EAB308] shadow-sm">
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
                <span class="font-extrabold text-slate-800">Statistiques</span>
            </div>
            <div class="flex items-center gap-2 text-slate-500 font-bold">
                <span>Année universitaire 2025–2026</span>
                <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full"></span>
            </div>
        </header>

        <!-- Titre principal -->
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Statistiques</h1>
            <p class="text-sm text-slate-500 font-semibold mt-1">Indicateurs clés de l'établissement</p>
        </div>

        <!-- Grille des 6 Cards Agrandies -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- Card Filières -->
            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 font-black flex items-center justify-center text-2xl shadow-inner">4</div>
                <div>
                    <span class="text-base font-bold text-slate-700 block">Filières</span>
                    <span class="text-xs font-semibold text-slate-400">Formations disponibles</span>
                </div>
            </div>

            <!-- Card Niveaux -->
            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 font-black flex items-center justify-center text-2xl shadow-inner">5</div>
                <div>
                    <span class="text-base font-bold text-slate-700 block">Niveaux</span>
                    <span class="text-xs font-semibold text-slate-400">Cursus de Licence & Master</span>
                </div>
            </div>

            <!-- Card Classes -->
            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 font-black flex items-center justify-center text-2xl shadow-inner">7</div>
                <div>
                    <span class="text-base font-bold text-slate-700 block">Classes</span>
                    <span class="text-xs font-semibold text-slate-400">Groupes de cours</span>
                </div>
            </div>

            <!-- Card Total Étudiants -->
            <div class="bg-[#0B1728] p-6 rounded-2xl text-white shadow-md flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl bg-[#1A283B] text-white font-black flex items-center justify-center text-3xl">12</div>
                <div>
                    <span class="text-lg font-extrabold text-white block">Total étudiants</span>
                    <span class="text-xs font-semibold text-slate-400">Inscrits cette année</span>
                </div>
            </div>

            <!-- Card Garçons -->
            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl bg-sky-50 text-sky-600 font-black flex items-center justify-center text-2xl shadow-inner">6</div>
                <div>
                    <span class="text-base font-bold text-slate-700 block">Garçons</span>
                    <span class="text-xs font-semibold text-slate-400">50% de l'effectif</span>
                </div>
            </div>

            <!-- Card Filles -->
            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl bg-pink-50 text-pink-600 font-black flex items-center justify-center text-2xl shadow-inner">6</div>
                <div>
                    <span class="text-base font-bold text-slate-700 block">Filles</span>
                    <span class="text-xs font-semibold text-slate-400">50% de l'effectif</span>
                </div>
            </div>
        </div>

        <!-- Section 1 : Répartition par sexe (Barre bicolore) -->
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm mb-8">
            <h2 class="text-base font-extrabold text-slate-900">Répartition par sexe</h2>
            <p class="text-xs text-slate-400 font-semibold mb-4">Parité au sein de l'établissement</p>
            
            <div class="w-full h-7 rounded-xl overflow-hidden flex font-extrabold text-[11px] text-white">
                <div class="bg-[#2B5292] h-full w-1/2 flex items-center justify-center">50%</div>
                <div class="bg-[#C22975] h-full w-1/2 flex items-center justify-center">50%</div>
            </div>
            
            <div class="flex gap-6 mt-4 text-xs font-bold text-slate-600">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-[#2B5292]"></span>
                    <span>Garçons (6)</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-[#C22975]"></span>
                    <span>Filles (6)</span>
                </div>
            </div>
        </div>

        <!-- Section 2 : Filières & Niveaux -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            
            <!-- Étudiants par filière -->
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
                <h2 class="text-base font-extrabold text-slate-900">Étudiants par filière</h2>
                <p class="text-xs text-slate-400 font-semibold mb-6">Répartition des effectifs</p>

                <div class="space-y-4 text-xs font-extrabold">
                    <div class="flex items-center justify-between gap-4">
                        <span class="w-28 text-slate-800">Informatique</span>
                        <div class="flex-1 bg-slate-100 h-8 rounded-xl overflow-hidden">
                            <div class="bg-[#0B1728] h-full w-[100%] rounded-xl flex items-center px-3 text-white">7</div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-4">
                        <span class="w-28 text-slate-800">Gestion</span>
                        <div class="flex-1 bg-slate-100 h-8 rounded-xl overflow-hidden">
                            <div class="bg-[#1E40AF] h-full w-[42%] rounded-xl flex items-center px-3 text-white">3</div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-4">
                        <span class="w-28 text-slate-800">Marketing</span>
                        <div class="flex-1 bg-slate-100 h-8 rounded-xl overflow-hidden">
                            <div class="bg-[#2563EB] h-full w-[28%] rounded-xl flex items-center px-3 text-white">2</div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-4">
                        <span class="w-28 text-slate-800 truncate">Communicati...</span>
                        <div class="flex-1 bg-slate-100 h-8 rounded-xl overflow-hidden">
                            <div class="bg-[#3B82F6] h-full w-[12%] rounded-xl flex items-center px-3 text-white">0</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Étudiants par niveau -->
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
                <h2 class="text-base font-extrabold text-slate-900">Étudiants par niveau</h2>
                <p class="text-xs text-slate-400 font-semibold mb-6">Répartition par année d'études</p>

                <div class="space-y-4 text-xs font-extrabold">
                    <div class="flex items-center justify-between gap-4">
                        <span class="w-24 text-slate-800">Licence 1</span>
                        <div class="flex-1 bg-slate-100 h-8 rounded-xl overflow-hidden">
                            <div class="bg-[#7C3AED] h-full w-[100%] rounded-xl flex items-center px-3 text-white">7</div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-4">
                        <span class="w-24 text-slate-800">Licence 2</span>
                        <div class="flex-1 bg-slate-100 h-8 rounded-xl overflow-hidden">
                            <div class="bg-[#8B5CF6] h-full w-[42%] rounded-xl flex items-center px-3 text-white">3</div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-4">
                        <span class="w-24 text-slate-800">Licence 3</span>
                        <div class="flex-1 bg-slate-100 h-8 rounded-xl overflow-hidden">
                            <div class="bg-[#A78BFA] h-full w-[20%] rounded-xl flex items-center px-3 text-white">1</div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-4">
                        <span class="w-24 text-slate-800">Master 1</span>
                        <div class="flex-1 bg-slate-100 h-8 rounded-xl overflow-hidden">
                            <div class="bg-[#C4B5FD] h-full w-[20%] rounded-xl flex items-center px-3 text-white">1</div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-4">
                        <span class="w-24 text-slate-800">Master 2</span>
                        <div class="flex-1 bg-slate-100 h-8 rounded-xl overflow-hidden">
                            <div class="bg-[#DDD6FE] h-full w-[10%] rounded-xl flex items-center px-3 text-white">0</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Section 3 : Effectifs par classe -->
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h2 class="text-base font-extrabold text-slate-900">Effectifs par classe</h2>
                    <p class="text-xs text-slate-400 font-semibold">Taux de remplissage des classes</p>
                </div>
                <div class="text-right">
                    <span class="text-[10px] text-slate-400 font-extrabold block uppercase tracking-wider">Classe la plus peuplée</span>
                    <span class="text-sm font-black text-slate-900 block">INFO L1 Groupe A</span>
                    <span class="text-xs font-bold text-amber-500 block">3 étudiants</span>
                </div>
            </div>

            <div class="space-y-4 text-xs font-extrabold">
                <!-- Classe 1 -->
                <div class="flex items-center justify-between gap-4">
                    <span class="w-36 text-slate-800">INFO L1 Groupe A</span>
                    <div class="flex-1 bg-slate-100 h-8 rounded-xl overflow-hidden">
                        <div class="bg-[#1E3A8A] h-full w-[22%] rounded-xl flex items-center px-3 text-white">3</div>
                    </div>
                    <span class="text-slate-400 text-[11px] font-bold">3/35 (9%)</span>
                </div>

                <!-- Classe 2 -->
                <div class="flex items-center justify-between gap-4">
                    <span class="w-36 text-slate-800">INFO L2 Groupe A</span>
                    <div class="flex-1 bg-slate-100 h-8 rounded-xl overflow-hidden">
                        <div class="bg-[#1E3A8A] h-full w-[16%] rounded-xl flex items-center px-3 text-white">2</div>
                    </div>
                    <span class="text-slate-400 text-[11px] font-bold">2/30 (7%)</span>
                </div>

                <!-- Classe 3 -->
                <div class="flex items-center justify-between gap-4">
                    <span class="w-36 text-slate-800">GES L1 Groupe A</span>
                    <div class="flex-1 bg-slate-100 h-8 rounded-xl overflow-hidden">
                        <div class="bg-[#1E3A8A] h-full w-[16%] rounded-xl flex items-center px-3 text-white">2</div>
                    </div>
                    <span class="text-slate-400 text-[11px] font-bold">2/40 (5%)</span>
                </div>

                <!-- Classe 4 -->
                <div class="flex items-center justify-between gap-4">
                    <span class="w-36 text-slate-800">MKT L1 Groupe A</span>
                    <div class="flex-1 bg-slate-100 h-8 rounded-xl overflow-hidden">
                        <div class="bg-[#1E3A8A] h-full w-[16%] rounded-xl flex items-center px-3 text-white">2</div>
                    </div>
                    <span class="text-slate-400 text-[11px] font-bold">2/30 (7%)</span>
                </div>

                <!-- Classe 5 -->
                <div class="flex items-center justify-between gap-4">
                    <span class="w-36 text-slate-800">INFO L3 Groupe A</span>
                    <div class="flex-1 bg-slate-100 h-8 rounded-xl overflow-hidden">
                        <div class="bg-[#1E3A8A] h-full w-[10%] rounded-xl flex items-center px-3 text-white">1</div>
                    </div>
                    <span class="text-slate-400 text-[11px] font-bold">1/25 (4%)</span>
                </div>

                <!-- Classe 6 -->
                <div class="flex items-center justify-between gap-4">
                    <span class="w-36 text-slate-800">GES L2 Groupe A</span>
                    <div class="flex-1 bg-slate-100 h-8 rounded-xl overflow-hidden">
                        <div class="bg-[#1E3A8A] h-full w-[10%] rounded-xl flex items-center px-3 text-white">1</div>
                    </div>
                    <span class="text-slate-400 text-[11px] font-bold">1/35 (3%)</span>
                </div>

                <!-- Classe 7 -->
                <div class="flex items-center justify-between gap-4">
                    <span class="w-36 text-slate-800">INFO M1 Groupe A</span>
                    <div class="flex-1 bg-slate-100 h-8 rounded-xl overflow-hidden">
                        <div class="bg-[#1E3A8A] h-full w-[10%] rounded-xl flex items-center px-3 text-white">1</div>
                    </div>
                    <span class="text-slate-400 text-[11px] font-bold">1/20 (5%)</span>
                </div>
            </div>
        </div>

    </main>

</body>
</html>