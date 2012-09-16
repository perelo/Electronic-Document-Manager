<?php
class MDocuments
{
  private $conn; // connexion à la Base de Données
  private $the_values; // tableau de récupération de plusieurs tuples
  private $value; // tableau de récupération d'un tuple ou de gestion de données (insert ou update)
 
  private $id;
  
  public function __construct ($id_doc = NULL)
  {
    // connexion à la Base de Données
    $this->conn = oci_connect (LOGIN, PASSWORD);
    
    $this->id = $id_doc;

  } // __construct ()

  public function __destruct ()
  {
    oci_close ($this->conn);

  } // __destruct()

  public function Set_value ($_value) { $this->value = $_value; }
  
  public function Select_all_docs ($theme)
  {
    $query = "select ID_DOC,
                     TITRE_DOC,
                     NOM_FICHIER,
                     AUTEUR,
                     PDF
              from DOCUMENTS
              where ID_THEME = $theme";

    $result = oci_parse ($this->conn, $query);
    oci_execute ($result) or die ('<h2>erreur all doc</h2>');
    oci_fetch_all ($result, $this->the_values, null, null, OCI_FETCHSTATEMENT_BY_COLUMN);

    oci_free_statement($result);

    return $this->the_values;

  } // Select_all_docs()
  
  function Select_doc ()
  {
	$query = "select ID_DOC,
                     TITRE_DOC,
                     NOM_FICHIER,
                     AUTEUR,
                     DATE_DOC,
                     PDF,
                     NOM_FICHIER
              from DOCUMENTS
              where ID_DOC = $this->id";

	$result = oci_parse ($this->conn, $query);
  	oci_execute ($result) or die ('<h2>Erreur select doc</h2>');
  	$this->value = oci_fetch_array($result, OCI_RETURN_NULLS);

  	oci_free_statement($result);
  	
  	$this->value['MOT_CLES'] = $this->Select_keywords();

  	return $this->value;
  	
  } // Select_doc()
  
  function Select_keywords ()
  {
  	$query = "select LIBELLE
  	          from MOTCLEFS
  	          where ID_DOC = $this->id";
  	 
  	$result = oci_parse ($this->conn, $query);
  	oci_execute ($result) or die ('<h2>erreur keywords</h2>');
  	oci_fetch_all ($result, $this->the_values, null, null, OCI_FETCHSTATEMENT_BY_COLUMN);

  	oci_free_statement($result);
  	
  	return $this->the_values['LIBELLE'];

  } // Select_keywords()
  
  function Select_nom_doc ()
  {
  	$query = "select TITRE_DOC,
  	  			  	  from DOCUMENTS
  	  	  	          where ID_DOC = $this->id";
  	
  	$result = oci_parse ($this->conn, $query);
  	oci_execute ($result) or die ('<h2>erreur nom doc</h2>');
  	$this->value = oci_fetch_array ($result, OCI_RETURN_NULLS);

  	oci_free_statement($result);

  	return $this->value['TITRE_DOC'];

  } // Select_nom_doc()
  
  public function Search_docs ($mot)
  {
  	$mot = strtoupper($mot);
    $query = "select ID_DOC,
                     TITRE_DOC,
                     NOM_FICHIER,
                     AUTEUR,
                     PDF
              from DOCUMENTS
              where upper(TITRE_DOC) = '$mot' or
                    upper(AUTEUR) = '$mot' or
                    ID_DOC IN (
                      select ID_DOC
                      from MOTCLEFS
                      where upper(LIBELLE) = '$mot')";

    $result = oci_parse ($this->conn, $query);
    oci_execute ($result) or die ('<h2>erreur search</h2>');
    oci_fetch_all ($result, $this->the_values, null, null, OCI_FETCHSTATEMENT_BY_COLUMN);

    oci_free_statement($result);

    return $this->the_values;

  } // Search_docs()
  
  public function Modify ($_type)
  {
    switch ($_type)
    {
      case 'insert'  : return $this->Insert    (); // on renvoie l'id
      case 'modify'  : return $this->Update    (); // on renvoie l'id
      case 'deleteDocument' : $this->DeleteDoc (); break;
      case 'deleteFichier'  : $this->DeletePdf (); break;
    }
    
    return;
    
  } // Modify()
  
  private function Insert ()
  {
  	// Ajout du document
    $query = "select max(ID_DOC) as ID_DOC
              from DOCUMENTS";

    $result = oci_parse ($this->conn, $query);
    oci_execute ($result) or die ('<h2>erreur max doc</h2>');;
    $value = oci_fetch_assoc ($result);

    if (!$value['ID_DOC'])
     $this->id = 1;
    else
     $this->id = ++$value['ID_DOC'];
    
    $ID_DOC = $this->id;
    $TITRE_DOC = ($this->value['TITRE_DOC']) ? '\''.$this->value['TITRE_DOC'].'\'' : 'NULL';
    $AUTEUR = ($this->value['AUTEUR']) ? '\''.$this->value['AUTEUR'].'\'' : 'NULL';
    $DATE_DOC = ($this->value['DATE_DOC']) ? '\''.$this->value['DATE_DOC'].'\'' : 'NULL';
    $ID_THEME = $this->value['ID_THEME'];
    $KEYWORDS = explode(' ', $this->value['MOTCLEFS']);
    
    $query = "insert into DOCUMENTS
              values ($ID_DOC,
                      $TITRE_DOC,
                      $AUTEUR,
                      $DATE_DOC,
                      NULL,
                      $ID_THEME,
                      NULL)";
    // les deux NULL = pas de fichier pdf associé au document pour l'instant

    $result = oci_parse ($this->conn, $query);
    oci_execute ($result) or die ('<h2>erreur insert doc</h2>');
    oci_free_statement($result);

    // Ajout de ses mot clefs
    if (count($KEYWORDS) == 0) return;
    $query = "select max(ID_MC) as ID_MC
              from MOTCLEFS";
    
    $result = oci_parse ($this->conn, $query);
    oci_execute ($result) or die ('<h2>erreur max mot cles</h2>');;
    $value = oci_fetch_assoc ($result);

    if (!$value['ID_MC'])
     $id_mc = 1;
    else
     $id_mc = ++$value['ID_MC'];
    
    foreach ($KEYWORDS as $keyword)
    {
     $query = "insert into MOTCLEFS
               values ($id_mc,
                       '$keyword',
                       $this->id)";

     $result = oci_parse ($this->conn, $query);
     oci_execute ($result) or die ('<h2>erreur insert mot cle</h2>');;
     oci_free_statement($result);
     ++$id_mc;
    }

    return $this->id;

  } // Insert()
  
  private function Update ()
  {
  	$ID_DOC = $this->id;
  	$TITRE_DOC = ($this->value['TITRE_DOC']) ? '\''.$this->value['TITRE_DOC'].'\'' : 'NULL';
  	$AUTEUR = ($this->value['AUTEUR']) ? '\''.$this->value['AUTEUR'].'\'' : 'NULL';
  	$DATE_DOC = ($this->value['DATE_DOC']) ? '\''.$this->value['DATE_DOC'].'\'' : 'NULL';
  	$ID_THEME = $this->value['ID_THEME'];
  	$KEYWORDS = explode(' ', $this->value['MOTCLEFS']);
  
  	$query = "update DOCUMENTS
  	          set TITRE_DOC = $TITRE_DOC,
  	              AUTEUR = $AUTEUR,
  	              DATE_DOC = $DATE_DOC
  	           where ID_DOC = $this->id";
  
  	$result = oci_parse ($this->conn, $query);
  	oci_execute ($result) or die ('<h2>Erreur update doc</h2>');
  	oci_free_statement($result);
  
  	// Modification de ses mot clefs
  	// suppression des anciens puis ajout des nouveaux
  	if (count($KEYWORDS) == 0) return;
  	
  	$query = "delete from MOTCLEFS
  	          where ID_DOC = $this->id";
  	
  	$result = oci_parse ($this->conn, $query);
  	oci_execute ($result) or die ('<h2>Erreur del mot cles</h2>');;
  	$value = oci_fetch_assoc ($result);

  	$query = "select max(ID_MC) as ID_MC
                from MOTCLEFS";
  
  	$result = oci_parse ($this->conn, $query);
  	oci_execute ($result) or die ('<h2>Erreur max mot cles</h2>');;
  	$value = oci_fetch_assoc ($result);
  
  	if (!$value['ID_MC'])
  	$id_mc = 1;
  	else
  	$id_mc = ++$value['ID_MC'];
  
  	foreach ($KEYWORDS as $keyword)
  	{
  		$query = "insert into MOTCLEFS
                  values ($id_mc,
                          '$keyword',
  		$this->id)";
  
  		$result = oci_parse ($this->conn, $query);
  		oci_execute ($result) or die ('<h2>Erreur insert mot cle</h2>');;
  		oci_free_statement($result);
  		++$id_mc;
  	}

  	return $this->id;

  } // Update()
  
  private function DeleteDoc ()
  {
  	// Supprimer le pdf avant le tuple
  	$this->DeletePdf();
  	
  	$query = "delete from MOTCLEFS
  	          where ID_DOC = $this->id";
  	
  	$result = oci_parse ($this->conn, $query);
    oci_execute ($result) or die ('<h2>Erreur suppression mot cles</h2>');
    oci_free_statement($result);
  	
  	$query = "delete from DOCUMENTS
  	          where ID_DOC = $this->id";
  	
  	$result = oci_parse ($this->conn, $query);
    oci_execute ($result) or die ('<h2>Erreur suppression doc</h2>');
    oci_free_statement($result);
    
    return;
  	
  } // DeleteDoc()
  
  function DeletePdf ()
  {
  	$query = "select NOM_FICHIER
  	  	          from DOCUMENTS
  	  	          where ID_DOC = $this->id";
  	 
  	$result = oci_parse ($this->conn, $query);
  	oci_execute ($result) or die ('<h2>Erreur selection pdf</h2>');
  	$this->value = oci_fetch_array ($result, OCI_RETURN_NULLS);
  	 
  	 unlink('../Doc/' . $this->value['NOM_FICHIER']);
  	 
  	 $query = "update DOCUMENTS
  	   	          set PDF = NULL,
  	   	              NOM_FICHIER = NULL
  	   	           where ID_DOC = $this->id";
  	 
  	 $result = oci_parse ($this->conn, $query);
  	 oci_execute ($result) or die ('<h2>Erreur update document</h2> '.$query);;
  	 oci_free_statement($result);
  	 
  	 return;

  } // DeletePdf()
  
  function AjoutPdf ($pdf)
  {
  	$query = "update DOCUMENTS
  	          set PDF = 1,
  	              NOM_FICHIER = '$pdf'
  	           where ID_DOC = $this->id";

  	$result = oci_parse ($this->conn, $query);
  	oci_execute ($result) or die ('<h2>Erreur update document</h2> '.$query);;
  	oci_free_statement($result);
  	
  	return;
  }
  
}; // class MDocuments 
?>