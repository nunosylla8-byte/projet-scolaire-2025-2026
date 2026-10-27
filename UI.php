<?php
// =====================================================
// ui.php : TOAST + MODAL + DRAWER communs à toutes les pages
// À inclure À LA FIN du <body>, APRÈS le <aside id="drawer"> de la page.
//
// Variables que la page peut définir avant l'include :
//   $flash    : message à afficher dans le toast  (lireFlash())
//   $reouvrir : ["donnees" => [...], "modif" => true/false]
//               pour rouvrir le drawer quand le formulaire contient des erreurs
// =====================================================
$flash    = $flash    ?? null;
$reouvrir = $reouvrir ?? null;

$jsonFlags = JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;
?>

<!-- Fond sombre derrière le drawer -->
<div id="fond" class="fixed inset-0 bg-black/50 z-50 opacity-0 pointer-events-none transition-opacity duration-300"></div>

<!-- Modal de confirmation (suppression) -->
<div id="modal" class="fixed inset-0 bg-black/50 z-[90] hidden items-center justify-center">
    <div class="bg-white rounded-2xl shadow-2xl w-[90%] max-w-sm p-6">
        <div class="flex items-start gap-3 mb-5">
            <i class="fi fi-rr-triangle-warning text-red-600 text-xl mt-0.5"></i>
            <p id="texte-confirm" class="font-bold text-slate-800"></p>
        </div>
        <div class="flex justify-end gap-2">
            <button id="btn-non" type="button" class="px-4 py-2 rounded-lg bg-slate-100 font-bold text-sm text-slate-600 hover:bg-slate-200">Annuler</button>
            <button id="btn-oui" type="button" class="px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white font-bold text-sm">Confirmer</button>
        </div>
    </div>
</div>

<!-- Toast (message en bas à droite) -->
<div id="toast" class="fixed bottom-6 right-6 translate-y-4 opacity-0 pointer-events-none transition-all duration-300 z-[80] px-5 py-3 rounded-lg font-bold text-sm text-white flex items-center gap-3 shadow-lg"></div>

<script>
// Ce script gère uniquement l'AFFICHAGE.
// Les données sont enregistrées par PHP (formulaires POST + $_SESSION).

const fond    = document.getElementById("fond");
const modal   = document.getElementById("modal");
const toastEl = document.getElementById("toast");
const drawer  = document.getElementById("drawer");
const form    = document.getElementById("formulaire");

// ---------- TOAST ----------
let minuteurToast;

function toast(message, erreur = false) {
    toastEl.innerHTML = "";

    const icone = document.createElement("i");
    icone.className = erreur ? "fi fi-rr-exclamation" : "fi fi-rr-check";

    const texte = document.createElement("span");
    texte.textContent = message;

    toastEl.append(icone, texte);
    toastEl.classList.toggle("bg-red-600", erreur);
    toastEl.classList.toggle("bg-emerald-600", !erreur);
    toastEl.classList.remove("opacity-0", "translate-y-4", "pointer-events-none");

    clearTimeout(minuteurToast);
    minuteurToast = setTimeout(() => {
        toastEl.classList.add("opacity-0", "translate-y-4", "pointer-events-none");
    }, 3000);
}

// ---------- MODAL DE CONFIRMATION ----------
// Tout formulaire avec la classe "form-suppr" demande confirmation avant d'être envoyé.
let formAConfirmer = null;

function fermerConfirm() {
    modal.classList.add("hidden");
    modal.classList.remove("flex");
    formAConfirmer = null;
}

document.addEventListener("submit", ev => {
    const f = ev.target;
    if (!f.classList.contains("form-suppr")) return;

    ev.preventDefault();                       // on attend la confirmation
    formAConfirmer = f;
    document.getElementById("texte-confirm").textContent = f.dataset.message || "Confirmer la suppression ?";
    modal.classList.remove("hidden");
    modal.classList.add("flex");
});

document.getElementById("btn-oui").addEventListener("click", () => {
    if (formAConfirmer) formAConfirmer.submit();   // envoi POST classique vers PHP
});
document.getElementById("btn-non").addEventListener("click", fermerConfirm);
modal.addEventListener("click", ev => { if (ev.target === modal) fermerConfirm(); });

// ---------- DRAWER (ajout / modification) ----------
// donnees       : valeurs à mettre dans les champs (null = formulaire vide)
// modif         : true = modification, false = ajout
// garderErreurs : true = on garde les messages d'erreur écrits par PHP
function ouvrirDrawer(donnees = null, modif = false, garderErreurs = false) {
    form.reset();

    if (!garderErreurs) {
        form.querySelectorAll(".erreur").forEach(p => p.classList.add("hidden"));
    }

    // Remplit les champs dont le nom correspond à une clé des données
    if (donnees) {
        Object.entries(donnees).forEach(([nom, valeur]) => {
            const champ = form.elements[nom];
            if (champ) champ.value = valeur ?? "";
        });
    }

    // Champs cachés lus par PHP : quelle opération, et quel élément modifier
    form.elements["operation"].value = modif ? "modifier" : "ajouter";
    const cle = drawer.dataset.cle;   // ex : "code" ou "matricule"
    form.elements["id_origine"].value = modif
        ? ((donnees && (donnees.id_origine ?? donnees[cle])) ?? "")
        : "";

    document.getElementById("titre-drawer").textContent = modif ? drawer.dataset.titreModif : drawer.dataset.titreAjout;
    document.getElementById("btn-valider").textContent  = modif ? "ENREGISTRER" : "AJOUTER";

    fond.classList.remove("opacity-0", "pointer-events-none");
    drawer.classList.remove("translate-x-full");
}

function fermerDrawer() {
    fond.classList.add("opacity-0", "pointer-events-none");
    drawer.classList.add("translate-x-full");
}

// Boutons de la page : [data-ouvrir-drawer] = Nouveau, .btn-modif = crayon, [data-fermer-drawer] = croix / Annuler
document.addEventListener("click", ev => {
    if (ev.target.closest("[data-ouvrir-drawer]")) { ouvrirDrawer(); return; }

    const m = ev.target.closest(".btn-modif");
    if (m) { ouvrirDrawer(JSON.parse(m.dataset.item), true); return; }

    if (ev.target.closest("[data-fermer-drawer]")) fermerDrawer();
});

fond.addEventListener("click", fermerDrawer);
document.addEventListener("keydown", ev => {
    if (ev.key === "Escape") { fermerConfirm(); fermerDrawer(); }
});

// ---------- AU CHARGEMENT DE LA PAGE ----------
<?php if ($flash): ?>
toast(<?= json_encode($flash["message"], $jsonFlags) ?>, <?= $flash["type"] === "erreur" ? "true" : "false" ?>);
<?php endif; ?>

<?php if ($reouvrir): ?>
ouvrirDrawer(<?= json_encode($reouvrir["donnees"], $jsonFlags) ?>, <?= $reouvrir["modif"] ? "true" : "false" ?>, true);
<?php endif; ?>
</script>
