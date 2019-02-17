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
}