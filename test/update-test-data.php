<?
/* 
update the test data:

then a bzr diff is the regression test

*/
$_GET['test']=1;
include('../bibtexbrowser.php');

$testbibs = array (
  'bibacid-iso8859.bib',
  'all.bib',
  'metrics.bib',
  'strings.bib;entries.bib'
);

foreach ($testbibs as $k) {
  $db = new BibDataBase();
  foreach(explode(MULTIPLE_BIB_SEPARATOR,$k) as $bibfile) {
    $db->load($bibfile);
  }

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
  file_put_contents($k.'.txt',$result);
}

?>