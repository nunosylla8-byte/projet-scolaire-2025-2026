<?php
// Menu latéral commun à toutes les pages.
// Le lien actif est détecté automatiquement grâce au nom du fichier en cours.
$pageActive = basename($_SERVER["SCRIPT_NAME"]);

// [fichier, texte affiché, icône]
$menu = [
    ["Tableaudebord.php", "Tableau de bord", "fi-rr-apps"],
    ["Filieres.php",      "Filières",        "fi-rr-graduation-cap"],
    ["Niveaux.php",       "Niveaux",         "fi-rr-stats"],
    ["Classes.php",       "Classes",         "fi-rr-bank"],
    ["Etudiants.php",     "Étudiants",       "fi-rr-users-alt"],
    ["Statistique.php",   "Statistiques",    "fi-rr-chart-histogram"],
];
?>
<aside class="w-64 bg-[#0B1728] text-white flex flex-col justify-between p-4 min-h-screen fixed left-0 top-0 z-10">
    <div>
        <div class="mb-8 px-3 pt-2">
            <h1 class="text-2xl font-black tracking-tight text-white">École 221</h1>
            <p class="text-xs text-slate-400 font-semibold mt-0.5">Gestion Scolaire</p>
        </div>

        <nav class="space-y-2">
            <?php foreach ($menu as [$fichier, $texte, $icone]): ?>
                <?php if ($fichier === $pageActive): ?>
                    <a href="<?= e($fichier) ?>" class="flex items-center gap-3.5 px-4 py-3 bg-[#1A283B] text-[#EAB308] font-bold text-sm rounded-xl border-l-4 border-[#EAB308] shadow-sm">
                <?php else: ?>
                    <a href="<?= e($fichier) ?>" class="flex items-center gap-3.5 px-4 py-3 text-slate-300 hover:bg-[#1A283B] hover:text-white font-bold text-sm rounded-xl transition">
                <?php endif; ?>
                        <i class="fi <?= e($icone) ?> text-base"></i>
                        <span><?= e($texte) ?></span>
                    </a>
            <?php endforeach; ?>
        </nav>
    </div>

    <div class="flex items-center gap-3 px-2 pt-4 border-t border-slate-800">
        <div class="w-10 h-10 rounded-full bg-[#EAB308] text-[#0B1728] flex items-center justify-center font-black text-sm">S</div>
        <div>
            <p class="text-sm font-bold text-white leading-tight">Secrétariat</p>
            <p class="text-xs text-slate-400 font-semibold">Administrateur</p>
        </div>
    </div>
</aside>
