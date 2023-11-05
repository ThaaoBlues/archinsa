<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload</title>
</head>
<body>

<!-- Input to choose files -->

<form id="uploadForm">
<input type="file" id="fileInput" multiple>
<input type="text" placeholder="titre" id="titre"></input>

<select id="select_type">
    <option value="1">annale</option>
    <option value="2">fiche_revision</option>

</select>

<input type="text" placeholder="commentaires généraux sur l'ensemble des documents" id="commentaire_auteur"></input>
<div id="selectedImages"></div>

<button type="button" onclick="uploadFiles()">Téléverser les fichiers</button>
</form>

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

    let ex = {ex1:{duree:"10",themes:["algèbre","analyse"],commentaire_exo:"cci est un commenataire"},ex2:{duree:"15",themes:["elec analogique"],commentaire_exo:""}}; 
    formData.append("exercices",JSON.stringify(ex))


    // Append each selected file to the FormData
    let i = 0;
    for (const file of fileInput.files) {
        formData.append('fichier' + i, file);
        i ++;
    }

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
</script>

</body>
</html>
