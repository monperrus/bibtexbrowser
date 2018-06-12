<?PHP
// $_GET['all']=1;
@define('BIBTEXBROWSER_DEFAULT_FRAME', 'all');
//@define('BIBTEXBROWSER_DEFAULT_TEMPLATE','HTMLTemplate');
//
$_GET['bib']='bibacid-utf8.bib';
$_GET['wrapper']='BIBTEXBROWSER_EMBEDDED_WRAPPER';
require( 'bibtexbrowser.php' );
