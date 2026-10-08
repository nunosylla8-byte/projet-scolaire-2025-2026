<?php
session_start();

require_once 'ensemble.php';

// Données des classes
if (!isset($_SESSION["Classes"])) {
    $_SESSION["Classes"] = [
        
    ];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classes - École 221</title>
    
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
                <a href="Classe.php" class="flex items-center gap-3.5 px-4 py-3 bg-[#1A283B] text-[#EAB308] font-bold text-sm rounded-xl border-l-4 border-[#EAB308] shadow-sm">
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
                <span class="font-extrabold text-slate-800">Classes</span>
            </div>
            <div class="flex items-center gap-2 text-slate-500 font-bold">
                <span>Année universitaire 2025–2026</span>
                <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full"></span>
            </div>
        </header>

        <!-- Titre principal avec Bouton d'action -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Classes</h1>
                <p class="text-sm text-slate-500 font-semibold mt-1"><?= count($_SESSION["Classes"]) ?> classes enregistrées</p>
            </div>
            <button class="bg-[#0B1728] hover:bg-[#1A283B] text-white px-5 py-3 rounded-xl font-bold text-sm flex items-center gap-2 transition shadow-sm">
                <i class="fi fi-rr-plus text-xs"></i>
                <span>Nouvelle classe</span>
            </button>
        </div>

        <!-- Grille des cartes Classes (4 colonnes) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($_SESSION["Classes"] as $classe): ?>
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex flex-col justify-between">
                <div>
                    <!-- Entête carte : Titre et Badge Niveau sombre -->
                    <div class="flex justify-between items-start mb-1">
                        <h2 class="text-lg font-extrabold text-slate-900 leading-snug">
                            <?= htmlspecialchars($classe['nom']) ?>
                        </h2>
                        <span class="bg-[#0B1728] text-white font-black text-xs px-2.5 py-1 rounded-xl">
                            <?= htmlspecialchars($classe['niveau']) ?>
                        </span>
                    </div>

                    <!-- Code sous le nom -->
                    <p class="text-xs text-slate-400 font-bold mb-4 uppercase tracking-wider">
                        <?= htmlspecialchars($classe['code']) ?>
                    </p>

                    <!-- Informations Filière et Capacité -->
                    <div class="space-y-1 text-xs font-semibold mb-6">
                        <p class="text-slate-400">
                            Filière <span class="text-slate-900 font-extrabold ml-1"><?= htmlspecialchars($classe['filiere']) ?></span>
                        </p>
                        <p class="text-slate-400">
                            Capacité <span class="text-slate-900 font-extrabold ml-1"><?= htmlspecialchars($classe['capacite']) ?> places</span>
                        </p>
                    </div>
                </div>

                <!-- Section Basse : Inscrits + Barre de progression -->
                <div>
                    <div class="flex justify-between items-center text-xs font-bold mb-1.5">
                        <span class="text-slate-500"><?= htmlspecialchars($classe['etudiants_count']) ?> <?= $classe['etudiants_count'] > 1 ? 'étudiants' : 'étudiant' ?></span>
                        <span class="text-slate-500"><?= htmlspecialchars($classe['pourcentage']) ?>%</span>
                    </div>
                    
                    <!-- Barre de progression -->
                    <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                        <div class="bg-[#0B1728] h-full rounded-full" style="width: <?= $classe['pourcentage'] ?>%;"></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

    </main>

</body>
</html>