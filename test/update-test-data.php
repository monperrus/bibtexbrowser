<?
/* 
update the test data:

then a bzr diff is the regression test

*/
$_GET['test']=1;
include(dirname(__FILE__).'/../bibtexbrowser.php');

$testbibs = array (
  dirname(__FILE__).'/input/bibacid-iso8859.bib',
  dirname(__FILE__).'/input/all.bib',
  dirname(__FILE__).'/input/metrics.bib',
  dirname(__FILE__).'/input/strings.bib;'.dirname(__FILE__).'/input/entries.bib'
);

foreach ($testbibs as $k) {
  echo $k."\n";
  $db = new BibDataBase();
  $output = array();
  foreach(explode(MULTIPLE_BIB_SEPARATOR,$k) as $bibfile) {
    $db->load($bibfile);
    $output[] = basename($bibfile);
  }
  $output = implode(';', $output).'.txt';

  // sorting the entries
  ksort($db->bibdb);

  // sorting the fields
  foreach($db->bibdb as $bib) {
    ksort($bib->fields);
  }
  
  $result = '';
  foreach($db->bibdb as $bib) {
    $result .= var_export($bib->fields, true)."\n";
  }
  //echo $result;
  file_put_contents(dirname(__FILE__).'/output/'.$output,$result);
}

?>