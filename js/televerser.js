var camera_open = false;
var video;



function televerser_fichiers() {
    const fileInput = document.getElementById('fileInput');
    
    // Create FormData object to append files
    const formData = new FormData();


    formData.append("type",document.getElementById("select_type").value);
    formData.append("titre",document.getElementById("titre").value);
    formData.append("commentaire_auteur",document.getElementById("commentaire_auteur").value);

    formData.append("corrige_inclu",document.getElementById("corrige_checkbox").value);

    formData.append("date_conception",document.getElementById("date_conception_input").value);

    //let ex = [{duree:"10",themes:["algèbre","analyse"],commentaire_exo:"ceci est un commenataire"},{duree:"15",themes:["elec analogique"],commentaire_exo:""}]; 
    
    var ex = [];
    // details des exos pour les annales
    if(formData.get("type") == "1"){
        var details = document.getElementsByClassName("input-details-exo");

        for(let i=0;i<details.length;i = i + 3){
            ex.push({
                duree:details[i].value,
                themes:details[i+1].value.split(","),
                commentaire_exo:details[i+2].value
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

    console.log(ex);

    //csrf token
    formData.append("jeton-csrf",jeton_csrf);
    //alert(jeton_csrf);

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
    .then(response => response.json())
    .then(data => {
        //console.log(data);
        if(data.status == 1){
            alert("le document a bien été envoyé ! Merci de votre participation :D")
        }else{
            alert("Une erreur s'est produite lors de l'envoi de votre fichier : "+data.msg);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function ouvrir_camera() {
    // test if camera is already open, in that case juste take a regular picture
    if(camera_open){
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
        return;
    }


    // Open the camera and take pictures
    // You can use the MediaDevices API to access the camera
    navigator.mediaDevices.getUserMedia({ video: true })
        .then(mediaStream => {
            video = document.createElement('video');
            document.body.appendChild(video);

            camera_open = true;

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


function init_date(){
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth()+1; 
    var yyyy = today.getFullYear()-1; // pourquoi 2025 ?????
    yyyy = parseInt(yyyy) + 1;
    today = yyyy+"-"+mm+"-"+dd;
    console.log(today);
    document.getElementById("date_conception_input").setAttribute("value",today);
}


document.addEventListener("DOMContentLoaded", (event) => {

    
    init_date();
    document.getElementById("select_type").addEventListener("change", (event) => {
        changer_mode();
    });

    document.getElementById("btn-soumettre").addEventListener("click", (event) => {
        televerser_fichiers();
    });

    document.getElementById("btn-camera").addEventListener("click", (event) => {
        ouvrir_camera();
    });

    document.getElementById("btn-details-exo").addEventListener("click", (event) => {
        ajouter_details_exo();
    });

    document.getElementById("titre").addEventListener("click", (event) => {
        window.location.pathname = "/archinsa";
    });

});

