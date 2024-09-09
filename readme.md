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
> choisir un insa à l'inscription
> rajouter automatiquement l'insa de celui qui dépose un truc dans la table des ensembles
> mettre un switch pour activer une recherche sur tout les insa


### téléverser.php :


- changer toutes les variables db avec $db_ devant
- rajouter des extensions en whitelist
- regex insa touloouse email inscription

- tout pack dans un json à l'envoi : 
``    
let ex = [{duree:"10",themes:["algèbre","analyse"],commentaire_exo:"cci est un commenataire"},{duree:"15",themes:["elec analogique"],commentaire_exo:""}]; 
; 
``
 

