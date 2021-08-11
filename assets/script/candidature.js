/*
  Le projet All in One est un produit Xelyos mis à disposition gratuitement
  pour tous les serveurs de jeux Role Play. En échange nous vous demandons de
  ne pas supprimer le ou les auteurs du projet.
  Created by : Xelyos - Aros
  Edited by :
*/

/* Masque la partie des antécédent force de l'ordre */
document.getElementById("force_suite").style.display = "none";
function change_force() {
  if (document.getElementById("faction").value == 0) {
    document.getElementById("force_suite").style.display = "none";
  }
  else {
    document.getElementById("force_suite").style.display = "grid";
  }
}

/* Masque la partie des dispo école */
function visibility_1() {
  if (document.getElementById("time_school").value == 0) {
    document.getElementById("dispo_1").style.display = "none";
  }
  else {
    document.getElementById("dispo_1").style.display = "block";
  }
}

/* Masque la partie des dispo vacance */
function visibility_2() {
  if (document.getElementById("time_work").value == 0) {
    document.getElementById("dispo_2").style.display = "none";
  }
  else {
    document.getElementById("dispo_2").style.display = "block";
  }
}

/* Masque la partie des dispo travail */
function visibility_3() {
  if (document.getElementById("time_vacances").value == 0) {
    document.getElementById("dispo_3").style.display = "none";
  }
  else {
    document.getElementById("dispo_3").style.display = "block";
  }
}

function checkSizeFiles() {
  var myFiles = document.getElementById('uploads').files;
  var tailleMax = document.getElementById('taillemax').value;

  var taille = 0;
  for (var i = 0; i < myFiles.length; i++) {
    taille += myFiles[i].size;
  }

  if (taille > tailleMax) {
    document.getElementById('submit-button').style.display = "none";
    document.getElementById('alert-taille').style.display = "block";
  }
  else {
    document.getElementById('submit-button').style.display = "block";
    document.getElementById('alert-taille').style.display = "none";
  }
}
