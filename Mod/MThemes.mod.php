<?php
class MThemes
{
  private $conn; // connexion à la Base de Données
  private $the_values; // tableau de récupération de plusieurs tuples
  private $value; // tableau de récupération d'un tuple ou de gestion de données (insert ou update)
  
  private $id;
 
  public function __construct ($theme = NULL)
  {
    // connexion à la Base de Données
    $this->conn = oci_connect (LOGIN, PASSWORD);
  	
    $this->id = $theme;

  } // __construct ()

  public function __destruct ()
  {
    oci_close ($this->conn);

  } // __destruct()

  public function Set_value ($_value) { $this->value = $_value; }
  
  function Select_all_themes ()
  {
  	$query = 'select ID_THEME,
  					 LIBELLE
  			  from THEME_DOC
  	          order by LIBELLE';
  	
  	$result = oci_parse ($this->conn, $query);
    oci_execute ($result) or die ('erreur themes');
    oci_fetch_all ($result, $this->the_values, null, null, OCI_FETCHSTATEMENT_BY_COLUMN);

    oci_free_statement($result);

    return $this->the_values;
  	
  } // Select_all_themes()

  function Select_theme ($theme)
  {
  	$query = "select LIBELLE
  			  	  from THEME_DOC
  	  	          where ID_THEME = $theme";

  	$result = oci_parse ($this->conn, $query);
    oci_execute ($result) or die ('erreur theme');
    $this->value = oci_fetch_array ($result, OCI_RETURN_NULLS);

    oci_free_statement($result);

    return $this->value['LIBELLE'];
    
  } // Select_theme()
  
  public function Modify ($_type)
  {
  	switch ($_type)
  	{
  	  case 'insert' : $this->Insert (); break;
  	  case 'update' : $this->Update (); break;
  	  case 'delete' : $this->Delete (); break;
  	}
   	
  	return;
  	
  } // Modify()
  
  private function Insert ()
  {
    $query = 'select max(ID_THEME) as ID_THEME
              from THEME_DOC';

    $result = oci_parse ($this->conn, $query);
    oci_execute ($result);
    $value = oci_fetch_assoc ($result);
    if (!$value['ID_THEME'])
     $this->id = 1;
    else
     $this->id = ++$value['ID_THEME'];
    
    $ID_THEME = $this->id;
    $LIBELLE = $this->value['LIBELLE'];
    
    $all_themes = $this->Select_all_themes();
    foreach ($all_themes['LIBELLE'] as $libelle)
    	if ($libelle == $LIBELLE) return;
    
    $query = "insert into THEME_DOC
              values ($ID_THEME, '$LIBELLE')";
    
    $result = oci_parse ($this->conn, $query);
    oci_execute ($result);

    oci_free_statement($result);
    
    return;

  } // Insert()
  
  private function Update ()
  {
    $ID_THEME = $this->id;
    $LIBELLE = $this->value['LIBELLE'];

  	$query = "update THEME_DOC
              set LIBELLE = '$LIBELLE'
              where ID_THEME = $ID_THEME";

    $result = oci_parse ($this->conn, $query);
    oci_execute ($result);

    oci_free_statement($result);
    
    return;

  } // Update()
  
  private function Delete ()
  {
    $query = "delete from THEME_DOC
              where ID_THEME = $this->id";
    
    $result = oci_parse ($this->conn, $query);
    oci_execute ($result);

    oci_free_statement($result);
    
    return;

  } // Delete ()

}; // class MDocuments 
?>