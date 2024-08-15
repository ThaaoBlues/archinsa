<?php

function assainir_et_valider_mel($og_mel): string {
    // Supprime les espaces en début et fin de chaîne
    $mel = trim($og_mel);

    // Assainit l'adresse e-mail en supprimant les caractères spéciaux
    $mel = filter_var($mel, FILTER_SANITIZE_EMAIL);

    // Vérifie si l'adresse e-mail est valide
    if (filter_var($mel, FILTER_VALIDATE_EMAIL)) {
        return $mel; // Si valide, renvoie l'adresse e-mail assainie
    } else {
        return "[ERREUR_MEL_MALSAINT]"; // Sinon, renvoie un message d'erreur
    }
}

?>