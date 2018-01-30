<?PHP
require_once "../src/Bibliography.php";

$_GET['bib']="bibacid-utf8.bib";
$_GET['all']=1;
$shit = new Monperrus\BibtexBrowser\Bibliography();
