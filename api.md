# API PHP

Ce document décrit le comportement de l'api utilisée par le site

## Endpoints

### Authentification

- **Endpoint:** `auth.php?auth`
- **Description:** Authentifie l'utilisateur et initialise une session.
- **Méthode HTTP:** GET
- **Réponse JSON:**
  ```json
  {
    "status": 1,
    "msg": "Authentification réussie."
  }
  ```
  En cas d'erreur :
  ```json
  {
    "status": 0,
    "msg": "Erreur pendant le traitement de la requête."
  }

### Déconnexion

- **Endpoint:** `auth.php?unauth`
- **Description:** Déconnecte l'utilisateur en mettant fin à la session.
- **Méthode HTTP:** GET
- **Réponse JSON:**
  ```json
  {
    "status": 1,
    "msg": "Déconnexion réussie."
  }
  ```

### Test d'authentification

- **Endpoint:** `auth.php?test_auth`
- **Description:** Vérifie si l'utilisateur est authentifié.
- **Méthode HTTP:** GET
- **Réponse JSON:**
  - Si l'utilisateur est authentifié :
    ```json
    {
      "status": 1,
      "msg": "Utilisateur bien authentifié."
    }
    ```
  - Si l'utilisateur n'est pas authentifié :
    ```json
    {
      "status": 4,
      "msg": "Utilisateur non authentifié."
    }
    ```

## Statuts de réponse

- **Status 1 :** Requête valide.
- **Status 0 :** Erreur pendant le traitement de la requête.
- **Status 2 :** Requête invalide.
- **Status 3 :** Session expirée.
- **Status 4 :** Utilisateur non authentifié, requête interdite.

## Gestion des sessions

Le fichier `session_verif.php` est inclus pour la gestion des sessions. Assurez-vous qu'il est présent et correctement configuré.

---

**Remarque :** Ce document est une documentation basique. Assurez-vous d'ajuster et d'améliorer la sécurité en fonction des besoins spécifiques de votre application.


## upload de plusieurs fichiers : 

```javascript
async function uploadMultiple(donneesFormulaires) {
  try {
    const reponse = await fetch("https://example.com/api", {
      method: "POST",
      body: donneesFormulaires,
    });
    const resultat = await reponse.json();
    console.log("Réussite :", resultat);
  } catch (erreur) {
    console.error("Erreur :", erreur);
  }
}

const docs = document.querySelector('input[type="file"][multiple]');
const donneesFormulaires = new FormData();

donneesFormulaires.append("title", "documents");

for (const [i, doc] of Array.from(docs.files).entries()) {
  donneesFormulaires.append(`doc_${i}`, doc);
}

uploadMultiple(donneesFormulaires);
```

## upload de données json
```javascript

async function postJSON(donnees) {
  try {
    const reponse = await fetch("https://example.com/profile", {
      method: "POST", // ou 'PUT'
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(donnees),
    });

    const resultat = await reponse.json();
    console.log("Réussite :", resultat);
  } catch (erreur) {
    console.error("Erreur :", erreur);
  }
}

const donnees = { login: "Jean Biche" };
postJSON(donnees);

```

## récupérer des documents

``` javascript

async function fetchImage() {
  try {
    const response = await fetch("flowers.jpg");
    if (!response.ok) {
      throw new Error("La réponse n'est pas OK");
    }
    const myBlob = await response.blob();
    monImage.src = URL.createObjectURL(myBlob);
  } catch (error) {
    console.error("Un problème est survenu lors de la récupération :", error);
  }
}


```

[source](https://developer.mozilla.org/fr/docs/Web/API/Fetch_API/Using_Fetch)


## récupérer des données
``` javascript


async function test_auth(){
  resp = await fetch("/annales/api.php?test_auth");
  data = await resp.json();
  document.getElementById("user_status").innerText = data["msg"];
}

  async function unauthenticate_user(){
      resp = await fetch("/annales/api.php?unauth");
      data = await resp.json();
      if(data.status == 1){
          document.getElementById("user_status").innerText = data["msg"];
      }
  }

```