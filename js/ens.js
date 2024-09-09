
// fetch l'api et afficher tout ce qu'elle nous rend
function querystring(key) {
  var re = new RegExp("(?:\\?|&)" + key + "=(.*?)(?=&|$)", "gi");
  var r = [],
    m;
  while ((m = re.exec(document.location.search)) != null) r[r.length] = m[1];
  return r;
}


/*async function gen_contenu() {
    try {
      const response = await fetch('api.php/decomposer_ensemble?ensemble_id='+querystring("ensemble_id"));
      const data = await response.json();
      console.log(data);
  
      if (data.status === "1" && data.msg.documents.length > 0) {
        const table = document.createElement('table');
        const thead = document.createElement('thead');
        const tbody = document.createElement('tbody');
  
        const headerRow = document.createElement('tr');
        const idHeader = document.createElement('th');
        idHeader.textContent = 'ID';
        const titreHeader = document.createElement('th');
        titreHeader.textContent = 'Titre';
        const typeHeader = document.createElement('th');
        typeHeader.textContent = 'Type';
        const uploadPathHeader = document.createElement('th');
        uploadPathHeader.textContent = 'Upload Path';
        const previewHeader = document.createElement('th');
        previewHeader.textContent = 'Preview';
        const commentaireHeader = document.createElement('th');
        commentaireHeader.textContent = 'Commentaire Auteur';
        const exerciceHeader = document.createElement('th');
        exerciceHeader.textContent = 'Exercices';
  
        headerRow.appendChild(idHeader);
        headerRow.appendChild(titreHeader);
        headerRow.appendChild(typeHeader);
        headerRow.appendChild(uploadPathHeader);
        headerRow.appendChild(previewHeader);
        headerRow.appendChild(commentaireHeader);
        headerRow.appendChild(exerciceHeader);
  
        thead.appendChild(headerRow);
  
        data.msg.documents.forEach(doc => {
          const row = document.createElement('tr');
          const idCell = document.createElement('td');
          idCell.textContent = doc.id;
          const titreCell = document.createElement('td');
          titreCell.textContent = doc.titre;
          const typeCell = document.createElement('td');
          typeCell.textContent = doc.type;
          const uploadPathCell = document.createElement('td');
          uploadPathCell.textContent = doc.upload_path;
  
          let previewCell;
          let ext = doc.upload_path.toString().split(".").pop();
  
          let image_extensions = [
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
            'raw'];

          switch (true) {
            case image_extensions.includes(ext): // image
              previewCell = document.createElement('td');
              const img = document.createElement('img');
              img.src = doc.upload_path;
              img.alt = doc.titre;
              previewCell.appendChild(img);

              let lien_img = document.createElement('a');
              lien_img.href = doc.upload_path;
              lien_img.textContent = 'Voir image';
              lien_img.target = '_blank';
              previewCell.appendChild(lien_img);

              break;
            case ext=="pdf": // pdf
              previewCell = document.createElement('td');
              const pdfLink = document.createElement('a');
              pdfLink.href = doc.upload_path;
              pdfLink.textContent = 'Voir PDF';
              pdfLink.target = '_blank';
              previewCell.appendChild(pdfLink);
              break;
            case ext == "mp4": // video
              previewCell = document.createElement('td');
              const video = document.createElement('video');
              video.src = doc.upload_path;
              video.controls = true;
              previewCell.appendChild(video);
              break;
            case ext == "html": 
              previewCell = document.createElement('td');
              const iframe = document.createElement('iframe');
              iframe.href = doc.upload_path;
              //iframe.textContent = doc.titre;
              previewCell.appendChild(iframe);
              break;

            default :
              previewCell = document.createElement('td');
              let lien = document.createElement('a');
              lien.href = doc.upload_path;
              lien.textContent = 'Type de fichier non supporté.';
              lien.target = '_blank';
              previewCell.appendChild(lien);
            break;

          }
  
          const commentaireCell = document.createElement('td');
          commentaireCell.textContent = data.msg.commentaire_auteur || '';
  
          const exerciceCell = document.createElement('td');
          if (doc.exercices && doc.exercices.length > 0) {
            const exerciceList = document.createElement('ul');
            doc.exercices.forEach(exercice => {
              const exerciceItem = document.createElement('li');
              exerciceItem.textContent = `Exo n°${exercice.id} ${exercice.commentaire_auteur}, Duree: ${exercice.duree}`;
              exerciceList.appendChild(exerciceItem);
            });
            exerciceCell.appendChild(exerciceList);
          } else {
            exerciceCell.textContent = 'Pas de détails sur les exercices';
          }
  
          row.appendChild(idCell);
          row.appendChild(titreCell);
          row.appendChild(typeCell);
          row.appendChild(uploadPathCell);
          row.appendChild(previewCell);
          row.appendChild(commentaireCell);
          row.appendChild(exerciceCell);
  
          tbody.appendChild(row);
        });
  
        table.appendChild(thead);
        table.appendChild(tbody);
  
        const dataContainer = document.getElementById('data-container');
        dataContainer.appendChild(table);
      } else {
        const dataContainer = document.getElementById('data-container');
        dataContainer.textContent = data.msg;
      }
    } catch (error) {
      console.error(error);
    }
}*/

async function gen_contenu() {
  try {
      const response = await fetch('api.php/decomposer_ensemble?ensemble_id=' + querystring("ensemble_id"));
      const data = await response.json();
      console.log(data);

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

      const dataContainer = document.getElementById('data-container');

      if (data.status === "1" && data.msg.documents.length > 0) {
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

              const typeDiv = document.createElement('div');
              typeDiv.textContent = `Type: ${doc.type}`;
              card.appendChild(typeDiv);

              /*const uploadPathDiv = document.createElement('div');
              uploadPathDiv.textContent = `Upload Path: ${doc.upload_path}`;
              card.appendChild(uploadPathDiv);*/

              // Ajout du contenu spécifique selon le type de fichier
              let ext = doc.upload_path.toString().split(".").pop();
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

              // Ajout du contenu restant de la carte
              const commentaireDiv = document.createElement('div');
              commentaireDiv.classList.add('title');
              commentaireDiv.textContent = `Commentaire Auteur: ${data.msg.commentaire_auteur || ''}`;
              card.appendChild(commentaireDiv);

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
    window.location.pathname = "/archinsa";
  });

});