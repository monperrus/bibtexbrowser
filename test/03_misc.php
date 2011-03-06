<?

$_GET['test']='';
define('BIBTEXBROWSER_SCRIPT',dirname(__FILE__).'/../bibtexbrowser.php');
include(BIBTEXBROWSER_SCRIPT);

ob_start();
function testshortopentags() {
preg_match_all('/<\?[\s$\/]/',file_get_contents(BIBTEXBROWSER_SCRIPT),$matches);
//echo count($matches[0]);
return array('short PHP tag &lt;? ',count($matches[0])==0);
}


function testLatex2html0() {
 $str='\`a\`{a}';
 //echo htmlentities(latex2html($str));
 return array('transformation of accents',latex2html($str)=='&agrave;&agrave;');
}

function testLatex2html1() {
 //echo htmlentities(latex2html('&'));
 //echo htmlentities(latex2html('\&'));
 return array('transformation of ampersamd',latex2html('\&')=='&amp;');
}

function testLatex2html2() {
 $str='\~nsdf~sd\~a';
 //echo htmlentities(latex2html($str));
 return array('transformation of non breaking space',latex2html($str)=='&ntilde;sdf&nbsp;sd&atilde;');
}

function testLatex2html3() {
 $str='asd {sdsdf}';
 return array('transformation of curly braces (serge\'s bug)',latex2html($str)=='asd sdsdf');
}


function test_search1() {
// see http://www.monperrus.net/martin/bibtexbrowser.php?search=banach+space&bib=bibacid.bib
$_GET['bib']='input/bibacid-iso8859.bib';
setDB();
 $query= array (Q_SEARCH=>'banach space');
 $selected = $_GET[Q_DB]->multisearch($query);
 return array('search for banach space (latex markup)',count($selected)==5);
}

// see http://www.monperrus.net/martin/bibtexbrowser.php?search=g%F6del&bib=bibacid.bib

function test_search2() {
$_GET['bib']='input/bibacid-iso8859.bib';
setDB();
 $query= array (Q_SEARCH=>'gödel');
 $selected = $_GET[Q_DB]->multisearch($query);
//echo var_export($selected);
 return array('search for goedel (latex and accents)',count($selected)==5);
}

function test_search2a() {
$_GET['bib']='input/bibacid-iso8859.bib';
setDB();
 $query= array (Q_SEARCH=>'g'.html_entity_decode('&ouml;').'del');
 $selected = $_GET[Q_DB]->multisearch($query);
  //echo var_export($selected);
 return array('search for goedel (latex and accents)',count($selected)==5);
}

function test_search2b() {
$_GET['bib']='input/bibacid-iso8859.bib';
setDB();
 $query= array (Q_SEARCH=>'g'.utf8_encode(html_entity_decode('&ouml;')).'del');
 $selected = $_GET[Q_DB]->multisearch($query);
  //echo var_export($selected);
 return array('search for goedel (latex and accents)',count($selected)==5);
}


function test_search3() {
$_GET['bib']='input/metrics.bib';
setDB();
 $query= array (Q_SEARCH=>'ocl');
 $selected = $_GET[Q_DB]->multisearch($query);
 return array('search for OCL',count($selected)==4);
}

function test_search4() {
$_GET['bib']='input/metrics.bib';
setDB();
 $query= array (Q_TYPE=>'book');
 $selected = $_GET[Q_DB]->multisearch($query);
 return array('search for TYPE:book',count($selected)==17);
}

$tests = array(
  'testLatex2html0',
  'testLatex2html1',
  'testLatex2html2',
  'testLatex2html3',
  'testshortopentags',
  'test_search1',
  'test_search2',
  'test_search2a',
  'test_search2b',
  'test_search3',
  'test_search4',
  
  );

foreach ($tests as $test) {
 echo "running ".$test.": ";
 list($description,$result)=$test();
 echo "\n";
//  $color= $result?'green':'red';
//  echo '<span style="color:'.$color.'">'.$description.'</span><br/>';
 $symbol= $result?'  -- ':'  XX ';
 echo $symbol.$description."\n";
}

$testresult = ob_get_clean();
file_put_contents('output/03_misc.txt',$testresult);
echo $testresult;
?>

