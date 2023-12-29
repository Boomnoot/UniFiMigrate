function SetKindSubmit(kind) {
    document.getElementById("actie").value = kind;
    document.getElementById('MainForm').submit();
}

function printValues(json) {
    var res = json.split(",");
    res.forEach(function(entry) {
        console.log(entry);
    });
}

function CheckForm() {
    var source = document.getElementById("source").value
    var destination = document.getElementById("Destination").value
    if(source==destination) {
        
        document.getElementById('result').classList.add("alert");
        document.getElementById('result').innerHTML = "<font color=\"white\">Source en destination zijn hetzelfde. Kan niet he?!</font>";
        
        setTimeout(function() {
            fadeOutEffect();
        }, 10000);
         setTimeout(function() {
            document.getElementById('result').classList.remove("alert");
            document.getElementById("result").innerHTML = '';
            document.getElementById("result").style.opacity = 1;
        }, 11000);
        
    } else {
        document.getElementById('ContrSwitcher').submit();
    }
    console.log(destination);
}

function fadeOutEffect() {
    var fadeTarget = document.getElementById("result");
    var fadeEffect = setInterval(function () {
        if (!fadeTarget.style.opacity) {
            fadeTarget.style.opacity = 1;
        }
        if (fadeTarget.style.opacity > 0) {
            fadeTarget.style.opacity -= 0.3;
        } else {
            clearInterval(fadeEffect);
        }
    }, 100);
}

function MigrateSite(sites,source,destination) {
    console.log('Start');
    
    document.getElementById('ja').classList.remove("btn-success");
    document.getElementById('ja').textContent = "Migrating";
    document.getElementById('ja').classList.add("btn-warning");
    document.getElementById('ja').onclick = '';
    document.getElementById('nee').classList.add("d-none");
    
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
            console.log('Eind');
            console.log(this.responseText);
			if(this.responseText =="OK") {
                document.getElementById('ja').classList.remove("btn-warning");
                document.getElementById('ja').classList.remove("w-25");
                document.getElementById('ja').classList.add("btn-success");
                document.getElementById('ja').textContent = "Geslaagd, terug";
                document.getElementById('ja').onclick = RedirectSelect;
                
                document.getElementById('nee').classList.remove("d-none");
                document.getElementById('nee').classList.remove("w-25");
                document.getElementById('nee').classList.add("btn-danger");
                document.getElementById('nee').textContent = "Verwijder sites";
                document.getElementById('nee').onclick = ContinueVerwijder;
                
                console.log('Gelukt');   
            }
		}
	};
	xmlhttp.open("GET", "ajax.php?actie=MigrateSite&sites="+sites+"&source="+source+"&destination="+destination, true);
	xmlhttp.send();
}

function RedirectSelect() {
    document.getElementById("hiddenform").action = 'SelectSites.php';
    document.getElementById("actie").value = 'SetController';
    document.getElementById('hiddenform').submit();
}

function ContinueVerwijder() {
    document.getElementById("actie").value = 'Delete';
    document.getElementById('hiddenform').submit();
}

function DeleteSite(sites,source,destination) {
    console.log('Start');
    
    document.getElementById('ja').classList.remove("btn-success");
    document.getElementById('ja').textContent = "Deleting";
    document.getElementById('ja').classList.add("btn-warning");
    document.getElementById('ja').onclick = '';
    document.getElementById('nee').classList.add("d-none");
    
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
            console.log('Eind');
            console.log(this.responseText);
			if(this.responseText =="OK") {
                document.getElementById('ja').classList.remove("btn-warning");
                document.getElementById('ja').classList.remove("w-25");
                document.getElementById('ja').classList.add("btn-success");
                document.getElementById('ja').textContent = "Geslaagd, terug";
                document.getElementById('ja').onclick = RedirectSelect;
                console.log('Gelukt');   
            }
		}
	};
	xmlhttp.open("GET", "ajax.php?actie=DeleteSite&sites="+sites+"&source="+source, true);
	xmlhttp.send();
}

