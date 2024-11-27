
// fetch l'api et afficher tout ce qu'elle nous rend
function querystring(key) {
  var re = new RegExp("(?:\\?|&)" + key + "=(.*?)(?=&|$)", "gi");
  var r = [],
    m;
  while ((m = re.exec(document.location.search)) != null) r[r.length] = m[1];
  return r;
}



async function gen_contenu() {
  try {
      const response = await fetch('api.php/decomposer_ensemble?ensemble_id=' + querystring("ensemble_id"));
      const data = await response.json();
      console.log(data);


      const dataContainer = document.getElementById('data-container');

      if (data.status === "1" && data.msg.documents.length > 0) {

          // Ajout du contenu restant de la carte
          const commentaireDiv = document.createElement('div');
          commentaireDiv.classList.add('title');
          commentaireDiv.textContent = `Commentaire Auteur: ${data.msg.commentaire_auteur || ''}`;
          document.body.appendChild(commentaireDiv);


          data.msg.documents.forEach(doc => {
              // Création d'une carte (card)
              const card = document.createElement('div');
              card.classList.add('card');

              // Construction du contenu de la carte
              /*const idDiv = document.createElement('div');
              idDiv.textContent = `ID: ${doc.id}`;
              card.appendChild(idDiv);*/

              const titreDiv = document.createElement('div');
              titreDiv.classList.add('title');
              titreDiv.textContent = `Titre: ${doc.titre}`;
              card.appendChild(titreDiv);

              /*const uploadPathDiv = document.createElement('div');
              uploadPathDiv.textContent = `Upload Path: ${doc.upload_path}`;
              card.appendChild(uploadPathDiv);*/
            console.log(doc.type)
              switch (doc.type) {   
                case 2: // image
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
                case 3: // pdf
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
                case 4: // video
                    const video = document.createElement('video');
                    video.src = doc.upload_path;
                    video.controls = true;
                    card.appendChild(video);
                    break;
                case 5:
                    const iframe = document.createElement('iframe');
                    iframe.src = doc.upload_path;
                    card.appendChild(iframe);
                    break;

                case 1:
                    const textarea = document.createElement('textarea');
                    var xmlhttp, text;
                    xmlhttp = new XMLHttpRequest();
                    xmlhttp.open('GET', doc.upload_path, false);
                    xmlhttp.send();
                    text = xmlhttp.responseText;
                    textarea.value = text;
                    card.appendChild(textarea);
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


              // Exercices
              if (doc.exercices && doc.exercices.length > 0) {
                  const exercicesTitle = document.createElement('div');
                  exercicesTitle.classList.add('title');
                  exercicesTitle.textContent = 'Exercices:';
                  card.appendChild(exercicesTitle);

                  const exercicesList = document.createElement('ul');
                  doc.exercices.forEach(exercice => {
                      const exerciceItem = document.createElement('li');
                      exerciceItem.classList.add('main-text');
                      exerciceItem.textContent = `Exo n°${exercice.id} ${exercice.commentaire_auteur}, Durée: ${exercice.duree} min`;
                      exercicesList.appendChild(exerciceItem);
                  });
                  card.appendChild(exercicesList);
              } else {
                  const noExercicesDiv = document.createElement('div');
                  noExercicesDiv.textContent = 'Pas de détails sur les exercices';
                  card.appendChild(noExercicesDiv);
              }

              
              // Ajout de la carte au conteneur principal
              dataContainer.appendChild(card);
          });


      } else {
          dataContainer.textContent = data.msg;
      }



  } catch (error) {
      console.error(error);
  }
}





document.addEventListener("DOMContentLoaded", (event)=>{

    gen_contenu();

  document.getElementById("titre").addEventListener("click", (event) => {
    window.location.pathname = "/";
  });

});