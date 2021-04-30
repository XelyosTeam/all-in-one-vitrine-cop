<?php
  /*
    Le projet All in One est un produit Xelyos mis à disposition gratuitement
    pour tous les serveurs de jeux Role Play. En échange nous vous demandons de
    ne pas supprimer le ou les auteurs du projet.
    Created by : Xelyos - Aros
    Edited by :
  */
  require "vendor/autoload.php"; // Inclusion de l'autoloader

  /* Associer Flight à Twig */
  $loader = new \Twig\Loader\FilesystemLoader(dirname(__FILE__) . '/views');
  $twigConfig = array(
      // 'cache' => './cache/twig/',
      // 'cache' => false,
      'debug' => true,
  );

  Flight::register('view', '\Twig\Environment', array($loader, $twigConfig), function ($twig)
  {
      $twig->addExtension(new \Twig\Extension\DebugExtension()); // Add the debug extension

      /* Paramètre Serveur */
      $twig->addGlobal('_adresseDiscord', serveurIni('Serveur', 'discord'));
      $twig->addGlobal('_nomServeur', serveurIni('Serveur', 'nom'));
      $twig->addGlobal('_jeuServeur', serveurIni('Serveur', 'jeu'));
      $twig->addGlobal('_nomFaction', serveurIni('Serveur', 'nomFaction'));
      $twig->addGlobal('_URLIntranet', serveurIni('Serveur', 'url_intranet'));
  });
  /* Associaiton Flight Twig */

  /* Association à la base de donnée */
  Flight::before('start', function(&$params, &$output)
  {
    $host = serveurIni('BDD', 'host');
    $name = serveurIni('BDD', 'name');
    $user = serveurIni('BDD', 'user');
    $mdp = serveurIni('BDD', 'mdp');
    ORM::configure("mysql:host=$host;dbname=$name;charset=utf8");
    ORM::configure('username', "$user");
    ORM::configure('password', "$mdp");
  });
  /* Association à la base de donnée */

  Flight::route('/', function()
  {
    Flight::view()->display('accueil.twig');
  });

  Flight::route('/Histoire', function()
  {
    Flight::view()->display('history.twig');
  });

  Flight::route('/Reglements', function()
  {
    $path = dirname(__FILE__);
    $struct = getStructure($path);

    $names = new ArrayObject();
    foreach ($struct->navigation as $value) {
      $names->append($value);
    }

    $files = new ArrayObject();
    foreach ($struct->contenu as $value) {
      $value->fichier = getFileContent($path, $value->fichier);
      $value->fichier = renderHTMLFromMarkdown($value->fichier);
      $files->append($value);
    }

    Flight::view()->display('rules_lspd.twig', array(
      'index' => $names,
      'content' => $files
    ));
  });

  Flight::route('/liste-ppa', function()
  {
    Flight::view()->display('liste_ppa.twig', array(
      'ppa' => Personne::getPPA(2)
    ));
  });

  Flight::route('/Candidature', function()
  {
    $response = Requests::get('http://' . serveurIni('Serveur', 'url_intranet') . '/recrutement/etat');
    $result = json_decode($response->body)->etat;
    switch ($result) {
      case 0:
        Flight::view()->display('close.twig');
        break;
      case 1:
        Flight::view()->display('candid_lspd.twig');
        break;
      default:
        Flight::view()->display('close.twig');
        break;
    }
  });

  Flight::route('/Amendes', function()
  {
    $amende = Amende::find_many(); // Récupère toutes les amendes
    $routier = Amende::where('type_delit', 1)->order_by_asc('intitule')->find_many();
    $leger = Amende::where('type_delit', 2)->order_by_asc('intitule')->find_many();
    $moyen = Amende::where('type_delit', 3)->order_by_asc('intitule')->find_many();
    $grave = Amende::where('type_delit', 4)->order_by_asc('intitule')->find_many();
    Flight::view()->display('liste_amende.twig', array(
      'all' => $amende,
      'routier' => $routier,
      'leger' => $leger,
      'moyen' => $moyen,
      'grave' => $grave
    ));
  });



  Flight::route('/ajout-candidature', function()
  {
    /* Déclaration de toutes les variables */
    $discord = $_POST['candid_discord'];
    $phone = $_POST['candid_phone'];
    $nom = $_POST['candid_name'];
    $prenom = $_POST['candid_firstname'];
    $age_ig = $_POST['candid_ig'];
    $age_irl = $_POST['candid_irl'];
    $tps_serv = $_POST['candid_server_time'];
    $tps_gta = $_POST['candid_gta_time'];
    $retour_faction = $_POST['candid_faction'];
    if (isset($_POST['candid_detail_faction'])) { $detail_faction = $_POST['candid_detail_faction']; } else { $detail_faction = ""; }

    /* Ecole */
    $candid_time_school = $_POST['candid_time_school'];
    if (isset($_POST['school_lun_am'])) { $school_lun_am = 1; } else { $school_lun_am = 0; }
    if (isset($_POST['school_lun_pm'])) { $school_lun_pm = 1; } else { $school_lun_pm = 0; }
    if (isset($_POST['school_lun_abend'])) { $school_lun_abend = 1; } else { $school_lun_abend = 0; }
    if (isset($_POST['school_lun_night'])) { $school_lun_night = 1; } else { $school_lun_night = 0; }
    if (isset($_POST['school_mar_am'])) { $school_mar_am = 1; } else { $school_mar_am = 0; }
    if (isset($_POST['school_mar_pm'])) { $school_mar_pm = 1; } else { $school_mar_pm = 0; }
    if (isset($_POST['school_mar_abend'])) { $school_mar_abend = 1; } else { $school_mar_abend = 0; }
    if (isset($_POST['school_mar_night'])) { $school_mar_night = 1; } else { $school_mar_night = 0; }
    if (isset($_POST['school_mer_am'])) { $school_mer_am = 1; } else { $school_mer_am = 0; }
    if (isset($_POST['school_mer_pm'])) { $school_mer_pm = 1; } else { $school_mer_pm = 0; }
    if (isset($_POST['school_mer_abend'])) { $school_mer_abend = 1; } else { $school_mer_abend = 0; }
    if (isset($_POST['school_mer_night'])) { $school_mer_night = 1; } else { $school_mer_night = 0; }
    if (isset($_POST['school_jeu_am'])) { $school_jeu_am = 1; } else { $school_jeu_am = 0; }
    if (isset($_POST['school_jeu_pm'])) { $school_jeu_pm = 1; } else { $school_jeu_pm = 0; }
    if (isset($_POST['school_jeu_abend'])) { $school_jeu_abend = 1; } else { $school_jeu_abend = 0; }
    if (isset($_POST['school_jeu_night'])) { $school_jeu_night = 1; } else { $school_jeu_night = 0; }
    if (isset($_POST['school_ven_am'])) { $school_ven_am = 1; } else { $school_ven_am = 0; }
    if (isset($_POST['school_ven_pm'])) { $school_ven_pm = 1; } else { $school_ven_pm = 0; }
    if (isset($_POST['school_ven_abend'])) { $school_ven_abend = 1; } else { $school_ven_abend = 0; }
    if (isset($_POST['school_ven_night'])) { $school_ven_night = 1; } else { $school_ven_night = 0; }
    if (isset($_POST['school_sam_am'])) { $school_sam_am = 1; } else { $school_sam_am = 0; }
    if (isset($_POST['school_sam_pm'])) { $school_sam_pm = 1; } else { $school_sam_pm = 0; }
    if (isset($_POST['school_sam_abend'])) { $school_sam_abend = 1; } else { $school_sam_abend = 0; }
    if (isset($_POST['school_sam_night'])) { $school_sam_night = 1; } else { $school_sam_night = 0; }
    if (isset($_POST['school_dim_am'])) { $school_dim_am = 1; } else { $school_dim_am = 0; }
    if (isset($_POST['school_dim_pm'])) { $school_dim_pm = 1; } else { $school_dim_pm = 0; }
    if (isset($_POST['school_dim_abend'])) { $school_dim_abend = 1; } else { $school_dim_abend = 0; }
    if (isset($_POST['school_dim_night'])) { $school_dim_night = 1; } else { $school_dim_night = 0; }
    $ecole = $school_lun_am . '-' . $school_lun_pm . '-' . $school_lun_abend . '-' . $school_lun_night
     . '-' . $school_mar_am . '-' . $school_mar_pm . '-' . $school_mar_abend . '-' . $school_mar_night
     . '-' . $school_mer_am . '-' . $school_mer_pm . '-' . $school_mer_abend . '-' . $school_mer_night
     . '-' . $school_jeu_am . '-' . $school_jeu_pm . '-' . $school_jeu_abend . '-' . $school_jeu_night
     . '-' . $school_ven_am . '-' . $school_ven_pm . '-' . $school_ven_abend . '-' . $school_ven_night
     . '-' . $school_sam_am . '-' . $school_sam_pm . '-' . $school_sam_abend . '-' . $school_sam_night
     . '-' . $school_dim_am . '-' . $school_dim_pm . '-' . $school_dim_abend . '-' . $school_dim_night;
    /* Ecole */

    /* Travail */
    $candid_time_work = $_POST['candid_time_work'];
    if (isset($_POST['work_lun_am'])) { $work_lun_am = 1; } else { $work_lun_am = 0; }
    if (isset($_POST['work_lun_pm'])) { $work_lun_pm = 1; } else { $work_lun_pm = 0; }
    if (isset($_POST['work_lun_abend'])) { $work_lun_abend = 1; } else { $work_lun_abend = 0; }
    if (isset($_POST['work_lun_night'])) { $work_lun_night = 1; } else { $work_lun_night = 0; }
    if (isset($_POST['work_mar_am'])) { $work_mar_am = 1; } else { $work_mar_am = 0; }
    if (isset($_POST['work_mar_pm'])) { $work_mar_pm = 1; } else { $work_mar_pm = 0; }
    if (isset($_POST['work_mar_abend'])) { $work_mar_abend = 1; } else { $work_mar_abend = 0; }
    if (isset($_POST['work_mar_night'])) { $work_mar_night = 1; } else { $work_mar_night = 0; }
    if (isset($_POST['work_mer_am'])) { $work_mer_am = 1; } else { $work_mer_am = 0; }
    if (isset($_POST['work_mer_pm'])) { $work_mer_pm = 1; } else { $work_mer_pm = 0; }
    if (isset($_POST['work_mer_abend'])) { $work_mer_abend = 1; } else { $work_mer_abend = 0; }
    if (isset($_POST['work_mer_night'])) { $work_mer_night = 1; } else { $work_mer_night = 0; }
    if (isset($_POST['work_jeu_am'])) { $work_jeu_am = 1; } else { $work_jeu_am = 0; }
    if (isset($_POST['work_jeu_pm'])) { $work_jeu_pm = 1; } else { $work_jeu_pm = 0; }
    if (isset($_POST['work_jeu_abend'])) { $work_jeu_abend = 1; } else { $work_jeu_abend = 0; }
    if (isset($_POST['work_jeu_night'])) { $work_jeu_night = 1; } else { $work_jeu_night = 0; }
    if (isset($_POST['work_ven_am'])) { $work_ven_am = 1; } else { $work_ven_am = 0; }
    if (isset($_POST['work_ven_pm'])) { $work_ven_pm = 1; } else { $work_ven_pm = 0; }
    if (isset($_POST['work_ven_abend'])) { $work_ven_abend = 1; } else { $work_ven_abend = 0; }
    if (isset($_POST['work_ven_night'])) { $work_ven_night = 1; } else { $work_ven_night = 0; }
    if (isset($_POST['work_sam_am'])) { $work_sam_am = 1; } else { $work_sam_am = 0; }
    if (isset($_POST['work_sam_pm'])) { $work_sam_pm = 1; } else { $work_sam_pm = 0; }
    if (isset($_POST['work_sam_abend'])) { $work_sam_abend = 1; } else { $work_sam_abend = 0; }
    if (isset($_POST['work_sam_night'])) { $work_sam_night = 1; } else { $work_sam_night = 0; }
    if (isset($_POST['work_dim_am'])) { $work_dim_am = 1; } else { $work_dim_am = 0; }
    if (isset($_POST['work_dim_pm'])) { $work_dim_pm = 1; } else { $work_dim_pm = 0; }
    if (isset($_POST['work_dim_abend'])) { $work_dim_abend = 1; } else { $work_dim_abend = 0; }
    if (isset($_POST['work_dim_night'])) { $work_dim_night = 1; } else { $work_dim_night = 0; }
    $work = $work_lun_am . '-' . $work_lun_pm . '-' . $work_lun_abend . '-' . $work_lun_night
    . '-' . $work_mar_am . '-' . $work_mar_pm . '-' . $work_mar_abend . '-' . $work_mar_night
    . '-' . $work_mer_am . '-' . $work_mer_pm . '-' . $work_mer_abend . '-' . $work_mer_night
    . '-' . $work_jeu_am . '-' . $work_jeu_pm . '-' . $work_jeu_abend . '-' . $work_jeu_night
    . '-' . $work_ven_am . '-' . $work_ven_pm . '-' . $work_ven_abend . '-' . $work_ven_night
    . '-' . $work_sam_am . '-' . $work_sam_pm . '-' . $work_sam_abend . '-' . $work_sam_night
    . '-' . $work_dim_am . '-' . $work_dim_pm . '-' . $work_dim_abend . '-' . $work_dim_night;
    /* Travail */

    /* Vacance */
    $candid_time_vacances = $_POST['candid_time_vacances'];
    if (isset($_POST['vacances_lun_am'])) { $vacances_lun_am = 1; } else { $vacances_lun_am = 0; }
    if (isset($_POST['vacances_lun_pm'])) { $vacances_lun_pm = 1; } else { $vacances_lun_pm = 0; }
    if (isset($_POST['vacances_lun_abend'])) { $vacances_lun_abend = 1; } else { $vacances_lun_abend = 0; }
    if (isset($_POST['vacances_lun_night'])) { $vacances_lun_night = 1; } else { $vacances_lun_night = 0; }
    if (isset($_POST['vacances_mar_am'])) { $vacances_mar_am = 1; } else { $vacances_mar_am = 0; }
    if (isset($_POST['vacances_mar_pm'])) { $vacances_mar_pm = 1; } else { $vacances_mar_pm = 0; }
    if (isset($_POST['vacances_mar_abend'])) { $vacances_mar_abend = 1; } else { $vacances_mar_abend = 0; }
    if (isset($_POST['vacances_mar_night'])) { $vacances_mar_night = 1; } else { $vacances_mar_night = 0; }
    if (isset($_POST['vacances_mer_am'])) { $vacances_mer_am = 1; } else { $vacances_mer_am = 0; }
    if (isset($_POST['vacances_mer_pm'])) { $vacances_mer_pm = 1; } else { $vacances_mer_pm = 0; }
    if (isset($_POST['vacances_mer_abend'])) { $vacances_mer_abend = 1; } else { $vacances_mer_abend = 0; }
    if (isset($_POST['vacances_mer_night'])) { $vacances_mer_night = 1; } else { $vacances_mer_night = 0; }
    if (isset($_POST['vacances_jeu_am'])) { $vacances_jeu_am = 1; } else { $vacances_jeu_am = 0; }
    if (isset($_POST['vacances_jeu_pm'])) { $vacances_jeu_pm = 1; } else { $vacances_jeu_pm = 0; }
    if (isset($_POST['vacances_jeu_abend'])) { $vacances_jeu_abend = 1; } else { $vacances_jeu_abend = 0; }
    if (isset($_POST['vacances_jeu_night'])) { $vacances_jeu_night = 1; } else { $vacances_jeu_night = 0; }
    if (isset($_POST['vacances_ven_am'])) { $vacances_ven_am = 1; } else { $vacances_ven_am = 0; }
    if (isset($_POST['vacances_ven_pm'])) { $vacances_ven_pm = 1; } else { $vacances_ven_pm = 0; }
    if (isset($_POST['vacances_ven_abend'])) { $vacances_ven_abend = 1; } else { $vacances_ven_abend = 0; }
    if (isset($_POST['vacances_ven_night'])) { $vacances_ven_night = 1; } else { $vacances_ven_night = 0; }
    if (isset($_POST['vacances_sam_am'])) { $vacances_sam_am = 1; } else { $vacances_sam_am = 0; }
    if (isset($_POST['vacances_sam_pm'])) { $vacances_sam_pm = 1; } else { $vacances_sam_pm = 0; }
    if (isset($_POST['vacances_sam_abend'])) { $vacances_sam_abend = 1; } else { $vacances_sam_abend = 0; }
    if (isset($_POST['vacances_sam_night'])) { $vacances_sam_night = 1; } else { $vacances_sam_night = 0; }
    if (isset($_POST['vacances_dim_am'])) { $vacances_dim_am = 1; } else { $vacances_dim_am = 0; }
    if (isset($_POST['vacances_dim_pm'])) { $vacances_dim_pm = 1; } else { $vacances_dim_pm = 0; }
    if (isset($_POST['vacances_dim_abend'])) { $vacances_dim_abend = 1; } else { $vacances_dim_abend = 0; }
    if (isset($_POST['vacances_dim_night'])) { $vacances_dim_night = 1; } else { $vacances_dim_night = 0; }
    $vacances = $vacances_lun_am . '-' . $vacances_lun_pm . '-' . $vacances_lun_abend . '-' . $vacances_lun_night
        . '-' . $vacances_mar_am . '-' . $vacances_mar_pm . '-' . $vacances_mar_abend . '-' . $vacances_mar_night
        . '-' . $vacances_mer_am . '-' . $vacances_mer_pm . '-' . $vacances_mer_abend . '-' . $vacances_mer_night
        . '-' . $vacances_jeu_am . '-' . $vacances_jeu_pm . '-' . $vacances_jeu_abend . '-' . $vacances_jeu_night
        . '-' . $vacances_ven_am . '-' . $vacances_ven_pm . '-' . $vacances_ven_abend . '-' . $vacances_ven_night
        . '-' . $vacances_sam_am . '-' . $vacances_sam_pm . '-' . $vacances_sam_abend . '-' . $vacances_sam_night
        . '-' . $vacances_dim_am . '-' . $vacances_dim_pm . '-' . $vacances_dim_abend . '-' . $vacances_dim_night;
    /* Vacance */

    $objectif = $_POST['candid_unite']; // --------------------------------
    $candid_motivations = $_POST['candid_motivations'];

    $candid_intervention = $_POST['candid_swat']; // --------------------------------
    $candid_k9 = $_POST['candid_k9']; // --------------------------------
    $concat = $_POST['candid_weapon'] . '-' . $_POST['candid_lieu'] . '-' . $_POST['code_de_la_route'] . '-' . $_POST['candid_ordre'];

    ajout_bdd($discord, $nom, $prenom, $phone, $age_ig, $age_irl, $tps_serv, $tps_gta, $retour_faction, $detail_faction, $candid_time_school, $ecole, $candid_time_vacances, $vacances, $candid_time_work, $work, $objectif, $candid_motivations, $candid_intervention, $candid_k9, $concat);

    echo "Votre candidature à bien été prise en compte !</br><p><a href='/'>Retourner vers la page principale</a></p>";
  });

  Flight::start(); // Denrière ligne du fichier
?>
