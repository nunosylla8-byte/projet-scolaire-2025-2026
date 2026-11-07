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
<!-- MENU RESPONSIVE (CSS seulement) :
     la case à cocher cachée sert d'interrupteur ; les éléments qui la suivent
     réagissent avec "peer-checked". Sur grand écran (lg), le menu est toujours visible. -->
<input type="checkbox" id="menu-toggle" class="peer hidden">

<!-- Barre du haut : visible uniquement sur mobile / tablette -->
<div class="lg:hidden fixed top-0 inset-x-0 h-14 bg-[#0B1728] text-white flex items-center gap-3 px-4 z-10 shadow-md">
    <label for="menu-toggle" class="cursor-pointer w-10 h-10 flex items-center justify-center rounded-xl hover:bg-[#1A283B]" title="Ouvrir le menu">
        <i class="fi fi-rr-menu-burger text-lg"></i>
    </label>
    <span class="text-lg font-black tracking-tight">École 221</span>
</div>

<!-- Fond sombre derrière le menu (mobile) : un clic dessus ferme le menu -->
<label for="menu-toggle" class="hidden peer-checked:block lg:peer-checked:hidden fixed inset-0 bg-black/50 z-20"></label>

<aside class="w-64 h-full overflow-y-auto bg-[#0B1728] text-white flex flex-col justify-between p-4 fixed left-0 top-0 z-30 -translate-x-full peer-checked:translate-x-0 lg:translate-x-0 transition-transform duration-300">
    <div>
        <div class="mb-8 px-3 pt-2 flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-black tracking-tight text-white">École 221</h1>
                <p class="text-xs text-slate-400 font-semibold mt-0.5">Gestion Scolaire</p>
            </div>
            <!-- Bouton fermer : mobile seulement -->
            <label for="menu-toggle" class="lg:hidden cursor-pointer w-9 h-9 flex items-center justify-center rounded-xl hover:bg-[#1A283B]" title="Fermer le menu">
                <i class="fi fi-rr-cross-small text-xl"></i>
            </label>
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

    <div class="flex items-center gap-3 px-2 pt-4 mt-6 border-t border-slate-800">
        <div class="w-10 h-10 rounded-full bg-[#EAB308] text-[#0B1728] flex items-center justify-center font-black text-sm">S</div>
        <div>
            <p class="text-sm font-bold text-white leading-tight">Secrétariat</p>
            <p class="text-xs text-slate-400 font-semibold">Administrateur</p>
        </div>
    </div>
</aside>