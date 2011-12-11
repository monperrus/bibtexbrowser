<?
// testing the recompilation
// bug found by Sébastien Lion
$_GET['test']=1;
include(dirname(__FILE__).'/../bibtexbrowser.php');

$a = tempnam('/tmp','').".bib";
$b = tempnam('/tmp','').".bib";

file_put_contents($a, "@misc{f,title=foo}");
file_put_contents($b, "@misc{g,title=bar}");

sleep(2);

$_GET['bib']=$a.";".$b;

function boolString($bValue = false) {                      // returns string
  return ($bValue ? 'true' : 'false');
}

$result = '';
$result .= 'should be true:'.boolString(setDB())."\n";
$result .= 'should be false:'.boolString(setDB())."\n";
$result .= 'should be false:'.boolString(setDB())."\n";

touch($a);
sleep(2);
$result .= 'should be true:'.boolString(setDB())."\n";
$result .= 'should be false:'.boolString(setDB())."\n";

touch($b);
sleep(2);
$result .= 'should be true:'.boolString(setDB())."\n";
$result .= 'should be false:'.boolString(setDB())."\n";

echo $result;
file_put_contents('output/04_setDB.txt',$result);

// clean
unlink($a);
unlink($b);

?>