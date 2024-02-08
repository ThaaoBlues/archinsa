
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
    var re=new RegExp('(?:\\?|&)'+key+'=(.*?)(?=&|$)','gi');
    var r=[], m;
    while ((m=re.exec(document.location.search)) != null) r[r.length]=m[1];
    return r;
}


async function gen_contenu(){
    resp = await fetch("/annales/api.php/decomposer_ensemble?ensemble_id="+querystring("ensemble_id"));
    data = await resp.json();

    if(data["status"] == 1){
        console.log(data);
    }
    
}