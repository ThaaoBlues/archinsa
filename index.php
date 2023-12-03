
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<?php
    session_start();

?>
    <a href="javascript:authenticate_user();">connection</a>
    <a href="javascript:unauthenticate_user();">déconnection</a>

    <div id="user_status">

    </div>

    <form>
        <input type="text" id="recherche_input" placeholder="Rechercher une fiche, annale ...">
        <input type="text" id="themes_input" placeholder="themes séparés par une virgule">
        <input type="number" id="duree_input" placeholder="durée en minutes">
    </form>

    <a href="televerser.php">Téléverser des documents</a>
</body>
<script>
    async function test_auth(){
        resp = await fetch("/annales/api.php/test_auth");
        data = await resp.json();
        document.getElementById("user_status").innerText = data["msg"];
    }

    // fonction de test, innutile en prod
    async function authenticate_user(){
        resp = await fetch("/annales/api.php/auth");
        data = await resp.json();
        console.log("test");
        if(data.status == 1){
            alert(1);
            document.getElementById("user_status").innerText = data["msg"];
        }
    }

    
    async function unauthenticate_user(){
        resp = await fetch("/annales/api.php/unauth");
        data = await resp.json();
        if(data.status == 1){
            document.getElementById("user_status").innerText = data["msg"];
        }
    }



    async function rechercher(){
        var req = document.getElementById("recherche_input").value;
  

        resp = await fetch("/annales/api.php/rechercher?req="+req);
        
        data = await resp.json();
        if(data.status == 1){
            data.resultats.forEach(doc => {
                const img = document.createElement("img");
                img.src = doc.upload_path;
                document.body.appendChild(img);
            });
        }
    }


    test_auth();
    document.getElementById("recherche_input").onkeydown =function(event) {
        if (event.key === "Enter"){
            rechercher();
        }
    }


    

</script>
</html>