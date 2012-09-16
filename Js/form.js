/**
 * Travaux Pratiques : Calcul5
 * 
 * Fonctions utilis�es pour les formulaires
 * 
 * @author Christian Bonhomme
 * @version 1.0
 * @package Calcul5
 */

/**
 * V�rification du formulaire 
 * V�rifie que les attributs de type NOT NULL soient renseign�s
 * @param element frm El�ment de type formulaire
 * 
 * return boolean
 */
function verifForm (frm)
{
  var tabLabel = frm.getElementsByTagName ('label'); // contient le tableau des �l�ments de tag label
  var nbLabel = tabLabel.length; // nombre d'�l�ments de tag label

  for (var i = 0, errors = 0, message = ''; i < nbLabel; ++i)
  {
    // R�cup�ration de l'�l�ment i du tableau des �l�ments de tag label
    var elemLabel =  tabLabel[i];

    // Recuperation dans l'�l�ment i du contenu de l'attribut for 
    // correspondant au id de l'�l�ment associe au label (input, select, ...)
    var atFor = elemLabel.getAttribute ('for');

    if (atFor)
    {
      // El�ment associe au label ayant pour id la valeur de contenu dans for
      var elemById = document.getElementById (atFor);

      // R�cup�ration de la valeur de la classe associ�e � l'id r�cup�r�
      var atClass = elemById.getAttribute ('class');

      // Si la class est mandatory et l'�l�ment input de tag label est null alors messsage
      var pattern = /(mandatory)/;
      if (pattern.test(atClass) && !elemById.value)
      {
        // firstChild : premier enfant de l'�l�ment label retourne le noeud (pour un label cela correspond � l'objet texte)
        // nodeValue : valeur du noeud (pour un objet texte cela correspond au texte)
        message += " - " + elemLabel.firstChild.nodeValue + "\n";
        ++errors;
      }
    }
  }

  // Si error est true alors alerte
  if (errors)
  {
    var deb_mes = (errors > 1) ? 'Vous devez renseigner les champs suivants :' : 'Vous devez renseigner le champ suivant :';

    message = deb_mes + '\n' + message;

    window.alert (message);

    return false;
  }

  return true;

} // verifForm (frm)

/**
 * Convertion d'un �v�nement clavier en cha�ne de caract�res
 * 
 * @param event �v�nement du clavier
 * 
 * @return string le caract�re frapp�
 */
function Key2Char (event)
{
  for (prop in event)
  {
    if ('which' == prop)
    {
      return String.fromCharCode (event.which);
    }
  }

  return String.fromCharCode (event.keyCode);

} // Key2Char (event)

/**
 * V�rifie que les entr�es clavier sont de type entier positif
 *  
 * @param event event �v�nement du clavier
 * @param element elem �l�ment de type input
 *
 * @return string le texte valide
 */
function isInteger (event, elem)
{
  var valid = /[0-9]/;
  var speciaux = /[\x00\x08\x0D]/ ;
  var verif = /^[0-9]*$/;

  var car = Key2Char (event);
  var text = elem.value.substr (0, elem.selectionStart) + car + elem.value.substr (elem.selectionEnd + 1);

  return valid.test (car) && verif.test (text) || speciaux.test (car);

} // isInteger (event, elem)
