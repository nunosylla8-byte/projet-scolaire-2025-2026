<?php
session_start();

/* =====================================================
   1. DONNÉES DE DÉPART (créées une seule fois dans la session)
   ===================================================== */

if (!isset($_SESSION["Filieres"])) {
    $_SESSION["Filieres"] = [
        ["code" => "INFO", "nom" => "Informatique",  "responsable" => "Dr. Moussa Diallo", "description" => "Formation en développement logiciel et réseaux informatiques"],
        ["code" => "GES",  "nom" => "Gestion",       "responsable" => "Dr. Fatou Seck",    "description" => "Formation en gestion et administration des entreprises"],
        ["code" => "MKT",  "nom" => "Marketing",     "responsable" => "M. Ibrahima Fall",  "description" => "Formation en marketing digital et communication commerciale"],
        ["code" => "COM",  "nom" => "Communication", "responsable" => "Mme. Aminata Ba",   "description" => "Formation en communication et relations publiques"],
    ];
}

if (!isset($_SESSION["Niveaux"])) {
    $_SESSION["Niveaux"] = [
        ["code" => "L1", "libelle" => "Licence 1", "bg_badge" => "bg-sky-100",     "text_badge" => "text-sky-600"],
        ["code" => "L2", "libelle" => "Licence 2", "bg_badge" => "bg-emerald-100", "text_badge" => "text-emerald-600"],
        ["code" => "L3", "libelle" => "Licence 3", "bg_badge" => "bg-amber-100",   "text_badge" => "text-amber-600"],
        ["code" => "M1", "libelle" => "Master 1",  "bg_badge" => "bg-purple-100",  "text_badge" => "text-purple-600"],
        ["code" => "M2", "libelle" => "Master 2",  "bg_badge" => "bg-rose-100",    "text_badge" => "text-rose-600"],
    ];
}

// filiere et niveau d'une classe = CODE de la filière / du niveau
if (!isset($_SESSION["Classes"])) {
    $_SESSION["Classes"] = [
        ["code" => "INFO-L1-A", "nom" => "INFO L1 Groupe A", "filiere" => "INFO", "niveau" => "L1", "capacite" => 35],
        ["code" => "INFO-L2-A", "nom" => "INFO L2 Groupe A", "filiere" => "INFO", "niveau" => "L2", "capacite" => 30],
        ["code" => "INFO-L3-A", "nom" => "INFO L3 Groupe A", "filiere" => "INFO", "niveau" => "L3", "capacite" => 25],
        ["code" => "INFO-M1-A", "nom" => "INFO M1 Groupe A", "filiere" => "INFO", "niveau" => "M1", "capacite" => 20],
        ["code" => "GES-L1-A",  "nom" => "GES L1 Groupe A",  "filiere" => "GES",  "niveau" => "L1", "capacite" => 40],
        ["code" => "GES-L2-A",  "nom" => "GES L2 Groupe A",  "filiere" => "GES",  "niveau" => "L2", "capacite" => 35],
        ["code" => "MKT-L1-A",  "nom" => "MKT L1 Groupe A",  "filiere" => "MKT",  "niveau" => "L1", "capacite" => 30],
    ];
}

// "classe" = CODE de la classe. La filière et le niveau se déduisent de la classe.
if (!isset($_SESSION["Etudiants"])) {
    $_SESSION["Etudiants"] = [
        ["matricule" => "221-2025-001", "nom" => "Diallo", "prenom" => "Mamadou",   "date_naissance" => "2005-03-14", "sexe" => "M", "telephone" => "77 123 45 67", "adresse" => "Mbour",       "classe" => "INFO-L1-A"],
        ["matricule" => "221-2025-002", "nom" => "Sow",    "prenom" => "Aissatou",  "date_naissance" => "2005-07-22", "sexe" => "F", "telephone" => "76 234 56 78", "adresse" => "Thiès",       "classe" => "INFO-L1-A"],
        ["matricule" => "221-2025-003", "nom" => "Fall",   "prenom" => "Omar",      "date_naissance" => "2004-11-05", "sexe" => "M", "telephone" => "78 345 67 89", "adresse" => "Dakar",       "classe" => "GES-L1-A"],
        ["matricule" => "221-2025-004", "nom" => "Ndiaye", "prenom" => "Fatima",    "date_naissance" => "2004-01-30", "sexe" => "F", "telephone" => "70 456 78 90", "adresse" => "Pikine",      "classe" => "INFO-L2-A"],
        ["matricule" => "221-2025-005", "nom" => "Ba",     "prenom" => "Ibrahima",  "date_naissance" => "2005-09-18", "sexe" => "M", "telephone" => "77 567 89 01", "adresse" => "Mbour",       "classe" => "MKT-L1-A"],
        ["matricule" => "221-2025-006", "nom" => "Diop",   "prenom" => "Mariama",   "date_naissance" => "2005-05-09", "sexe" => "F", "telephone" => "76 678 90 12", "adresse" => "Saint-Louis", "classe" => "GES-L1-A"],
        ["matricule" => "221-2025-007", "nom" => "Sarr",   "prenom" => "Modou",     "date_naissance" => "2002-12-02", "sexe" => "M", "telephone" => "78 789 01 23", "adresse" => "Thiès",       "classe" => "INFO-L3-A"],
        ["matricule" => "221-2025-008", "nom" => "Mbaye",  "prenom" => "Rokhaya",   "date_naissance" => "2003-04-25", "sexe" => "F", "telephone" => "70 890 12 34", "adresse" => "Dakar",       "classe" => "GES-L2-A"],
        ["matricule" => "221-2025-009", "nom" => "Thiam",  "prenom" => "Cheikh",    "date_naissance" => "2005-08-11", "sexe" => "M", "telephone" => "77 901 23 45", "adresse" => "Mbour",       "classe" => "INFO-L1-A"],
        ["matricule" => "221-2025-010", "nom" => "Gueye",  "prenom" => "Binta",     "date_naissance" => "2001-02-17", "sexe" => "F", "telephone" => "76 012 34 56", "adresse" => "Rufisque",    "classe" => "INFO-M1-A"],
        ["matricule" => "221-2025-011", "nom" => "Cisse",  "prenom" => "Abdoulaye", "date_naissance" => "2004-06-28", "sexe" => "M", "telephone" => "78 123 45 00", "adresse" => "Kaolack",     "classe" => "INFO-L2-A"],
        ["matricule" => "221-2025-012", "nom" => "Camara", "prenom" => "Kadiatou",  "date_naissance" => "2005-10-03", "sexe" => "F", "telephone" => "70 234 56 11", "adresse" => "Mbour",       "classe" => "MKT-L1-A"],
    ];
}


/* =====================================================
   2. FONCTIONS UTILES
   ===================================================== */

// Protège l'affichage contre le HTML (à utiliser pour TOUT ce qui vient d'un tableau)
function e($texte) {
    return htmlspecialchars((string)$texte, ENT_QUOTES, "UTF-8");
}

// Redirige vers une page puis arrête le script
function rediriger($page) {
    header("Location: " . $page);
    exit;
}

// Position d'un élément dans une liste (-1 si absent). Comparaison sans tenir compte de la casse.
function trouverIndex($liste, $cle, $valeur) {
    foreach ($liste as $i => $element) {
        if (strcasecmp((string)$element[$cle], (string)$valeur) === 0) {
            return $i;
        }
    }
    return -1;
}

// Retourne l'élément trouvé, ou null
function trouver($liste, $cle, $valeur) {
    $i = trouverIndex($liste, $cle, $valeur);
    return $i >= 0 ? $liste[$i] : null;
}


/* =====================================================
   3. MESSAGES FLASH (affichés dans le toast après une redirection)
   ===================================================== */

function flash($message, $type = "succes") {
    $_SESSION["flash"] = ["message" => $message, "type" => $type];
}

// Lit le message puis l'efface (il ne s'affiche qu'une fois)
function lireFlash() {
    $f = $_SESSION["flash"] ?? null;
    unset($_SESSION["flash"]);
    return $f;
}


/* =====================================================
   4. CALCULS (plus aucun chiffre écrit en dur)
   ===================================================== */

function nbEtudiantsDeClasse($codeClasse) {
    $n = 0;
    foreach ($_SESSION["Etudiants"] as $etudiant) {
        if (strcasecmp($etudiant["classe"], $codeClasse) === 0) {
            $n++;
        }
    }
    return $n;
}

function nbClassesDeNiveau($codeNiveau) {
    $n = 0;
    foreach ($_SESSION["Classes"] as $classe) {
        if (strcasecmp($classe["niveau"], $codeNiveau) === 0) {
            $n++;
        }
    }
    return $n;
}

function nbClassesDeFiliere($codeFiliere) {
    $n = 0;
    foreach ($_SESSION["Classes"] as $classe) {
        if (strcasecmp($classe["filiere"], $codeFiliere) === 0) {
            $n++;
        }
    }
    return $n;
}

// Classe, filière et niveau d'un étudiant (déduits de sa classe)
function infosEtudiant($etudiant) {
    $classe  = trouver($_SESSION["Classes"], "code", $etudiant["classe"]);
    $filiere = $classe ? trouver($_SESSION["Filieres"], "code", $classe["filiere"]) : null;
    $niveau  = $classe ? trouver($_SESSION["Niveaux"], "code", $classe["niveau"]) : null;

    return [
        "classe"  => $classe["nom"] ?? "—",
        "filiere" => $filiere["code"] ?? "—",
        "niveau"  => $niveau["code"] ?? "—",
    ];
}

function nbEtudiantsDeFiliere($codeFiliere) {
    $n = 0;
    foreach ($_SESSION["Etudiants"] as $etudiant) {
        if (strcasecmp(infosEtudiant($etudiant)["filiere"], $codeFiliere) === 0) {
            $n++;
        }
    }
    return $n;
}

function nbEtudiantsDeNiveau($codeNiveau) {
    $n = 0;
    foreach ($_SESSION["Etudiants"] as $etudiant) {
        if (strcasecmp(infosEtudiant($etudiant)["niveau"], $codeNiveau) === 0) {
            $n++;
        }
    }
    return $n;
}

function nbParSexe($sexe) {
    $n = 0;
    foreach ($_SESSION["Etudiants"] as $etudiant) {
        if ($etudiant["sexe"] === $sexe) {
            $n++;
        }
    }
    return $n;
}
