function Consulta() {
    dataini = document.getElementById("dini_fraquia").value;
    datafim = document.getElementById("dfim_fraquia").value;

    if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
    } else {// code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            document.getElementById("franquia").innerHTML = xmlhttp.responseText;

        } else {
            document.getElementById("franquia").innerHTML = '<center><img src="./loading.gif" border="0" alt="Loading, please wait..." />';
        }
    }
    xmlhttp.open("GET", "./abc.php?dini=" + dataini + '&dfim=' + datafim, true);
    xmlhttp.send();
}



