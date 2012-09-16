function rechercher (id, php, param)
{
	var mot = document.getElementById('recherche').value;

	param += '&MOT=' + mot;

	changeContent(id, php, param);

	return false;
	
} // rechercher ()

function resultatFormConnect (id, php, param)
{
	  if (!verifForm (document.getElementById('form')))
	  {
	    return false;
	  }

	  var login = document.getElementById('login').value;
	  var pass = document.getElementById('pass').value;

	  param += '&LOGIN='+login+'&PASS='+pass;

	  changeContent (id, php, param); // changement de l'entete si root:toor
	  changeContent ('content', php, 'EX=afficherHome');

	  return false;

} // resultatFormConnect()

function saveTheme (php)
{
	var libelle = encodeURIComponent (document.getElementById('LIBELLE').value);
	var theme = document.getElementById('THEME').value;

	if (theme == 0) // Nouveau
	{
		if (!libelle) return false; // on ne veux pas ajouter de themes sans nom...
		var EX = 'EX=insertTheme';
	}
	else
		var EX = (libelle) ? 'EX=updateTheme' : 'EX=deleteTheme';

	var param = EX;
	param += '&THEME=' + encodeURIComponent (theme);
	param += '&LIBELLE=' + libelle;

	actionForm(php, param);
	changeContent('nav', php, 'EX=refreshNav');
	changeContent('content', php, 'EX=themes');
	
	return false;
	
} // saveTheme ()

function deleteDoc (id, php, quoi) // quoi = 'document' ou 'fichier' (pdf)
{
	var doc = document.getElementById('ID_DOC').value;

	if (!confirm('Voulez-vous vraiment supprimer ce ' + quoi + ' ? ')) return;
	
	param =  'EX=deleteDoc';
	param += '&ID_DOC=' + doc;
	// On doit dire au php si on supprime uniquement le fichier,
	// ou le document (tuple dans DOCUMENTS + fichier)
	param += '&QUOI=' + quoi;

	changeContent(id, php, param);
		
	return;

} // deleteDoc()