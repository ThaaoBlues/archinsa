function deconnection(){


    const formData = new FormData();

    formData.append("jeton-csrf",jeton_csrf);

    fetch('api.php/deconnection', {
        method: 'POST',
        body:formData
    })
    .then(response => response.json())
    .then(data => {
        //console.log(data);
        if(data.status == 1){
            window.location.href = "index.php";
        }else{
            alert("Une erreur s'est produite lors de votre déconnection : "+data.msg);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

window.onload = function(){
    deconnection();
}