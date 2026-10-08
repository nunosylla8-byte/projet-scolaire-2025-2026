<?php
session_start();

 $Etudiants = [
        ["matricule" => "221-2025-001", "nom" => "Diallo", "prenom" => "Mamadou", "sexe" => "M", "classe" => "INFO L1 Groupe A", "filiere" => "INFO", "niveau" => "L1"],
        ["matricule" => "221-2025-002", "nom" => "Sow", "prenom" => "Aissatou", "sexe" => "F", "classe" => "INFO L1 Groupe A", "filiere" => "INFO", "niveau" => "L1"],
        ["matricule" => "221-2025-003", "nom" => "Fall", "prenom" => "Omar", "sexe" => "M", "classe" => "GES L1 Groupe A", "filiere" => "GES", "niveau" => "L1"],
        ["matricule" => "221-2025-004", "nom" => "Ndiaye", "prenom" => "Fatima", "sexe" => "F", "classe" => "INFO L2 Groupe A", "filiere" => "INFO", "niveau" => "L2"],
        ["matricule" => "221-2025-005", "nom" => "Ba", "prenom" => "Ibrahima", "sexe" => "M", "classe" => "MKT L1 Groupe A", "filiere" => "MKT", "niveau" => "L1"],
        ["matricule" => "221-2025-006", "nom" => "Diop", "prenom" => "Mariama", "sexe" => "F", "classe" => "GES L1 Groupe A", "filiere" => "GES", "niveau" => "L1"],
        ["matricule" => "221-2025-007", "nom" => "Sarr", "prenom" => "Modou", "sexe" => "M", "classe" => "INFO L3 Groupe A", "filiere" => "INFO", "niveau" => "L3"],
        ["matricule" => "221-2025-008", "nom" => "Mbaye", "prenom" => "Rokhaya", "sexe" => "F", "classe" => "GES L2 Groupe A", "filiere" => "GES", "niveau" => "L2"],
        ["matricule" => "221-2025-009", "nom" => "Thiam", "prenom" => "Cheikh", "sexe" => "M", "classe" => "INFO L1 Groupe A", "filiere" => "INFO", "niveau" => "L1"],
        ["matricule" => "221-2025-010", "nom" => "Gueye", "prenom" => "Binta", "sexe" => "F", "classe" => "INFO M1 Groupe A", "filiere" => "INFO", "niveau" => "M1"],
        ["matricule" => "221-2025-011", "nom" => "Cisse", "prenom" => "Abdoulaye", "sexe" => "M", "classe" => "INFO L2 Groupe A", "filiere" => "INFO", "niveau" => "L2"],
        ["matricule" => "221-2025-012", "nom" => "Camara", "prenom" => "Kadiatou", "sexe" => "F", "classe" => "MKT L1 Groupe A", "filiere" => "MKT", "niveau" => "L1"],
    ];

$Classes = [
        ["nom" => "INFO L1 Groupe A","code" => "INFO-L1-A","niveau" => "L1","filiere" => "Informatique","capacite" => 35,"etudiants_count" => 3,"pourcentage" => 9],
        ["nom" => "INFO L2 Groupe A","code" => "INFO-L2-A","niveau" => "L2","filiere" => "Informatique","capacite" => 30,"etudiants_count" => 2,"pourcentage" => 7],
        ["nom" => "INFO L3 Groupe A","code" => "INFO-L3-A","niveau" => "L3","filiere" => "Informatique","capacite" => 25,"etudiants_count" => 1,"pourcentage" => 4],
        ["nom" => "GES L1 Groupe A","code" => "GES-L1-A","niveau" => "L1","filiere" => "Gestion","capacite" => 40,"etudiants_count" => 2,"pourcentage" => 5],
        ["nom" => "GES L2 Groupe A","code" => "GES-L2-A","niveau" => "L2","filiere" => "Gestion","capacite" => 35,"etudiants_count" => 1,"pourcentage" => 3],
        ["nom" => "MKT L1 Groupe A","code" => "MKT-L1-A","niveau" => "L1","filiere" => "Marketing","capacite" => 30,"etudiants_count" => 2,"pourcentage" => 7],
        ["nom" => "INFO M1 Groupe A","code" => "INFO-M1-A","niveau" => "M1","filiere" => "Informatique","capacite" => 20,"etudiants_count" => 1,"pourcentage" => 5]
    ];


 $Niveaux = [
        ["code" => "L1","nom" => "Licence 1","classes_count" => 3,"bg_badge" => "bg-sky-100","text_badge" => "text-sky-600"],
        ["code" => "L2","nom" => "Licence 2","classes_count" => 2,"bg_badge" => "bg-emerald-100","text_badge" => "text-emerald-600"],
        ["code" => "L3","nom" => "Licence 3","classes_count" => 1,"bg_badge" => "bg-amber-100","text_badge" => "text-amber-600"],
        ["code" => "M1","nom" => "Master 1","classes_count" => 1,"bg_badge" => "bg-purple-100","text_badge" => "text-purple-600"],
        ["code" => "M2","nom" => "Master 2","classes_count" => 0,"bg_badge" => "bg-rose-100","text_badge" => "text-rose-600"]
    ];
 

     $Filieres= [
        ["code" => "INFO","nom" => "Informatique","responsable" => "Dr. Moussa Diallo","description" => "Formation en développement logiciel et réseaux informatiques","etudiants_count" => 7],
        ["code" => "GES","nom" => "Gestion","responsable" => "Dr. Fatou Seck","description" => "Formation en gestion et administration des entreprises","etudiants_count" => 3],
        ["code" => "MKT","nom" => "Marketing","responsable" => "M. Ibrahima Fall","description" => "Formation en marketing digital et communication commerciale","etudiants_count" => 2],
        ["code" => "COM","nom" => "Communication","responsable" => "Mme. Aminata Ba","description" => "Formation en communication et relations publiques","etudiants_count" => 0]
    ];


if (!isset($_SESSION["Etudiants"])) {
    $_SESSION["Etudiants"]=$Etudiants;
}

if (!isset($_SESSION["Classes"])) {
    $_SESSION["Classes"]=$Classes;
}

if (!isset($_SESSION["Filieres"])) {
    $_SESSION["Filieres"]=$Filieres;
}

if (!isset($_SESSION["Niveaux"])) {
    $_SESSION["Niveaux"]=$Niveaux;
}
?>