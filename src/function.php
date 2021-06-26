<?php
  /*
    Le projet All in One est un produit Xelyos mis à disposition gratuitement
    pour tous les serveurs de jeux Role Play. En échange nous vous demandons de
    ne pas supprimer le ou les auteurs du projet.
    Created by : Xelyos - Aros
    Edited by :
  */
  use Michelf\Markdown;

  function ajout_bdd($discord, $nom, $prenom, $phone, $age_ig, $age_irl, $tps_serv, $tps_gta, $retour_faction, $detail_faction, $candid_time_school, $ecole, $candid_time_vacances, $vacances, $candid_time_work, $work, $objectif, $candid_motivations, $candid_intervention, $candid_k9, $concat)
  {
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
                  'reponse_intervention' => $candid_intervention,
                  'reponse_k9' => $candid_k9,
                  'concat' => $concat,
                  'date_depot' => date("Y-m-d"),
                  'adress_ip' => $_SERVER['REMOTE_ADDR']
                )
              );
    $candidature->save();
  }


  function serveurIni($categorie, $param) {
    $params = parse_ini_file(".env", true);
    return $params[$categorie][$param];
  }

  function getStructure($path) {
    $file = file_get_contents($path . "/content/index.json", TRUE);
    return json_decode($file);
  }

  function getFileContent($path, $file) {
    return file_get_contents($path . "/content/" . $file);
  }

  function renderHTMLFromMarkdown($string_markdown_formatted) {
    return Markdown::defaultTransform($string_markdown_formatted);
  }
?>
