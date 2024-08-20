
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
        url = url +"&duree="+duree;

    }
    console.log(url);


    var tout_les_insa_switch = document.getElementById("tout_les_insa_switch").checked;
    if(tout_les_insa_switch){
        url = url+"&tout_les_insa=1"
    }

    resp = await fetch(url);
    
    data = await resp.json();

    console.log(data);

    // vide d'abord les éléments présents dans la liste sur la page
    document.getElementById("liste_resultats").innerHTML = "";

    // ensuite on ajoute un petit titre à la chronologie
    let titre = document.createElement("h1");
    titre.innerText = "Voilà les "+data.resultats.length+" résultats de ta recherche :";
    document.getElementById("liste_resultats").appendChild(titre);
    
    if(data.status == 1){
        data.resultats.forEach(doc => {


            const card = document.createElement('div');
            card.classList.add('card');                

            // on affiche le titre du résultat parce qu'on est pas des sauvages
            let titre_ensemble;
            titre_ensemble = document.createElement("h2");
            titre_ensemble.innerText = doc.titre;
            titre_ensemble.setAttribute("onclick","document.location.href='ens.php?ensemble_id="+doc.ensemble_id.toString()+"'");
            
            card.appendChild(titre_ensemble);
            
            // fichiers spéciaux ?
            let ext = doc.upload_path.toString().split(".").pop();
            const image_extensions = [
                'jpg', 
                'jpeg',
                'png',
                'gif',
                'bmp',
                'tiff', 
                'tif',
                'webp',
                'svg',
                'ico',
                'raw'
            ];

            switch (true) {
                case image_extensions.includes(ext): // image
                    const img = document.createElement('img');
                    img.src = doc.upload_path;
                    img.alt = doc.titre;
                    card.appendChild(img);

                    const imageLink = document.createElement('a');
                    imageLink.href = doc.upload_path;
                    imageLink.classList.add('lien');
                    imageLink.textContent = 'Voir image';
                    imageLink.target = '_blank';
                    card.appendChild(imageLink);
                    break;
                case ext == "pdf": // pdf
                    const embed = document.createElement('embed');
                    embed.src = doc.upload_path;
                    card.appendChild(embed);

                    const pdfLink = document.createElement('a');
                    pdfLink.href = doc.upload_path;
                    pdfLink.classList.add('lien');
                    pdfLink.textContent = 'Voir PDF en grand';
                    pdfLink.target = '_blank';
                    card.appendChild(pdfLink);
                    break;
                case ext == "mp4": // video
                    const video = document.createElement('video');
                    video.src = doc.upload_path;
                    video.controls = true;
                    card.appendChild(video);
                    break;
                case ext == "html":
                    const iframe = document.createElement('iframe');
                    iframe.src = doc.upload_path;
                    card.appendChild(iframe);
                    break;
                default:
                    const unsupportedLink = document.createElement('a');
                    unsupportedLink.href = doc.upload_path;
                    unsupportedLink.classList.add('lien');
                    unsupportedLink.textContent = 'Type de fichier non supporté.';
                    unsupportedLink.target = '_blank';
                    card.appendChild(unsupportedLink);
                    break;
            }

            
            const ele = document.createElement("a");
            ele.innerText = "Voir ce que c'est";
            ele.href = `ens.php?ensemble_id=${doc.ensemble_id}`;
            ele.classList.add("lien")
            card.appendChild(ele)

            document.getElementById("liste_resultats").appendChild(card);



        });
    }
}



async function gen_chronologie(){
    var url = "api.php/generer_chronologie";

    console.log(url);

    resp = await fetch(url);

    data = await resp.json();
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

                const card = document.createElement('div');
                card.classList.add('card');                

                // on affiche le titre du résultat parce qu'on est pas des sauvages
                let titre_ensemble;
                titre_ensemble = document.createElement("h2");
                titre_ensemble.innerText = doc.titre;
                titre_ensemble.setAttribute("onclick","document.location.href='ens.php?ensemble_id="+doc.ensemble_id.toString()+"'");
                
                card.appendChild(titre_ensemble);
                
                // fichiers spéciaux ?
                let ext = doc.upload_path.toString().split(".").pop();
                const image_extensions = [
                    'jpg', 
                    'jpeg',
                    'png',
                    'gif',
                    'bmp',
                    'tiff', 
                    'tif',
                    'webp',
                    'svg',
                    'ico',
                    'raw'
                ];

                switch (true) {
                    case image_extensions.includes(ext): // image
                        const img = document.createElement('img');
                        img.src = doc.upload_path;
                        img.alt = doc.titre;
                        card.appendChild(img);
  
                        const imageLink = document.createElement('a');
                        imageLink.href = doc.upload_path;
                        imageLink.classList.add('lien');
                        imageLink.textContent = 'Voir image';
                        imageLink.target = '_blank';
                        card.appendChild(imageLink);
                        break;
                    case ext == "pdf": // pdf
                        const embed = document.createElement('embed');
                        embed.src = doc.upload_path;
                        card.appendChild(embed);
  
                        const pdfLink = document.createElement('a');
                        pdfLink.href = doc.upload_path;
                        pdfLink.classList.add('lien');
                        pdfLink.textContent = 'Voir PDF en grand';
                        pdfLink.target = '_blank';
                        card.appendChild(pdfLink);
                        break;
                    case ext == "mp4": // video
                        const video = document.createElement('video');
                        video.src = doc.upload_path;
                        video.controls = true;
                        card.appendChild(video);
                        break;
                    case ext == "html":
                        const iframe = document.createElement('iframe');
                        iframe.src = doc.upload_path;
                        card.appendChild(iframe);
                        break;
                    default:
                        const unsupportedLink = document.createElement('a');
                        unsupportedLink.href = doc.upload_path;
                        unsupportedLink.classList.add('lien');
                        unsupportedLink.textContent = 'Type de fichier non supporté.';
                        unsupportedLink.target = '_blank';
                        card.appendChild(unsupportedLink);
                        break;
                }

                
                const ele = document.createElement("a");
                ele.innerText = "Voir ce que c'est";
                ele.href = `ens.php?ensemble_id=${doc.ensemble_id}`;
                ele.classList.add("lien")
                card.appendChild(ele)

                document.getElementById("liste_resultats").appendChild(card);

            });

            

        });
    }
}


document.addEventListener("DOMContentLoaded", (event)=>{
    gen_chronologie();

    document.getElementById("recherche_input").addEventListener("keydown", (event)=>{
        if (event.key === "Enter"){
            event.preventDefault();
            rechercher();
        }
    });

    document.getElementById("recherche_form").onsubmit = function(event){
        event.preventDefault();
        // faire tomber le clavier sur mobile
        document.activeElement.blur();
        rechercher();


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

    document.getElementById("titre").addEventListener("click", (event) => {
        window.location.pathname = "/archinsa";
    });


});

