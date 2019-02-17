var ajax = new XMLHttpRequest();

function doCad(nome, sexo, idade, email, senha) {
    const data = "nome=" + nome + "&sexo=" + sexo + "&idade=" + idade + "&email=" + email + "&senha=" + senha;
    if (ajax) {
        ajax.open('POST', '/ajax/subscriber.php', true);
        ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        ajax.send(data);
        ajax.onreadystatechange = function () {
            if (ajax.readyState == 4) {
                if (ajax.status == 200) {
                    domStatus(ajax.responseText);
                } else {
                    domStatus(false);
                }
            }
        }

    } else {
        // sem ajax e agora?
        alert('no ajax');
    }
} // end doCad


function domStatus(response) {
    var displayMsg = document.querySelector("#showmsg");
    if (response) {
        displayMsg.style.display = "block";
        displayMsg.innerHTML = response;
        if (response == "Cadastro com sucesso") {
            document.location = "/?signUp=true";
        }

    }
} // end doStatus






function getCrush(lastID, limit, callback) {
   
    if (ajax) {
        ajax.open('POST', 'ajax/getCrush.php', true);
        ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        ajax.send("id="+lastID+"&limit="+limit);
        ajax.onreadystatechange = function () {
            if (ajax.readyState == 4) {
                if (ajax.status == 200) {
                  jsonResponse = JSON.parse(ajax.response);
                  callback(jsonResponse);

                } else {
                   // erro de requisição
                }
            }
        }

    } else {
        // sem ajax e agora?
        alert('no ajax');
    }
}