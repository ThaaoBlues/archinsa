<?php
// Database connection parameters
include("test_creds.php");

// Create connection
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create tables
$sql = "
    CREATE TABLE IF NOT EXISTS token(
        id INT AUTO_INCREMENT PRIMARY KEY,
        id_user INTEGER,
        TOKEN VARCHAR(255),
        create_time DATETIME DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        nom_insa VARCHAR(25) NOT NULL,
        admin BOOLEAN DEFAULT 0,
        verifie BOOLEAN DEFAULT 0
    );

    CREATE TABLE IF NOT EXISTS themes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL
    );

    CREATE TABLE IF NOT EXISTS ensembles (
        id INT AUTO_INCREMENT PRIMARY KEY,
        commentaire_auteur TEXT,
        valide BOOLEAN NOT NULL DEFAULT FALSE,
        corrige_inclu BOOLEAN NOT NULL DEFAULT FALSE,
        date_televersement DATETIME DEFAULT CURRENT_TIMESTAMP,
        date_conception VARCHAR(10),
        id_auteur INT,
        FOREIGN KEY (id_auteur) REFERENCES users(id)
    );

    CREATE TABLE IF NOT EXISTS documents (
        id INT AUTO_INCREMENT PRIMARY KEY,
        titre VARCHAR(255) NOT NULL,
        type INT,
        upload_path TEXT NOT NULL,
        commentaire_auteur TEXT,
        ensemble_id INT,
        theme_id INT,
        FOREIGN KEY (theme_id) REFERENCES themes(id),
        FOREIGN KEY (ensemble_id) REFERENCES ensembles(id)
    );

    CREATE TABLE IF NOT EXISTS exercices (
        id INT AUTO_INCREMENT PRIMARY KEY,
        commentaire_auteur TEXT,
        ensemble_id INT,
        document_id INT,
        duree INT,
        FOREIGN KEY (ensemble_id) REFERENCES ensembles(id),
        FOREIGN KEY (document_id) REFERENCES documents(id)

    );


    CREATE TABLE IF NOT EXISTS exercices_themes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        exercice_id INT,
        ensemble_id INT,
        theme_id INT,
        FOREIGN KEY (exercice_id) REFERENCES exercices(id),
        FOREIGN KEY (ensemble_id) REFERENCES ensembles(id),
        FOREIGN KEY (theme_id) REFERENCES themes(id)
    );

";

if ($conn->multi_query($sql) === TRUE) {
    echo "Tables created successfully";
} else {
    echo "Error creating tables: " . $conn->error;
}

// Close the connection
$conn->close();
?>