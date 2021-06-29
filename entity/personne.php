<?php
/*
Le projet All in One est un produit Xelyos mis à disposition gratuitement
pour tous les serveurs de jeux Role Play. En échange nous vous demandons de
ne pas supprimer le ou les auteurs du projet.
Created by : Xelyos - Aros
Edited by :
*/

class Personne extends Model {
  public static $_table = 'personnes'; // Liaison avec la table

  /* Récupération des valeurs dans les tables */
  public static function getPPA($info) {
    return Personne::where(array(
                     'ppa' => $info,
                     'present' => 2
                    ))
                    ->order_by_asc(array('nom', 'prenom'))
                    ->find_many();
  }
}
?>
