<?php
// Page utilitaire : efface toutes les données de la session et recharge les données de départ.
// Utile pendant le développement (ouvrir http://localhost/.../reinitialiser.php).
session_start();
session_unset();
session_destroy();
header("Location: Tableaudebord.php");
exit;