<?php /** Bibtexbrowser style contributed by János Tapolcai
see: [[http://www.monperrus.net/martin/bibtexbrowser]]

It looks like the IEEE transaction style.

Add the following line in "bibtexbrowser.local.php"
<code>
include( 'bibtexbrowser-style-janos.php' );
define('BIBLIOGRAPHYSTYLE','JanosBibliographyStyle');
</code>

[[http://www.monperrus.net/martin/bibtexbrowser-style-janos.php.txt]]

*/
function JanosBibliographyStyle(&$bibentry) {
  $title = $bibentry->getTitle();
  $type = $bibentry->getType();

  $entry=array();

  // author
  if ($bibentry->hasField('author')) {
    $entry[] = $bibentry->formattedAuthors();
  }

  // title
  $title = '"'.$title.'"';
  if ($bibentry->hasField('url')) $title = ' <a href="'.$bibentry->getField("url").'">'.$title.'</a>';
  $entry[] = $title;


  // now the origin of the publication is in italic
  $booktitle = '';

  if (($type=="misc") && $bibentry->hasField("note")) {
    $booktitle = $bibentry->getField("note");
  }

  if ($type=="inproceedings") {
      $booktitle = 'In '.$bibentry->getField(BOOKTITLE);
  }

  if ($type=="incollection") {
      $booktitle = 'Chapter in '.$bibentry->getField(BOOKTITLE);
  }

  if ($type=="article") {
      $booktitle = 'In '.$bibentry->getField("journal");
  }



  //// ******* EDITOR
  $editor='';
  if ($bibentry->hasField(EDITOR)) {
    $editors = $bibentry->getFormattedEditors();
  }

  if ($booktitle!='') {
    if ($editor!='') $booktitle .=' ('.$editor.')';
    $entry[] = '<i>'.$booktitle.'</i>';
  }


  $publisher='';
  if ($type=="phdthesis") {
      $publisher = 'PhD thesis, '.$bibentry->getField(SCHOOL);
  }

  if ($type=="mastersthesis") {
      $publisher = 'Master\'s thesis, '.$bibentry->getField(SCHOOL);
  }
  if ($type=="techreport") {
      $publisher = 'Technical report, '.$bibentry->getField("institution");
  }
  if ($bibentry->hasField("publisher")) {
    $publisher = $bibentry->getField("publisher");
  }

  if ($publisher!='') $entry[] = $publisher;

  if ($bibentry->hasField('volume')) $entry[] =  "vol. ".$bibentry->getField("volume");
  if ($bibentry->hasField('number')) $entry[] =  'no. '.$bibentry->getField("number");

  if ($bibentry->hasField('address')) $entry[] =  $bibentry->getField("address");

  if ($bibentry->hasField('pages')) $entry[] = str_replace("--", "-", "pp. ".$bibentry->getField("pages"));


  if ($bibentry->hasField(YEAR)) $entry[] = $bibentry->getYear();

  $result = implode(", ",$entry).'.';

  // some comments (e.g. acceptance rate)?
  if ($bibentry->hasField('comment')) {
      $result .=  " (".$bibentry->getField("comment").")";
  }

  // add the Coin URL
  $result .=  "\n".$bibentry->toCoins();

  return $result;
}
   
?>
