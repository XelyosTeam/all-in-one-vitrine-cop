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
    'cache' => './cache/twig/',
    // 'debug' => true,
  );

  Flight::register('view', '\Twig\Environment', array($loader, $twigConfig), function ($twig) {
    $twig->addExtension(new \Twig\Extension\DebugExtension()); // Add the debug extension

    /* Paramètre Serveur */
    $twig->addGlobal('_adresseDiscord', serveurIni('Serveur', 'discord'));
    $twig->addGlobal('_nomServeur', serveurIni('Serveur', 'nom'));
    $twig->addGlobal('_jeuServeur', serveurIni('Serveur', 'jeu'));
    $twig->addGlobal('_nomFaction', serveurIni('Serveur', 'nomFaction'));
    $twig->addGlobal('_URLIntranet', serveurIni('Serveur', 'url_intranet'));
    $twig->addGlobal('_Devise', serveurIni('PARAMETRE', 'devise'));
  });
  /* Associaiton Flight Twig */

  /* Association à la base de donnée */
  Flight::before('start', function(&$params, &$output) {
    $host = serveurIni('BDD', 'host');
    $name = serveurIni('BDD', 'name');
    $user = serveurIni('BDD', 'user');
    $mdp = serveurIni('BDD', 'mdp');
    ORM::configure("mysql:host=$host;dbname=$name;charset=utf8");
    ORM::configure('username', "$user");
    ORM::configure('password', "$mdp");
  });
  /* Association à la base de donnée */

  Flight::route('/', function() {
    Flight::view()->display('accueil.twig');
  });

  Flight::route('/Histoire', function() {
    Flight::view()->display('history.twig');
  });

  Flight::route('/Reglements', function() {
    $path = dirname(__FILE__);
    $struct = getStructure($path, 'index');

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

  Flight::route('/liste-ppa', function() {
    Flight::view()->display('liste_ppa.twig', array(
      'ppa' => Personne::getPPA(2)
    ));
  });

  Flight::route('/Candidature', function() {
    $response = Requests::get('http://' . serveurIni('Serveur', 'url_intranet') . '/recrutement/etat');
    $result = json_decode($response->body)->etat;
    switch ($result) {
      case 0:
        Flight::view()->display('close.twig');
        break;
      case 1:
        $path = dirname(__FILE__);
        Flight::view()->display('candid_lspd.twig', array(
          'formulaires' => getStructure($path, 'candidature')
        ));
        break;
      default:
        Flight::view()->display('close.twig');
        break;
    }
  });

  Flight::route('/Candidature/Confirm', function() {
    Flight::view()->display('candid_confirm.twig');
  });

  Flight::route('/Amendes', function() {
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

  Flight::route('/ajout-candidature', function() {
    foreach ($_POST as $key => $value) {
      $_POST[$key] = urlencode($value);
    }
    $datas = json_encode($_POST);

    /* Vérifications fichiers */
    $files = $_FILES['attachments'];

    /* Vérification de la taille des fichiers */
    $tailleMax = 2000000;
    $taille = 0;
    for ($cpt=0; $cpt < count($files['name']); $cpt++) {
      $taille += $files['size'][$cpt];
    }

    // Taille limite dépassée
    if ($taille > $tailleMax) { return Flight::redirect("/candidature"); }

    /* Uploads des fichiers */
    $uploads = uploadFile("assets/documents/", $tailleMax, $files, array('pdf'));

    $version = serveurIni('PARAMETRE', 'version_candidature');

    ajout_bdd($version, $datas, $uploads);

    Flight::redirect('/Candidature/Confirm');
  });

  Flight::map('notFound', function() {
    Flight::view()->display('404.twig');
  });

  Flight::start(); // Denrière ligne du fichier
?>
