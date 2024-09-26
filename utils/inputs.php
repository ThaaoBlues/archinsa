<?php

function assainir_et_valider_mel($og_mel): string {
    // Supprime les espaces en début et fin de chaîne
    $mel = trim($og_mel);

    // Assainit l'adresse e-mail en supprimant les caractères spéciaux
    $mel = filter_var($mel, FILTER_SANITIZE_EMAIL);

    // Vérifie si l'adresse e-mail est valide
    $reg_pattern = "/^[a-zA-Z0-9._%+-]+@insa-toulouse\.fr$/";
    if (filter_var($mel, FILTER_VALIDATE_EMAIL) && preg_match($mel,$reg_pattern)) {
        return $mel; // Si valide, renvoie l'adresse e-mail assainie
    } else {
        return "[ERREUR_MEL_MALSAINT]"; // Sinon, renvoie un message d'erreur
    }
}

function getFileSignature($filePath, $length = 8) {
    // Open the file and read the first few bytes (file signature)
    if ($file = fopen($filePath, 'rb')) {
        $signature = fread($file, $length);
        fclose($file);
        return bin2hex($signature); // Return as hexadecimal string
    }
    return false;
}

function checkFileTypeSecure($filePath) {
    if (!file_exists($filePath)) {
        return -1; // File does not exist
    }

    // Get the file's signature (magic bytes)
    $fileSignature = getFileSignature($filePath);

    // Check for common signatures
    $signatures = [
        'text' => [
            'txt' => 'efbbbf', // UTF-8 encoded text files (BOM)
        ],
        'pdf' => [
            'pdf' => '25504446', // PDF files always start with "%PDF" in hex
        ],
        'image' => [
            'jpeg0' => 'ffd8ffe0', // JPEG
            'jpeg1' => 'ffd8ffe1', // JPEG but different you know they like to stand out (exif)
            'jpeg2' => 'ffd8ffe2', // NO SHIT ??? (jfif or spiff)
            'png'  => '89504e47', // PNG
            'gif'  => '47494638', // GIF
            'bmp'  => '424d',     // BMP
            'webp' => '52494646', // WebP starts with "RIFF"
            'tiff' => '49492a00'  // TIFF
         ],
        'video' => [
            'mp4'  => '00000018', // MP4
            //'avi'  => '52494646', // AVI starts with "RIFF" bah relou du coup c'est pareil que webp
            'mkv'  => '1a45dfa3', // MKV
            'mov'  => '00000014'  // MOV
        ],
        'html' => [
            'html' => '3c68746d', // HTML documents start with "<html"
        ]
    ];

    // Check against known file signatures

    // Check for plain text
    foreach ($signatures['text'] as $format => $signature) {
        if (strpos($fileSignature, $signature) === 0) {
            return 1; // Plain text file
        }
    }

    // Check for PDF
    foreach ($signatures['pdf'] as $format => $signature) {
        if (strpos($fileSignature, $signature) === 0) {
            return 3; // PDF file
        }
    }

    // Check for images
    foreach ($signatures['image'] as $format => $signature) {
        if (strpos($fileSignature, $signature) === 0) {
            return 2; // Image file
        }
    }

    // Check for videos
    foreach ($signatures['video'] as $format => $signature) {
        if (strpos($fileSignature, $signature) === 0) {
            return 4; // Video file
        }
    }

    // Check for HTML documents
    foreach ($signatures['html'] as $format => $signature) {
        if (strpos($fileSignature, $signature) === 0) {
            return 5; // HTML file
        }
    }

    return 0; // Unknown or unsupported file type
}


?>