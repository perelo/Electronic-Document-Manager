<?php

header ('Content-Type:text/html; charset=UTF-8');

/**
 *  * Récupération des fichiers de l'application
 */
require ('../Inc/ged.inc.php');

$EX = isset ($_REQUEST['EX']) ? $_REQUEST['EX'] : 'home';

switch ($EX)
{
	case 'home'              : home(); break;
	case 'afficherHome'      : afficherHome(); exit;
	case 'liste'             : liste(); exit;
	case 'ficheDoc'          : ficheDocument(); exit; 
	case 'connect'           : formConnect(); exit;
	case 'disconnect'        : home(); break;
	case 'themes'            : themes(); exit;
	case 'chargement'        : chargement(); break;
	case 'consultation'      : consultation(); exit;
	case 'rechercher'        : rechercher(); exit;
	case 'envoieForm'        : envoieForm(); exit;
	case 'activerChangement' : activerChangement(); exit;
	case 'formDoc'           : formDoc(); exit;
	case 'uploadDoc'         : uploadDoc('insert'); break;
	case 'modifyDoc'         : uploadDoc('modify'); break;
	case 'deleteDoc'         : deleteDoc(); exit;
	case 'insertTheme'       : modifyTheme('insert'); exit;
	case 'updateTheme'       : modifyTheme('update'); exit;
	case 'deleteTheme'       : modifyTheme('delete'); exit;
	case 'refreshNav'        : refreshNav(); exit;
	default                  : mauvaisEx(); break;
}

require('../View/layout.view.php');

function home ()
{
	global $page;
	$page['title'] = "Gestion électronique de documents";
	$page['class']  = 'VDocument';
	$page['method'] = 'ShowHome';
	$page['arg']    = '';
	
	global $entete;
	$entete['method'] = 'EnteteNonConnecte';
	$entete['arg']    = '';

} // base()

function afficherHome ()
{
	$home = new VDocument();
	$home->ShowHome();

} // afficherHome()

function liste ()
{
	$theme = $_POST['THEME']; // à sécuriser
	$mdocuments = new MDocuments();
	$documents = $mdocuments->Select_all_docs ($theme);
	
	$mthemes = new MThemes($theme);
	$documents['titre'] = $mthemes->Select_theme($theme) . ' : Liste des documents';
	$documents['theme'] = $theme;

	$vliste = new VDocument();
	$vliste->ShowListeDoc($documents);

} // liste()

function ficheDocument()
{
	$vdocument = new VDocument();
	$vdocument->ShowDoc($_POST['DOC']);

} // ficheDocument()

function formConnect ()
{
	$vconnect = new VDocument();
	$vconnect->ShowFormConnect();

} // formConnect()

function themes ()
{
	$vliste = new VDocument();
	$vliste->ShowListeThemes();

} // themes()

function chargement ()
{
	global $page;
	
	$page['title'] = "Gestion électronique de documents";
	$page['class']  = 'VDocument';
	$page['method'] = 'ShowHome';
	$page['arg']    = '';
	$page['css_menu'] = "consultation";
	
	global $entete;
	$entete['method'] = 'EnteteNonConnecte';
	$entete['arg']    = '';

} // chargement()

function rechercher ()
{
	$mot = $_POST['MOT'];
	$mdocuments = new MDocuments();
	$documents = $mdocuments->Search_docs ($mot);

	$nb = count($documents['ID_DOC']);
	if ($nb == 0)
	{
		echo '<h2>Aucun document ne correspond à ' . $mot . '</h2>';
		return;
	}
	
	$s = ($nb > 1) ? 's' : '';
	$documents['titre'] = 'Document'.$s.' correspondant'.$s.' à '.$mot;

	$vliste = new VDocument();
	$vliste->ShowListeDoc($documents);

	return;
	
} // rechercher()

function envoieForm ()
{
	$connect = $_POST;
	$cconnect = new CConnect($connect);

	global $entete;
	$ventete = new VDocument();
	if ($cconnect->TestLogin())
	{
		$entete['method'] = 'EnteteConnecte';
		$entete['arg']    = $_POST['LOGIN'];
	}
	else
	{
		$entete['method'] = 'EnteteNonConnecte';
		$entete['arg'] = '';
	}
	
	$ventete->$entete['method']($entete['arg']);

} // envoieForm()

function activerChangement ()
{
	$id_theme = $_POST['THEME'];
	$mthemes = new MThemes();
	$libelle_theme = $mthemes->Select_theme($id_theme);

	echo <<<HERE
	
<td class='inputTheme'>
 <form action="#" method="post" onsubmit="return saveTheme('../Php/Ged.php')">
  <input id="LIBELLE" size=10 value="$libelle_theme" />
  <input id="THEME" type="hidden" value="$id_theme" />
 </form>
</td>

HERE;
	
} // activerChangement()

function formDoc ()
{
	$theme = $_POST['THEME'];
	$doc = $_POST['DOC'];

	$vdoc = new VDocument();
	if ($theme) $vdoc->ShowFormDoc('ajout', $theme);
	else        $vdoc->ShowFormDoc('modif', $doc);

} // formDoc()

function modifyTheme ($type)
{
	$theme = $_POST['THEME'];
	$mthemes = new MThemes($theme);
	if ($type != 'delete') $mthemes->Set_value($_POST);
	$mthemes->Modify ($type);
	
} // modifyTheme()

function uploadDoc ($type)
{
	global $page;
	
	$page['title'] = 'Gestion électronique de documents';
	$page['class']  = 'VDocument';

	global $entete;
	$entete['method'] = 'EnteteNonConnecte';
	$entete['arg']    = '';

	$nom = $_FILES['NOM_FICHIER']['name'];
	if ($nom) // on a un fichier a uploader
		if ($_FILES['NOM_FICHIER']['type'] != 'application/pdf')
		{
			$page['method'] = 'ShowMauvaiseExtension';
			$page['arg']    = '';
			return;
		}
	
	$mdocuments = new MDocuments($_POST['ID_DOC']);
	$mdocuments->Set_value($_POST);
	$id_doc = $mdocuments->Modify($type);
	
	$page['method'] = 'ShowDoc' . ucfirst($type);
	$page['arg']    = $id_doc;

	if ($nom) // ajout effectif du pdf
	{
		$mdocuments->AjoutPdf($nom);
		$resultat = move_uploaded_file($_FILES['NOM_FICHIER']['tmp_name'],
	                               '../Doc/'.$nom);
	}
	
	return;

} // uploadDoc()

function deleteDoc ()
{
	$quoi = ucfirst($_POST['QUOI']);
	$mdocuments = new MDocuments($_POST['ID_DOC']);

	$mdocuments->Modify ('delete'.$quoi);

	echo '<h2>' . ucfirst($quoi) . ' supprimé</h2>';

} // modifyDoc()

function refreshNav ()
{
	global $page;
    $vnav = new VDocument;
    $mthemes = new MThemes;
    $vnav->ShowNav($mthemes->Select_all_themes(), $page['css_menu']);

} // refreshNav()

function mauvaisEx ()
{
	global $page;

	$page['title'] = "Gestion électronique de documents";
	$page['class']  = 'VDocument';
	$page['method'] = 'ShowMauvaisEx';
	$page['arg']    = '';

	global $entete;
	$entete['method'] = 'EnteteNonConnecte';
	$entete['arg']    = '';

} // mauvaisEx()

?>