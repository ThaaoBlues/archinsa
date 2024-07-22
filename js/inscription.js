function inscription(){

    const formData = new FormData();

    formData.append("username",document.getElementById("username-input").value);
    formData.append("password",document.getElementById("password-input").value);

    formData.append("jeton-csrf",jeton_csrf);

    fetch('api.php/inscription', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.status == 1){
            window.location.href = "index.php";
        }else{
            alert("Une erreur s'est produite lors de votre inscription. Ce nom d'utilisateur doit être déjà pris ! ");
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}