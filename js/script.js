function displayMSG(msg,duration = 5) {
    duration = duration * 1000;
    var divu  = document.createElement("div");
    divu.style = 'min-width:250px;padding:8px;position:relative;background-color:#653496;text-align:center;float:left';
    divu.innerHTML = msg;
    document.body.prepend(divu);
    setInterval(function () {
        divu.remove();
    },duration);
    divu.onclick = function () {  divu.remove()};
} // end displayMSG


var toggle = document.querySelector("#toggle");
toggle.addEventListener('click',function () {
  var menuLi = document.querySelector("#secnav nav ul");
  var x = document.querySelector("#x");
  if(menuLi.style.display == 'none' || menuLi.style.display ==''){
      show([menuLi,x]);

      timerHide = setTimeout(function () {
          hide([menuLi,x])
      },4000 * 50);

      x.onclick = function () {
          clearTimeout(timerHide);
          hide([menuLi,x]);
      };

  }else{
      clearTimeout(timerHide);
      hide([menuLi,x]);
  }

}); // event #toggle click


function show(elements,display = 'block'){
    elements.forEach(function (ele) {
        ele.style.display = ''+display+'';
    })
}

function hide(elements) {
    elements.forEach(function (ele) {
        ele.style.display = 'none';
    })
}



function doAjax(method, url,data,callback) {
    var ajax = new XMLHttpRequest();
    if(ajax){
        ajax.open(method,url,true);
        ajax.send(data);
        ajax.onreadystatechange = function () {
            if(ajax.readyState == 4 && ajax.status == 200){
                callback(ajax.response)
            }
        }
    }else{
        alert('Atualize seu navegador ou baixe um mais moderno para usar nosso site');
}
}  // end doAjax


doAjax('GET','/ajax/updateOnline.php',null,console.log);

setInterval(function () {
   doAjax('GET','/ajax/updateOnline.php',null,console.log)
},30000);



