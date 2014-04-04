$(document).ready(function () {
    $("#add").validate();

    setInterval(function(){
        document.getElementById("frame").style.height = document.getElementById("frame").contentWindow.document.body.scrollHeight + 'px';
    },1000)

});