/**
 * Fonctions utilisées pour les tableaux 
 */

/**
 * Insertion des données dans le tableau
 * 
 * @param object data données du tableau
 * 
 * @return none
 */
function insertTr (data)
{
  var tableau = document.getElementsByTagName ('table')[0];
  var tbody = tableau.getElementsByTagName ('tbody')[0];
  var tr = tbody.getElementsByTagName ('tr');
  var nb_tr = tr.length;

  if (nb_tr)
  {
    var tr_clone = tr[0].cloneNode (true);
    tr_clone.getElementsByTagName ('td')[0].innerHTML = data.NOM;
    tr_clone.getElementsByTagName ('td')[1].innerHTML = data.PRENOM;

    tbody.appendChild (tr_clone);
  }
  else
  {
    createTr (tbody, data);
  }
  
  return;

} // insertTr (data)

/**
 * Création de la première ligne du tableau
 * 
 * @param string tbody élément de type <tbody>
 * @param object data données du tableau
 * 
 * @return none
 */
function createTr (tbody, data)
{
  var tr = document.createElement ('tr') ;
  var td1 = document.createElement ('td') ;
  var td2 = document.createElement ('td') ;

  td1.innerHTML = data.NOM;
  td2.innerHTML = data.PRENOM;

  tr.appendChild (td1);
  tr.appendChild (td2);

  tbody.appendChild (tr);
    
  return;

} // createTr (tbody, data)


var type_tri = 'asc';
var col_tri = 0;

/**
 * Méthode de tri pour les colonnes d'un tableau
 * 
 * @param string id_table identifiant de l'élément table
 * @param int col identifiant de la colonne 
 * @param string type type de tri ('asc' ou 'desc') 
 * 
 * @return none;
 */
function triBulle (id_table, col, type)
{
  var tableau = document.getElementById (id_table);
  var tbody = tableau.getElementsByTagName ('tbody')[0];
  var tr = tbody.getElementsByTagName ('tr');
  var nb_tr = tr.length;
  var text = new Array ();
  var trier = true;
  var tmp_text = null;
  var tmp_tr = null;
  var tr_clone = new Array ();

  col_tri=col;
  type_tri = (type != null)  ? type : type_tri;

  for (var i = 0; i < nb_tr; ++i)
  {
    text[i] = tr[i].getElementsByTagName ('td')[col].innerHTML.toUpperCase ();

    tr_clone[i] = tr[i].cloneNode (true);
  }

  for (i = 0; i < nb_tr && trier; ++i)
  {
    trier = false;
    for (var j = 1; j < nb_tr - i; ++j)
    {
      if (text[j] < text[j-1])
      {
        tmp_text = text[j-1];
        text[j-1] = text[j];
        text[j] = tmp_text;

        tmp_tr = tr_clone[j-1];
        tr_clone[j - 1] = tr_clone[j];
        tr_clone[j] = tmp_tr;

        trier = true;
      }
    }
  }

  for (var i = 0; i < nb_tr; ++i)
  {
     tbody.removeChild(tr[0]);
  }

  if ('desc' == type_tri)
  {
    for (var i = 0; i < nb_tr; ++i)
    {
      tr_clone[i].setAttribute('class', 'ligne' + i%2);
      tbody.appendChild (tr_clone[i]);
      type_tri = 'asc';
    }
  }
  else
  {
    for (var i = nb_tr - 1; i >= 0; --i)
    {
      tr_clone[i].setAttribute('class', 'ligne' + i%2);
      tbody.appendChild (tr_clone[i]);
      type_tri = 'desc';
    }
  }

} // triBulle ()
