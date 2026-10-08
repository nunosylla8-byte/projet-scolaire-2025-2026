<?php
session_start();

require_once 'ensemble.php';


?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Étudiants - École 221</title>
    
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
                <a href="Tableaudebord.php" class="flex items-center gap-3.5 px-4 py-3 text-slate-300 hover:bg-[#1A283B] hover:text-white font-bold text-sm rounded-xl transition">
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

                <a href="Classes.php" class="flex items-center gap-3.5 px-4 py-3 bg-[#1A283B] text-[#EAB308] font-bold text-sm rounded-xl border-l-4 border-[#EAB308] shadow-sm">
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
                <span class="font-extrabold text-slate-800">Étudiants</span>
            </div>
            <div class="flex items-center gap-2 text-slate-500 font-bold">
                <span>Année universitaire 2025–2026</span>
                <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full"></span>
            </div>
        </header>

        <!-- Titre principal avec Bouton d'action -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Étudiants</h1>
                <p class="text-sm text-slate-500 font-semibold mt-1"><?= count($_SESSION["Etudiants"]) ?> étudiants enregistrés</p>
            </div>
            <button class="bg-[#0B1728] hover:bg-[#1A283B] text-white px-5 py-3 rounded-xl font-bold text-sm flex items-center gap-2 transition shadow-sm">
                <i class="fi fi-rr-plus text-xs"></i>
                <span>Nouvel étudiant</span>
            </button>
        </div>

        <!-- Card Conteneur de la Table -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">
            
            <!-- Barre de Recherche et Filtres -->
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-6">
                <!-- Champ de recherche -->
                <div class="w-full sm:w-2/3">
                    <input type="text" placeholder="Rechercher par nom, prénom ou matricule..." class="w-full bg-[#F8FAFC] border border-slate-200 text-xs font-bold text-slate-700 placeholder-slate-400 px-4 py-3 rounded-xl focus:outline-none focus:border-slate-400 transition">
                </div>

                <!-- Boutons filtres -->
                <div class="flex items-center gap-2 w-full sm:w-auto justify-end text-xs font-bold">
                    <button class="bg-[#0B1728] text-white px-5 py-2.5 rounded-xl">Tous</button>
                    <button class="bg-[#F1F5F9] text-slate-500 hover:bg-slate-200 px-5 py-2.5 rounded-xl transition">Garçons</button>
                    <button class="bg-[#F1F5F9] text-slate-500 hover:bg-slate-200 px-5 py-2.5 rounded-xl transition">Filles</button>
                </div>
            </div>

            <!-- Tableau des Étudiants -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-[11px] font-black text-slate-400 uppercase tracking-wider border-b border-slate-100">
                            <th class="py-3 px-4">MATRICULE</th>
                            <th class="py-3 px-4">NOM</th>
                            <th class="py-3 px-4">PRÉNOM</th>
                            <th class="py-3 px-4 text-center">SEXE</th>
                            <th class="py-3 px-4">CLASSE</th>
                            <th class="py-3 px-4">FILIÈRE</th>
                            <th class="py-3 px-4">NIVEAU</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs font-bold text-slate-700">
                        <?php foreach ($_SESSION["Etudiants"] as $e): ?>
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-4 px-4 font-bold text-slate-500"><?= htmlspecialchars($e['matricule']) ?></td>
                            <td class="py-4 px-4 font-black text-slate-900"><?= htmlspecialchars($e['nom']) ?></td>
                            <td class="py-4 px-4 font-bold text-slate-700"><?= htmlspecialchars($e['prenom']) ?></td>
                            
                            <!-- Badge Sexe (M / F) rond -->
                            <td class="py-4 px-4 text-center">
                                <?php if ($e['sexe'] === 'M'): ?>
                                    <span class="w-6 h-6 rounded-full bg-sky-100 text-sky-600 font-extrabold text-[11px] inline-flex items-center justify-center">M</span>
                                <?php else: ?>
                                    <span class="w-6 h-6 rounded-full bg-pink-100 text-pink-600 font-extrabold text-[11px] inline-flex items-center justify-center">F</span>
                                <?php endif; ?>
                            </td>

                            <td class="py-4 px-4 font-bold text-slate-700"><?= htmlspecialchars($e['classe']) ?></td>
                            
                            <!-- Badge Filière gris clair -->
                            <td class="py-4 px-4">
                                <span class="bg-slate-100 text-slate-700 font-extrabold text-[10px] px-2.5 py-1 rounded-md">
                                    <?= htmlspecialchars($e['filiere']) ?>
                                </span>
                            </td>

                            <td class="py-4 px-4 font-extrabold text-slate-500"><?= htmlspecialchars($e['niveau']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Footer sous le tableau -->
            <div class="mt-6 text-xs font-bold text-slate-400">
                <?= count($_SESSION["Etudiants"]) ?> résultats sur <?= count($_SESSION["Etudiants"]) ?> étudiants
            </div>

        </div>

    </main>

</body>
</html>