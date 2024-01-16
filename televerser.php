<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload</title>
</head>
<body>
<?php
include("session_verif.php");
// Include the PHP-CSRF library
include('php-csrf.php');
verifier_session();

$csrf = new CSRF();
?>

<!-- Input to choose files -->

<form id="uploadForm">
<input type="file" id="fileInput" multiple>
<input type="text" placeholder="titre" id="titre"></input>

<select id="select_type" onchange="changer_mode()">
    <option value="1" >annale</option>
    <option value="2" >fiche_revision</option>
    <option value="3" >HTML personnalisé</option>
</select>

<input type="text" placeholder="commentaires généraux sur l'ensemble des documents" id="commentaire_auteur"></input>
<div id="selectedImages"></div>

<div id="corrige_checkbox_wrapper">
    <input type="checkbox" id="corrige_checkbox">
    <label for="corrige_checkbox">Corrigé inclu</label>
</div>


<button type="button" onclick="uploadFiles()">Téléverser les fichiers</button>
</form>

<div id="exercices_details_wrapper">
    <button onclick="ajouter_details_exo()">Ajouter les détails d'un exercice</button>

</div>
<!-- Button to open the camera -->
<button onclick="openCamera()">Open Camera</button>



<script>
function uploadFiles() {
    const fileInput = document.getElementById('fileInput');
    
    // Create FormData object to append files
    const formData = new FormData();

    formData.append("type",document.getElementById("select_type").value);
    formData.append("titre",document.getElementById("titre").value);
    formData.append("commentaire_auteur",document.getElementById("commentaire_auteur").value);

    formData.append("corrige_inclu",document.getElementById("corrige_checkbox").value);

    //let ex = [{duree:"10",themes:["algèbre","analyse"],commentaire_exo:"ceci est un commenataire"},{duree:"15",themes:["elec analogique"],commentaire_exo:""}]; 
    
    let ex = [];

    // details des exos pour les annales
    if(formData["type"] == "1"){
        let details = document.getElementsByClassName("input-details-exo");

        for(let i=0;i<=details.length;i = i + 3){
            ex.push({
                // duree
                duree:details[i].getAttribute.value,
                themes:details[i+1].getAttribute.value.split(","),
                commentaire_exo:details[i+2].getAttribute.value
            })
        }
    }


    formData.append("exercices",JSON.stringify(ex))


    // Append each selected file to the FormData
    let i = 0;
    for (const file of fileInput.files) {
        formData.append('fichier' + i, file);
        i ++;
    }

    //csrf token
    formData.append("jeton-csrf","<?=$csrf->string($context="televersement")?>");

    // Append captured images as files to the FormData
    const capturedImages = document.querySelectorAll('#selectedImages img');

    i = 0;
    capturedImages.forEach((img, index) => {
        const imageDataUrl = img.src;
        const blob = dataURLtoBlob(imageDataUrl);
        const file = new File([blob], `camera_image_${index}.jpg`);
        formData.append('fichier'+i, file);
        i ++;
    });

    // Make a POST request using Fetch API
    fetch('api.php/aj_doc', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        console.log(data);
        // Handle the response from the server
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function openCamera() {
    // Open the camera and take pictures
    // You can use the MediaDevices API to access the camera
    navigator.mediaDevices.getUserMedia({ video: true })
        .then(mediaStream => {
            const video = document.createElement('video');
            document.body.appendChild(video);

            // Display the camera stream in a video element
            video.srcObject = mediaStream;
            video.play();

            // Capture an image from the video stream
            video.addEventListener('click', () => {
                const canvas = document.createElement('canvas');
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                const context = canvas.getContext('2d');
                context.drawImage(video, 0, 0, canvas.width, canvas.height);

                // Convert the canvas content to a data URL
                const imageDataUrl = canvas.toDataURL('image/jpeg');

                // Display the captured image
                const img = document.createElement('img');
                img.src = imageDataUrl;
                img.style.maxWidth = '100px';
                document.getElementById('selectedImages').appendChild(img);
                
            });

            // POUR FERMER LA CAMERA :
            // mediaStream.getTracks().forEach(track => track.stop());


        })
        .catch(error => {
            console.error('Error accessing camera:', error);
        });
}



function dataURLtoBlob(dataURL) {
    const arr = dataURL.split(',');
    const mime = arr[0].match(/:(.*?);/)[1];
    const bstr = atob(arr[1]);
    let n = bstr.length;
    const u8arr = new Uint8Array(n);
    while (n--) {
        u8arr[n] = bstr.charCodeAt(n);
    }
    return new Blob([u8arr], { type: mime });
}


function ajouter_details_exo(){
    duree = document.createElement("input");
    duree.setAttribute("type","number");
    duree.setAttribute("placeholder","Entrez la durée de l'exercice en minutes.")

    // classe imortante pour itérer sur toutes les input
    // dans le bon ordre et les associer aux exos dans la requête post
    duree.setAttribute("class","input-details-exo");

    document.getElementById("exercices_details_wrapper").appendChild(duree);
    

    themes = document.createElement("input");
    themes.setAttribute("type","text");
    themes.setAttribute("placeholder","Entrez les themes abordés par l'exercice séparés par une virgule.");
    themes.setAttribute("class","input-details-exo");

    document.getElementById("exercices_details_wrapper").appendChild(themes);


    comm = document.createElement("input");
    comm.setAttribute("type","text");
    comm.setAttribute("placeholder","Un ptit commentaire sur l'exo ?");
    comm.setAttribute("class","input-details-exo");

    document.getElementById("exercices_details_wrapper").appendChild(comm);


    // un peu de tendresse dans ce monde de brutes
    br =document.createElement("br");
    document.getElementById("exercices_details_wrapper").appendChild(br);
    hr =document.createElement("hr");
    document.getElementById("exercices_details_wrapper").appendChild(hr);
}



function mode_html(){

    document.getElementById("exercices_details_wrapper").setAttribute("hidden",true);
    document.getElementById("corrige_checkbox_wrapper").setAttribute("hidden",true);

}
function mode_fiche(){
    document.getElementById("exercices_details_wrapper").setAttribute("hidden",true);
    document.getElementById("corrige_checkbox_wrapper").setAttribute("hidden",true);
    
}

function mode_annale(){
    document.getElementById("corrige_checkbox_wrapper").removeAttribute("hidden");
    document.getElementById("exercices_details_wrapper").removeAttribute("hidden");
}


function changer_mode(){


    switch(document.getElementById("select_type").value){
        // annale
        case "1":
            mode_annale();
            break;
        // fiche
        case "2":
            mode_fiche();
            break;
        
        // html personnalisé
        case "3":
            mode_html();
            break;

    }
}


</script>

</body>
</html>
