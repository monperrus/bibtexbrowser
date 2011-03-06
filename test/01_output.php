<?



// loading bibtexbrowser
$_GET['test']=1;
define('BIBTEXBROWSER_SCRIPT',dirname(__FILE__).'/../bibtexbrowser.php');
include(BIBTEXBROWSER_SCRIPT);
// to simulate a normal web call
$_SERVER['SCRIPT_FILENAME'] = realpath(BIBTEXBROWSER_SCRIPT);

$bib = dirname(__FILE__).'/input/metrics.bib';

$url=array(
//'no bibfile as parameted' => '',
'menu view' => 'bib='.$bib.'&menu',
'all view' => 'bib='.$bib.'&all',
'book view' => 'bib='.$bib.'&type=book',
'year view' => 'bib='.$bib.'&year=2004',
'author view' => 'bib='.$bib.'&author=Victor+R.+Basili',
'non existent author' => 'bib='.$bib.'&author=Victor+R.+Basilis', //
'key view' => 'bib='.$bib.'&key=Basili1996',
'key view tech report' => 'key=SMACCHIA.COM2006&bib='.$bib.'',
'key view book' => 'key=Kan95a&bib='.$bib.'',
//'nonexistent key' => 'bib='.$bib.'&key=Basili1996s', //
'keyword view' => 'bib='.$bib.'&keywords=components',
'search view' => 'bib='.$bib.'&search=ocl',
'multisearch with or parameter ' => 'bib='.$bib.'&author=Martin+Monperrus&type=article|incollection',//
'multisearch with exclude and author' => 'bib='.$bib.'&exclude=workshop&author=Martin+Monperrus',//
'multisearch with author and search' => 'bib='.$bib.'&author=Martin+Monperrus&search=workshop',//
'all & academic view' => 'bib='.$bib.'&all&academic',//
'author & academic view' => 'bib='.$bib.'&author=Martin+Monperrus&academic',//
'author & academic view (deprecated)' => 'bib='.$bib.'&academic=Martin+Monperrus',//
'the default view with frameset' => 'bib='.$bib.'&frameset',//
'RSS feed all' => 'bib='.$bib.'&all&rss',
'RSS feed author' => 'bib='.$bib.'&author=Martin+Monperrus&rss',
/*'included in another page all' => $base.'bibtexbrowser-test-include1.php',
'included in another page all/academic' => $base.'bibtexbrowser-test-include2.php',
'included in another page author' => $base.'bibtexbrowser-test-include3.php',
'included in another page author/academic' => $base.'bibtexbrowser-test-include4.php',
'included no bib file' => $base.'bibtexbrowser-test-include5.php',
'Test Library and IndependentYearMenu' => $base.'bibtexbrowser-test-include6.php',
'include and key' => $base.'bibtexbrowser-test-include7.php',
'all view' => 'bib=../bib/strings.bib;../bib/entries.bib&all'*/
);


// checking if XML (XHTML) is correct
foreach($url as $desc => $query_string) {

  parse_str($query_string, $_GET);
  //echo var_export($_GET);
  
  $_SERVER['HTTP_HOST']='localhost';
  $_SERVER['SERVER_PORT']='80';
  $_SERVER['REQUEST_URI']=$query_string;
  
  ob_start();
  $db = new Dispatcher();
  $data = ob_get_clean();
  
  if (!preg_match('/<.xml/',$data)) {
    $data = '<?xml version="1.0" encoding="'.ENCODING.'"?>'.$data;
  };
  file_put_contents(dirname(__FILE__).'/output/'.md5($query_string).".html", $data);

  //echo "<a href=\"${x}\">${desc}</a>: "; 
  //echo "<pre>".htmlentities($data)."</pre>"; 
  // true is really important
  $xmlparser = xml_parser_create('ISO-8859-1');

  if (xml_parse($xmlparser,$data,true) == 1) {
    echo "-- ".$query_string."\n";
  }
  else {   
    echo "XX ".$query_string."\n";
    echo "   ".md5($data).".html\n";
    echo xml_error_string  ( xml_get_error_code  (  $xmlparser  ))."<br/>";
    echo xml_get_current_line_number  ( $xmlparser  )."<br/>";
    echo xml_get_current_column_number  ( $xmlparser  )."<br/>";
  }
}


?>