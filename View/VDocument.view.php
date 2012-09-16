<?php

class VDocument
{
	public function __constructor () { return; }
	public function __destructor  () { return; }
	
	public function ShowHome ()
	{
		echo <<<HERE
	<center><h1>Bienvenue sur la GED</h1>
	<h5>Version 2011</h5></center>
	
HERE;
	}
	
	// Comment = 'ajout' ou 'modify'
	// si 'ajout', qui vaut le n° du theme dans lequel ajouter le document
	// si 'modify', qui vaut le n° du document a modifier
	public function ShowFormDoc ($comment, $qui)
	{
		if ($comment == 'ajout')
		{
			$form = <<<HERE
<form id="form" action="../Php/Ged.php?EX=uploadDoc" method="post" onsubmit="return verifForm(this);" enctype="multipart/form-data">
HERE;
			$document['ID_THEME'] = $qui;
			
			$quoi = <<<HERE
<input type="hidden" id="theme" name="ID_THEME" value="{$document['ID_THEME']}" />
HERE;
		}
		else if ($comment == 'modif')
		{
			$form = <<<HERE
<form id="form" action="../Php/Ged.php?EX=modifyDoc" method="post" onsubmit="return verifForm(this);" enctype="multipart/form-data">
HERE;

			$mdocument = new MDocuments($qui);
			$document = $mdocument->Select_doc();
			
			$quoi = <<<HERE
<input type="hidden" id="document" name="ID_DOC" value="{$document['ID_DOC']}" />
HERE;
			
			foreach ($document['MOT_CLES'] as $motcle)
			{
				$mot_cles .= $motcle; 
				$mot_cles .= ' '; 
			}
			$mot_cles = substr($mot_cles, 0, -1);
		}
		
		$label_fichier = ($document['PDF']) ? 'Changer ' : 'Ajouter ';
		$label_fichier .= 'fichier';
		
		echo <<<HERE
  $form
   <fieldset id="connect">
    <legend>Insertion <button class="save" title="Sauvegarder"></button></legend>
    <p><label for="titre">Titre</label>
    <input id="titre" name="TITRE_DOC" class="mandatory" value="{$document['TITRE_DOC']}" size="15" maxlength="250"/></p>
    <p><label for="auteur">Auteur</label>
    <input id="auteur" name="AUTEUR" value="{$document['AUTEUR']}" size="15" maxlength="250"/></p>
    <p><label for="date">Date</label>
    <input id="date" name="DATE_DOC" value="{$document['DATE_DOC']}" size="15" maxlength="250"/></p>
    <p><label for="keyword">Mots cles</label>
    <input id="keyword" name="MOTCLEFS" value="$mot_cles" size="15" maxlength="250"/></p>
    <p><label for="file">$label_fichier</label>
    <input type="file" id="file" name="NOM_FICHIER" size="15" maxlength="250"/></p>
    $quoi
   </fieldset>
  </form>
HERE;

	} // ShowFormDoc()

    function ShowDoc ($doc)
    {
    	$mdocument = new MDocuments($doc);
    	$_doc = $mdocument->Select_doc();

    	$inconnu = 'inconnu';
        $nb = count($_doc['MOT_CLES']);
        if ($nb != 0)
        {
            $mots_cles = '<tr><td id="L1Carac">Mots clefs : </td><td>';
            foreach ($_doc['MOT_CLES'] as $mot_cle)
                $mots_cles .= '"' . $mot_cle . '" ';
            $mots_cles .= '</td></tr>';
        }

        $titre = $_doc['TITRE_DOC']; // pas besoin de tester s'il est valide
                                     // car mandatory + cannot be null (bdd)
        $nom = ($_doc['NOM_FICHIER']) ? $_doc['NOM_FICHIER'] : $inconnu;
        $auteur = ($_doc['AUTEUR']) ? $_doc['AUTEUR'] : $inconnu;
        $date = ($_doc['DATE_DOC']) ? $_doc['DATE_DOC'] : $inconnu;
        
        if ($_doc['PDF'] == 1)
        {
        	$delPdf = <<<HERE

    <button title="Supprimer le fichier" class="delPdf" onclick="deleteDoc('content', '../Php/Ged.php', 'fichier')"></button>
HERE;
            $pdf = '<tr><td id="L1Carac">Fichier : </td>
                <td><a href=../Doc/'.$nom.'><button class="pdf"></button></a></td></tr>';
        }

        echo <<<HERE
 <table class="tableau">
  <thead>
    <th id="Head" class="doc" colspan="2" >$titre<br>
    <button title="Imprimer (non fonctionnel)" class="print"></button>
    <button title="Supprimer le document" class="delete" onclick="deleteDoc('content', '../Php/Ged.php', 'document')"></button>$delPdf
    <button title="Modifier le document" class="modify" onclick="changeContent('content', '../Php/Ged.php', 'EX=formDoc&DOC=$doc')"></button></th>
  </thead>
  <tbody id="Corps">
    <tr><td id="L1Carac">Auteur :</td><td>$auteur</td></tr>
    <tr><td id="L1Carac">Date : </td><td>$date</td></tr>
    $mots_cles
    $pdf
    <input type="hidden" id="ID_DOC" value="{$_doc['ID_DOC']}" />
  </tbody>
 </table>

HERE;

    } // ShowDoc()
    
    function ShowDocInsert ($doc)
    {
    	echo '<h2>Document ajouté</h2>';
    	$this->ShowDoc($doc);

    } // ShowDocInsert()
    
    function ShowDocModify ($doc)
    {
    	echo '<h2>Document modifié</h2>';
    	$this->ShowDoc($doc);
    
    } // ShowDocInsert()
    
    public function ShowFormConnect ()
    {
    	echo <<<HERE
      <form id="form" action="#" method="post" onsubmit="return resultatFormConnect('header', '../Php/Ged.php', 'EX=envoieForm')">
       <fieldset id="connect">
        <legend>Connection</legend>
        <p><label for="login">Login</label>
        <input type"text" id="login" name="LOGIN" class="mandatory" value="" size="15" maxlength="250"/></p>
        <p><label for="pass">Password</label>
        <input type="password" id="pass" name="PASS" class="mandatory" value="" size="15" maxlength="250"/></p>
        <p><input class="button" type="submit" name="OK" value="Ok" /></p>
       </fieldset>
      </form>
HERE;
    
    } // ShowFormConnect()
    
    public function EnteteNonConnecte ()
    {
    	$CONTROLER = CONTROLER;
    	echo <<<HERE
    <ol id="entete">
     <li id='connect' class='liste'><a class="pointer" onclick="changeContent('content', '../Php/Ged.php', 'EX=connect')">Connexion</a></li>
     <li><a href="mailto:contact@contact.net">Contact</a></li>
     <li>
      <form method="post" onsubmit="return rechercher('content', '$CONTROLER', 'EX=rechercher')">
       <input size="9" id="recherche" value="Rechercher"/>
      </form>
     </li>
    </ol>
HERE;
    
    } // EnteteNonConnecte()
    
    public function EnteteConnecte ($user)
    {
    	echo <<<HERE
    <ol id="entete">
     <li><a href="mailto:contact@contact.net">Contact</a></li>
     <li>
      <form method="post" onsubmit="return rechercher()">
       <input size="9" id="recherche" value="Rechercher"/>
       <input type="hidden" type="submit", value="OK"/>
      </form>
     </li>
     <li class="liste"><a href="#">$user</a>
      <ol class="drop-down">
       <li class="pointer"><a onclick="changeContent('content', '../Php/Ged.php', 'EX=themes')">Thèmes</a></li>
       <li class="pointer"><a href="../Php/Ged.php?EX=chargement">Chargement</a></li>
       <li><a href="Ged.php">Déconnexion</a></li>
      </ol>
     </li>
    </ol>
HERE;
    
    } // EnteteConnecte()
    
	public function ShowNav ($_themes, $comment)
	{
		$nb = count ($_themes['ID_THEME']);
  		for ($i = 0, $tr = ''; $i < $nb; ++$i)
  		{
  	 		$li .= <<<HERE
  	 		
  <li class=pointer onclick="changeContent('content', '../Php/Ged.php', 'EX=liste&THEME={$_themes['ID_THEME'][$i]}')"><a>{$_themes['LIBELLE'][$i]}</a></li>
HERE;
  		}
  		/* */
  		$li .= <<<HERE
  		
  <li class=pointer onclick="changeContent('content', '../Php/Ged.php', 'EX=themes')"><a>THEMES</a></li>
HERE;
  		/* */
		echo <<<HERE
 <h1 id="logo" title="Logo" onclick="window.open ('../Php/Ged.php', '_self')")>Logo</h1>
 <ol id="menu" class="$comment">$li
 </ol>
HERE;
		
	} // ShowNav
	
	public function ShowListeDoc ($_docs)
	{
		$nb = count ($_docs['ID_DOC']);
		for ($i = 0, $tr = ''; $i < $nb; ++$i)
		{
			if ($_docs['PDF'][$i] == 1)
			$pdf = '<a href=../Doc/'.$_docs['NOM_FICHIER'][$i].'><button title="Télécharger le PDF" class="pdf"></button></a>';
			else $pdf = '';
				
			$mod = $i%2;
	
			$tr .= <<<HERE
	
	<tr class="ligne$mod">
	 <td><a class="pointer" onclick="changeContent('content', '../Php/Ged.php', 'EX=ficheDoc&DOC={$_docs['ID_DOC'][$i]}')">{$_docs['TITRE_DOC'][$i]}</a>$pdf</td>
	 <td>{$_docs['AUTEUR'][$i]}</td>
	</tr>
HERE;
		}
	
		$boutonNew = (!$_docs['theme']) ? '' : <<<HERE
	
	  <button title="Nouveau" class="new" onclick="changeContent('content', '../Php/Ged.php', 'EX=formDoc&THEME={$_docs['theme']}')"></button>
HERE;
	
		echo <<<HERE
	 <table id="liste" class="tableau">
	  <thead>
	   <th id="Head" class="theme" colspan="2">
	    {$_docs['titre']}<br>
		<button title="Imprimer (non fonctionnel)" class="print"></button>$boutonNew
	   </th>
	   <tr>
	    <th class="pointer" onclick="triBulle ('liste', 0)">Titre</th>
	    <th class="pointer" onclick="triBulle ('liste', 0)">Auteur</th>
	   </tr>
	  </thead>
	  <tbody>$tr
	  </tbody>
	 </table>
	 
HERE;
	
	} // ShowListeDoc()
	
	function ShowListeThemes ()
	{
		$mthemes = new MThemes();
		$themes = $mthemes->Select_all_themes();
	
		for ($i = 0, $tr = ''; $i < count($themes['ID_THEME']); ++$i)
		{
			$themeCourant = $themes['ID_THEME'][$i];
			$mod = $i%2;
			$tr .= <<<HERE
	<tr class="ligne$mod" id="theme$themeCourant"><td align="center" onclick="changeContent('theme$themeCourant', '../Php/Ged.php', 'EX=activerChangement&THEME=$themeCourant')">{$themes['LIBELLE'][$i]}</td></tr>
	
HERE;
	
		}
	
		echo <<<HERE
	 <table class="tableau">
	  <thead>
	    <th id="Head" class="themes">Liste des thèmes</th>
	  </thead>
	  <tbody>
		$tr
	    <tr><td class='inputTheme'>
	     <form method="post" onsubmit="return saveTheme('../Php/Ged.php')">
	      <input id="LIBELLE" size=10 value="Nouveau" />
	      <input id="THEME" value="0" type="hidden" />
	     </form>
	    </td>
	   </tr>
	  </tbody>
	 </table>
HERE;
	
	} // ShowListeThemes()
	
	function ShowMauvaiseExtension ()
	{
		echo '<h2>Mauvaise extension</h2>'; 
		
	} // ShowMauvaiseExtension
	
	
	public function ShowMauvaisEx ()
	{
		echo <<<HERE
  <h2 class="erreur">Mauvaise variable d'exécution</h2>
HERE;
	} // ShowMauvaisEx()

}; //VDocument

?>