<?php

require_once 'ensemble.php';

// Listes de départ (utilisées seulement si le localStorage d'une page est vide)
$seedEtudiants = $_SESSION["Etudiants"] ?? [];
$seedFilieres  = $_SESSION["Filieres"]  ?? [];
$seedNiveaux   = $_SESSION["Niveaux"]   ?? [];
$seedClasses   = $_SESSION["Classes"]   ?? [];

$flags = JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;
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

    <!-- ==================== SIDEBAR GAUCHE ==================== -->
    <aside class="w-64 bg-[#0B1728] text-white flex flex-col justify-between p-4 min-h-screen fixed left-0 top-0 z-10">
        <div>
            <div class="mb-8 px-3 pt-2">
                <h1 class="text-2xl font-black tracking-tight text-white">École 221</h1>
                <p class="text-xs text-slate-400 font-semibold mt-0.5">Gestion Scolaire</p>
            </div>

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

                <a href="Classe.php" class="flex items-center gap-3.5 px-4 py-3 text-slate-300 hover:bg-[#1A283B] hover:text-white font-bold text-sm rounded-xl transition">
                    <i class="fi fi-rr-bank text-base"></i>
                    <span>Classes</span>
                </a>

                <a href="Etudiants.php" class="flex items-center gap-3.5 px-4 py-3 text-slate-300 hover:bg-[#1A283B] hover:text-white font-bold text-sm rounded-xl transition">
                    <i class="fi fi-rr-users-alt text-base"></i>
                    <span>Étudiants</span>
                </a>

                <a href="Statistique.php" class="flex items-center gap-3.5 px-4 py-3 bg-[#1A283B] text-[#EAB308] font-bold text-sm rounded-xl border-l-4 border-[#EAB308] shadow-sm">
                    <i class="fi fi-rr-chart-histogram text-base"></i>
                    <span>Statistiques</span>
                </a>
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

    <!-- ==================== CONTENU PRINCIPAL ==================== -->
    <main class="flex-1 ml-64 p-8">

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

        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Statistiques</h1>
            <p class="text-sm text-slate-500 font-semibold mt-1">Indicateurs clés de l'établissement</p>
        </div>

        <!-- 6 cartes (les chiffres sont écrits par le JavaScript) -->
        <div id="grille-cartes" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-5">
                <div id="c-filieres" class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 font-black flex items-center justify-center text-2xl shadow-inner">0</div>
                <div>
                    <span class="text-base font-bold text-slate-700 block">Filières</span>
                    <span class="text-xs font-semibold text-slate-400">Formations disponibles</span>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-5">
                <div id="c-niveaux" class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 font-black flex items-center justify-center text-2xl shadow-inner">0</div>
                <div>
                    <span class="text-base font-bold text-slate-700 block">Niveaux</span>
                    <span class="text-xs font-semibold text-slate-400">Cursus de Licence & Master</span>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-5">
                <div id="c-classes" class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 font-black flex items-center justify-center text-2xl shadow-inner">0</div>
                <div>
                    <span class="text-base font-bold text-slate-700 block">Classes</span>
                    <span class="text-xs font-semibold text-slate-400">Groupes de cours</span>
                </div>
            </div>

            <div class="bg-[#0B1728] p-6 rounded-2xl text-white shadow-md flex items-center gap-5">
                <div id="c-total" class="w-14 h-14 rounded-2xl bg-[#1A283B] text-white font-black flex items-center justify-center text-3xl">0</div>
                <div>
                    <span class="text-lg font-extrabold text-white block">Total étudiants</span>
                    <span class="text-xs font-semibold text-slate-400">Inscrits cette année</span>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-5">
                <div id="c-garcons" class="w-14 h-14 rounded-2xl bg-sky-50 text-sky-600 font-black flex items-center justify-center text-2xl shadow-inner">0</div>
                <div>
                    <span class="text-base font-bold text-slate-700 block">Garçons</span>
                    <span id="c-garcons-pct" class="text-xs font-semibold text-slate-400"></span>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-5">
                <div id="c-filles" class="w-14 h-14 rounded-2xl bg-pink-50 text-pink-600 font-black flex items-center justify-center text-2xl shadow-inner">0</div>
                <div>
                    <span class="text-base font-bold text-slate-700 block">Filles</span>
                    <span id="c-filles-pct" class="text-xs font-semibold text-slate-400"></span>
                </div>
            </div>
        </div>

        <!-- Répartition par sexe -->
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm mb-8">
            <h2 class="text-base font-extrabold text-slate-900">Répartition par sexe</h2>
            <p class="text-xs text-slate-400 font-semibold mb-4">Parité au sein de l'établissement</p>

            <div class="w-full h-7 rounded-xl overflow-hidden flex font-extrabold text-[11px] text-white bg-slate-100">
                <div id="barre-m" class="bg-[#2B5292] h-full flex items-center justify-center transition-all duration-500" style="width:0%"></div>
                <div id="barre-f" class="bg-[#C22975] h-full flex items-center justify-center transition-all duration-500" style="width:0%"></div>
            </div>

            <div class="flex gap-6 mt-4 text-xs font-bold text-slate-600">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-[#2B5292]"></span>
                    <span id="leg-m">Garçons (0)</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-[#C22975]"></span>
                    <span id="leg-f">Filles (0)</span>
                </div>
            </div>
        </div>

        <!-- Filières & Niveaux -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
                <h2 class="text-base font-extrabold text-slate-900">Étudiants par filière</h2>
                <p class="text-xs text-slate-400 font-semibold mb-6">Répartition des effectifs</p>
                <div id="liste-filieres" class="space-y-4 text-xs font-extrabold"></div>
            </div>

            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
                <h2 class="text-base font-extrabold text-slate-900">Étudiants par niveau</h2>
                <p class="text-xs text-slate-400 font-semibold mb-6">Répartition par année d'études</p>
                <div id="liste-niveaux" class="space-y-4 text-xs font-extrabold"></div>
            </div>
        </div>

        <!-- Effectifs par classe -->
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h2 class="text-base font-extrabold text-slate-900">Effectifs par classe</h2>
                    <p class="text-xs text-slate-400 font-semibold">Taux de remplissage des classes</p>
                </div>
                <div class="text-right">
                    <span class="text-[10px] text-slate-400 font-extrabold block uppercase tracking-wider">Classe la plus peuplée</span>
                    <span id="top-nom" class="text-sm font-black text-slate-900 block">—</span>
                    <span id="top-nb" class="text-xs font-bold text-amber-500 block"></span>
                </div>
            </div>

            <div id="liste-classes" class="space-y-4 text-xs font-extrabold"></div>
        </div>

    </main>

<script>
// =====================================================
// 1. LECTURE DES DONNÉES (le localStorage de chaque page)
// =====================================================

// Listes de départ envoyées par PHP (si une page n'a encore rien enregistré)
const SEED = {
    etudiants: <?= json_encode($seedEtudiants, $flags) ?>,
    filieres:  <?= json_encode($seedFilieres,  $flags) ?>,
    niveaux:   <?= json_encode($seedNiveaux,   $flags) ?>,
    classes:   <?= json_encode($seedClasses,   $flags) ?>,
};

// Les mêmes clés que celles utilisées par les 4 autres pages
const CLES = {
    etudiants: "etudiants_ecole221",
    filieres:  "filieres_ecole221",
    niveaux:   "niveaux_ecole221",
    classes:   "classes_ecole221",
};

// Lit une liste : localStorage d'abord, sinon liste de départ
function lire(nom) {
    try {
        const d = JSON.parse(localStorage.getItem(CLES[nom]));
        if (Array.isArray(d)) return d;
    } catch (e) {}
    return Array.isArray(SEED[nom]) ? SEED[nom] : [];
}

// Pour comparer sans tenir compte des majuscules ni des espaces autour
function norm(t) { return String(t ?? "").trim().toLowerCase(); }

// Protège l'affichage contre le HTML
function echapper(t) {
    return String(t ?? "")
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;");
}

function pourcent(n, total) { return total > 0 ? Math.round(n / total * 100) : 0; }


// =====================================================
// 2. OUTIL : UNE LIGNE AVEC BARRE
// =====================================================
// largeur de l'étiquette (classe Tailwind), nom, valeur, maximum, couleur, texte à droite (optionnel)
function ligneBarre(largeur, nom, valeur, max, couleur, droite = "") {
    // La barre la plus longue = 100 %. Minimum 8 % pour que le chiffre reste lisible.
    const w = max > 0 ? Math.max(8, Math.round(valeur / max * 100)) : 8;
    return `
        <div class="flex items-center justify-between gap-4">
            <span class="${largeur} text-slate-800 truncate" title="${echapper(nom)}">${echapper(nom)}</span>
            <div class="flex-1 bg-slate-100 h-8 rounded-xl overflow-hidden">
                <div class="h-full rounded-xl flex items-center px-3 text-white transition-all duration-500" style="width:${w}%; background:${couleur}">${valeur}</div>
            </div>
            ${droite ? `<span class="text-slate-400 text-[11px] font-bold whitespace-nowrap">${droite}</span>` : ""}
        </div>`;
}

const vide = msg => `<p class="text-center text-slate-400 font-bold py-6">${msg}</p>`;

const COULEURS_FILIERES = ["#0B1728", "#1E40AF", "#2563EB", "#3B82F6", "#60A5FA", "#93C5FD"];
const COULEURS_NIVEAUX  = ["#7C3AED", "#8B5CF6", "#A78BFA", "#C4B5FD", "#DDD6FE", "#EDE9FE"];


// =====================================================
// 3. CALCUL ET AFFICHAGE DE TOUTES LES STATISTIQUES
// =====================================================
function afficher() {
    const etudiants = lire("etudiants");
    const filieres  = lire("filieres");
    const niveaux   = lire("niveaux");
    const classes   = lire("classes");

    const total = etudiants.length;

    // ---- Cartes du haut ----
    const garcons = etudiants.filter(e => e.sexe === "M").length;
    const filles  = etudiants.filter(e => e.sexe === "F").length;

    document.getElementById("c-filieres").textContent = filieres.length;
    document.getElementById("c-niveaux").textContent  = niveaux.length;
    document.getElementById("c-classes").textContent  = classes.length;
    document.getElementById("c-total").textContent    = total;
    document.getElementById("c-garcons").textContent  = garcons;
    document.getElementById("c-filles").textContent   = filles;
    document.getElementById("c-garcons-pct").textContent = pourcent(garcons, total) + "% de l'effectif";
    document.getElementById("c-filles-pct").textContent  = pourcent(filles, total) + "% de l'effectif";

    // ---- Barre bicolore Garçons / Filles ----
    const pm = pourcent(garcons, total);
    const pf = total > 0 ? 100 - pm : 0;          // la somme fait toujours 100 %
    const barreM = document.getElementById("barre-m");
    const barreF = document.getElementById("barre-f");
    barreM.style.width = pm + "%";
    barreF.style.width = pf + "%";
    barreM.textContent = pm > 0 ? pm + "%" : "";
    barreF.textContent = pf > 0 ? pf + "%" : "";
    document.getElementById("leg-m").textContent = "Garçons (" + garcons + ")";
    document.getElementById("leg-f").textContent = "Filles (" + filles + ")";

    // ---- Étudiants par filière (égal au code OU au nom de la filière) ----
    const parFiliere = filieres.map(f => {
        const code = norm(f.code), nom = norm(f.nom);
        const nb = etudiants.filter(e => {
            const v = norm(e.filiere);
            return v !== "" && (v === code || v === nom);
        }).length;
        return { nom: f.nom || f.code, nb };
    });
    const maxF = Math.max(0, ...parFiliere.map(x => x.nb));
    document.getElementById("liste-filieres").innerHTML = parFiliere.length
        ? parFiliere.map((x, i) => ligneBarre("w-28", x.nom, x.nb, maxF, COULEURS_FILIERES[i % COULEURS_FILIERES.length])).join("")
        : vide("Aucune filière enregistrée");

    // ---- Étudiants par niveau (égal au code OU au nom du niveau) ----
    const parNiveau = niveaux.map(n => {
        const code = norm(n.code), nom = norm(n.nom);
        const nb = etudiants.filter(e => {
            const v = norm(e.niveau);
            return v !== "" && (v === code || v === nom);
        }).length;
        return { nom: n.nom || n.code, nb };
    });
    const maxN = Math.max(0, ...parNiveau.map(x => x.nb));
    document.getElementById("liste-niveaux").innerHTML = parNiveau.length
        ? parNiveau.map((x, i) => ligneBarre("w-24", x.nom, x.nb, maxN, COULEURS_NIVEAUX[i % COULEURS_NIVEAUX.length])).join("")
        : vide("Aucun niveau enregistré");

    // ---- Effectifs par classe (égal au nom de la classe) ----
    const parClasse = classes.map(c => {
        const nom = norm(c.nom);
        const nb = etudiants.filter(e => norm(e.classe) === nom).length;
        return { nom: c.nom, nb, capacite: Number(c.capacite) || 0 };
    }).sort((a, b) => b.nb - a.nb);          // la plus peuplée en premier

    const maxC = Math.max(0, ...parClasse.map(x => x.nb));
    document.getElementById("liste-classes").innerHTML = parClasse.length
        ? parClasse.map(x => ligneBarre(
            "w-36", x.nom, x.nb, maxC, "#1E3A8A",
            x.capacite > 0 ? x.nb + "/" + x.capacite + " (" + pourcent(x.nb, x.capacite) + "%)" : x.nb + "/—"
          )).join("")
        : vide("Aucune classe enregistrée");

    // ---- Classe la plus peuplée ----
    const top = parClasse[0];
    document.getElementById("top-nom").textContent = top && top.nb > 0 ? top.nom : "—";
    document.getElementById("top-nb").textContent  = top && top.nb > 0
        ? top.nb + (top.nb > 1 ? " étudiants" : " étudiant") : "";
}


// =====================================================
// 4. MISE À JOUR AUTOMATIQUE
// =====================================================

// Une autre page / un autre onglet modifie les données : on recalcule tout de suite
window.addEventListener("storage", e => {
    if (Object.values(CLES).includes(e.key)) afficher();
});

// Retour sur la page avec le bouton "précédent" : on recalcule aussi
window.addEventListener("pageshow", afficher);

// Premier affichage
afficher();

// =====================================================
// 5. EFFET ZOOM SUR LES 6 CARTES
// =====================================================
// Chaque carte est placée dans un "wrapper" immobile qui écoute la souris,
// et c'est la carte (la "boîte") à l'intérieur qui grossit.
// Ainsi le zoom ne fait pas clignoter la carte quand elle grandit sous le curseur.
document.querySelectorAll("#grille-cartes > div").forEach(boite => {
    const wrapper = document.createElement("div");
    wrapper.style.position = "relative";
    boite.replaceWith(wrapper);
    wrapper.appendChild(boite);

    boite.style.transition = "transform 0.7s ease-in-out";
    let minuteur;

    wrapper.addEventListener("mouseenter", () => {
        clearTimeout(minuteur);
        wrapper.style.zIndex = "10";               // la carte passe au-dessus des voisines
        boite.style.transform = "scale(1.15)";
    });
    wrapper.addEventListener("mouseleave", () => {
        boite.style.transform = "scale(1)";
        minuteur = setTimeout(() => { wrapper.style.zIndex = ""; }, 700);
    });
});
</script>
</body>
</html>