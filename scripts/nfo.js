var sparat = 0;
document.getElementById("desc").value = '';
document.getElementById("imdburl").value = '';
function sparaeller()
    {
    if(sparat == 0)
    {
    if(document.getElementById("desc").value.length > 50){
    //alert("");
    pspara();
    sparat = 1;
    }
    }
    else
    {
    //alert("");
    }
    }

    function pspara()
    {
    var parameters = 'desc=' + encodeURIComponent(document.getElementById("desc").value);
    document.getElementById("desc").disabled = true;
    var url = 'ajax_nfostrip.php';
    if(window.XMLHttpRequest)
    {
    ajax_spara = new XMLHttpRequest();
    }
    else
    {
    ajax_spara = new ActiveXObject("Microsoft.XMLHTTP");
    }
    ajax_spara.onreadystatechange = ajsparago;
    ajax_spara.open('POST', url, true);
    ajax_spara.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    ajax_spara.setRequestHeader("Content-length", parameters.length);
    ajax_spara.setRequestHeader("Connection", "close");
    ajax_spara.send(parameters);
    }

    function ajsparago() {
    if (ajax_spara.readyState == 4) {
    if (ajax_spara.status == 200) {
    document.getElementById("desc").disabled = false;
    var respons = ajax_spara.responseText; 
    var ar = respons.split(";_trala_");
    document.getElementById('imdburl').value = ar[0];
    document.getElementById("desc").value = ar[1];
    }
    }
    }