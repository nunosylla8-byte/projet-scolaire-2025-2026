<?php
session_start();

require_once 'ensemble.php';

// Initialisation du tableau des filières s'il n'existe pas
if (!isset($_SESSION["Filieres"])) {
   
}

// Traitement du formulaire d'ajout d'une filière
if (isset($_POST['ajout_filiere'])) {
    $Newfiliere = [
        "code"           => strtoupper($_POST['code']),
        "nom"            => $_POST['nom'],
        "responsable"    => $_POST['responsable'],
        "description"    => $_POST['description'],
        "etudiants_count" => 0
    ];
    $_SESSION["Filieres"][] = $Newfiliere;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filières - École 221</title>
    
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

                <a href="Classes.php" class="flex items-center gap-3.5 px-4 py-3 bg-[#1A283B] text-[#EAB308] font-bold text-sm rounded-xl border-l-4 border-[#EAB308] shadow-sm">
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
                <span class="font-extrabold text-slate-800">Filières</span>
            </div>
            <div class="flex items-center gap-2 text-slate-500 font-bold">
                <span>Année universitaire 2025–2026</span>
                <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full"></span>
            </div>
        </header>

        <!-- Titre principal avec Bouton d'action -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Filières</h1>
                <p class="text-sm text-slate-500 font-semibold mt-1"><?= count($_SESSION["Filieres"]) ?> filières enregistrées</p>
            </div>
            <button class="bg-[#0B1728] hover:bg-[#1A283B] text-white px-5 py-3 rounded-xl font-bold text-sm flex items-center gap-2 transition shadow-sm">
                <i class="fi fi-rr-plus text-xs"></i>
                <span>Nouvelle filière</span>
            </button>
        </div>

        <!-- Grille des cartes de Filières (Hauteur réduite & textes agrandis) -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <?php foreach ($_SESSION["Filieres"] as $filiere): ?>
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex flex-col justify-between">
        <div>
            <!-- Header carte : Badge Code Noir et Badge Étudiants Bleu clair -->
            <div class="flex justify-between items-center mb-5">
                <span class="bg-[#0B1728] text-white font-black text-xs px-3.5 py-2 rounded-xl tracking-wider uppercase">
                    <?= htmlspecialchars($filiere['code']) ?>
                </span>
                <span class="bg-[#F0F5FD] text-[#475569] font-bold text-xs px-3 py-1 rounded-full">
                    <?= htmlspecialchars($filiere['etudiants_count']) ?> étudiants
                </span>
            </div>

            <!-- Titre de la filière -->
            <h2 class="text-xl font-extrabold text-slate-900 leading-snug">
                <?= htmlspecialchars($filiere['nom']) ?>
            </h2>

            <!-- Responsable avec l'icône BLEUE -->
            <div class="flex items-center gap-1.5 mt-1 text-sm font-bold text-[#EAB308]">
                <i class="fi fi-rr-user text-blue-500 text-xs"></i>
                <span><?= htmlspecialchars($filiere['responsable']) ?></span>
            </div>
        </div>

        <!-- Description agrandie et directement sous le bloc sans grand vide -->
        <p class="text-sm text-slate-400 font-semibold leading-relaxed mt-4">
            <?= htmlspecialchars($filiere['description']) ?>
        </p>
    </div>
    <?php endforeach; ?>
</div>

    </main>

</body>
</html>