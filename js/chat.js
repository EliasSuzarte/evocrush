function getChat(id) {
   var ajax = new XMLHttpRequest();
    if (ajax) {
        ajax.open('POST', 'ajax/getChat.php', true);
        ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        ajax.send("id="+id);
        ajax.onreadystatechange = function (){
            if (ajax.readyState == 4) {
                if (ajax.status == 200) {
                    jsonResponse = JSON.parse(ajax.response);
                    implementHTML(jsonResponse);

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


function implementHTML(jsonResponse) {
    var tot = jsonResponse.length;
    if(tot>0){
        var i = 0;
        for(i; i < tot; i++){
            var div = document.createElement('div');
            div.setAttribute('class','msgs');
            var sexo = jsonResponse[i]['sexo'];
            var nome = jsonResponse[i]['nome'];
            var msg = jsonResponse[i]['msg'];
            var tempo = jsonResponse[i]['tempo'];
            var id = jsonResponse[i]['id'];
            div.id = id;
            var msgToDisplay = `<b class="${sexo}">${nome}</b> Disse: ${msg}`;
            div.innerHTML = msgToDisplay;
            var msgsBlock = document.querySelector(".msgsBlock");
            msgsBlock.append(div);

        }
    }
}

getChat(1); // inicia ao entrar na página

setInterval(function () {
    var lasMsg = document.querySelector(".msgs:nth-last-of-type(1)");
    var id = parseInt(lasMsg.id);
    getChat(id);
    var msgBlock = document.querySelector(".msgsBlock");
    msgBlock.scrollTop = msgBlock.scrollHeight;
},1000);






function postChat(msg) {
   var ajax = new XMLHttpRequest();
    if (ajax) {
        ajax.open('POST', 'ajax/sendChat.php', true);
        ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        ajax.send("msg="+msg);
        ajax.onreadystatechange = function (){
            if (ajax.readyState == 4) {
                if (ajax.status == 200) {
                    if(ajax.response =='[success]'){
                        // tudo ok
                    }else{
                        console.log(ajax.response);
                        alert('Erro ao enviar mensagem');
                    }


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





function sendMsg(){
    var tarea = document.querySelector(".textbar textarea");
    var msg = tarea.value;
    if(msg.length <2){
        alert('mensagem muito pequena');
    }else{
        tarea.value = '';
        postChat(msg);
    }
}

var submitJS = document.querySelector("#submitJS");

submitJS.addEventListener('click',function (e) {
    e.preventDefault();
    sendMsg()

});


window.onkeydown = function (e) {
    if(e.key == 'Enter' || e.keyCode == 13){
      sendMsg();
    }
};


