<?php

require_once 'ensemble.php';

// Données des classes (liste de départ, utilisée tant que le localStorage est vide)
if (!isset($_SESSION["Classes"])) {
    $_SESSION["Classes"] = [

    ];
}
$classes = $_SESSION["Classes"];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classes - École 221</title>

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
                <span class="font-extrabold text-slate-800">Classes</span>
            </div>
            <div class="flex items-center gap-2 text-slate-500 font-bold">
                <span>Année universitaire 2025–2026</span>
                <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full"></span>
            </div>
        </header>

        <!-- Titre + bouton "Nouvelle classe" -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Classes</h1>
                <!-- Écrit par le JavaScript -->
                <p id="total-haut" class="text-sm text-slate-500 font-semibold mt-1"></p>
            </div>
            <button id="btn-ajouter" type="button" class="bg-[#0B1728] hover:bg-[#1A283B] text-white px-5 py-3 rounded-xl font-bold text-sm flex items-center gap-2 transition shadow-sm">
                <i class="fi fi-rr-plus text-xs"></i>
                <span>Nouvelle classe</span>
            </button>
        </div>

        <!-- Grille des cartes (remplie par le JavaScript) -->
        <div id="grille" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6"></div>

    </main>

<script>
// =====================================================
// 1. DONNÉES + LOCALSTORAGE
// =====================================================

// Nom sous lequel la liste des classes est gardée dans le navigateur
const CLE = "classes_ecole221";

// Clé utilisée par la page Étudiants (pour compter les inscrits)
const CLE_ETUDIANTS = "etudiants_ecole221";

// Liste de départ envoyée par PHP
const departPHP = <?= json_encode($classes, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

function charger() {
    try {
        const d = JSON.parse(localStorage.getItem(CLE));
        if (Array.isArray(d)) return d;
    } catch (e) {}
    return Array.isArray(departPHP) ? departPHP : [];
}

function sauvegarder() {
    try {
        localStorage.setItem(CLE, JSON.stringify(classes));
    } catch (e) {
        toast("Stockage plein : impossible de sauvegarder", true);
    }
}

// Protège l'affichage contre le HTML
function echapper(t) {
    return String(t ?? "")
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;");
}

let classes = charger();
let aSupprimer = null;
let dernierSupprime = null;
let positionSupprime = -1;

const grille    = document.getElementById("grille");
const totalHaut = document.getElementById("total-haut");

// Nombre d'étudiants d'une classe :
// on compte ceux de la page Étudiants dont la classe porte le même nom.
// S'il n'y a pas de liste d'étudiants, on garde le nombre fourni par PHP.
function nbEtudiants(c) {
    try {
        const liste = JSON.parse(localStorage.getItem(CLE_ETUDIANTS));
        if (Array.isArray(liste)) {
            const nom = String(c.nom || "").trim().toLowerCase();
            return liste.filter(e => String(e.classe || "").trim().toLowerCase() === nom).length;
        }
    } catch (e) {}
    return Number(c.etudiants_count) || 0;
}


// =====================================================
// 2. AFFICHAGE DES CARTES + COMPTEUR
// =====================================================
function carteHTML(c, i) {
    const capacite = Number(c.capacite) || 0;
    const nb = nbEtudiants(c);
    const pct = capacite > 0 ? Math.min(100, Math.round(nb / capacite * 100)) : 0;

    return `
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex flex-col justify-between">
            <div>
                <div class="flex justify-between items-start mb-1 gap-2">
                    <h2 class="text-lg font-extrabold text-slate-900 leading-snug">${echapper(c.nom)}</h2>
                    <span class="bg-[#0B1728] text-white font-black text-xs px-2.5 py-1 rounded-xl">${echapper(c.niveau)}</span>
                </div>

                <div class="flex justify-between items-center mb-4">
                    <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">${echapper(c.code)}</p>
                    <div class="whitespace-nowrap">
                        <button type="button" data-index="${i}" class="btn-modif text-slate-400 hover:text-sky-600 transition mr-3" title="Modifier">
                            <i class="fi fi-rr-pencil text-base pointer-events-none"></i>
                        </button>
                        <button type="button" data-index="${i}" class="btn-suppr text-slate-400 hover:text-red-600 transition" title="Supprimer">
                            <i class="fi fi-rr-trash text-base pointer-events-none"></i>
                        </button>
                    </div>
                </div>

                <div class="space-y-1 text-xs font-semibold mb-6">
                    <p class="text-slate-400">
                        Filière <span class="text-slate-900 font-extrabold ml-1">${echapper(c.filiere)}</span>
                    </p>
                    <p class="text-slate-400">
                        Capacité <span class="text-slate-900 font-extrabold ml-1">${echapper(capacite)} places</span>
                    </p>
                </div>
            </div>

            <div>
                <div class="flex justify-between items-center text-xs font-bold mb-1.5">
                    <span class="text-slate-500">${nb} ${nb > 1 ? "étudiants" : "étudiant"}</span>
                    <span class="text-slate-500">${pct}%</span>
                </div>
                <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                    <div class="bg-[#0B1728] h-full rounded-full" style="width: ${pct}%;"></div>
                </div>
            </div>
        </div>`;
}

function afficher() {
    grille.innerHTML = classes.length
        ? classes.map(carteHTML).join("")
        : '<p class="col-span-full text-center text-slate-400 font-bold py-16">Aucune classe enregistrée</p>';

    const n = classes.length;
    totalHaut.textContent = n + (n > 1 ? " classes enregistrées" : " classe enregistrée");
}


// =====================================================
// 3. LE TOAST (avec bouton Annuler pour la suppression)
// =====================================================
const toastEl = document.createElement("div");
toastEl.className = "fixed bottom-6 right-6 translate-y-4 opacity-0 pointer-events-none transition-all duration-300 z-[80] px-5 py-3 rounded-lg font-bold text-sm text-white flex items-center gap-3 shadow-lg";
document.body.appendChild(toastEl);

let minuteurToast;

function cacherToast() {
    toastEl.classList.add("opacity-0", "translate-y-4", "pointer-events-none");
    dernierSupprime = null;
    positionSupprime = -1;
}

function annulerSuppression() {
    if (!dernierSupprime) return;
    classes.splice(positionSupprime, 0, dernierSupprime);
    sauvegarder();
    clearTimeout(minuteurToast);
    cacherToast();
    afficher();
}

function toast(message, erreur = false, avecAnnuler = false) {
    toastEl.innerHTML = "";

    const icone = document.createElement("i");
    icone.className = erreur ? "fi fi-rr-exclamation" : "fi fi-rr-check";

    const texte = document.createElement("span");
    texte.textContent = message;

    toastEl.append(icone, texte);

    if (avecAnnuler) {
        const b = document.createElement("button");
        b.type = "button";
        b.textContent = "Annuler";
        b.className = "ml-2 px-3 py-1 rounded bg-white/20 hover:bg-white/30 text-xs font-extrabold";
        b.addEventListener("click", annulerSuppression);
        toastEl.append(b);
    }

    toastEl.classList.toggle("bg-red-600", erreur);
    toastEl.classList.toggle("bg-emerald-600", !erreur);
    toastEl.classList.remove("opacity-0", "translate-y-4", "pointer-events-none");

    clearTimeout(minuteurToast);
    minuteurToast = setTimeout(cacherToast, avecAnnuler ? 5000 : 2500);
}


// =====================================================
// 4. LE MODAL DE CONFIRMATION (suppression)
// =====================================================
const modal = document.createElement("div");
modal.className = "fixed inset-0 bg-black/50 z-[90] hidden items-center justify-center";
modal.innerHTML = `
    <div class="bg-white rounded-2xl shadow-2xl w-[90%] max-w-sm p-6">
        <div class="flex items-start gap-3 mb-5">
            <i class="fi fi-rr-triangle-warning text-red-600 text-xl mt-0.5"></i>
            <p id="texte-confirm" class="font-bold text-slate-800"></p>
        </div>
        <div class="flex justify-end gap-2">
            <button id="btn-non" type="button" class="px-4 py-2 rounded-lg bg-slate-100 font-bold text-sm text-slate-600 hover:bg-slate-200">Annuler</button>
            <button id="btn-oui" type="button" class="px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white font-bold text-sm">Confirmer</button>
        </div>
    </div>`;
document.body.appendChild(modal);

function fermerConfirm() {
    modal.classList.add("hidden");
    modal.classList.remove("flex");
    aSupprimer = null;
}

function demanderSuppression(c) {
    aSupprimer = c;
    document.getElementById("texte-confirm").textContent =
        "Voulez-vous supprimer la classe « " + c.nom + " » ?";
    modal.classList.remove("hidden");
    modal.classList.add("flex");
}

document.getElementById("btn-non").addEventListener("click", fermerConfirm);
modal.addEventListener("click", ev => { if (ev.target === modal) fermerConfirm(); });

document.getElementById("btn-oui").addEventListener("click", () => {
    if (!aSupprimer) return;
    const nom = aSupprimer.nom;

    dernierSupprime = aSupprimer;
    positionSupprime = classes.indexOf(aSupprimer);
    classes.splice(positionSupprime, 1);

    sauvegarder();
    fermerConfirm();
    afficher();
    toast("« " + nom + " » a été supprimée", false, true);
});

// Clics sur les boutons des cartes (la grille est recréée, on écoute donc le conteneur)
grille.addEventListener("click", ev => {
    const s = ev.target.closest(".btn-suppr");
    if (s) { demanderSuppression(classes[s.dataset.index]); return; }

    const m = ev.target.closest(".btn-modif");
    if (m) ouvrirDrawer(Number(m.dataset.index));
});

// Touche Échap : ferme le modal et le tiroir
document.addEventListener("keydown", ev => {
    if (ev.key === "Escape") { fermerConfirm(); fermerDrawer(); }
});


// =====================================================
// 5. LE TIROIR (ajout et modification)
// =====================================================
const fond = document.createElement("div");
fond.className = "fixed inset-0 bg-black/50 z-50 opacity-0 pointer-events-none transition-opacity duration-300";

const drawer = document.createElement("aside");
drawer.className = "fixed top-0 right-0 h-full w-full max-w-md bg-white text-black z-[60] translate-x-full transition-transform duration-300 flex flex-col";

// [nom technique, texte affiché, type]
const champs = [
    ["nom",      "Nom de la classe", "text"],
    ["code",     "Code",             "text"],
    ["filiere",  "Filière",          "text"],
    ["niveau",   "Niveau",           "text"],
    ["capacite", "Capacité (places)", "number"],
];

drawer.innerHTML = `
    <div class="flex items-center gap-4 p-4 border-b">
        <button id="btn-fermer" type="button" class="text-slate-500 hover:text-black" title="Fermer">
            <i class="fi fi-rr-cross-small text-2xl"></i>
        </button>
        <h2 id="titre-drawer" class="text-xl font-extrabold">Nouvelle classe</h2>
    </div>

    <form id="formulaire" novalidate class="flex flex-col flex-1 min-h-0">
        <div class="flex-1 overflow-y-auto p-4 space-y-4">
            ${champs.map(([id, label, type]) => `
                <div>
                    <label class="block text-sm font-bold mb-1">${label}</label>
                    <input name="${id}" type="${type}" ${type === "number" ? 'min="1"' : ""} data-requis class="w-full border rounded-lg px-3 py-2" />
                    <p class="erreur hidden text-sm text-red-600 mt-1">Ce champ est obligatoire</p>
                </div>`).join("")}
        </div>

        <div class="flex items-center justify-end gap-4 p-4 border-t">
            <button id="btn-annuler" type="button" class="text-gray-600 font-bold">Annuler</button>
            <button id="btn-valider" type="submit" class="bg-[#0B1728] text-white font-bold px-6 py-2 rounded-lg">AJOUTER</button>
        </div>
    </form>
`;

document.body.append(fond, drawer);

const formulaire = document.getElementById("formulaire");

// Position de la classe en cours de modification (-1 = nouvelle classe)
let indexEdition = -1;

function ouvrirDrawer(index = -1) {
    indexEdition = index;
    formulaire.reset();
    formulaire.querySelectorAll(".erreur").forEach(e => {
        e.textContent = "Ce champ est obligatoire";
        e.classList.add("hidden");
    });

    const titre = document.getElementById("titre-drawer");
    const valider = document.getElementById("btn-valider");

    if (index >= 0) {
        const c = classes[index];
        champs.forEach(([nom]) => { formulaire.elements[nom].value = c[nom] ?? ""; });
        titre.textContent = "Modifier la classe";
        valider.textContent = "ENREGISTRER";
    } else {
        titre.textContent = "Nouvelle classe";
        valider.textContent = "AJOUTER";
    }

    fond.classList.remove("opacity-0", "pointer-events-none");
    drawer.classList.remove("translate-x-full");
}

function fermerDrawer() {
    fond.classList.add("opacity-0", "pointer-events-none");
    drawer.classList.add("translate-x-full");
}

function montrerErreur(nomChamp, message) {
    const msg = formulaire.elements[nomChamp].parentElement.querySelector(".erreur");
    msg.textContent = message;
    msg.classList.remove("hidden");
}

formulaire.addEventListener("submit", e => {
    e.preventDefault();
    let valide = true;

    // 1) Champs obligatoires
    formulaire.querySelectorAll("[data-requis]").forEach(champ => {
        const vide = champ.value.trim() === "";
        const msg = champ.parentElement.querySelector(".erreur");
        msg.textContent = "Ce champ est obligatoire";
        msg.classList.toggle("hidden", !vide);
        if (vide) valide = false;
    });

    // 2) Capacité : nombre entier positif
    const cap = formulaire.elements["capacite"].value.trim();
    if (cap !== "" && (!/^\d+$/.test(cap) || Number(cap) < 1)) {
        montrerErreur("capacite", "Entrez un nombre entier supérieur à 0");
        valide = false;
    }

    // 3) Code déjà utilisé ? (en modification, on ignore la classe elle-même)
    const code = formulaire.elements["code"].value.trim().toLowerCase();
    if (code !== "" && classes.some((x, i) => i !== indexEdition && String(x.code).trim().toLowerCase() === code)) {
        montrerErreur("code", "Ce code existe déjà");
        valide = false;
    }

    if (!valide) return;

    // 4) Création de l'objet classe (valeurs nettoyées)
    const nouvelle = {};
    new FormData(formulaire).forEach((valeur, nom) => { nouvelle[nom] = String(valeur).trim(); });
    nouvelle.capacite = Number(nouvelle.capacite);

    const modification = indexEdition >= 0;
    if (modification) {
        classes[indexEdition] = nouvelle;
    } else {
        classes.push(nouvelle);
    }

    sauvegarder();
    afficher();
    fermerDrawer();
    indexEdition = -1;
    toast("« " + nouvelle.nom + " » a été " + (modification ? "modifiée" : "ajoutée"));
});

document.getElementById("btn-ajouter").addEventListener("click", () => ouvrirDrawer());
document.getElementById("btn-fermer").addEventListener("click", fermerDrawer);
document.getElementById("btn-annuler").addEventListener("click", fermerDrawer);
fond.addEventListener("click", fermerDrawer);


// =====================================================
// 6. PREMIER AFFICHAGE (toujours à la fin du script)
// =====================================================
afficher();
</script>
</body>
</html>