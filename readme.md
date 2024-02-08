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
| ensemble_id  | INT    | FOREIGN KEY (ensemble_id) REFERENCES ensembles(id) |
| theme_id     | INT    | FOREIGN KEY (theme_id) REFERENCES themes(id) |
| id           | INT    | AUTO_INCREMENT, PRIMARY KEY                |

### Table: exercices

| Column             | Type          | Constraints                              |
|--------------------|---------------|------------------------------------------|
| id                 | INT           | AUTO_INCREMENT, PRIMARY KEY              |
| commentaire_auteur | TEXT          |                                          |
| ensemble_id        | INT           | FOREIGN KEY (ensemble_id) REFERENCES ensembles(id) |
| document_id        | INT           | FOREIGN KEY (document_id) REFERENCES documents(id)
| duree              | INT           |                                          |
(la durée est en minutes)

### Table: ensembles

| Column             | Type          | Constraints                              |
|--------------------|---------------|------------------------------------------|
| id                 | INT           | AUTO_INCREMENT                           |
| commentaire_auteur | TEXT          |                                          |
| valide             | BOOLEAN       | NOT NULL                                 |
| corrige_inclu      | BOOLEAN       | DEFAULT NULL                             |
| date_televersement | DATE          | DEFAULT CURRENT_TIMESTAMP                |
| date_conception    | VARCHAR(9)    |                                          |


> le champ "corrige_inclu" ne sera utilisé que pour des annales

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


## TOUDOU : 
> Tester le code qui a été séparé en plusieurs fichiers différents (les pages pour utilisateurs)


### téléverser.php :

- tout pack dans un json à l'envoi : 
``    
let ex = [{duree:"10",themes:["algèbre","analyse"],commentaire_exo:"cci est un commenataire"},{duree:"15",themes:["elec analogique"],commentaire_exo:""}]; 
; 
``
 
### _partials/_head.php
- définir la variable $titre_page avant de l'inclure
- va s'occuper de generer tout ce qu'on met dans les tags <head> ainsi que d'importer un fichier css du même nom que la page depuis css/<page>.css (s'il existe)
### _partials/_footer.php
- tout ce qu'on veut faire en fin de chargement de page
- va inclure un script depuis js/<page>.js (s'il existe).

