async function test_auth(){
    resp = await fetch("api.php/test_auth");
    data = await resp.json();
    document.getElementById("user_status").innerText = data["msg"];
}

// fonction de test, innutile en prod
async function authenticate_user(){
    resp = await fetch("api.php/auth");
    data = await resp.json();
    console.log("test");
    if(data.status == 1){
        document.getElementById("user_status").innerText = data["msg"];
    }
}


async function unauthenticate_user(){
    resp = await fetch("api.php/unauth");
    data = await resp.json();
    if(data.status == 1){
        document.getElementById("user_status").innerText = data["msg"];
    }
}



async function rechercher(){
    var req = document.getElementById("recherche_input").value;
    var themes = [];
    Array.from(document.getElementsByClassName("theme")).forEach(function (el) {
        // on encode en  url pour pouvoir le passer dans la requete GET
        themes.push(encodeURIComponent(el.innerText));
    });
    var duree =document.getElementById("duree_input").value


    var url = "api.php/rechercher?req="+req;
    if(themes.toString() != ""){
        url = url +"&themes="+themes.toString();
    } 

    if(duree != ""){
        url = url +"duree="+duree;

    }
    console.log(url);

    resp = await fetch(url);
    
    data = await resp.json();

    // vide d'abord les éléments présents dans la liste sur la page
    document.getElementById("liste_resultats").innerHTML = "";
    
    if(data.status == 1){
        data.resultats.forEach(doc => {



            // on affiche le titre du résultat parce qu'on est pas des sauvages
            let titre_ensemble;
            titre_ensemble = document.createElement("h2");
            titre_ensemble.innerText = doc.titre;
            titre_ensemble.setAttribute("onclick","document.location.href='ens.php?ensemble_id="+doc.ensemble_id.toString()+"'");

            document.getElementById("liste_resultats").appendChild(titre_ensemble);
            

            // images ou pdf ?
            let ele;
            if(doc.upload_path.toString().split(".").pop() == "pdf"){
                ele = document.createElement("embed");


            }else{
                ele = document.createElement("img");
            }

            ele.src = doc.upload_path;
            ele.setAttribute("onclick","document.location.href='ens.php?ensemble_id="+doc.ensemble_id.toString()+"'");
            document.getElementById("liste_resultats").appendChild(ele);
            



        });
    }
}



async function gen_chronologie(){
    var url = "api.php/generer_chronologie";

    console.log(url);

    resp = await fetch(url);

    data = await resp.json();
    console.log(data);
    // vide d'abord les éléments présents dans la liste sur la page
    document.getElementById("liste_resultats").innerHTML = "";

    // ensuite on ajoute un petit titre à la chronologie
    let titre = document.createElement("h1");
    titre.innerText = "Documents récemment publiés";
    document.getElementById("liste_resultats").appendChild(titre);
    
    // et on remplis avec ce que l'api a généré
    if(data.status == 1){
        data.resultats.forEach(ens => {

            ens.documents.forEach(doc=>{
                // on affiche le titre du résultat parce qu'on est pas des sauvages
                let titre_ensemble;
                titre_ensemble = document.createElement("h2");
                titre_ensemble.innerText = doc.titre;
                titre_ensemble.setAttribute("onclick","document.location.href='ens.php?ensemble_id="+doc.ensemble_id.toString()+"'");
                document.getElementById("liste_resultats").appendChild(titre_ensemble);
                
                // fichiers spéciaux ?
                let apercu;
                let ext = doc.upload_path.toString().split(".").pop();
                switch(ext){
                    case "pdf":
                        ele = document.createElement("embed");
                        break;
                    case "html":
                        ele = document.createElement("iframe");
                        ele.setAttribute("sandbox","allow-forms allow-modals allow-scripts");
                        break;
                    default:
                        ele = document.createElement("img");
                        break;


                }

                ele.src = doc.upload_path;
                ele.setAttribute("onclick","document.location.href='ens.php?ensemble_id="+doc.ensemble_id.toString()+"'");
                document.getElementById("liste_resultats").appendChild(ele);

            });

            

        });
    }
}


document.addEventListener("DOMContentLoaded", (event)=>{
    gen_chronologie();

    test_auth();
    document.getElementById("recherche_input").onkeydown =function(event) {
        if (event.key === "Enter"){
            rechercher();
        }
    }
    document.getElementById("themes_input").onkeydown =function(event) {
        if (event.key === "Enter"){
            var theme = document.createElement("div");
            theme.setAttribute("class","theme");
            theme.innerText = document.getElementById("themes_input").value;
    
            document.getElementById("recherche_form").appendChild(theme);
            document.getElementById("themes_input").value = "";
        }
    }


    document.getElementById("btn-connection").addEventListener("click", (event) => {
        authenticate_user();
    });
    document.getElementById("btn-deconnection").addEventListener("click", (event) => {
        unauthenticate_user();
    });

});

