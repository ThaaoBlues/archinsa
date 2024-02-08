function valider_ensemble(ensembleId) {

    const formData = new FormData();
    formData.append("jeton-csrf",jeton_valider_ensemble);
    formData.append("ensemble_id",ensembleId);
    fetch('api.php/valider_ensemble', {
        method: 'POST',
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        if (data.status == 1) {
            alert(data.msg)
        }else{
            alert(data.msg)
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}


function supprimer_ensemble(ensembleId) {
    const formData = new FormData();
    formData.append("jeton-csrf",jeton_supprimer_ensemble);
    formData.append("ensemble_id",ensembleId);
    
    fetch('api.php/supprimer_ensemble', {
        method: 'POST',
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        if (data.status == 1) {
            alert(data.msg)
            document.location.reload();
        }else{
            alert(data.msg)
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}


document.addEventListener("DOMContentLoaded", (event) => {

    let liens = document.getElementsByClassName('lien-valider-ens');

    for (var i = 0; i < liens.length; i++) {
        liens[i].addEventListener('click', function(event) {

            event.preventDefault();

            valider_ensemble(liens[i].getAttribute("id_ens"));
        
        });
    }

    liens = document.getElementsByClassName('lien-supprimer-ens');

    for (var i = 0; i < liens.length; i++) {
        liens[i].addEventListener('click', function(event) {

            event.preventDefault();

            supprimer_ensemble(liens[i].getAttribute("id_ens"));
        
        });
    }

});