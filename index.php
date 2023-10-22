
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<?php
    session_start();

?>
    <a href="javascript:authenticate_user();">connection</a>
    <a href="javascript:unauthenticate_user();">déconnection</a>

    <div id="user_status">

    </div>
</body>
<script>
    async function test_auth(){
        resp = await fetch("/annales/api.php?test_auth");
        data = await resp.json();
        document.getElementById("user_status").innerText = data["msg"];
    }

    // fonction de test, innutile en prod
    async function authenticate_user(){
        resp = await fetch("/annales/api.php?auth");
        data = await resp.json();
        if(data.status == 1){
            document.getElementById("user_status").innerText = data["msg"];
        }
    }

    
    async function unauthenticate_user(){
        resp = await fetch("/annales/api.php?unauth");
        data = await resp.json();
        if(data.status == 1){
            document.getElementById("user_status").innerText = data["msg"];
        }
    }

    test_auth();

</script>
</html>