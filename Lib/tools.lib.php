<?php

function __autoload($class)
{
  switch ($class[0])
  {
  	case 'V' : require_once ('../View/'.$class.'.view.php'); break;
  	case 'M' : require_once ('../Mod/'.$class.'.mod.php');   break;
  	case 'C' : require_once ('../Class/'.$class.'.class.php');
  }
	
	return;

} // autoload()
 
 /**
 * Affichage pour le débuggage 
 * 
 * @param array $val tableau à afficher
 * 
 * @return none;
 */
function debug ($val)
{
  echo '<pre>DEBUG : '; print_r ($val); echo '</pre>';
  
  return;

} // debug ($val)

?>