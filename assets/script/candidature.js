/*
  Le projet All in One est un produit Xelyos mis à disposition gratuitement
  pour tous les serveurs de jeux Role Play. En échange nous vous demandons de
  ne pas supprimer le ou les auteurs du projet.
  Created by : Xelyos - Aros
  Edited by :
*/

/* Masque la partie des dispo école */
function visibility(number) {
  if (document.getElementById(`date_${number}`).value == 0) {
    document.getElementById(`dispo_${number}`).style.display = "none";
  }
  else {
    document.getElementById(`dispo_${number}`).style.display = "block";
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
