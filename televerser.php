<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload</title>
</head>
<body>

<!-- Input to choose files -->
<input type="file" id="fileInput" multiple>
<button onclick="uploadFiles()">Upload Files</button>

<!-- Button to open the camera -->
<button onclick="openCamera()">Open Camera</button>

<input type="text" placeholder="titre" id="titre"></input>

<select id="select_type">
    <option value="annale">annale</option>
    <option value="fiche_revision">fiche_revision</option>

</select>

<script>
function uploadFiles() {
    const fileInput = document.getElementById('fileInput');
    
    // Create FormData object to append files
    const formData = new FormData();

    formData.append("type",document.getElementById("select_type").getAttribute("value"));
    formData.append("titre",document.getElementById("titre").getAttribute("value"));

    // Append each selected file to the FormData
    for (const file of fileInput.files) {
        formData.append('files[]', file);
    }

    // Make a POST request using Fetch API
    fetch('api.php/aj_doc', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
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

                // Close the camera stream
                mediaStream.getTracks().forEach(track => track.stop());

                // Make a POST request to upload the image
                fetch('api.php/aj_doc', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        files: [{ name: 'camera_image.jpg', data: imageDataUrl.split(',')[1] }]
                    })
                })
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    // Handle the response from the server
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
        })
        .catch(error => {
            console.error('Error accessing camera:', error);
        });
}
</script>

</body>
</html>
