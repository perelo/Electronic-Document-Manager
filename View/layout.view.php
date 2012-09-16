<?php

global $page;
$vpage = new $page['class'];

global $entete;
$ventete = new VDocument;

echo '<?xml version="1.0" encoding="utf-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
 <title><?=$page['title']?></title>
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
 <link rel="icon" type="image/png" href="<?=ICONE_PAGE?>" />
 <style type="text/css">@import url('<?=CSS_PAGE?>');</style>
 <script type="text/javascript" src="<?=JS_PAGE?>"></script>
</head>

<body>

<div id="header">
 <?php
  $ventete->$entete['method']($entete['arg'])
 ?>
</div><!-- id="header" -->

<div id="nav">
 <?php
  $vnav = new VDocument();
  $mthemes = new MThemes;
  $vnav->ShowNav($mthemes->Select_all_themes(), $page['css_menu']);
 ?>
</div><!-- id="nav" -->

<div id="content">
 <?php $vpage->$page['method']($page['arg']) ?>
</div><!-- id="content" -->

</body>
</html>