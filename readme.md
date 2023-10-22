# Arch'insa
Ce site a pour but à therme de remplacer le site actuel des annales de L'INSA Toulouse, avec une recherche par themes/classes/temps de résolution, la possibilité de prendre directement des photos de son exercice pour le téléverser et d'envoyer toutes sortes de supports tels que des fiches de cours. Des commentaires seront aussi disponibles pour les auteurs pour donner un contexte ou des indications sur un exercice en particulier, ou un paquet de documents en entier.
D'autres fonctionnalités seront ajoutées petit à petit. (si vous avez des suggestions, n'hésitez pas à contacter le club info ou moi directement)


## structure bdd
### Table: themes

| Column | Type            | Constraints              |
|--------|-----------------|--------------------------|
| id     | INT             | AUTO_INCREMENT, PRIMARY KEY |
| name   | VARCHAR(255)    | NOT NULL                 |

### Table: exercices_themes

| Column       | Type   | Constraints                               |
|--------------|--------|-------------------------------------------|
| exercice_id  | INT    | FOREIGN KEY (exercice_id) REFERENCES exercises(id) |
| theme_id     | INT    | FOREIGN KEY (theme_id) REFERENCES themes(id) |
| PRIMARY KEY  |        | (exercice_id, theme_id)                   |

### Table: exercices

| Column             | Type          | Constraints                              |
|--------------------|---------------|------------------------------------------|
| id                 | INT           | AUTO_INCREMENT, PRIMARY KEY              |
| titre              | VARCHAR(255)  | NOT NULL                                 |
| commentaire_auteur | TEXT          |                                          |
| document_id        | INT           | FOREIGN KEY (document_id) REFERENCES documents(id) |

### Table: ensemble

| Column             | Type          | Constraints                              |
|--------------------|---------------|------------------------------------------|
| id                 | INT           | AUTO_INCREMENT                           |
| commentaire_auteur | TEXT          |                                          |

### Table: documents

| Column             | Type          | Constraints                              |
|--------------------|---------------|------------------------------------------|
| id                 | INT           | AUTO_INCREMENT, PRIMARY KEY              |
| titre              | VARCHAR(255)  | NOT NULL                                 |
| type               | INT           |                                          |
| upload_path        | TEXT          | NOT NULL                                 |
| commentaire_auteur | TEXT          |                                          |
| ensemble_id        | INT           | FOREIGN KEY (ensemble_id) REFERENCES ensemble(id) |
| theme_id           | INT           | FOREIGN KEY (theme_id) REFERENCES themes(id) |
