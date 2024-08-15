function inscription(){

    const formData = new FormData();

    formData.append("username",document.getElementById("username-input").value);
    formData.append("password",document.getElementById("password-input").value);
    console.log(document.getElementById("insa-input").value);
    formData.append("nom_insa",document.getElementById("insa-input").value)
    formData.append("jeton-csrf",jeton_csrf);

    fetch('api.php/inscription', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        alert(data.msg);
    })
    .catch(error => {
        console.error('Error:', error);
    });
}