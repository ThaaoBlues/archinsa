/*

    pour les docs afficher un truc du même acabit que la php :
        if (strtolower($extension) === 'pdf'):
        echo "<embed src=\"{$doc['upload_path']}\" type=\"application/pdf\" width=\"100%\" height=\"600px\" />";
    elseif (in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif'])):
        echo "<img src=\"{$doc['upload_path']}\">";
    else:
        echo "<p>Oups ! Je ne sais pas afficher ce document :/ (Rales autant que tu veux je men fous) </p>".$doc['upload_path'];
    endif;
    */

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
  
          switch (ext) {
            case "jpg": // image
              previewCell = document.createElement('td');
              const img = document.createElement('img');
              img.src = doc.upload_path;
              img.alt = doc.titre;
              previewCell.appendChild(img);
              break;
            case "pdf": // pdf
              previewCell = document.createElement('td');
              const pdfLink = document.createElement('a');
              pdfLink.href = doc.upload_path;
              pdfLink.textContent = 'View PDF';
              pdfLink.target = '_blank';
              previewCell.appendChild(pdfLink);
              break;
            case "mp4": // video
              previewCell = document.createElement('td');
              const video = document.createElement('video');
              video.src = doc.upload_path;
              video.controls = true;
              previewCell.appendChild(video);
              break;
            case "html": 
              previewCell = document.createElement('td');
              const iframe = document.createElement('iframe');
              iframe.href = doc.upload_path;
              //iframe.textContent = doc.titre;
              previewCell.appendChild(iframe);
              break;

            default :
              previewCell = document.createElement('td');
              const link = document.createElement('a');
              link.href = doc.upload_path;
              link.textContent = 'Type de fichier non supporté.';
              link.target = '_blank';
              previewCell.appendChild(link);
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
      alert(error);
    }
}


document.addEventListener("DOMContentLoaded", (event)=>{

    gen_contenu();

  document.getElementById("titre").addEventListener("click", (event) => {
    window.location.pathname = "/archinsa";
  });

});