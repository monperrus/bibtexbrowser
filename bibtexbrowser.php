<?php /* bibtexbrowser: a PHP script to browse and search bib entries from BibTex files

[[#Download]] | [[#Screenshot]] | [[#Features]] | [[#Related_tools]] | [[#Users]] | [[#Copyright]]

bibtexbrowser is a PHP script to browse and search bib entries from BibTex files. For instance, on the [[http://www.monperrus.net/martin/bibtexbrowser.php|bibtexbrowser demonstration site]], you can browse my main bibtex file.

For feature requests or bug reports, [[http://www.monperrus.net/martin/|please drop me an email ]].

Thanks to all [[#Users]] of bibtexbrowser :-)

=====Download=====

**[[http://www.monperrus.net/martin/bibtexbrowser.php.txt|Download bibtexbrowser]]**

=====Screenshot=====

<a href="bibtexbrowser-screenshot.png"><img height="500" src="bibtexbrowser-screenshot.png" alt="bibtexbrowser screenshot"/></a>

=====Features=====

* **New (02/2009)** bibtexbrowser can display all entries for an author with an academic style [[http://www.monperrus.net/martin/bibtexbrowser.php?bib=biblio_monperrus.bib&amp;academic=Martin+Monperrus|demo]]
* **New (01/2009)** bibtexbrowser allows multi criteria search, e.g. ?type=inproceedings&amp;year=2004
* **HOT: bibtexbrowser can be used to include your publication list into your home page** [[http://www.monperrus.net/martin/|demo]]
* bibtexbrowser can display the menu and all entries without filtering from the $filename hardcoded in the script [[http://www.monperrus.net/martin/bibtexbrowser.php|demo]]
* bibtexbrowser can display the menu and all entries without filtering from the file name passed as parameter [[http://www.monperrus.net/martin/bibtexbrowser.php?bib=biblio_monperrus.bib|demo]]
* bibtexbrowser can display all entries  out of a bibtex file [[http://www.monperrus.net/martin/bibtexbrowser.php?bib=biblio_monperrus.bib&amp;all|demo]]
* bibtexbrowser can display all entries for a given year [[http://www.monperrus.net/martin/bibtexbrowser.php?bib=biblio_monperrus.bib&amp;year=2004|demo]]
* bibtexbrowser can display a single entry [[http://www.monperrus.net/martin/bibtexbrowser.php?bib=biblio_monperrus.bib&amp;key=monperrus08phd|demo]]
* bibtexbrowser can display all entries with a bib keyword [[http://www.monperrus.net/martin/bibtexbrowser.php?bib=biblio_monperrus.bib&amp;keywords=mda|demo]]
* bibtexbrowser can display found entries with a search word (it can be in any bib field) [[http://www.monperrus.net/martin/bibtexbrowser.php?bib=biblio_monperrus.bib&amp;search=ocl|demo]]
* bibtexbrowser outputs valid XHTML 1.0 Transitional
* bibtexbrowser in designed to be search engine friendly.
* bibtexbrowser can display all entries for an author [[http://www.monperrus.net/martin/bibtexbrowser.php?bib=biblio_monperrus.bib&amp;author=Barbara+A.+Kitchenham|demo]]
* bibtexbrowser can be used with different encodings (change the default iso-8859-1 encoding if your bib file is in utf-8 ''define('ENCODING','utf-8')'' )

=====How to include your publication list in your home page=====

Use this PHP snippet:
&#60;?php
$_GET&#91;'bib'&#93;='mybib.bib';
$_GET&#91;'academic'&#93;='Martin Monperrus';
include('bibtexbrowser.php');
?>


Tailor it with a CSS style, for example:
&#60;style>
.date {   background-color: blue; }
.rheader {  font-size: large }
.bibline {  padding:3px; padding-left:15px;  vertical-align:top;}
&#60;/style>


=====Users=====
Don't hesitate to [[http://www.monperrus.net/martin/|contact me]] to be added in the list!

* [[http://telecom.inescporto.pt/~jsc/bibtexbrowser.php|Jaime dos Santos Cardoso, INESC, Portugal]]
* [[http://ccm.uma.pt/bibtexbrowser.php|Centro de Ciências Matemáticas, Portugal]]
* [[http://bioinfo.lri.fr/publi/bibtexbrowser.php|Bioinformatics Group of LRI, Paris, France]]
* [[http://grapple.dcs.warwick.ac.uk/bibtexbrowser/bibtexbrowser.php|M. Hendrix, University of Warwick, UK]]
* [[http://www.cs.usask.ca/home/eramian/bib/Refereed_Journal_Articles.php|Mark Eramian, University of Saskatchewan, Canada]]
* [[https://www.cs.tcd.ie/~marined/publications.php|Dan Marinescu, Trinity College Dublin, Ireland]]
* [[http://kom.aau.dk/~gpp/publications.php?l=EN|Gian Paolo Perrucci, University of Aalborg, Denmark]]
* [[http://dme.uma.pt/luis/page5/bibtexbrowser.php?bib=jluisbib.bib|José Luís da Silva, Universidade da madeira, Portugal]]

=====Related_tools=====

Old-fashioned:
[[http://nxg.me.uk/dist/bibhtml/|bibhtml]], [[http://www.litech.org/~wkiri/bib2html/|bib2html]], [[http://ilab.usc.edu/bibTOhtml/|bibtohtml]], [[http://people.csail.mit.edu/rahimi/bibtex/|bibtextohtml]], [[http://www.lri.fr/~filliatr/bibtex2html/|bibtex2html]], [[http://people.csail.mit.edu/mernst/software/bibtex2web.html |bibtex2web]], [[http://strategoxt.org/Stratego/BibtexTools|stratego bibtex module]]
Unlike them, **bibtexbrowser is dynamic**.i.e.; generates the HTML pages on the fly.
Thus, you do not need to regenerate the static HTML files each time the bib file is changed.
Furthermore you can search any string in it.

Heavyweight:
[[http://www.rennes.supelec.fr/ren/perso/etotel/PhpBibtexDbMng/|PHP BibTeX Database Manager]], [[http://gforge.inria.fr/projects/bibadmin/|bibadmin]], [[http://artis.imag.fr/Software/Basilic/|basilic]], [[http://phpbibman.sourceforge.net/|phpbibman]], [[http://www.aigaion.nl/|aigaion]], [[http://www.refbase.net/|refbase]], [[http://wikindx.sourceforge.net/|wikindx]]
Unlike them, **bibtexbrowser does not need a MySQL database** and does not need a tedious import step each time the bib file is changed.

Main competitors:
[[http://code.google.com/p/simplybibtex/|SimplyBibtex]] has the same spirit and makes different architectural and presentation choices
=> **bibtexbrowser is much more lightweight** (just one file!).
[[http://www.cs.toronto.edu/~fritz/bibbase/|BibBase]] is a nice and very similar script, but written in Perl
=> **bibtexbrowser does not require a CGI/Perl compliant webserver** .

Misc:
[[http://www.sat.ltu.se/publications/publications.m|This matlab ;-) script is similar ]]

=====Copyright=====

This script is a fork from an excellent script of the University of Texas at El Paso.

(C) 2006-2007-2008-2009 Martin Monperrus
(C) 2005-2006 The University of Texas at El Paso / Joel Garcia, Leonardo Ruiz, and Yoonsik Cheon
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License as
published by the Free Software Foundation; either version 2 of the
License, or (at your option) any later version.

*/

define('ENCODING','iso-8859-1');
//define('ENCODING','utf-8');

define('READLINE_LIMIT',1024);
define('PAGE_SIZE',25);

define('YEAR_SIZE',10);
define('Q_YEAR', 'year');
define('Q_YEAR_PAGE', 'year_page');

define('Q_FILE', 'bib');

define('AUTHORS_SIZE',20);
define('Q_AUTHOR', 'author');
define('Q_AUTHOR_PAGE', 'author_page');

define('TAGS_SIZE',20);
define('Q_TAG', 'keywords');
define('Q_TAG_PAGE', 'keywords_page');

define('Q_TYPE', 'type');
define('Q_TYPE_PAGE', 'type_page');


define('Q_ALL', 'all');
define('Q_ENTRY', 'entry');
define('Q_KEY', 'key');
define('Q_SEARCH', 'search');
define('Q_EXCLUDE', 'exclude');
define('Q_RESULT', 'result');
define('Q_ACADEMIC', 'academic');
define('AUTHOR', 'author');
define('EDITOR', 'editor');
define('SCHOOL', 'school');
define('TITLE', 'title');
define('BOOKTITLE', 'booktitle');
define('YEAR', 'year');

// SCRIPT_NAME is used to create correct links when oncluding a publication list
// in another page
// this constant may have already been initialized
// when using include('')
@define('SCRIPT_NAME',basename(__FILE__));

// for clean search engine links
// we disable url rewriting
// ... and hope that your php configuration will accept one of these
@ini_set("session.use_only_cookies",1);
@ini_set("session.use_trans_sid",0);
@ini_set("url_rewriter.tags","");

// we ensure that the pages won't get polluted
// if future versions of PHP change warning mechanisms...
@error_reporting(E_ERROR);

// default bib file, if no file is specified in the query string.
global $filename;
$filename = "biblio_monperrus.bib";
// retrieve the filename sent as query or hidden data, if exists.
if (isset($_GET[Q_FILE])) {
  $filename = $_GET[Q_FILE];
}

if (!file_exists($filename)) {
 // to automate dectection of faulty links with tools such as webcheck
 header('HTTP/1.1 404 Not found');
 die('<b>the bib file '.$filename.' does not exist !</b>');
}

// save bandwidth and server cpu
// (imagine the number of requests from search engine bots...)
if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && (strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE'])>filemtime($filename))) {
  header("HTTP/1.1 304 Not Modified");
  exit;
}


// for sake of performance, once the bibtex file is parsed
// we try to save a "compiled" in a txt file
$compiledbib = $filename.'.txt';
// do we have a compiled version ?
if (is_file($compiledbib) && is_readable($compiledbib)) {
    // is it up to date ?
    if (filemtime($filename)>filemtime($compiledbib)) {
        // no, then reparse
        $bibdb = new BibDataBase($filename);
    }
    else {
        // yes then take it
        $bibdb = unserialize(file_get_contents($compiledbib));
    }
}
// we don't have a compiled version
else {
    // then parsing the file
    $bibdb = new BibDataBase($filename);

    // are we able to save the compiled version ?
    if ((!is_file($compiledbib) && is_writable(dirname($compiledbib))) || (is_file($compiledbib) && is_writable($compiledbib)) ) {
        // we can use file_put_contents
        // but don't do it for compatibility with PHP 4.3
        $f = fopen($compiledbib,'w');
        fwrite($f,serialize(new BibDataBase($filename)));
        fclose($f);
    }
}

$displaymanager=new DisplayManager($bibdb);






////////////////////////////////////////////////////////

/** This class is a generic parser of bibtex files
 * It has no dependencies, i.e. it can be used outside of bibtexbrowser
 * To use it, simply instantiate it and pass it an object that will receive semantic events
 * The delegate is expected to have some methods
 * see classes BibtexbrowserBibDB and XMLPrettyPrinter
 */
class StateBasedBibtexParser {

function StateBasedBibtexParser($bibfilename, $delegate) {

$f=str_split(file_get_contents($bibfilename));


// STATE DEFINITIONS

define('NOTHING',1);
define('GETTYPE',2);
define('GETKEY',3);
define('GETVALUE',4);
define('GETVALUEDELIMITEDBYQUOTES',5);
define('GETVALUEDELIMITEDBYQUOTES_ESCAPED',6);
define('GETVALUEDELIMITEDBYCURLYBRACKETS',7);
define('GETVALUEDELIMITEDBYCURLYBRACKETS_ESCAPED',8);
define('GETVALUEDELIMITEDBYCURLYBRACKETS_1NESTEDLEVEL',9);
define('GETVALUEDELIMITEDBYCURLYBRACKETS_1NESTEDLEVEL_ESCAPED',10);
define('GETVALUEDELIMITEDBYCURLYBRACKETS_2NESTEDLEVEL',11);
define('GETVALUEDELIMITEDBYCURLYBRACKETS_2NESTEDLEVEL_ESCAPED',12);
define('GETVALUEDELIMITEDBYCURLYBRACKETS_3NESTEDLEVEL',11);
define('GETVALUEDELIMITEDBYCURLYBRACKETS_3NESTEDLEVEL_ESCAPED',12);


$state=NOTHING;
$entrytype='';
$entrykey='';
$entryvalue='';
$finalkey='';
$entrysource='';

// metastate
$isinentry = false;

$delegate->beginFile();

foreach($f as $s) {

 if ($isinentry) $entrysource.=$s;

 if ($state==NOTHING) {
  // this is the beginning of an entry
  if ($s=='@') {
   $delegate->beginEntry();
   $state = GETTYPE;
   $isinentry = true;
   $entrysource='@';
  }
 }

 else if ($state==GETTYPE) {
  // this is the beginning of a key
  if ($s=='{') {
   $state = GETKEY;
   $delegate->setEntryType($entrytype);
   $entrytype='';
   }
  else   $entrytype=$entrytype.$s;
 }

 else if ($state==GETKEY) {
  // now we get the value
  if ($s=='=') {
   $state = GETVALUE;
   $finalkey=$entrykey;
   $entrykey='';}
  // oups we only have the key :-) anyway
  else if ($s=='}') {
   $state = NOTHING;$isinentry = false;$delegate->endEntry($entrysource);
   $delegate->setEntryKey($entrykey);
   $entrykey='';
   }
   // OK now we look for values
  else if ($s==',') {
   $state=GETKEY;
   $delegate->setEntryKey($entrykey);
   $entrykey='';}
  else { $entrykey=$entrykey.$s; }
  }
  // we just got a =, we can now receive the value, but we don't now whether the value
  // is delimited by curly brackets, double quotes or nothing
  else if ($state==GETVALUE) {

    // the value is delimited by double quotes
    if ($s=='"') {
    $state = GETVALUEDELIMITEDBYQUOTES;
    $entryvalue='';}
    // the value is delimited by curly brackets
    else if ($s=='{') {
    $state = GETVALUEDELIMITEDBYCURLYBRACKETS;
    $entryvalue='';}
    // the end of the key and no value found: it is the bibtex key e.g. \cite{Descartes1637}
    else if ($s==',') {
    $state = GETKEY;
    $delegate->setEntryField(trim($finalkey),$entryvalue);
    $entryvalue='';}
    // this is the end of the value AND of the entry
    else if ($s=='}') {
    $state = NOTHING;$isinentry = false;
    $delegate->setEntryField(trim($finalkey),$entryvalue);
    $delegate->endEntry($entrysource);
    $entryvalue='';}
    else { $entryvalue=$entryvalue.$s;}
  }


/* GETVALUEDELIMITEDBYCURLYBRACKETS* handle entries delimited by curly brackets and the possible nested curly brackets */
 else if ($state==GETVALUEDELIMITEDBYCURLYBRACKETS) {

  if ($s=='\\') {
   $state = GETVALUEDELIMITEDBYCURLYBRACKETS_ESCAPED;
   $entryvalue=$entryvalue.$s;}
  else if ($s=='{') {
   $state = GETVALUEDELIMITEDBYCURLYBRACKETS_1NESTEDLEVEL;}
  else if ($s=='}') {
   $state = GETVALUE;}
  else { $entryvalue=$entryvalue.$s;}
 }
  // handle anti-slashed brackets
  else if ($state==GETVALUEDELIMITEDBYCURLYBRACKETS_ESCAPED) {
    $state = GETVALUEDELIMITEDBYCURLYBRACKETS;
    $entryvalue=$entryvalue.$s;
    }
 // in first level of curly bracket
 else if ($state==GETVALUEDELIMITEDBYCURLYBRACKETS_1NESTEDLEVEL) {
  if ($s=='\\') {
   $state = GETVALUEDELIMITEDBYCURLYBRACKETS_1NESTEDLEVEL_ESCAPED;
   $entryvalue=$entryvalue.$s;}
  else if ($s=='{') {
   $state = GETVALUEDELIMITEDBYCURLYBRACKETS_2NESTEDLEVEL;}
  else if ($s=='}') {
   $state = GETVALUEDELIMITEDBYCURLYBRACKETS;}
  else { $entryvalue=$entryvalue.$s;}
 }
  // handle anti-slashed brackets
  else if ($state==GETVALUEDELIMITEDBYCURLYBRACKETS_1NESTEDLEVEL_ESCAPED) {
    $state = GETVALUEDELIMITEDBYCURLYBRACKETS_1NESTEDLEVEL;
    $entryvalue=$entryvalue.$s;
    }

 // in second level of curly bracket
 else if ($state==GETVALUEDELIMITEDBYCURLYBRACKETS_2NESTEDLEVEL) {
  if ($s=='\\') {
   $state = GETVALUEDELIMITEDBYCURLYBRACKETS_2NESTEDLEVEL_ESCAPED;
   $entryvalue=$entryvalue.$s;}
  else if ($s=='{') {
   $state = GETVALUEDELIMITEDBYCURLYBRACKETS_3NESTEDLEVEL;}
  else if ($s=='}') {
   $state = GETVALUEDELIMITEDBYCURLYBRACKETS_1NESTEDLEVEL;}
  else { $entryvalue=$entryvalue.$s;}
  }
  // handle anti-slashed brackets
  else if ($state==GETVALUEDELIMITEDBYCURLYBRACKETS_2NESTEDLEVEL_ESCAPED) {
    $state = GETVALUEDELIMITEDBYCURLYBRACKETS_2NESTEDLEVEL;
    $entryvalue=$entryvalue.$s;
  }

 // in third level of curly bracket
 else if ($state==GETVALUEDELIMITEDBYCURLYBRACKETS_3NESTEDLEVEL) {
  if ($s=='\\') {
   $state = GETVALUEDELIMITEDBYCURLYBRACKETS_3NESTEDLEVEL_ESCAPED;
   $entryvalue=$entryvalue.$s;}
  else if ($s=='}') {
   $state = GETVALUEDELIMITEDBYCURLYBRACKETS_2NESTEDLEVEL;}
  else { $entryvalue=$entryvalue.$s;}
 }
 // handle anti-slashed brackets
 else if ($state==GETVALUEDELIMITEDBYCURLYBRACKETS_3NESTEDLEVEL_ESCAPED) {
  $state = GETVALUEDELIMITEDBYCURLYBRACKETS_3NESTEDLEVEL;
  $entryvalue=$inentryvaluedelimitedA0.$s;
 }

/* handles entries delimited by double quotes */
 else if ($state==GETVALUEDELIMITEDBYQUOTES) {
  if ($s=='\\') {
   $state = GETVALUEDELIMITEDBYQUOTES_ESCAPED;
   $inentryvaluedelimitedB=$inentryvaluedelimitedB.$s;}
  else if ($s=='"') {
   $state = GETVALUE;
   $entryvalue=$entryvalue.$inentryvaluedelimitedB;
   $inentryvaluedelimitedB='';}
  else {   $inentryvaluedelimitedB=$inentryvaluedelimitedB.$s;}
 }
 // handle anti-double quotes
 else if ($state==GETVALUEDELIMITEDBYQUOTES_ESCAPED) {
  $state = GETVALUEDELIMITEDBYQUOTES;
  $inentryvaluedelimitedB=$inentryvaluedelimitedB.$s;
 }

}
$delegate->endFile();
} // end function
} // end class

/** This class can be used together with StateBasedBibParser */
class XMLPrettyPrinter {
  function beginFile() {
    header('Content-type: text/xml;');
    print '<?xml version="1.0" encoding="'.ENCODING.'"?>';
    print '<bibfile>';
  }


  function endFile() {
    print '</bibfile>';
  }
  function setEntryField($finalkey,$entryvalue) {
    print "<data>\n<key>".$finalkey."</key>\n<value>".$entryvalue."</value>\n</data>\n";
  }

  function setEntryType($entrytype) {
    print '<type>'.$entrytype.'</type>';
  }

  function setEntryKey($entrykey) {
    print '<keyonly>'.$entrykey.'</keyonly>';
  }

  function beginEntry() {
    print "<entry>\n";
  }

  function endEntry($entrysource) {
    print "</entry>\n";
  }
} // end class XMLPrettyPrinter

/** This class can be used together with StateBasedBibParser */
class BibtexbrowserBibDB {

  /** A hashtable from keys to bib entries (BibEntry). */
  var $bibdb;

  var $currentEntry;

  function beginFile() {
    $bibdb = array();
  }

  function endFile() { //nothing
  }

  function setEntryField($finalkey,$entryvalue) {

    if ($finalkey!='url') $formatedvalue = xtrim(latex2html($entryvalue));
    else $formatedvalue = $entryvalue;
    $this->currentEntry->setField($finalkey,$formatedvalue);
  }

  function setEntryType($entrytype) {
    $this->currentEntry->setType($entrytype);
  }

  function setEntryKey($entrykey) {
    //echo "new entry:".$entrykey."\n";
    $this->currentEntry->setField('key',$entrykey);
  }

  function beginEntry() {
    $this->currentEntry = new BibEntry();
  }

  function endEntry($entrysource) {
    $this->currentEntry->text = $entrysource;
    $this->bibdb[$this->currentEntry->getKey()] = $this->currentEntry;
  }
} // end class BibtexbrowserBibDB




/** extended version of the trim function
 * removes linebreks, tabs, etc.
 */
function xtrim($line) {
  $line = trim($line);
  // we remove the unneeded line breaks
  // this is *required* to correctly split author lists into names
  $line = str_replace("\n\r",' ', $line);//windows like
  $line = str_replace("\n",' ', $line);//unix-like
  // we also replace tabs
  $line = str_replace("\t",' ', $line);
  // remove superfluous spaces e.g. John+++Bar
  $line = ereg_replace(' {2,}',' ', $line);
  return $line;
}

/** encapsulates the conversion of a single latex chars to the corresponding HTML entity
 * this works thanks to the regularity of html entities
 * it expects a **lower** char
 */
function char2html($line,$latexmodifier,$char,$entitiyfragment) {
  $line = str_replace('\\'.$latexmodifier.$char,'&'.$char.''.$entitiyfragment.';', $line);
  $line = str_replace('\\'.$latexmodifier.'{'.$char.'}','&'.$char.''.$entitiyfragment.';', $line);
  $line = str_replace('\\'.$latexmodifier.strtoupper($char),'&'.strtoupper($char).''.$entitiyfragment.';', $line);
  $line = str_replace('\\'.$latexmodifier.'{'.strtoupper($char).'}','&'.strtoupper($char).''.$entitiyfragment.';', $line);
  return $line;
}

/** converts latex chars to HTML entities
 * it uses a naive algortihm
 * I still look for a comprehensive translation table from late chars to html
 * just have this http://isdc.unige.ch/Newsletter/help.html
 */
function latex2html($line) {
  // performance increases with this test
  if (strpos($line,'\\')===false) return $line;

  foreach(str_split("abcdefghijklmnopqrstuvwxyz") as $letter) {
    $line = char2html($line,"'",$letter,"acute");
    $line = char2html($line,"`",$letter,"grave");
    $line = char2html($line,"~",$letter,"tilde");
    $line = char2html($line,'"',$letter,"uml");
    $line = char2html($line,'^',$letter,"circ");
  }

  // special things
  $line = str_replace('\\\c{c}','&ccedil;', $line);
  $line = str_replace('\\\c{C}','&Ccedil;', $line);

  $line = str_replace('\\o','&oslash;', $line);
  $line = str_replace('\\O','&Oslash;', $line);

  // clean out extra tex curly brackets, usually used for preserving capitals
  $line = str_replace('}','', $line);
  $line = str_replace('{','', $line);


  // and some spaces
  return trim($line);
}


// ----------------------------------------------------------------------
// BIB ENTRIES
// ----------------------------------------------------------------------

/**
 * Class to represent a bibliographic entry.
 */
class BibEntry {

  /** The fields (fieldName -> value) of this bib entry. */


  var $fields;

  /** The verbatim copy (i.e., whole text) of this bib entry. */
  var $text;

  /** Creates an empty new bib entry. Each bib entry is assigned a unique
   * identification number. */
  function BibEntry() {
    static $id = 0;
    $this->id = $id++;
    $this->fields = array();
    $this->text ='';
  }

  /** Returns the type of this bib entry. */
  function getType() {
    // strtolower is important to be case-insensitive
    return strtolower($this->getField(Q_TYPE));
  }

  /** Sets a field of this bib entry. */
  function setField($name, $value) {
    $this->fields[$name] = $value;
  }

  /** Sets a type of this bib entry. */
  function setType($value) {
    $this->fields[Q_TYPE] = $value;
  }


  /** Has this entry the given field? */
  function hasField($name) {
    return array_key_exists(strtolower($name), $this->fields);
  }

  /** Returns the authors of this entry. If no author field exists,
   * returns the editors. If none of authors and editors exists,
   * return a string 'Unknown'. */
  function getAuthor() {
    if (array_key_exists(AUTHOR, $this->fields)) {
      return $this->fields[AUTHOR];
    }
    if (array_key_exists(EDITOR, $this->fields)) {
      return $this->fields[EDITOR];
    }
    return 'Unknown';
  }

  /** Returns the key of this entry */
  function getKey() {
    return $this->getField('key');
  }

  /** Returns the title of this entry? */
  function getTitle() {
    return $this->getField('title');
  }

  /** Returns the authors of this entry as an arry */
  function getAuthors() {
    $authors = array();
    foreach (explode(' and ', $this->getAuthor()) as $author) {
      $authors[]=$author;
    }
    return $authors;
  }

  /** Returns the editors of this entry as an arry */
  function getEditors() {
    $editors = array();
    foreach (explode(' and ', $this->getField(EDITOR)) as $editor) {
      $editors[]=$editor;
    }
    return $editors;
  }

  /**
  * Returns a compacted string form of author names by throwing away
  * all author names except for the first one and appending ", et al."
  */
  function getCompactedAuthors($author){
    $authors = $this->getAuthors();
    $etal = count($authors) > 1 ? ', et al.' : '';
    return formatAuthor($authors[0]) . $etal;
  }


  /** Returns the year of this entry? */
  function getYear() {
    return $this->getField('year');
  }

  /** Returns the value of the given field? */
  function getField($name) {
    if ($this->hasField($name))
 {return $this->fields[strtolower($name)];}
    else return 'missing '.$name;
  }



  /** Returns the fields */
  function getFields() {
    return $this->fields;
  }

  /** Returns the identification number. */
  function getId() {
    return $this->id;
  }

  function getText() {
  /** Returns the verbatim text of this bib entry. */
    return $this->text;
  }

  /** Returns true if this bib entry contains the given phrase
   * in the given field. if $field is null, all fields are considered.
   * Note that this method is NOT case sensitive */
  function hasPhrase($phrase, $field = null) {
    if (!$field) {
      return eregi($phrase,$this->getText());
      //return stripos($this->getText(), $phrase) !== false;
    }
    if ($this->hasField($field) &&  (eregi($phrase,$this->getField($field)) ) ) {
    //if ($this->hasField($field) &&  (stripos($this->getField($field), $phrase) !== false) ) {
      return true;
    }

    return false;
  }


  /** Outputs an HTML string
  */
  function toString() {
        $id = $this->getId();
        $key = $this->getKey();
        $title = $this->getField(TITLE);
        $type = $this->getType();
        $href = makeHref(array(Q_KEY => urlencode($key)));
        echo '<tr>';
        echo '<td  class="bibline"><a name="'.$id.'"></a>['.$id.']</td> ';


        echo '<td>';
        if ($this->hasField('url')) echo ' <a href="'.$this->getField("url").'">';
        echo '<b>'.$title.'</b>';
        if ($this->hasField('url')) echo '</a>';

        if ($this->hasField('author')) {
          $authors = array();
          foreach ($this->getAuthors() as $author) {
            $authors[]=formatAuthor($author);
          }
          echo ' ('.implode(', ',$authors).')';
        }


        if (($type=="phdthesis") ) {
            echo " <i>PhD thesis, ".$this->getField(SCHOOL)."</i>";
        }

        if (($type=="mastersthesis") ) {
            echo " <i>Master's thesis, ".$this->getField(SCHOOL)."</i>";
        }

        if (($type=="techreport") ) {
            echo " <i>Technical report, ".$this->getField("institution")."</i>";
        }

        if (($type=="inproceedings") ) {
            echo " In <i>".$this->getField(BOOKTITLE)."</i>";
        }

        if (($type=="incollection")) {
            echo " Chapter in <i>".$this->getField(BOOKTITLE)."</i>";
        }

        if ($type=="article") {
            echo " In <i>".$this->getField("journal")."</i>";
            echo ", volume ".$this->getField("volume");
        }

        if ($this->hasField(EDITOR)) {
          $editors = array();
          foreach ($this->getEditors() as $editor) {
            $editors[]=formatAuthor($editor);
          }
          echo ' <i>('.implode(', ',$editors).', '.(count($editors)>1?'eds.':'ed.').')</i>';
        }
        echo ", ".$this->getYear().".";
        echo " <a {$href}>[bib]</a>";

        if ($this->hasField('url')) {
            echo ' <a href="'.$this->getField("url").'">[pdf]</a>';
        }

        if ($this->hasField('doi')) {
            echo ' <a href="http://dx.doi.org/'.$this->getField("doi").'">[doi]</a>';
        }

        echo '</td></tr>';

   }

   /**
   * Displays a unformated (verbatim) text of the given bib entry.
   * The text is displayed in <pre><code></code></pre> tag.
   * The object may be mutated to read the rest of the fields.
   */
  function toEntryUnformatted() {
    $text =$this->getText();
    ?>
    <!-- Note that the indentation does matter in the PRE tag -->
<pre><code><?php echo $text; ?></code></pre>
    <?php
   }

}

// ----------------------------------------------------------------------
// DISPLAY MANAGEMENT
// ----------------------------------------------------------------------

/**
 * Given a query, an array of key value pairs, returns a href string
 * of the form: href="bibtex.php?bib=testing.bib&search=JML.
 */
function makeHref($query = NULL) {
  global $filename;

  $qstring = Q_FILE .'='. urlencode($filename);
  if ($query) {
    foreach ($query as $key => $val) {
      $qstring .= '&amp;'. $key .'='. $val;
    }
  }
  return 'href="'. SCRIPT_NAME .'?'. $qstring .'"';
}

/**
 * Returns the last name of an author name. The argument is assumed to be
 * <FirstName LastName> or <LastName, FirstName>.
 */
function getLastName($author){
    $author = trim($author);
    // the author format is "Joe Dupont"
    if (strpos($author,',')===false) {
 $parts=explode(' ', $author);
 // get the last name
 return array_pop($parts);
    }
    // the author format is "Dupont, J."
    else {
 $parts=explode(',', $author);
 // get the last name
 return array_shift($parts);
    }
}

/**
 * Returns the formated author name.
 */
function formatAuthor($author){
  return trim($author);
}


/**
 * A class providing GUI views and controllers. In general, the views
 * are tables that can be incorporated into bigger GUI tables.
 */
class DisplayManager {

  /** The bibliographic database, an instance of class BibDataBase. */
  var $db;

  /** The result to display */
  var $display;

  /** Creates a new display manager that uses the given bib database. */
  function DisplayManager(&$db) {
    $this->db =$db;
  }

  /** Displays the title in a table. */
  function titleView() {
    global $filename;
    ?>
    <table>
      <tr>
        <td class="title">Generated from <?php echo $filename; ?></td>
      </tr>
    </table>
    <?php
  }

  /** Displays the search view in a form. */
  function searchView() {
    global $filename;
    ?>
    <form action="<?php echo SCRIPT_NAME; ?>" method="get" target="main">
      <input type="text" name="<?php echo Q_SEARCH; ?>" class="input_box" size="18"/>
      <input type="hidden" name="<?php echo Q_FILE; ?>" value="<?php echo $filename; ?>"/>
      <br/>
      <input type="submit" value="search" class="input_box"/>
    </form>
    <?php
  }

  /** Displays the main menu in a table. */
  function tocView() {
    $yearHref = makeHref();
    ?>
    <table class="menu">
      <tr>
        <td class="header"><b>List All</b></td>
      <tr>
        <td align="right">
          <a <?php echo $yearHref; ?>>By year</a>
          <div class="mini_se"></div>
        </td>
      </tr>
    </table>
    <?php
  }

  /** Displays and controls the types menu in a table. */
  function typeVC() {
      $types = array();
      foreach ($this->db->getTypes() as $type) {
        $types[$type] = $type;
      }
    // retreive or calculate page number to display
    if (isset($_GET[Q_TYPE_PAGE])) {
      $page = $_GET[Q_TYPE_PAGE];
    }
    else $page = 1;

    $this->displayMenu('Types', $types, $page, 10, Q_TYPE_PAGE, Q_TYPE);
  }

  /** Displays and controls the authors menu in a table. */
  function authorVC() {
    // retrieve authors list to display
      $authors = $this->db->authorIndex();

    // determine the authors page to display
    if (isset($_GET[Q_AUTHOR_PAGE])) {
      $page = $_GET[Q_AUTHOR_PAGE];
    }
    else $page = 1;


    $this->displayMenu('Authors', $authors, $page, AUTHORS_SIZE, Q_AUTHOR_PAGE,
         Q_AUTHOR);
  }

  /** Displays and controls the tag menu in a table. */
  function tagVC() {
    // retrieve authors list to display
      $tags = $this->db->tagIndex();

    // determine the authors page to display
    if (isset($_GET[Q_TAG_PAGE])) {
      $page = $_GET[Q_TAG_PAGE];
    }  else $page = 1;


    $this->displayMenu('Keywords', $tags, $page, TAGS_SIZE, Q_TAG_PAGE,
         Q_TAG);
  }

  /** Displays and controls the tag menu in a table. */
  function yearVC() {
    // retrieve authors list to display
      $years = $this->db->yearIndex();

    // determine the authors page to display
    if (isset($_GET[Q_YEAR_PAGE])) {
      $page = $_GET[Q_YEAR_PAGE];
    }
else $page = 1;


    $this->displayMenu('Years', $years, $page, YEAR_SIZE, Q_YEAR_PAGE,
         Q_YEAR);
  }

  /** Displays the main contents . */
  function mainVC() {
      $this->display->display();
  }

  /** Process the GET parameters */
  function processRequest() {

    global $filename;
    $this->display = null;

    if (@$_GET[Q_KEY]!=''){
      if (isset($this->db->bibdb[$_GET[Q_KEY]])) {
        $this->display = new BibEntryDisplay(
        $this->db->getEntryByKey($_GET[Q_KEY]));
        }
      else { header('HTTP/1.1 404 Not found'); $this->display = new  ErrorDisplay(); }
    } else if(isset($_GET[Q_ALL])) {
      $to_find = $_GET[Q_ALL];
      $searched = array_values($this->db->bibdb);
      $header = 'Bibtex entries';
      $this->display = new PagedDisplay($searched, $header, array(Q_ALL =>''));
    }
    else if(isset($_GET[Q_ACADEMIC])) {
      $this->display = new AcademicDisplay($_GET[Q_ACADEMIC], $this->db);
    }
    else {
      $query = array();
      if (@$_GET[Q_EXCLUDE]!='') { $query[Q_EXCLUDE]=$_GET[Q_EXCLUDE]; }
      if (@$_GET[Q_SEARCH]!='') { $query[Q_SEARCH]=$_GET[Q_SEARCH]; }
      if (@$_GET[Q_AUTHOR]!='') { $query[Q_AUTHOR]=$_GET[Q_AUTHOR]; }
      if (@$_GET[Q_TAG]!='') { $query[Q_TAG]=$_GET[Q_TAG]; }
      if (@$_GET[Q_YEAR]!='') { $query[Q_YEAR]=$_GET[Q_YEAR]; }
      if (@$_GET[Q_TYPE]!='') { $query[Q_TYPE]=$_GET[Q_TYPE]; }
      //print_r($query);
      if (count($query)<1) return false;
      $searched = $this->db->multisearch($query);
      $headers = array();
      foreach($query as $k=>$v) $headers[] = ucwords($k).': '.ucwords($v);
      $header = join(' &amp; ',$headers);
      $this->display = new PagedDisplay($searched, $header, $query);
     }


    // adding the bibtex filename
    if (isset($this->display)) $this->display->header.=' in '.$filename;

    // requesting a different page of the result view?
    if (isset($this->display) && isset($_GET[Q_RESULT])) {
      $this->display->setPage($_GET[Q_RESULT]);
      // we add the page number to the title

      // in order to have unique titles
      // google prefers that
      $this->display->header.=' - page '.$_GET[Q_RESULT];
    }


    // return true if bibtexbrowser has found something to do
    return $this->display!==null;
  }

  /** Displays a list menu in a table.
   *
   * $title: title of the menu (string)
   * $list: list of menu items (string)
   * $page: page number to display (number)
   * $pageSize: size of each page
   * $pageKey: URL query name to send the page number to the server
   * $targetKey: URL query name to send the target of the menu item
   */
  function displayMenu($title, $list, $page, $pageSize, $pageKey,
         $targetKey) {
    $numEntries = count($list);
    $startIndex = ($page - 1) * $pageSize;
    $endIndex = $startIndex + $pageSize;
    ?>
    <table class="menu">
      <tr>
        <td>
        <table border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="header"><b><?php echo $title; ?></b></td>
            <td class="header" align="right"><b>
                <?php echo $this->menuPageBar($pageKey, $numEntries, $page,
           $pageSize, $startIndex, $endIndex);?></b></td>
   </tr>
        </table>
        </td>
      </tr>
      <tr>
        <td align="right">
          <?php $this->displayMenuItems($list, $startIndex, $endIndex,
     $targetKey); ?>
        </td>
      </tr>
    </table>
  <?php
  }

  /** Returns a string to displays forward and reverse page controls.
   *
   * $queryKey: key to send the page number as a URL query string
   * $page: current page number to display
   * $numEntries: number of menu items
   * $start: start index of the current page
   * $end: end index of the current page
   */
  function menuPageBar($queryKey, $numEntries, $page, $pageSize,
         $start, $end) {
    // fast (2 pages) reverse (<<)
    $result = '';
    if ($start - $pageSize > 0) {
      $href = makeHref(array($queryKey => $page - 2,'menu'=>''));
      $result .= '<a '. $href ."><b>&#171;</b></a>\n";
    }

    // (1 page) reverse (<)
    if ($start > 0) {
      $href = makeHref(array($queryKey => $page - 1,'menu'=>''));
      $result .= '<a '. $href ."><b>&lt;</b></a>\n";
    }

    // (1 page) forward (>)
    if ($end < $numEntries) {
      $href = makeHref(array($queryKey => $page + 1,'menu'=>''));
      $result .= '<a '. $href ."><b>&gt;</b></a>\n";
    }

    // fast (2 pages) forward (>>)
    if (($end + $pageSize) < $numEntries) {
      $href = makeHref(array($queryKey => $page + 2,'menu'=>''));
      $result .= '<a '. $href ."><b>&#187;</b></a>\n";
    }
    return $result;
  }

  /**
   * Displays menu items (anchors) from the start index (inclusive) to
   * the end index (exclusive). For each menu, the following form of
   * string is printed:
   *
   * <a href="...?bib=cheon.bib&search_author=Yoonsik+Cheon">
   *    Cheon, Yoonsik</a>
   * <div class="mini_se"></div>
   */
  function displayMenuItems($items, $startIndex, $endIndex, $queryKey) {
    $index = 0;
    foreach ($items as $key => $item) {
      if ($index >= $startIndex && $index < $endIndex) {
 $href = makeHref(array($queryKey => urlencode($key)));
 echo '<a '. $href .' target="main">'. $item ."</a>\n";
 echo "<div class=\"mini_se\"></div>\n";
      }
      $index++;
    }
  }
}

/** Class to display a search result, a list of bib entries. */
class Display {
  /** the header string. */
  var $header;

  function setPage($page) { /* unimplemented */ }

  function display() { /* unimplemented */ }

  /** Returns the powered by part */
  function poweredby() {
    $poweredby = "\n".'<div style="text-align:right;font-size: xx-small;opacity: 0.6;" class="poweredby">';
    $poweredby .= '<!-- If you like bibtexbrowser, thanks to keep the link :-) -->';
    $poweredby .= 'Powered by <a href="http://www.monperrus.net/martin/bibtexbrowser/">bibtexbrowser</a>';
    $poweredby .= '</div>'."\n";
    return $poweredby;
   }

  function  formatedHeader() { return "<div class=\"rheader\">{$this->header}</div>\n";}


}

/** Class to display a result as a set of pages. */
class PagedDisplay extends Display {
  /** the bib entries to display. */
  var $result;

  /** the page number to display. */
  var $page;

  /** the total number of pages to display. */
  var $noPages;

  /** the start index to display. */
  var $startIndex;

    /** the end index to display. */
  var $endIndex;

/** the original filter author, year, etc */
  var $filter;

  /** the content strategy (cf. pattern strategy) */
  var $contentStrategy;

  /** Creates an instance with the given entries and header. */
  function PagedDisplay(&$result, $header,$filter) {
    $this->result = $result;
    $this->header = $header;
    $this->page = 1;
    $this->filter = $filter;
    $this->contentStrategy = new DefaultContentStrategy();
  }

  /** Sets the page number to display. */
  function setPage($page) {
    $this->page = $page;
  }


  /** Displays the entries preceded with the header. */
  function display() {

    $page = $this->page;

    // print error message if no entry.
    if (empty($this->result)) {
      echo "<b>No match found!</b>\n";
      return;
    }

    $this->noPages = ceil(count($this->result) / PAGE_SIZE);

    if ($this->noPages>1) $this->displayPageBar($this->noPages, $page);

    $this->startIndex = ($page - 1) * PAGE_SIZE;
    $this->endIndex =$this->startIndex + PAGE_SIZE;

      /** Displays the header stringg. */
     if ($this->header!="")  echo $this->formatedHeader();

    $this->contentStrategy->display($this);
     if ($this->noPages>1) $this->displayPageBar($this->noPages, $page);

     echo $this->poweredby();

  }

  function isDisplayed($index) {
    if ($index >= $this->startIndex && $index < $this->endIndex) return true;
    return false;
  }

  /** Displays a page bar consisting of clickable page numbers. */
  function displayPageBar($noPages, $page) {
    $barSize = 10; // *2
    $start = ($page - $barSize) > 0 ? $page - $barSize : 1;
    $end = min($noPages, $start + $barSize * 2);

    echo '<div class="menu"><center>';

    // fast reverse (<<)
    if ($start - $barSize*2 > 0) {
      $this->filter[Q_RESULT] = $page - $barSize * 2;
      $href = makeHref($this->filter);
      echo '<a '. $href .">[&#171;]</a>";
    }

    // reverse (<)
    if ($start > 1) {
      $this->filter[Q_RESULT] =$start - 1;
      $href = makeHref($this->filter);
      echo '<a '. $href .">[&lt;]</a>";
    }

    // page numbers
    foreach (range($start, $end) as $i) {
      // default page is #1
      // we don't duplicate URLs
      if ($i > 1) $this->filter[Q_RESULT] = $i;
      $href = makeHref($this->filter);
      if ($i == $page) {
        // don't make links for current page
 echo '<b>['. $i .']</b>';
      } else {
 echo '<a '. $href .'>['. $i .']</a>';
      }
    }

    // forward (>)
    if ($end < $noPages) {
      $this->filter[Q_RESULT] = $end + 1;
      $href = makeHref($this->filter);
      echo '<a '. $href .">[&gt;]</a>";
    }

    // fast forward (>>)
    if (($end + $barSize*2) <= $noPages) {
      $this->filter[Q_RESULT] =$end + $barSize * 2;
      $href = makeHref($this->filter);
      echo '<a '. $href .">[&#187;]</a>\n";
    }
    //print_r($this->filter);
    echo '</center></div>';
  }
}



/**
  * Displays the summary information of each bib entries of the
  * current page. For each entry, this method displays the author,
  * title; the bib entries are displayed grouped by the
  * publication years. If the bib list is empty, an error message is
  * displayed.
  */
class DefaultContentStrategy  {

  function display($display) {
    // create a year -> entries map to display by years
    $years = array();
    foreach ($display->result as $e) {
      $y =  trim($e->getField(YEAR));
      $years[$y][$e->getKey()] = $e;
    }
    krsort($years);

    ?>

    <table class="result" >
    <?php

    $index = 0;
    $refnum = count($display->result)-(($display->page-1)*PAGE_SIZE);
    foreach ($years as $year => $entries) {


      if ($display->isDisplayed($index)) {
      ?>
      <tr class="date">
        <td colspan="2" class="header"><?php echo $year; ?></td>
      </tr>
      <?php
      }
      // sort by keys, enable a nice sorting as Dupont2008a, Dupont2008b, Dupont2008c
      krsort($entries);
      foreach ($entries as $bib) {
        if ($display->isDisplayed($index)) {
          $bib->id = $refnum--;
          $bib->toString();
        }
        $index++;

      } // end foreach
    }
    ?>
    </table>
    <?php
  } // end function
} // end class


/** Class to display en error message */
class ErrorDisplay extends Display  {

  function display() {

    ?>
    <b>Sorry, this bib entry does not exist.</b>
    <a href="?">Back to bibtexbrowser</a>

    <?php
  }
}

/** Class to display the publication records of academics. */
class AcademicDisplay extends Display {

  function AcademicDisplay($authorName, &$db) {
    global $filename;
    $this->author=$authorName;
    $this->db=$db;
    $this->header = 'Publications of '.$authorName;
  }

  function display() {
    echo $this->formatedHeader();

    // Books
    $entries = $this->db->multisearch(array(Q_AUTHOR=>$this->author, Q_TYPE=>'book'));
    if (count($entries)>0) {
    echo '<div class="header">Books</div>';
    echo '<table class="result" >';
    foreach ($entries as $bib) {
        $bib->id = $bib->getYear();
        $bib->toString();
    } // end foreach
    echo '</table>';
    }

        // Journal / Bookchapters
    $entries = $this->db->multisearch(array(Q_AUTHOR=>$this->author, Q_TYPE=>'article|incollection'));
    if (count($entries)>0) {
    echo '<div class="header">Articles and Book Chapters</div>';
    echo '<table class="result" >';
    foreach ($entries as $bib) {
        $bib->id = $bib->getYear();
        $bib->toString();
    } // end foreach
    echo '</table>';
    }

    // conference papers
    $entries = $this->db->multisearch(array(Q_AUTHOR=>$this->author, Q_TYPE=>'inproceedings',Q_EXCLUDE=>'workshop'));
    if (count($entries)>0) {
    echo '<div class="header">Conference Papers</div>';
    echo '<table class="result" >';
    foreach ($entries as $bib) {
        $bib->id = $bib->getYear();
        $bib->toString();
    } // end foreach
    echo '</table>';
    }

    // workshop papers
    $entries = $this->db->multisearch(array(Q_AUTHOR=>$this->author, Q_TYPE=>'inproceedings',Q_SEARCH=>'workshop'));
    if (count($entries)>0) {
    echo '<div class="header">Workshop Papers</div>';
    echo '<table class="result" >';
    foreach ($entries as $bib) {
        $bib->id = $bib->getYear();
        $bib->toString();
    } // end foreach
    echo '</table>';
    }

    // misc and thesis
    $entries = $this->db->multisearch(array(Q_AUTHOR=>$this->author, Q_TYPE=>'misc|phdthesis|mastersthesis'));
    if (count($entries)>0) {
    echo '<div class="header">Other Publications</div>';
    echo '<table class="result" >';
    foreach ($entries as $bib) {
        $bib->id = $bib->getYear();
        $bib->toString();
    } // end foreach
    echo '</table>';
    }


    echo $this->poweredby();

  }

}



/** Class to display a single bibentry. */
class BibEntryDisplay extends Display {

  /** the bib entry to display */
  var $bib;

  /** Creates an instance with the given bib entry and header.
   * It the object is an instance of BibIndexEntry, it may be
   * mutated to read the rest of the fields.
   */
  function BibEntryDisplay(&$bibentry) {
    $this->bib = $bibentry;
    global $filename;
    $this->header = 'Bibtex entry: '.$this->bib->getTitle();
  }

  function display() {
    echo $this->formatedHeader();
    echo $this->bib->toEntryUnformatted();
    echo $this->poweredby();

  }

}

// ----------------------------------------------------------------------
// DATABASE MANAGEMENT
// ----------------------------------------------------------------------

/**
 * Abstraction of bibliographic database to contain a set of
 * bibliographic entries and maintain them.
 */
class BibDataBase {
  /** A hash table from keys (e.g. Goody1994) to bib entries (BibEntry instances). */
  var $bibdb;

  /** Creates a new database by parsing bib entries from the given
   * file. */
  function BibDataBase($filename) {
  $db = new BibtexbrowserBibDB();
  new StateBasedBibtexParser($filename, $db);
  //print_r($parser);
  $this->bibdb =$db->bibdb;
  }

  /** Returns all entries as an array. Each entry is an instance of
   * class BibEntry. */
  function getEntries() {
    return $this->bibdb;
  }

  /** Returns all entries categorized by types. The returned value is
   * a hashtable from types to arrays of bib entries.
   */
  function getEntriesByTypes() {
    $result = array();
    foreach ($this->bibdb as $b) {
      $result[$b->getType()][] = $b;
    }
    return $result;
  }

  /** Returns an array containing all the bib types (strings). */
  function getTypes() {
    $result = array();
    foreach ($this->bibdb as $b) {
      $result[$b->getType()] = 1;
    }
    $result = array_keys($result);
    return $result;
  }

  /** Generates and returns an array consisting of all authors.
   * The returned array is a hash table with keys <FirstName LastName>
   * and values <LastName, FirstName>.
   */
  function authorIndex(){
    $result = array();
    foreach ($this->bibdb as $bib) {
      foreach($bib->getAuthors() as $a){
        //we use an array because several authors can have the same lastname
        @$result[getLastName($a)][$a]++;
      }
    }
    ksort($result);

    // now authors are sorted by last name
    // we rebuild a new array for having good keys in author page
    $realresult = array();
    foreach($result as $x) {
        ksort($x);
        foreach($x as $v => $tmp) $realresult[$v] = formatAuthor($v);
    }

    return $realresult;
  }

  /** Generates and returns an array consisting of all tags.
   */
  function tagIndex(){
    $result = array();
    foreach ($this->bibdb as $bib) {
      $tags =explode(' and ', $bib->getField("keywords"));
      foreach($tags as $a){
 $ta = trim($a);
   $result[$ta] = $ta;
      }
    }
    asort($result);
    return $result;
  }

  /** Generates and returns an array consisting of all years.
   */
  function yearIndex(){
    $result = array();
    foreach ($this->bibdb as $bib) {
      $year = $bib->getField("year");
      $result[$year] = $year;
      }
    arsort($result);
    return $result;
  }

  /** Given its ID, return the bib entry. */
  function getEntry($id){
    foreach($this->bibdb as $bib) {
      if($bib->getId() == $id)
 return $bib;
    }
    return null;
  }

  /** Given its key, return the bib entry. */
  function getEntryByKey($key) {
    return $this->bibdb[$key];
  }

  /**
   * Returns an array containing all bib entries matching the given
   * type.
   */
  function searchType($type){
    $result = array();
    foreach($this->bibdb as $bib) {
      if($bib->getType() == $type)
 $result[] = $bib;
    }
    return $result;
  }

  /** Returns an array of bib entries (BibEntry) that satisfy the query
   * $query is an hash with entry type as key and searched fragment as value
   * the returned array is sorted by year
   */
  function multisearch($query) {
    if (count($query)<1) {return array();}
    $result = array();

    foreach ($this->bibdb as $bib) {
        $entryisselected = true;
        foreach ($query as $field => $fragment) {
          if ($field==Q_SEARCH) {
            // we search in the whole bib entry
            if (!$bib->hasPhrase($fragment)) {
              $entryisselected = false;
            }
          }
          else if ($field==Q_EXCLUDE) {
            if ($bib->hasPhrase($fragment)) {
              $entryisselected = false;
            }
          }
          else {
            if (!$bib->hasPhrase($fragment, $field))  {
              $entryisselected = false;
            }
          }

        }
        if ($entryisselected)  $result[$bib->getYear().$bib->getKey()] = $bib;
      }
      krsort($result);
      return $result;
  }
} // end class


function printHTMLHeaders($title,$noindex_metatag=false) {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo ENCODING ?>"/>
<meta name="generator" content="bibtexbrowser v__DATE__" />
<?php if ($noindex_metatag) echo '<meta name="robots" content="noindex"/>' ?>
<title><?php echo $title; ?></title>
<style type="text/css">
<!--
body {
  font-family: Geneva, Verdana, Arial, Helvetica, sans-serif;
  font-size: small;
  margin: 0px;
  padding: 10px;
}



.title {
  color: #003366;
  font-size: 20pt;
  font-weight: bold;
  text-align: right;
}
.header {
  background-color: #995124;
  color: #FFFFFF;
  padding: 1px 2px 1px 2px;
}
.rheader {
  font-weight: bold;
  background-color: #003366;
  color: #ffffff;
  padding: 2px;
  margin-bottom: 10px;
  border-bottom: #ff6633 2px solid;

}
.menu {
  font-size: x-small;
  background-color: #EFDDB4;
  padding: 0px;
  border: 1px solid #000000;
  margin: 0px;
}
.menu a {
  text-decoration: none;
  color: #003366;
}
.menu a:hover {
  color: #ff6633;
}
.header a {
  text-decoration: none;
  color: #FFFFFF;
}

.bibline {
  padding:7px;
  padding-left:15px;
  font-size: small;
}

TD {   vertical-align:text-top; }

.result {
  padding:0px;
  border: 1px solid #000000;
  margin:0px;
  background-color: #ffffff;

}
.result a {
  text-decoration: none;
  color: #469AF8;
}
.result a:hover {
  color: #ff6633;
}

TABLE {
width: 100%;
}

pre {
  background-color:#FFFFFF;
  font-size: small;
  border: 1px solid #000000;
}
.input_box{
  margin-bottom : 2px;
}
.mini_se {
  border: none 0;
  border-top: 1px dashed #717171;
  height: 1px;
}
.a_name a {
  color:#469AF8;
  width:130px;

}
.bit_big{
  font-size: small;
}

-->
</style>


</head>
<body>
<?php

}


$included=(__FILE__!=$_SERVER['SCRIPT_FILENAME']);
if (isset($_GET['menu']))
{
  // menu pages don't need to be indexed by search engines
  // we don't set the title and set noindex metatag to true
  printHTMLHeaders("",true);
  echo $displaymanager->searchView().'<br/>';
  echo $displaymanager->typeVC().'<br/>';
  echo $displaymanager->yearVC().'<br/>';
  echo $displaymanager->authorVC().'<br/>';
  echo $displaymanager->tagVC().'<br/>';
  echo '</body></html>';
} // end isset($_GET['menu']
else if ($displaymanager->processRequest()) {

    if (!$included) printHTMLHeaders($displaymanager->display->header);
    $displaymanager->mainVC();
    if (!$included) echo '</body></html>';


}
else if (!$included) {

// this is a frameset definition
    ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html  xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="generator" content="bibtexbrowser v__DATE__" />
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo ENCODING ?>"/>
<title>You are browsing <?php echo $filename; ?> with bibtexbrowser</title>
</head>
    <frameset cols="15%,*">
    <frame name="menu" src="<?php echo SCRIPT_NAME .'?'.Q_FILE.'='. urlencode($filename).'&amp;menu'; ?>" />
    <frame name="main" src="<?php echo SCRIPT_NAME .'?'.Q_FILE.'='. urlencode($filename).'&amp;all'; ?>" />
    </frameset>
    </html>

    <?php
}
// if we are included; do nothing bibtexbrowser.php is used as a library
?>