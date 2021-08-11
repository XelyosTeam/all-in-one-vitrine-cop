<?php
/*
Le projet All in One est un produit Xelyos mis à disposition gratuitement
pour tous les serveurs de jeux Role Play. En échange nous vous demandons de
ne pas supprimer le ou les auteurs du projet.
Created by : Xelyos - Aros
Edited by :
*/
use Michelf\Markdown;

function ajout_bdd($discord, $nom, $prenom, $phone, $age_ig, $age_irl, $tps_serv, $tps_gta, $retour_faction, $detail_faction, $candid_time_school, $ecole, $candid_time_vacances, $vacances, $candid_time_work, $work, $objectif, $candid_motivations, $concat, $attachments) {
  $candidature = Model::factory('Candidature')->create();
  $candidature->set(array(
    'discord_id' => $discord,
    'nom' => $nom,
    'prenom' => $prenom,
    'phone' => $phone,
    'age_IG' => $age_ig,
    'age_IRL' => $age_irl,
    'tmps_serveur' => $tps_serv,
    'tmps_life' => $tps_gta,
    'ancien_flic' => $retour_faction,
    'detail_flic' => $detail_faction,
    'dispo_ecole' => $candid_time_school,
    'detail_ecole' => $ecole,
    'dispo_vacance' => $candid_time_vacances,
    'detail_vacance' => $vacances,
    'dispo_travail' => $candid_time_work,
    'detail_travail' => $work,
    'objectif_lspd' => $objectif,
    'motivation_lspd' => $candid_motivations,
    'reponse_concat' => $concat,
    'attachments' => $attachments,
    'date_depot' => date("Y-m-d"),
    'adress_ip' => $_SERVER['REMOTE_ADDR']
  )
);
$candidature->save();
}

function serveurIni($categorie, $param) {
  $params = parse_ini_file(".env", true); // Insertion du fichier : server.ini
  return $params[$categorie][$param];
}

function getStructure($path, $name) {
  $file = file_get_contents($path . "/content/$name.json", TRUE);
  return json_decode($file);
}

function getFileContent($path, $file) {
  return file_get_contents($path . "/content/" . $file);
}

function renderHTMLFromMarkdown($string_markdown_formatted) {
  return Markdown::defaultTransform($string_markdown_formatted);
}

/* Ajout d'un fichier en lui intégrant un timestamp*/
function uploadFile($destination, $maxsize, $file, $extensionArray) {
  $final = new ArrayObject();
  for ($i=0; $i < count($file['name']); $i++) {
    /* check Size */
    $continue = true;
    if ($file['size'][$i] > $maxsize) {
      $continue = false;
    }

    /* Check Extensions */
    if (!in_array(strtolower(explode(".", $file['name'][$i])[count(explode(".", $file['name'][$i]))-1]), $extensionArray)) {
      $continue = false;
    }

    if ($continue) {
      /* Rename file */
      $explode = explode(".", $file['name'][$i]);
      $toReplace = array(" ", '_', '"', "'", "/", "\\", "(", ")");
      $file['name'][$i] = implode('.',array_slice($explode, 0, count($explode) - 1)) . '-' . time() . "." . $explode[count($explode) - 1];
      $file['name'][$i] = strtr($file['name'][$i],'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ','AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
      $file['name'][$i] = preg_replace('/([^.a-z0-9]+)/i', '-', $file['name'][$i]);
      $file['name'][$i] = str_replace($toReplace, "-", $file['name'][$i]);

      while (strpos($file['name'][$i], '--')) {
        $file['name'][$i] = str_replace('--', "-", $file['name'][$i]);
      }

      try {
        move_uploaded_file($file['tmp_name'][$i], $destination . $file['name'][$i]);
        $final->append($file['name'][$i]);
      } catch (\Exception $e) { }
    }
  }

  return implode(',', (array)$final);
}
?>
