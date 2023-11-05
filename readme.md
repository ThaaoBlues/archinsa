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
| id           | INT    | AUTO_INCREMENT, PRIMARY KEY                |

### Table: exercices

| Column             | Type          | Constraints                              |
|--------------------|---------------|------------------------------------------|
| id                 | INT           | AUTO_INCREMENT, PRIMARY KEY              |
| commentaire_auteur | TEXT          |                                          |
| ensemble_id        | INT           | FOREIGN KEY (ensemble_id) REFERENCES ensembles(id) |
| duree              | INT           |                                          |
(la durée est en secondes)

### Table: ensembles

| Column             | Type          | Constraints                              |
|--------------------|---------------|------------------------------------------|
| id                 | INT           | AUTO_INCREMENT                           |
| commentaire_auteur | TEXT          |                                          |
| valide             | BOOLEAN       | NOT NULL                                 |
| corrige_inclu      | BOOLEAN       |                                          |

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

### téléverser.php :
- ajouter un element "commentaire_doc_< i >" pour chaque document

- ssi le type est "annale" ajouter un element "commentaire_exo_< i >" pour chaque exercice déclaré dans chaque document
- Ajouter de même un champ "themes" qui porterons sur les thèmes abordés par l'exercice, possibilité d'en inscrire autant que l'on veut
- ajouter un champ "duree" pour chaque exercice
- tout pack dans un json à l'envoi : 
``    
let ex = [{duree:"10",themes:["algèbre","analyse"],commentaire_exo:"cci est un commenataire"},{duree:"15",themes:["elec analogique"],commentaire_exo:""}]; 
; 
``
 

- ssi le type est "annale" Ajouter une checkbox pour spécifier si l'ensemble de documents comprend un corrigé ou non identifiant : "corrige_inclu"

- dans le cas d'une fiche de révisions, on ajouter seulement un champ "themes"




