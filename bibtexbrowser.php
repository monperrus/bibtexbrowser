<?php /* bibtexbrowser: publication lists with bibtex and PHP
<!--this is version v__MTIME__, see http://www.monperrus.net/martin/bibtexbrowser/ -->

bibtexbrowser is a PHP script that creates publication lists from Bibtex files.
 bibtexbrowser is stable, mature and easy to install. It is used in [[users|50+ different universities]] around the globe.

+++TOC+++

=====Major features=====
* **(11/2009)** bibtexbrowser generates [[http://www.monperrus.net/martin/accurate+bibliographic+metadata+and+google+scholar|Google Scholar metadata]] so as to improve the visibility of your papers on Google Scholar. Since Google has now [[http://scholar.google.com/intl/en/scholar/inclusion.html|documented this feature]], as of version &#8805;20100621, Google Scholar Metadata should be completely correct.
* **(11/2009)** More and more academics use bibliographic software like [[http://www.zotero.org/|Zotero]] or [[http://www.mendeley.com/|Mendeley]]. bibtexbrowser generates [[http://ocoins.info/|COinS]] for automatic import of bibliographic entries with [[http://www.zotero.org/|Zotero]] and [[http://www.mendeley.com/|Mendeley]].
* **(10/2009)** People can subscribe to the RSS publication feed of an individual or a group so as to being kept up-to-date: bibtexbrowser generates RSS feeds for all queries (simply add &#38;rss at the end of the URL)! [[http://www.monperrus.net/martin/bibtexbrowser.php?bib=monperrus.bib&amp;all&amp;rss|demo]]
* **(02/2009)** bibtexbrowser can display all entries for an author with an academic style (i.e book, articles, conference, workshop): [[http://www.monperrus.net/martin/bibtexbrowser.php?bib=metrics.bib&amp;academic=Ducasse|demo]]
* **(05/2008)**: bibtexbrowser can be used to embed a publication list into another page: [[http://www.monperrus.net/martin/|demo]]
* **(04/2007)**: bibtexbrowser is easy to install: just a single file.

=====Other features=====
* **(03/2011)** bibtexbrowser uses progressive enhancement with Javascript
* **(10/2010)** bibtexbrowser now supports cross-references (Bibtex crossref)
* **(09/2010)** bibtexbrowser now supports multiple bibtex files (''bibtexbrowser.php?bib=file1.bib;file2.bib'')
* **(05/2010)** bibtexbrowser adds links to your co-author pages if you define the corresponding @string (see function addHomepageLink)
* **(01/2010)** bibtexbrowser can handle user-defined bibliographic styles
* **(10/2009)** bibtexbrowser is able to generate a bibtex file containing only the selected entries (simply add &#38;astext at the end of the link)
* **(10/2009)** bibtexbrowser is now independent of the configuration of register_globals
* **(01/2009)** bibtexbrowser allows multi criteria search, e.g.  [[http://www.monperrus.net/martin/bibtexbrowser.php?bib=metrics.bib&amp;type=inproceedings&amp;year=2004|demo]]
* bibtexbrowser replaces constants defined in @STRING
* bibtexbrowser is very fast because it keeps a compiled version of the bibtex file (PHP object serialized)
* bibtexbrowser is compatible with PHP 4.x and PHP 5.x
* bibtexbrowser can display the menu and all entries without filtering from the file name passed as parameter [[http://www.monperrus.net/martin/bibtexbrowser.php?bib=metrics.bib|demo]]
* bibtexbrowser can display all entries  out of a bibtex file [[http://www.monperrus.net/martin/bibtexbrowser.php?bib=metrics.bib&amp;all|demo]]
* bibtexbrowser can display all entries for a given year [[http://www.monperrus.net/martin/bibtexbrowser.php?bib=metrics.bib&amp;year=2004|demo]]
* bibtexbrowser can display a single bibtex entry [[http://www.monperrus.net/martin/bibtexbrowser.php?bib=metrics.bib&amp;key=monperrus08d|demo]]
* bibtexbrowser can display found entries with a search word (it can be in any bib field) [[http://www.monperrus.net/martin/bibtexbrowser.php?bib=metrics.bib&amp;search=ocl|demo]]
* bibtexbrowser can display all entries with a bib keyword
* bibtexbrowser outputs valid XHTML 1.0 Transitional
* bibtexbrowser can display all entries for an author [[http://www.monperrus.net/martin/bibtexbrowser.php?bib=metrics.bib&amp;author=Barbara+A.+Kitchenham|demo]]
* bibtexbrowser can be used with different encodings (change the default iso-8859-1 encoding if your bib file is in utf-8 ''define('ENCODING','utf-8')'' )


=====Download=====
For feature requests, bug reports, or patch proposals, [[http://www.monperrus.net/martin/|please drop me an email ]] or comment this page. Don't hesitate to contact me to be added in the [[users|lists of bibtexbrowser users]] :-)

You may try bibtexbrowser without installation with [[http://my.publications.li]].

**[[http://www.monperrus.net/martin/bibtexbrowser.php.txt|Download bibtexbrowser]]** <?php if (is_readable('bibtexbrowser-rc.php')) {echo '<a href="http://www.monperrus.net/martin/bibtexbrowser-rc.php.txt">(Try the release candidate!)</a>';} ?>


=====Demo and screenshot=====

Demo: [[http://www.monperrus.net/martin/bibtexbrowser.php?bib=metrics.bib|Here, you can browse a bibtex file dedicated to software metrics]]

<a href="bibtexbrowser-screenshot.png"><img height="500" src="bibtexbrowser-screenshot.png" alt="bibtexbrowser screenshot"/></a>

=====Basic installation=====
<div class="wikitous" title="/bibtexbrowser/docs/basic-install"> 
Create a bib file with the publication records (e.g. csgroup2008.bib) and upload it to your server.
* Use the link ''bibtexbrowser.php?bib=csgroup2008.bib'' (frameset based view)
* Use the link ''bibtexbrowser.php?bib=csgroup2008.bib&amp;all'' (pub list sorted by year)
* Use the link ''bibtexbrowser.php?bib=csgroup2008.bib&amp;all&amp;academic'' (pub list  sorted by publication type, then by year)

** Warning **: bibtexbrowser maintains a cached version of the parsed bibtex, for high performance, check that PHP can write in the working directory of PHP.

**Handling mutliple bibtex files**: If you want to include several bibtex files, just give bibtexbrowser the files separated by semi-columns e.g:
''bibtexbrowser.php?bib=strings.bib;csgroup2008.bib''

</div>
=====How to embed your publication list in your home page=====
<div class="wikitous" title="/bibtexbrowser/docs/embed"> 

<table border="1">
<tr><th></th><th>Sorted by year </th><th>Sorted by publication type</th></tr>
<tr><td>For a group/team/lab</td>
<td>
&#60;?php
$_GET&#91;'bib'&#93;='csgroup2008.bib';
$_GET&#91;'all'&#93;=1;
include( 'bibtexbrowser.php' );
?>
</td>
<td>
&#60;?php
$_GET&#91;'bib'&#93;='csgroup2008.bib';
$_GET&#91;'all'&#93;=1;
$_GET&#91;'academic'&#93;=1;
include( 'bibtexbrowser.php' );
?>
</td>
</tr><!-- end group -->
<tr><td>For an individual</td>
<td>
  &#60;?php
$_GET&#91;'bib'&#93;='mybib.bib';
$_GET&#91;'author'&#93;='Martin Monperrus';
include( 'bibtexbrowser.php' );
?>
</td>
<td>
&#60;?php
$_GET&#91;'bib'&#93;='mybib.bib';
$_GET&#91;'author'&#93;='Martin Monperrus';
$_GET&#91;'academic'&#93;=1;
include( 'bibtexbrowser.php' );
?>
</td>
</tr><!-- end individual -->
</table>
</div>
=====How to tailor bibtexbrowser?=====

====By modifying the CSS====
<div class="wikitous" title="/bibtexbrowser/docs/tailor/css"> 

If bibtexbrowser.css exists, it is used, otherwise bibtexbrowser uses its own embedded CSS style (see function bibtexbrowserDefaultCSS). An example of CSS tailoring is:
<pre>
.date {   background-color: blue; }
.rheader {  font-size: large }
.bibref {  padding:3px; padding-left:15px;  vertical-align:top;}
.bibtitle { font-weight:bold; }
.bibbooktitle { font-style:italic; }
</pre>
</div>

====By modifying the bibliography style ====
<div class="wikitous" title="/bibtexbrowser/docs/tailor/style"> 
The bibliography style is encapsulated in a function. If you want to modify the bibliography style, you can copy the default style ([[bibtexbrowser-style-default.php.txt|source]]) in a new file, say ''bibtexbrowser-yourstyle.php'', and rename the function ''DefaultBibliographyStyle'' in say ''MyFancyBibliographyStyle''.
Then, add in the file ''bibtexbrowser.local.php'' (see below):
<code>
&#60;?php
include( 'bibtexbrowser-yourstyle.php' );
define('BIBLIOGRAPHYSTYLE','MyFancyBibliographyStyle');
?>
</code>

[[http://www.monperrus.net/martin/bibtexbrowser-style-janos.php.txt|Janos Tapolcai contributed with this style, which looks like IEEE references]].
For contributing with a new style, [[http://www.monperrus.net/martin/|please drop me an email ]]
</div>

====By creating a "bibtexbrowser.local.php"====
<div class="wikitous" title="/bibtexbrowser/docs/tailor/local"> 

All the variable parts of bibtexbrowser can be modified with a file called ''bibtexbrowser.local.php''.

<pre>
&#60;?php
// ------------------------------- NOVICE LEVEL
// if your bibtex file is utf-8 encodedd
// define("ENCODING","utf-8");

// number of bib items per page
// define('PAGE_SIZE',50);

// disable Javascript progressive enhancement
// define('BIBTEXBROWSER_USE_PROGRESSIVE_ENHANCEMENT',false);


// see the other define(...) in the source, they are all overridable

// ------------------------------- INTERMEDIATE LEVEL

// if you are not satisifed with the default style
// define('BIBLIOGRAPHYSTYLE','MyFancyBibliographyStyle');
function MyFancyBibliographyStyle() {
   // see function DefaultBibliographyStyle
}

// if you are not satisifed with the default sections
// define('BIBLIOGRAPHYSECTIONS','mySections');
function mySections() {
return  
  array(
  // Books
    array(
      'query' => array(Q_TYPE=>'book'),
      'title' => 'Cool Books'
    ),
  // .. see function DefaultBibliographySections
);
}


// ------------------------------- EXPERT LEVEL
// define('BIBTEXBROWSER_URL','path/to/bibtexbrowser.php'); // if bibtexbrowser.php is in another directory in embedded mode
// define('BIBTEXBROWSER_URL',''); // to get the individual bib pages embedded as well

?>
</pre>
</div>
<a name="modify-bibstyle"/>


=====How to add links to the slides of a conference/workshop paper?=====

You can simply fill the ''comment'' field of the bib entry with an HTML link:
<code>
@inproceedings{foo,
author="Jean Dupont",
title="Bibtexbrowser",
year=2009,
booktitle="Proceedings of the BIB conference",
comment={&lt;a href="myslides.pdf">[slides]&lt;/a>}
}
</code>

This comment field can also be used to add acceptance rates and impact factors.

=====Related tools=====

Old-fashioned:
[[http://nxg.me.uk/dist/bibhtml/|bibhtml]], [[http://www.litech.org/~wkiri/bib2html/|bib2html]], [[http://ilab.usc.edu/bibTOhtml/|bibtohtml]], [[http://people.csail.mit.edu/rahimi/bibtex/|bibtextohtml]], [[http://www.lri.fr/~filliatr/bibtex2html/|bibtex2html]], [[http://people.csail.mit.edu/mernst/software/bibtex2web.html |bibtex2web]], [[http://strategoxt.org/Stratego/BibtexTools|stratego bibtex module]]
Unlike them, **bibtexbrowser is dynamic**.i.e.; generates the HTML pages on the fly. Thus, you do not need to regenerate the static HTML files each time the bib file is changed.

Heavyweight:
[[http://www.rennes.supelec.fr/ren/perso/etotel/PhpBibtexDbMng/|PHP BibTeX Database Manager]], [[http://gforge.inria.fr/projects/bibadmin/|bibadmin]], [[http://artis.imag.fr/Software/Basilic/|basilic]], [[http://phpbibman.sourceforge.net/|phpbibman]], [[http://www.aigaion.nl/|aigaion]], [[http://www.refbase.net/|refbase]], [[http://wikindx.sourceforge.net/|wikindx]], [[http://refdb.sourceforge.net/|refdb]]
Unlike them, **bibtexbrowser does not need a MySQL database**


Main competitor:
[[http://code.google.com/p/simplybibtex/|SimplyBibtex]] has the same spirit, but the project seems dead since 2006

Misc:
[[http://www.sat.ltu.se/publications/publications.m|This matlab script is similar]]

=====Copyright=====

This script is a fork from an excellent script of the University of Texas at El Paso.

(C) 2006-2011 Martin Monperrus
(C) 2005-2006 The University of Texas at El Paso / Joel Garcia, Leonardo Ruiz, and Yoonsik Cheon
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License as
published by the Free Software Foundation; either version 2 of the
License, or (at your option) any later version.

<script type="text/javascript">wikitous_option_buttonText = 'Improve this documentation';</script><script type="text/javascript" src="http://wikitous.appspot.com/wikitous.js"></script>
*/

// Wednesday, June 01 2011: bug found by Carlos Br�s
// it should be possible to include( 'bibtexbrowser.php' ); several times in the same script
if (!defined('BIBTEXBROWSER')) {
// this if block ends at the very end of this file, after all class and function declarations.
define('BIBTEXBROWSER','v__MTIME__');


// *************** CONFIGURATION
// I recommend to put your changes in bibtexbrowser.local.php
// it will help you to upgrade the script with a new version
@include(dirname(__FILE__).'/bibtexbrowser.local.php');
// there is no encoding transformation from the bibtex file to the html file
// if your bibtex file contains 8 bits characters in utf-8
// change the following parameter
@define('ENCODING','iso-8859-1');//define('ENCODING','utf-8');//define('ENCODING','windows-1252');
// number of bib items per page
@define('PAGE_SIZE',isset($_GET['nopage'])?10000:25);
// bibtexbrowser uses a small piece of Javascript to improve the user experience
// see http://en.wikipedia.org/wiki/Progressive_enhancement
// if you don't like it, you can be disable it by adding in bibtexbrowser.local.php
// @define('BIBTEXBROWSER_USE_PROGRESSIVE_ENHANCEMENT',false);
@define('BIBTEXBROWSER_USE_PROGRESSIVE_ENHANCEMENT',true);
// if you disable the Javascript progressive enhancement, 
// you may want the links to be open in a new window/tab
// if yes, add in bibtexbrowser.local.php  define('BIBTEXBROWSER_BIB_IN_NEW_WINDOW',true);
@define('BIBTEXBROWSER_BIB_IN_NEW_WINDOW',false);
@define('BIBLIOGRAPHYSTYLE','DefaultBibliographyStyle');// this is the name of a function
@define('BIBLIOGRAPHYSECTIONS','DefaultBibliographySections');// this is the name of a function
// can we load bibtex files on external servers?
@define('BIBTEXBROWSER_LOCAL_BIB_ONLY', true);
@define('COMMA_NAMES',false);// do have authors in a comma separated form?
@define('TYPES_SIZE',10); // number of entry types per table
@define('YEAR_SIZE',20); // number of years per table
@define('AUTHORS_SIZE',30); // number of authors per table
@define('TAGS_SIZE',30); // number of keywords per table
@define('READLINE_LIMIT',1024);
@define('Q_YEAR', 'year');
@define('Q_YEAR_PAGE', 'year_page');
@define('Q_FILE', 'bib');
@define('Q_AUTHOR', 'author');
@define('Q_AUTHOR_PAGE', 'author_page');
@define('Q_TAG', 'keywords');
@define('Q_TAG_PAGE', 'keywords_page');
@define('Q_TYPE', 'type');
@define('Q_TYPE_PAGE', 'type_page');
@define('Q_ALL', 'all');
@define('Q_ENTRY', 'entry');
@define('Q_KEY', 'key');
@define('Q_SEARCH', 'search');
@define('Q_EXCLUDE', 'exclude');
@define('Q_RESULT', 'result');
@define('Q_ACADEMIC', 'academic');
@define('Q_DB', 'bibdb');
@define('AUTHOR', 'author');
@define('EDITOR', 'editor');
@define('SCHOOL', 'school');
@define('TITLE', 'title');
@define('BOOKTITLE', 'booktitle');
@define('YEAR', 'year');
@define('BUFFERSIZE',100000);
@define('MULTIPLE_BIB_SEPARATOR',';');
@define('METADATA_GS',true);
@define('METADATA_DC',true);
@define('METADATA_EPRINTS',false);

// in embedded mode, we still need a URL for displaying bibtex entries alone
// this is usually resolved to bibtexbrowser.php
// but can be overridden in bibtexbrowser.local.php 
// for instance with @define('BIBTEXBROWSER_URL',''); // links to the current page with ?
@define('BIBTEXBROWSER_URL',basename(__FILE__));

// *************** END CONFIGURATION

// for clean search engine links
// we disable url rewriting
// ... and hope that your php configuration will accept one of these
@ini_set("session.use_only_cookies",1);
@ini_set("session.use_trans_sid",0);
@ini_set("url_rewriter.tags","");

// we ensure that the pages won't get polluted
// if future versions of PHP change warning mechanisms...

@error_reporting(/*pp4php:serl*/E_ALL/*lres*/);

/** sets the database of bibtex entries (object of type BibDataBase)
  * in $_GET[Q_DB]
  * Uses a caching mechanism on disk for sake of performance
  */
function setDB() {

  // default bib file, if no file is specified in the query string.
  if (!isset($_GET[Q_FILE]) || $_GET[Q_FILE] == "") {
  ?>
  <div id="bibtexbrowser_message">
  Congratulations! bibtexbrowser is correctly installed!<br/>
  Now you have to pass the name of the bibtex file as parameter (e.g. bibtexbrowser.php?bib=mybib.php)<br/>
  You may browse:<br/>
  <?php
  foreach (glob("*.bib") as $bibfile) {
    $url="?bib=".$bibfile; echo '<a href="'.$url.'">'.$bibfile.'</a><br/>';
  }
  echo "</div>";
  return; // we cannot set the db wtihout a bibfile
  }

  // first does the bibfiles exist:
  // $_GET[Q_FILE] can be urlencoded for instance if they contain slashes
  // so we decode it
  $_GET[Q_FILE] = urldecode($_GET[Q_FILE]);

  // ---------------------------- HANDLING unexistent files
  foreach(explode(MULTIPLE_BIB_SEPARATOR, $_GET[Q_FILE]) as $bib) {
  
    // this is a security protection
    if (BIBTEXBROWSER_LOCAL_BIB_ONLY && !file_exists($bib)) {
     // to automate dectection of faulty links with tools such as webcheck
     header('HTTP/1.1 404 Not found');
     die('<b>the bib file '.$bib.' does not exist !</b>');
    }
  } // end for each

  // ---------------------------- HANDLING HTTP If-modified-since
  // testing with $ curl -v --header "If-Modified-Since: Fri, 23 Oct 2010 19:22:47 GMT" "... bibtexbrowser.php?key=wasylkowski07&bib=..%252Fstrings.bib%253B..%252Fentries.bib"
  // and $ curl -v --header "If-Modified-Since: Fri, 23 Oct 2000 19:22:47 GMT" "... bibtexbrowser.php?key=wasylkowski07&bib=..%252Fstrings.bib%253B..%252Fentries.bib"

  // save bandwidth and server cpu
  // (imagine the number of requests from search engine bots...)
  $bib_is_unmodified = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ;
  foreach(explode(MULTIPLE_BIB_SEPARATOR, $_GET[Q_FILE]) as $bib) {
      $bib_is_unmodified = 
                    $bib_is_unmodified                               
                    &&  (strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE'])>filemtime($bib));
  } // end for each
  if ( $bib_is_unmodified && !headers_sent()) { 
    header("HTTP/1.1 304 Not Modified");
    exit; 
  }



  // ---------------------------- HANDLING caching of compiled bibtex files
  // for sake of performance, once the bibtex file is parsed
  // we try to save a "compiled" in a txt file
  $compiledbib = 'bibtexbrowser_'.md5($_GET[Q_FILE]).'.dat';

  $parse=true;
  foreach(explode(MULTIPLE_BIB_SEPARATOR, $_GET[Q_FILE]) as $bib) {
  // do we have a compiled version ?
  if (is_file($compiledbib) && is_readable($compiledbib)) {
    // is it up to date ? wrt to the bib file and the script
    // then upgrading with a new version of bibtexbrowser triggers a new compilation of the bib file
    if (filemtime($bib)<filemtime($compiledbib) && filemtime(__FILE__)<filemtime($compiledbib)) {
      $_GET[Q_DB] = unserialize(file_get_contents($compiledbib));
      // basic test
      // do we have an correct version of the file
      if (is_a($_GET[Q_DB],'BibDataBase')) {
        // at least we can switch off the parsing
        $parse=false;
      }
    }
  }
  } // end for each

  // we don't have a compiled version
  if ($parse) {
    //echo '<!-- parsing -->';
    // then parsing the file
    $db = new BibDataBase();
  foreach(explode(MULTIPLE_BIB_SEPARATOR, $_GET[Q_FILE]) as $bib) {
      $db->load($bib);
    }    
    $_GET[Q_DB]=$db;

    // are we able to save the compiled version ?
    // note that the compiled version is saved in the current working directory
    if ((!is_file($compiledbib) && is_writable(getcwd())) || (is_file($compiledbib) && is_writable($compiledbib)) ) {
      // we can use file_put_contents
      // but don't do it for compatibility with PHP 4.3
      $f = fopen($compiledbib,'w');
      //we use a lock to avoid that a call to bbtexbrowser made while we write the object loads an incorrect object
      if (flock($f,LOCK_EX)) fwrite($f,serialize($_GET[Q_DB]));
      fclose($f);
    }
    //else echo '<!-- please chmod the directory containing the bibtex file to be able to keep a compiled version (much faster requests for large bibtex files) -->';
  } // end parsing and saving
} // end function setDB





////////////////////////////////////////////////////////

/** This class is a generic parser of bibtex files
 * It has no dependencies, i.e. it can be used outside of bibtexbrowser
 * To use it, simply instantiate it and pass it an object that will receive semantic events
 * The delegate is expected to have some methods
 * see classes BibDBBuilder and XMLPrettyPrinter
 */
class StateBasedBibtexParser {

function StateBasedBibtexParser($bibfilename, &$delegate) {


// STATE DEFINITIONS
@define('NOTHING',1);
@define('GETTYPE',2);
@define('GETKEY',3);
@define('GETVALUE',4);
@define('GETVALUEDELIMITEDBYQUOTES',5);
@define('GETVALUEDELIMITEDBYQUOTES_ESCAPED',6);
@define('GETVALUEDELIMITEDBYCURLYBRACKETS',7);
@define('GETVALUEDELIMITEDBYCURLYBRACKETS_ESCAPED',8);
@define('GETVALUEDELIMITEDBYCURLYBRACKETS_1NESTEDLEVEL',9);
@define('GETVALUEDELIMITEDBYCURLYBRACKETS_1NESTEDLEVEL_ESCAPED',10);
@define('GETVALUEDELIMITEDBYCURLYBRACKETS_2NESTEDLEVEL',11);
@define('GETVALUEDELIMITEDBYCURLYBRACKETS_2NESTEDLEVEL_ESCAPED',12);
@define('GETVALUEDELIMITEDBYCURLYBRACKETS_3NESTEDLEVEL',11);
@define('GETVALUEDELIMITEDBYCURLYBRACKETS_3NESTEDLEVEL_ESCAPED',12);


$state=NOTHING;
$entrytype='';
$entrykey='';
$entryvalue='';
$finalkey='';
$entrysource='';

// metastate
$isinentry = false;

$delegate->beginFile();

$handle = fopen($bibfilename, "r");
if (!$handle) die ('cannot open '.$bibfilename);
// if you encounter this error "Allowed memory size of xxxxx bytes exhausted"
// then decrease the size of the temp buffer below
$bufsize=BUFFERSIZE;

while (!feof($handle)) {
$sread=fread($handle,$bufsize);
//foreach(str_split($sread) as $s) {
for ( $i=0; $i < strlen( $sread ); $i++) { $s=$sread[$i];

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
    }
    // the value is delimited by curly brackets
    else if ($s=='{') {
    $state = GETVALUEDELIMITEDBYCURLYBRACKETS;
    }
    // the end of the key and no value found: it is the bibtex key e.g. \cite{Descartes1637}
    else if ($s==',') {
    $state = GETKEY;
    $delegate->setEntryField(trim($finalkey),$entryvalue);
    $entryvalue=''; // resetting the value buffer
    }
    // this is the end of the value AND of the entry
    else if ($s=='}') {
    $state = NOTHING;
    $delegate->setEntryField(trim($finalkey),$entryvalue);
    $isinentry = false;$delegate->endEntry($entrysource);
    $entryvalue=''; // resetting the value buffer
    }
    else if ($s==' ' || $s=="\t"  || $s=="\n" || $s=="\r" ) {
      // blank characters are not taken into account when values are not in quotes or curly brackets
    }
    else { $entryvalue=$entryvalue.$s;}
  }


/* GETVALUEDELIMITEDBYCURLYBRACKETS* handle entries delimited by curly brackets and the possible nested curly brackets */
 else if ($state==GETVALUEDELIMITEDBYCURLYBRACKETS) {

  if ($s=='\\') {
   $state = GETVALUEDELIMITEDBYCURLYBRACKETS_ESCAPED;
   $entryvalue=$entryvalue.$s;}
  else if ($s=='{') {
   $state = GETVALUEDELIMITEDBYCURLYBRACKETS_1NESTEDLEVEL;$entryvalue=$entryvalue.$s;}
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
   $state = GETVALUEDELIMITEDBYCURLYBRACKETS_2NESTEDLEVEL;$entryvalue=$entryvalue.$s;}
  else if ($s=='}') {
   $state = GETVALUEDELIMITEDBYCURLYBRACKETS;$entryvalue=$entryvalue.$s;}
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
   $state = GETVALUEDELIMITEDBYCURLYBRACKETS_3NESTEDLEVEL;$entryvalue=$entryvalue.$s;}
  else if ($s=='}') {
   $state = GETVALUEDELIMITEDBYCURLYBRACKETS_1NESTEDLEVEL;$entryvalue=$entryvalue.$s;}
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
   $state = GETVALUEDELIMITEDBYCURLYBRACKETS_2NESTEDLEVEL;$entryvalue=$entryvalue.$s;}
  else { $entryvalue=$entryvalue.$s;}
 }
 // handle anti-slashed brackets
 else if ($state==GETVALUEDELIMITEDBYCURLYBRACKETS_3NESTEDLEVEL_ESCAPED) {
  $state = GETVALUEDELIMITEDBYCURLYBRACKETS_3NESTEDLEVEL;
  $entryvalue=$entryvalue.$s;
 }

/* handles entries delimited by double quotes */
 else if ($state==GETVALUEDELIMITEDBYQUOTES) {

  if ($s=='\\') {
   $state = GETVALUEDELIMITEDBYQUOTES_ESCAPED;
   $entryvalue=$entryvalue.$s;}
  else if ($s=='"') {
   $state = GETVALUE;
  }
  else {  $entryvalue=$entryvalue.$s;}
 }
 // handle anti-double quotes
 else if ($state==GETVALUEDELIMITEDBYQUOTES_ESCAPED) {
  $state = GETVALUEDELIMITEDBYQUOTES;
  $entryvalue=$entryvalue.$s;
 }

} // end for
} // end while
$delegate->endFile();
fclose($handle);
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
class BibDBBuilder {

  /** A hashtable from keys to bib entries (BibEntry). */
  var $builtdb;

  /** A hashtable of constant strings */
  var $stringdb;

  var $currentEntry;

  function BibDBBuilder($filename, &$builtdb, &$stringdb) {
    $this->builtdb = $builtdb;
    $this->stringdb = $stringdb;
    new StateBasedBibtexParser($filename, $this);
  }


  function beginFile() {
  }

  function endFile() {
    // resolving crossrefs
    // we are careful with PHP 4 semantics 
    foreach (array_keys($this->builtdb) as $key) {
      $bib = &$this->builtdb[$key];
      if ($bib->hasField('crossref')) {
        if (isset($this->builtdb[$bib->getField('crossref')])) {
          $crossrefEntry = $this->builtdb[$bib->getField('crossref')];
          $bib->crossref = $crossrefEntry;
          foreach($crossrefEntry->getFields() as $k => $v) {
            // copying the fields of the cross ref
            // only if they don't exist yet
            if (!$bib->hasField($k)) {
              $bib->setField($k,$v);
            }
          }
        }
      }
    }
  }

  function setEntryField($finalkey,$entryvalue) {
    // is it a constant? then we replace the value
    // we support advanced features of bibtexbrowser
    // see http://newton.ex.ac.uk/tex/pack/bibtex/btxdoc/node3.html
    $entryvalue_array=explode('#',$entryvalue);
    foreach ($entryvalue_array as $k=>$v) {
      // spaces are allowed when using #, they are not taken into account
      // however # is not istself replaced by a space
      // warning: @strings are not case sensitive
      // see http://newton.ex.ac.uk/tex/pack/bibtex/btxdoc/node3.html
      $stringKey=strtolower(trim($v));
      if (isset($this->stringdb[$stringKey]))
      {
        // this field will be formated later by xtrim and latex2html
        $entryvalue_array[$k]=$this->stringdb[$stringKey];

        // we keep a trace of this replacement
        // so as to produce correct bibtex snippets
        $this->currentEntry->constants[$stringKey]=$this->stringdb[$stringKey];
      }
    }
    $entryvalue=implode('',$entryvalue_array);

    $this->currentEntry->setField($finalkey,$entryvalue);
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
  
    // we can set the fulltext
    $this->currentEntry->text = $entrysource;
    
    // we format the author names in a special field
    // to enable search
    if ($this->currentEntry->hasField('author')) {
      $this->currentEntry->setField('_author',$this->currentEntry->getFormattedAuthorsImproved());
    }

    // ignoring jabref comments
    if (($this->currentEntry->getType()=='comment')) {
      /* do nothing for jabref comments */
    } 
    
    // we add it to the string database
    else if ($this->currentEntry->getType()=='string') {
      foreach($this->currentEntry->fields as $k => $v) {
        $k!='type' and $this->stringdb[$k]=$v;
      }
    } 
    
    // we add it to the database
    else {
      $this->builtdb[$this->currentEntry->getKey()] = $this->currentEntry;
    }
  }
} // end class BibDBBuilder




/** extended version of the trim function
 * removes linebreaks, tabs, etc.
 */
function xtrim($line) {
  $line = trim($line);
  // we remove the unneeded line breaks
  // this is *required* to correctly split author lists into names
  // 2010-06-30
  // bug found by Thomas
  // windows new line is **\r\n"** and not the other way around!!
  $line = str_replace("\r\n",' ', $line);//windows like
  $line = str_replace("\n",' ', $line);//unix-like
  // we also replace tabs
  $line = str_replace("\t",' ', $line);
  // remove superfluous spaces e.g. John+++Bar
  $line = preg_replace('/ {2,}/',' ', $line);
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
  $line = preg_replace('/([^\\\\])~/','\\1&nbsp;', $line);

  // performance increases with this test
  // bug found by Serge Barral: what happens if we have curly braces only (typically to ensure case in Latex)
  // added && strpos($line,'{')===false
  if (strpos($line,'\\')===false && strpos($line,'{')===false) return $line;

  // handling \url{....}
  // often used in howpublished for @misc
  $line = preg_replace('/\\\\url\{(.*)\}/U','<a href="\\1">\\1</a>', $line);

  // Friday, April 01 2011
  // added support for accented i
  // for instance \`\i
  // see http://en.wikibooks.org/wiki/LaTeX/Accents
  // " the letters "i" and "j" require special treatment when they are given accents because it is often desirable to replace the dot with the accent. For this purpose, the commands \i and \j can be used to produce dotless letters."
  $line = preg_replace('/\\\\([ij])/i','\\1', $line);

  $line = char2html($line,"'",'a',"acute");
  $line = char2html($line,"'",'e',"acute");
  $line = char2html($line,"'",'i',"acute");
  $line = char2html($line,"'",'o',"acute");
  $line = char2html($line,"'",'u',"acute");
  $line = char2html($line,"'",'y',"acute");
  $line = char2html($line,"'",'n',"acute");
  
  $line = char2html($line,'`','a',"grave");
  $line = char2html($line,'`','e',"grave");
  $line = char2html($line,'`','i',"grave");
  $line = char2html($line,'`','o',"grave");
  $line = char2html($line,'`','u',"grave");

  $line = char2html($line,'~','a',"tilde");
  $line = char2html($line,'~','n',"tilde");
  $line = char2html($line,'~','o',"tilde");

  $line = char2html($line,'"','a',"uml");
  $line = char2html($line,'"','e',"uml");
  $line = char2html($line,'"','i',"uml");
  $line = char2html($line,'"','o',"uml");
  $line = char2html($line,'"','u',"uml");
  $line = char2html($line,'"','y',"uml");

  $line = char2html($line,'^','a',"circ");
  $line = char2html($line,'^','e',"circ");
  $line = char2html($line,'^','i',"circ");
  $line = char2html($line,'^','o',"circ");
  $line = char2html($line,'^','u',"circ");

  $line = char2html($line,'.','a',"ring");

  $line = char2html($line,'c','c',"cedil");

  $line = str_replace('\\ae','&aelig;', $line);
  $line = str_replace('\\ss','&szlig;', $line);

  $line = str_replace('\\o','&oslash;', $line);
  $line = str_replace('\\O','&Oslash;', $line);
  $line = str_replace('\\&','&amp;', $line);

  // clean out extra tex curly brackets, usually used for preserving capitals
  $line = str_replace('}','', $line);
  $line = str_replace('{','', $line);

  return $line;
}

/** Note that & are encoded as %26 and not as &amp; so it does not break the Z3988 URL */
function s3988($s) {return urlencode(utf8_encode($s));}

/** @deprecated */
function formatAuthor() {
  die('Sorry, this function does not exist anymore, however, you can simply use $bibentry->formatAuthor($author) instead.');
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

  /** The constants @STRINGS referred to by this entry */
  var $constants;

  /** The crossref entry if there is one */
  var $crossref;

  /** The verbatim copy (i.e., whole text) of this bib entry. */
  var $text;

  /** Creates an empty new bib entry. Each bib entry is assigned a unique
   * identification number. */
  function BibEntry() {
    static $id = 0;
    $this->id = $id++;
    $this->fields = array();
    $this->constants = array();
    $this->text ='';
  }

  /** Returns the type of this bib entry. */
  function getType() {
    // strtolower is important to be case-insensitive
    return strtolower($this->getField(Q_TYPE));
  }

  /** Sets a field of this bib entry. */
  function setField($name, $value) {
    $name = strtolower($name);
    // fields that should not be transformed
    // we assume that "comment" is never latex code
    // but instead could contain HTML code (with links using the character "~" for example)
    // so "comment" is not transformed too
    if ($name!='url' && $name!='comment') { 
      $value = xtrim($value); 
      $value = latex2html($value);
    } else {
      //echo "xx".$value."xx\n";
    }
    
    $this->fields[$name] = $value;
  }

  /** Sets a type of this bib entry. */
  function setType($value) {
    // 2009-10-25 added trim
    // to support space e.g. "@article  {"
    // as generated by ams.org
    // thanks to Jacob Kellner 
    $this->fields[Q_TYPE] =trim($value);
  }

  /** Tries to build a good URL for this entry */
  function getURL() {
    if ($this->hasField('url')) return $this->getField('url');
    else return "http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']).'/'.BIBTEXBROWSER_URL.'?'.createQueryString(array('key'=>$this->getKey()));
  }

  /** returns a "[pdf]" link if relevant */
  function getUrlLink() {
    if ($this->hasField('url')) return ' <a href="'.$this->getField('url').'">[pdf]</a>';
    return '';
  }

  /** Reruns the abstract */
  function getAbstract() {
    if ($this->hasField('abstract')) return $this->getField('abstract');
    else return '';
  }


  /** Has this entry the given field? */
  function hasField($name) {
    return isset($this->fields[strtolower($name)]);
  }

  /** Returns the authors of this entry. If "author" is not given,
   * return a string 'Unknown'. */
  function getAuthor() {
    if (array_key_exists(AUTHOR, $this->fields)) {
      return $this->fields[AUTHOR];
    }
    // 2010-03-02: commented the following, it results in misleading author lists
    // issue found by Alan P. Sexton
    //if (array_key_exists(EDITOR, $this->fields)) {
    //  return $this->fields[EDITOR];
    //}
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

   /** Returns the publisher of this entry
    * It encodes a specific logic
    * */
  function getPublisher() {
    // citation_publisher
    if ($this->hasField("publisher")) {
      return $this->getField("publisher");
    }
    if ($this->getType()=="phdthesis") {
      return $this->getField(SCHOOL);
    }
    if ($this->getType()=="mastersthesis") {
      return $this->getField(SCHOOL);
    }
    if ($this->getType()=="bachelorsthesis") {
      return $this->getField(SCHOOL);
    }
    if ($this->getType()=="techreport") {
      return $this->getField("institution");
    }
    // then we don't know
    return '';
  }

  /** Returns the authors of this entry as an array */
  function getRawAuthors() {
    $authors = array();
    foreach (preg_split('/ and /i', $this->getAuthor()) as $author) {
      $authors[]=$author;
    }
    return $authors;
  }
  
  /**
  * Returns the formated author name w.r.t to the user preference encoded in COMMA_NAMES
  */
  function formatAuthor($author){
    if (COMMA_NAMES) {
      return $this->formatAuthorCommaSeparated($author);
    }
    else return $this->formatAuthorCanonical($author);
  }

  /**
  * Returns the formated author name as "FirstName LastName".
  */
  function formatAuthorCanonical($author){
      list($firstname, $lastname) = splitFullName($author);
      if ($firstname!='') return $firstname.' '.$lastname;
      else return $lastname;
  }

  /**
  * Returns the formated author name as "LastName, FirstName".
  */
  function formatAuthorCommaSeparated($author){
      list($firstname, $lastname) = splitFullName($author);
      if ($firstname!='') return $lastname.', '.$firstname;
      else return $lastname;
  }


  /** Returns the authors as a string depending on the configuration parameter COMMA_NAMES  */
  function getFormattedAuthors() {
    $authors = array();
    foreach ($this->getRawAuthors() as $author) {
      $authors[]=$this->formatAuthor($author);
    }
    return $authors;
  }

  /** @deprecated
  *   @see getFormattedAuthorsImproved()
  */
  function formattedAuthors() {  return $this->getFormattedAuthorsImproved(); }
  
  /** Adds to getFormattedAuthors() the home page links and returns a string (not an array)
  */
  function getFormattedAuthorsImproved() {
    $array_authors = $this->getFormattedAuthors();
    foreach ($array_authors as $k => $author) {
      $array_authors[$k]=$this->addHomepageLink($author);
    }
    
    if (COMMA_NAMES) {$sep = '; ';} else {$sep = ', ';}
    return implode($sep ,$array_authors);
  }

  /** Returns the authors of this entry as an array in a canonical form */
  function getCanonicalAuthors() {
    $authors = array();
    foreach ($this->getRawAuthors() as $author) {
      $authors[]=$this->formatAuthorCanonical($author);
    }
    return $authors;
  }

  /** Returns the authors of this entry as an array in a comma-separated form */
  function getArrayOfCommaSeparatedAuthors() {
    $authors = array();
    foreach ($this->getRawAuthors() as $author) {
      $authors[]=$this->formatAuthorCommaSeparated($author);
    }
    return $authors;
  }

  /**
  * Returns a compacted string form of author names by throwing away
  * all author names except for the first one and appending ", et al."
  */
  function getCompactedAuthors($author){
    $authors = $this->getAuthors();
    $etal = count($authors) > 1 ? ', et al.' : '';
    return $this->formatAuthor($authors[0]) . $etal;
  }


  /** add the link to the homepage if it is defined in a string
   *  e.g. @string{hp_MartinMonperrus="http://www.monperrus.net/martin"}
   *  The string is a concatenation of firstname, lastname, prefixed by hp_ 
   * Warning: by convention @string are case sensitive so please be keep the same case as author names
   * @thanks Eric Bodden for the idea
   */
  function addHomepageLink($author) {
    // hp as home page
    // accents are handled normally
    // e.g. @STRING{hp_Jean-MarcJézéquel="http://www.irisa.fr/prive/jezequel/"}
    $homepage = strtolower('hp_'.preg_replace('/ /', '', $author));
    if (isset($_GET[Q_DB]->stringdb[$homepage]))
      $author='<a href="'.$_GET[Q_DB]->stringdb[$homepage].'">'.$author.'</a>';
    return $author;
  }


  /** Returns the editors of this entry as an arry */
  function getEditors() {
    $editors = array();
    foreach (preg_split('/ and /i', $this->getField(EDITOR)) as $editor) {
      $editors[]=$editor;
    }
    return $editors;
  }

  /** Returns the editors of this entry as an arry */
  function getFormattedEditors() {
    $editors = array();
    foreach ($this->getEditors() as $editor) {
      $editors[]=$this->formatAuthor($editor);
    }
    if (COMMA_NAMES) {$sep = '; ';} else {$sep = ', ';}
    return implode($sep, $editors).', '.(count($editors)>1?'eds.':'ed.');
  }


  /** Returns the year of this entry? */
  function getYear() {
    return $this->getField('year');
  }

  /** Returns the value of the given field? */
  function getField($name) {
    // 2010-06-07: profiling showed that this is very costly
    // hence returning the value directly    
    //if ($this->hasField($name))
    //    {return $this->fields[strtolower($name)];}
    //else return 'missing '.$name;
    
    return @$this->fields[strtolower($name)];
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
  
    // 2010-01-25
    // bug found by jacob kellner
    // we have to search in the formatted fileds and not in the raw entry
    // i.e. all latex markups are not considered for searches
    // i.e. added join(" ",$this->getFields())
    // and html_entity_decode
    if (!$field) {
      // warning html_entity_decode supports encoding since PHP5
      return preg_match('/'.$phrase.'/i',$this->getConstants().' '.@html_entity_decode(join(" ",$this->getFields()),ENT_NOQUOTES,ENCODING));
      //return stripos($this->getText(), $phrase) !== false;
    }
    if ($this->hasField($field) &&  (preg_match('/'.$phrase.'/i',$this->getField($field)) ) ) {
    //if ($this->hasField($field) &&  (stripos($this->getField($field), $phrase) !== false) ) {
      return true;
    }

    return false;
  }


  /** Outputs an HTML line (<tr>)with two TDS inside
  */
  function toTR() {
        echo '<tr class="bibline">';
        echo '<td  class="bibref"><a name="'.$this->getId().'"></a>['.$this->getId().']</td> ';
        echo '<td class="bibitem">';
        echo bib2html($this);

        $href = 'href="'.BIBTEXBROWSER_URL.'?'.createQueryString(array(Q_KEY => $this->getKey())).'"';

        // we add biburl and title to be able to retrieve this important information
        // using Xpath expressions on the XHTML source
        echo " <a".(BIBTEXBROWSER_BIB_IN_NEW_WINDOW?' target="_blank" ':'')." class=\"biburl\" title=\"".$this->getKey()."\" {$href}>[bib]</a>";

        // returns an empty string if no url present
        echo $this->getUrlLink();

        if ($this->hasField('doi')) {
            echo ' <a href="http://dx.doi.org/'.$this->getField("doi").'">[doi]</a>';
        }

        echo "</td></tr>\n";


  }

  /** Outputs an coins URL: see http://ocoins.info/cobg.html
   * Used by Zotero, mendeley, etc.
  */
  function toCoins() {
    $url_parts=array();
    $url_parts[]='ctx_ver=Z39.88-2004';

    $type = $this->getType();
    if ($type=="book") {
      $url_parts[]='rft_val_fmt='.s3988('info:ofi/fmt:kev:mtx:book');
      $url_parts[]='rft.btitle='.s3988($this->getTitle());
      $url_parts[]='rft.genre=book';
    } else if ($type=="inproceedings") {
      $url_parts[]='rft_val_fmt='.s3988('info:ofi/fmt:kev:mtx:book');
      $url_parts[]='rft.atitle='.s3988($this->getTitle());
      $url_parts[]='rft.btitle='.s3988($this->getField(BOOKTITLE));

      // zotero does not support with this proceeding and conference
      // they give the wrong title
      //$url_parts[]='rft.genre=proceeding';
      //$url_parts[]='rft.genre=conference';
      $url_parts[]='rft.genre=bookitem';
    } else if ($type=="incollection" ) {
      $url_parts[]='rft_val_fmt='.s3988('info:ofi/fmt:kev:mtx:book');
      $url_parts[]='rft.btitle='.s3988($this->getField(BOOKTITLE));
      $url_parts[]='rft.atitle='.s3988($this->getTitle());
      $url_parts[]='rft.genre=bookitem';
    } else if ($type=="article") {
      $url_parts[]='rft_val_fmt='.s3988('info:ofi/fmt:kev:mtx:journal');
      $url_parts[]='rft.atitle='.s3988($this->getTitle());
      $url_parts[]='rft.jtitle='.s3988($this->getField("journal"));
      $url_parts[]='rft.volume='.s3988($this->getField("volume"));
      $url_parts[]='rft.issue='.s3988($this->getField("issue"));
    } else { // techreport, phdthesis
      $url_parts[]='rft_val_fmt='.s3988('info:ofi/fmt:kev:mtx:book');
      $url_parts[]='rft.btitle='.s3988($this->getTitle());
      $url_parts[]='rft.genre=report';
    }

    $url_parts[]='rft.pub='.s3988($this->getPublisher());

    // referent
    if ($this->hasField('url')) {
      $url_parts[]='rft_id='.s3988($this->getField("url"));
    } else if ($this->hasField('doi')) {
      $url_parts[]='rft_id='.s3988('info:doi/'.$this->getField("doi"));
    }

    // referrer, the id pf a collection of objects
    // see also http://www.openurl.info/registry/docs/pdf/info-sid.pdf
    $url_parts[]='rfr_id='.s3988('info:sid/'.$_SERVER['HTTP_HOST'].':'.$_GET[Q_FILE]);

    $url_parts[]='rft.date='.s3988($this->getYear());

    foreach ($this->getFormattedAuthors() as $au) $url_parts[]='rft.au='.s3988($au);


    return '<span class="Z3988" title="'.implode('&amp;',$url_parts).'"></span>';

  }



   /**
   * rebuild the set of constants used if any as a string
   */
  function getConstants() {
    $result='';
    foreach ($this->constants as $k=>$v) {
      $result.='@string{'.$k.'="'.$v."\"}\n";
    }
    return $result;
  }

   /**
   * Displays a <pre> text of the given bib entry.
   * URLs are replaced by HTML links.
   */
  function toEntryUnformatted() {
    $result = "";
    $result .= '<pre class="purebibtex">'; // pre is nice when it is embedded with no CSS available
    $entry = str_replace('<','&lt;',$this->getFullText());
    if ($this->hasField('url')) {
      $url = $this->getField('url');
      // this is not a parsing but a simple replacement
      $entry = str_replace($url,'<a href="'.$url.'">'.$url.'</a>', $entry);
    }

    $result .=  $entry;
    $result .=  '</pre>';
    return $result;
   }

   /**
   * Gets the raw text of the entry (crossref + strings + entry)
   */
  function getFullText() {
    $s = '';
    // adding the crossref if necessary
    if ($this->crossref!=null) { $s .= $this->crossref->getFullText()."\n";}
    $s.=$this->getConstants();
    $s.=$this->getText();
    return $s;
  }
}


/** this function encapsulates the user-defined name for bib to HTML*/
function bib2html(&$bibentry) {
  $function = BIBLIOGRAPHYSTYLE;
  return $function($bibentry);
}

/**bibtexbrowser uses this function which encapsulates the user-defined sections*/
function _DefaultBibliographySections() {
  $function = BIBLIOGRAPHYSECTIONS;
  return $function();
}

/** the default sections */
function DefaultBibliographySections() {
return  
  array(
  // Books
    array(
      'query' => array(Q_TYPE=>'book'),
      'title' => 'Books'
    ),
  // Journal / Bookchapters
    array(
      'query' => array(Q_TYPE=>'article|incollection'),
      'title' => 'Refereed Articles and Book Chapters'
    ),
  // conference papers
    array(
      'query' => array(Q_TYPE=>'inproceedings|conference',Q_EXCLUDE=>'workshop'),
      'title' => 'Refereed Conference Papers'
    ),
  // workshop papers
    array(
      'query' => array(Q_TYPE=>'inproceedings',Q_SEARCH=>'workshop'),
      'title' => 'Refereed Workshop Papers'
    ),
  // misc and thesis
    array(
      'query' => array(Q_TYPE=>'misc|phdthesis|mastersthesis|bachelorsthesis|techreport'),
      'title' => 'Other Publications'
    )
  );
}


include('bibtexbrowser-style-default.php');





// ----------------------------------------------------------------------
// DISPLAY MANAGEMENT
// ----------------------------------------------------------------------


/**
 * Given an array of parameter, creates a query string
 */
function createQueryString($array_param) {
 // first we add the name of the bib file
 $array_param[Q_FILE] = urlencode($_GET[Q_FILE]);

 // then a simple transformation and implode
 foreach ($array_param as $key => $val) {
      $array_param[$key]=$key .'='. urlencode($val);
 }
 return implode("&amp;",$array_param);
}

/**
 * Given a query, an array of key value pairs, returns a href string
 * of the form: href="?bib=testing.bib&search=JML.
 */
function makeHref($query = NULL) {
  return 'href="?'. createQueryString($query) .'"';
}

/**
 * Returns the last name of an author name.
 */
function getLastName($author){
    list($firstname, $lastname) = splitFullName($author);
    return $lastname;
}

/**
 * Returns the splitted name of an author name as an array. The argument is assumed to be
 * <FirstName LastName> or <LastName, FirstName>.
 */
function splitFullName($author){
    $author = trim($author);
    // the author format is "Joe Dupont"
    if (strpos($author,',')===false) {
      $parts=explode(' ', $author);
      // get the last name
      $lastname = array_pop($parts);
      $firstname = implode(" ", $parts);
    }
    // the author format is "Dupont, J."
    else {
      $parts=explode(',', $author);
      // get the last name
      $lastname = str_replace(',','',array_shift($parts));
      $firstname = implode(" ", $parts);
    }
  return array(trim($firstname), trim($lastname));
}


/** New undocumented feature, used by Benoit Baudry
 * see http://www.irisa.fr/triskell/perso_pro/bbaudry/publications.php 
 *
 * $_GET['library']=1;
 * $_GET['bib']='metrics.bib';
 * $_GET['all']=1;
 * include( 'bibtexbrowser.php' );
 * setDB();
 * new IndependentYearMenu();
 * new Dispatcher();
 *
 */
class IndependentYearMenu  {
  function IndependentYearMenu() { 
    if (!isset($_GET[Q_DB])) {die('Did you forget to call setDB() before instantiating this class?');}
    $yearIndex = $_GET[Q_DB]->yearIndex();
    echo '<div id="yearmenu">Year: ';
    $formatedYearIndex = array();
    $formatedYearIndex[] = '<a '.makeHref(array(Q_YEAR=>'.*')).'>All</a>';    
    foreach($yearIndex as $year) {
      $formatedYearIndex[] = '<a '.makeHref(array(Q_YEAR=>$year)).'>'.$year.'</a>';
    }
    
    // by default the separator is a |
    echo implode('|',$formatedYearIndex);
    echo '</div>';
  }
}

/** Class to encapsulates the header formatting and the powered by footer  */
class BibtexBrowserDisplay {
  /** the title */
  var $title;

  function getTitle() { return $this->title; }

  function display() { /* unimplemented */ }

  /** returns the url of this display (e.g. base on the query)*/
  function getURL() { return '';}

  /** returns the url of the RSS  */
  function getRSS() { return '';}

  /** Returns the powered by part */
  function poweredby() {
    $poweredby = "\n".'<div style="text-align:right;font-size: xx-small;opacity: 0.6;" class="poweredby">';
    $poweredby .= '<!-- If you like bibtexbrowser, thanks to keep the link :-) -->';
    $poweredby .= 'Powered by <a href="http://www.monperrus.net/martin/bibtexbrowser/">bibtexbrowser</a><!--v__MTIME__-->';
    $poweredby .= '</div>'."\n";
    return $poweredby;
   }

  function  formatedHeader() { return "<div class=\"rheader\">{$this->title}</div>\n";}

  /** Adds a touch of AJAX in bibtexbrowser to display bibtex entries inline.
   * It uses the HIJAX design pattern: the Javascript code fetches the normal bibtex HTML page
   * and extracts the bibtex.
   * In other terms, URLs and content are left perfectly optimized for crawlers 
   * Note how beautiful is this piece of code thanks to JQuery.
   */
  function javascript() {
  // we use jquery with the official content delivery URLs
  // Microsoft and Google also provide jquery with their content delivery networks
?><script type="text/javascript" src="http://code.jquery.com/jquery-1.5.1.min.js"></script> 
<script type="text/javascript" ><!--
// Javascript progressive enhancement for bibtexbrowser
$('a.biburl').each(function(item) { // for each url "[bib]"
  var biburl = $(this);
  biburl.click(function(ev) { // we change the click semantics
    ev.preventDefault(); // no open url
    if (biburl.nextAll('pre').length == 0) { // we don't have yet the bibtex data
      var bibtexEntryUrl = $(this).attr('href');
      $.ajax({url: bibtexEntryUrl,  dataType: 'xml', success: function (data) { // we download it
        var elem = $('<pre class="purebibtex"/>'); // the element containing bibtex entry, creating a new element is required for Chrome and IE
        elem.text($('.purebibtex', data).text()); // both text() are required for IE
        // we add a link so that users clearly see that even with AJAX
        // there is still one URL per paper (which is important for crawlers and metadata)
        elem.append(
           $('<div>%% Bibtex entry URL: <a href="'+bibtexEntryUrl+'">'+bibtexEntryUrl+'</a></div>')
           ).appendTo(biburl.parent());
      }, error: function() {window.location.href = biburl.attr('href');}});
    } else {biburl.nextAll('pre').toggle();}  // we toggle the view    
  });
});
--></script><?php
  }
}



/**
 * A class providing GUI controllers in a frame.
 */
class MenuManager extends BibtexBrowserDisplay {

  /** The bibliographic database, an instance of class BibDataBase. */
  var $db;

  /** Creates a new display manager that uses the given bib database. */
  function MenuManager(&$db) {
    $this->db =$db;
  }

  /** function called back by HTMLWrapper */
  function display() {
  echo $this->searchView().'<br/>';
  echo $this->typeVC().'<br/>';
  echo $this->yearVC().'<br/>';
  echo $this->authorVC().'<br/>';
  echo $this->tagVC().'<br/>';
  }

  /** Displays the title in a table. */
  function titleView() {
    ?>
    <table>
      <tr>
        <td class="title">Generated from <?php echo $_GET[Q_FILE]; ?></td>
      </tr>
    </table>
    <?php
  }

  /** Displays the search view in a form. */
  function searchView() {
    ?>
    <form action="?" method="get" target="main">
      <input type="text" name="<?php echo Q_SEARCH; ?>" class="input_box" size="18"/>
      <input type="hidden" name="<?php echo Q_FILE; ?>" value="<?php echo $_GET[Q_FILE]; ?>"/>
      <br/>
      <input type="submit" value="search" class="input_box"/>
    </form>
    <?php
  }

  /** Displays and controls the types menu in a table. */
  function typeVC() {
      $types = array();
      $types[''] = 'all types';
      foreach ($this->db->getTypes() as $type) {
        $types[$type] = $type;
      }
    // retreive or calculate page number to display
    if (isset($_GET[Q_TYPE_PAGE])) {
      $page = $_GET[Q_TYPE_PAGE];
    }
    else $page = 1;

    $this->displayMenu('Types', $types, $page, TYPES_SIZE, Q_TYPE_PAGE, Q_TYPE);
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


    if (count($tags)>0) $this->displayMenu('Keywords', $tags, $page, TAGS_SIZE, Q_TAG_PAGE,
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
    <table style="width:100%"  class="menu">
      <tr>
        <td>
        <!-- this table is used to have the label on the left 
        and the navigation links on the right -->
        <table style="width:100%" border="0" cellspacing="0" cellpadding="0">
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

    $result = '';
    /* // commented after the usability remarks of Benoit Combemale
    // fast (2 pages) reverse (<<)
    if ($start - $pageSize > 0) {
      $href = makeHref(array($queryKey => $page - 2,'menu'=>''));
      $result .= '<a '. $href ."><b>&#171;</b></a>\n";
    }*/

    // (1 page) reverse (<)
    if ($start > 0) {
      $href = makeHref(array($queryKey => $page - 1,'menu'=>''));
      $result .= '<a '. $href ."><b>[prev]</b></a>\n";
    }

    // (1 page) forward (>)
    if ($end < $numEntries) {
      $href = makeHref(array($queryKey => $page + 1,'menu'=>''));
      $result .= '<a '. $href ."><b>[next]</b></a>\n";
    }

    /*// commented after the usability remarks of Benoit Combemale
    // fast (2 pages) forward (>>)
    if (($end + $pageSize) < $numEntries) {
      $href = makeHref(array($queryKey => $page + 2,'menu'=>''));
      $result .= '<a '. $href ."><b>&#187;</b></a>\n";
    }*/
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
 $href = makeHref(array($queryKey => $key));
 echo '<a '. $href .' target="main">'. $item ."</a>\n";
 echo "<div class=\"mini_se\"></div>\n";
      }
      $index++;
    }
  }
}

/** transforms an array representing a query into a formatted string */
function query2title(&$query) {
    $headers = array();
    foreach($query as $k=>$v) {
      if($k == '_author') { $k = 'author'; }
      if($k == 'type') { $v = substr($v,1,strlen($v)-2); }
      $headers[$k] = ucwords($k).': '.ucwords($v);
  }
    // special cases
    if (isset($headers[Q_ALL])) $headers[Q_ALL] = 'Publications in '.$_GET[Q_FILE];
    if (isset($headers[Q_AUTHOR])) $headers[Q_AUTHOR] = 'Publications of '.$_GET[Q_AUTHOR];
    return join(' &amp; ',$headers);
}


/** Class to display a result as a set of pages. */
class PagedDisplay extends BibtexBrowserDisplay {
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

  /** the content strategy (cf. pattern strategy) */
  var $contentStrategy;

  /** the query to reinject in links to different pages */
  var $filter;

  /** Creates an instance with the given entries and header. */
  function PagedDisplay(&$result, $filter) {
    $this->result = $result;
    $this->filter = $filter;
    // requesting a different page of the result view?
    if (isset($_GET[Q_RESULT])) {
      $this->setPage($_GET[Q_RESULT]);
    } else $this->page = 1;
    $this->setTitle();
    $this->contentStrategy = new DefaultContentStrategy();
  }

  /** sets the $this->title of BibtexBrowserDisplay based on the $filter */
  function setTitle() {
    $this->title = query2title($this->filter);
    if ($this->page>1) $this->title.=' - page '.$this->page;
  }

  /** Sets the page number to display. */
  function setPage($page) {
    $this->page = $page;
  }

  /** overrides */
  function  formatedHeader() { return '<div class="rheader">'.$this->title.' '.createRSSLink($this->filter).'</div>';}

  /** overrides */
  function getURL() { return '?'.createQueryString($this->filter);}

  /** overrides */
  function getRSS() { return BIBTEXBROWSER_URL.'?'.createQueryString($this->filter).'&amp;rss';}

  /** Displays the entries preceded with the header. */
  function display() {

    $page = $this->page;

    // print error message if no entry.
    if (empty($this->result)) {
      echo "<b>No match found!</b>\n";
      return;
    }

    $this->noPages = ceil(count($this->result) / PAGE_SIZE);

      /** Displays the header stringg. */
    echo $this->formatedHeader();

    if ($this->noPages>1) $this->displayPageBar($this->noPages, $page);

    $this->startIndex = ($page - 1) * PAGE_SIZE;
    $this->endIndex =$this->startIndex + PAGE_SIZE;

    $this->contentStrategy->display($this);
    if ($this->noPages>1) $this->displayPageBar($this->noPages, $page);

    echo $this->poweredby();
    
    if (BIBTEXBROWSER_USE_PROGRESSIVE_ENHANCEMENT) {
      $this->javascript();
    }

  }

  function isDisplayed($index) {
    if ($index >= $this->startIndex && $index < $this->endIndex) return true;
    return false;
  }


  /** Displays a page bar consisting of clickable page numbers. */
  function displayPageBar($noPages, $page) {

    // bug found by benoit, first we have to reset the q_result
    $this->filter[Q_RESULT] = 1;

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

/** creates an RSS link with text or image depending on the environment */
  function createRSSLink($filter) {
    // default label
    $label='[rss]';
    // auto adaptive code :-)
    //if (is_file('rss.png')) $label='<img src="rss.png"/>';
    return '<a href="'.BIBTEXBROWSER_URL.'?'.createQueryString($filter).'&amp;rss" class="rsslink">'.$label.'</a>';
}


/**
  * Displays the summary information of each bib entries of the
  * current page. For each entry, this method displays the author,
  * title; the bib entries are displayed grouped by the
  * publication years. If the bib list is empty, an error message is
  * displayed.
  */
class DefaultContentStrategy  {

  /** $display: an instance of PagedDisplay */
  function display(&$display) {
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
          $bib->toTR();
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
class NonExistentBibEntryError  {

  function NonExistentBibEntryError() {
    header('HTTP/1.1 404 Not found');
    ?>
    <b>Sorry, this bib entry does not exist.</b>
    <a href="?">Back to bibtexbrowser</a>
    <?php
    exit;
  }
}

/** Class to display the publication records sorted by publication types. */
class AcademicDisplay extends BibtexBrowserDisplay {

  /** the query */
  var $query;

  /**
   * $entries: an array of bib entries
   * $query: the array representing the query
   */
  function AcademicDisplay(&$entries,&$query) {
    $this->query=$query;
    $this->db=new BibDataBase();
    $this->db->bibdb = $entries;
    $this->title = query2title($query);
  }

  /** overrides */
  function  formatedHeader() { return '<div class="rheader">'.$this->title.' '.createRSSLink($this->query).'</div>';}


  /** transforms a query to HTML 
   * $ query is an array (e.g. array(Q_TYPE=>'book'))
   * $title is a string, the title of the section
   */
  function search2html($query, $title) {
    $entries = $this->db->multisearch($query);
    if (count($entries)>0) {
    echo "\n".'<div class="header">'.$title.'</div>'."\n";
    echo '<table class="result">'."\n";
    foreach ($entries as $bib) {
        $bib->id = $bib->getYear();
        $bib->toTR();
    } // end foreach
    echo '</table>';
    }

  }
  
  function display() {
    echo $this->formatedHeader();
    foreach (_DefaultBibliographySections() as $section) {
      $this->search2html($section['query'],$section['title']);
    }

    echo $this->poweredby();
    
    if (BIBTEXBROWSER_USE_PROGRESSIVE_ENHANCEMENT) {
      $this->javascript();
    }

  }

}




/** Class to display a single bib entry.
 * This view is optimized for Google Scholar
 * */
class BibEntryDisplay extends BibtexBrowserDisplay {

  /** the bib entry to display */
  var $bib;

  /** Creates an instance with the given bib entry and header.
   * It the object is an instance of BibIndexEntry, it may be
   * mutated to read the rest of the fields.
   */
  function BibEntryDisplay(&$bibentry) {
    $this->bib = $bibentry;
    $this->title = $this->bib->getTitle().' (bibtex)';
    //$this->title = $this->bib->getTitle().' (bibtex)'.$this->bib->getUrlLink();
  }


  function display() {
    // we encapsulate everything so that the output of display() is still valid XHTML
    echo '<div>';
    echo $this->bib->toCoins();
    echo $this->formatedHeader();
    echo $this->bib->toEntryUnformatted();
    //echo $this->bib->getUrlLink();
    echo $this->poweredby();
    echo '</div>';
  }

  /** Creates metadata for Google Scholar
   * + a description
   * @see http://scholar.google.com/intl/en/scholar/inclusion.html
   * @see http://www.monperrus.net/martin/accurate+bibliographic+metadata+and+google+scholar
   * */
  function metadata() {
    $result=array();
    
    if (METADATA_GS) {
    // the description may mix with the Google Scholar tags
    // we remove it
    // $result[] = array('description',trim(strip_tags(str_replace('"','',bib2html($this->bib)))));
    $result[] = array('citation_title',$this->bib->getTitle());
    $authors = $this->bib->getArrayOfCommaSeparatedAuthors();
    $result[] = array('citation_authors',implode("; ",$authors));
    foreach($authors as $author) {
      $result[] = array('citation_author',$author);
    }
    $result[] = array('citation_date',$this->bib->getYear());

    // this page
    $result[] = array('citation_abstract_html_url','http://'.$_SERVER['HTTP_HOST'].($_SERVER['SERVER_PORT']=='80'?'':$_SERVER['SERVER_PORT']).str_replace('&','&amp;',$_SERVER['REQUEST_URI']));
    
    if ($this->bib->hasField("publisher")) {
      $result[] = array('citation_publisher',$this->bib->getPublisher());
    }

    // BOOKTITLE: JOURNAL NAME OR PROCEEDINGS
    if ($this->bib->getType()=="article") { // journal article
      $result[] = array('citation_journal_title',$this->bib->getField("journal"));
      $result[] = array('citation_volume',$this->bib->getField("volume"));
      if ($this->bib->hasField("issue")) {
        $result[] = array('citation_issue',$this->bib->getField("issue"));
      }
      if ($this->bib->hasField("issn")) {
        $result[] = array('citation_issue',$this->bib->getField("issn"));
      }
    } 

    if ($this->bib->getType()=="inproceedings" || $this->bib->getType()=="conference") {
       $result[] = array('citation_conference_title',$this->bib->getField(BOOKTITLE));
       $result[] = array('citation_conference',$this->bib->getField(BOOKTITLE));
    }

    if ($this->bib->getType()=="phdthesis"
         || $this->bib->getType()=="mastersthesis"
         || $this->bib->getType()=="bachelorsthesis"
       )
    {
       $result[] = array('citation_dissertation_institution',$this->bib->getField('school'));
    }

    if ($this->bib->getType()=="techreport"
         && $this->bib->hasField("number")
       )
    {
       $result[] = array('citation_technical_report_number',$this->bib->getField('number'));
    }

    if ($this->bib->getType()=="techreport"
         && $this->bib->hasField("institution")
       )
    {
       $result[] = array('citation_technical_report_institution',$this->bib->getField('institution'));
    }

    // generic
    if ($this->bib->hasField("doi")) {
      $result[] = array('citation_doi',$this->bib->getField("doi"));
    }

    if ($this->bib->hasField("url")) {
      $result[] = array('citation_pdf_url',$this->bib->getField("url"));
    }
    }
    
    
    // we don't introduce yet another kind of bibliographic metadata
    // the core bibtex metadata will simply be available as json
    // now adding the pure bibtex with no translation
    //foreach ($this->bib->getFields() as $k => $v) {
    //  if (!preg_match("/^_/",$k)) {
    //    $result[] = array("bibtex:".$k,$v);  
    //  }
    //}

    
    // a fallback to essential dublin core
    // Dublin Core should not be used for bibliographic metadata
    // according to several sources 
    //  * Google Scholar: "Use Dublin Core tags (e.g., DC.title) as a last resort - they work poorly for journal papers"
    //  * http://reprog.wordpress.com/2010/09/03/bibliographic-data-part-2-dublin-cores-dirty-little-secret/
    // however it seems that Google Scholar needs at least DC.Title to trigger referencing
    // reference documentation: http://dublincore.org/documents/dc-citation-guidelines/
    if (METADATA_DC) {
    $result[] = array('DC.Title',$this->bib->getTitle());
    foreach($this->bib->getArrayOfCommaSeparatedAuthors() as $author) {
      $result[] = array('DC.Creator',$author);
    }
    $result[] = array('DC.Date',$this->bib->getYear());
    }

    // --------------------------------- BEGIN METADATA EPRINTS
    // and now adding eprints metadata
    // why adding eprints metadata?
    // because eprints is a well known bibliographic software and several crawlers/desktop software
    // use their metadata
    // unfortunately, the metadata is even less documented than Google Scholar citation_
    // reference documentation: the eprints source code (./perl_lib/EPrints/Plugin/Export/Simple.pm)
    // examples: conference paper: http://tubiblio.ulb.tu-darmstadt.de/44344/
    //           journal paper: http://tubiblio.ulb.tu-darmstadt.de/44344/
    if (METADATA_EPRINTS) {
    $result[] = array('eprints.title',$this->bib->getTitle());
    $authors = $this->bib->getArrayOfCommaSeparatedAuthors();
    foreach($authors as $author) {
      $result[] = array('eprints.creators_name',$author);
    }
    $result[] = array('eprints.date',$this->bib->getYear());

    if ($this->bib->hasField("publisher")) {
      $result[] = array('eprints.publisher',$this->bib->getPublisher());
    }

    if ($this->bib->getType()=="article") { // journal article
      $result[] = array('eprints.type','article');
      $result[] = array('eprints.publication',$this->bib->getField("journal"));
      $result[] = array('eprints.volume',$this->bib->getField("volume"));
      if ($this->bib->hasField("issue")) {
        $result[] = array('eprints.number',$this->bib->getField("issue"));}
    } 

    if ($this->bib->getType()=="inproceedings" || $this->bib->getType()=="conference") {
       $result[] = array('eprints.type','proceeding');
       $result[] = array('eprints.book_title',$this->bib->getField(BOOKTITLE));
    }

    if ($this->bib->getType()=="phdthesis"
         || $this->bib->getType()=="mastersthesis"
         || $this->bib->getType()=="bachelorsthesis"
       )
    {
       $result[] = array('eprints.type','thesis');
       $result[] = array('eprints.institution',$this->bib->getField('school'));
    }

    if ($this->bib->getType()=="techreport")
    {
       $result[] = array('eprints.type','monograph');
       if ($this->bib->hasField("number")) {
         $result[] = array('eprints.number',$this->bib->getField('number'));
       }
       if ($this->bib->hasField("institution")) {
         $result[] = array('eprints.institution',$this->bib->getField('institution'));
       }
    }

    // generic
    if ($this->bib->hasField("doi")) {
      $result[] = array('eprints.id_number',$this->bib->getField("doi"));
    }

    if ($this->bib->hasField("url")) {
      $result[] = array('eprints.official_url',$this->bib->getField("url"));
    }
    }
    // --------------------------------- END METADATA EPRINTS

    return $result;

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

  /** A hashtable of constant strings */
  var $stringdb;

  /** Creates a new database by parsing bib entries from the given
   * file. */
  function load($filename) {
    $db = new BibDBBuilder($filename, $this->bibdb, $this->stringdb);
    //print_r($parser);
    $this->bibdb = $db->builtdb;
    $this->stringdb = $db->stringdb;
    //print_r($this->stringdb);
  }

  /** Creates a new empty database */
  function BibDataBase() {
    $this->bibdb = array();
    $this->stringdb = array();
  }

  /** Returns all entries as an array. Each entry is an instance of
   * class BibEntry. */
  function getEntries() {
    return $this->bibdb;
  }
  /** tests wheter the database contains a bib entry with $key */
  function contains($key) {
    return isset($this->bibdb[$key]);
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
      foreach($bib->getRawAuthors() as $a){
        //we use an array because several authors can have the same lastname
        @$result[getLastName($a)][$bib->formatAuthor($a)]++;
      }
    }
    ksort($result);

    // now authors are sorted by last name
    // we rebuild a new array for having good keys in author page
    $realresult = array();
    foreach($result as $x) {
        ksort($x);
        foreach($x as $v => $tmp) $realresult[$v] = $v;
    }

    return $realresult;
  }

  /** Generates and returns an array consisting of all tags.
   */
  function tagIndex(){
    $result = array();
    foreach ($this->bibdb as $bib) {
      if (!$bib->hasField("keywords")) continue;
      $tags =preg_split('/[,;]/', $bib->getField("keywords"));
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
      if (!$bib->hasField("year")) continue;
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
    if (isset($query[Q_ALL])) return array_values($this->bibdb);

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

/* returns the default embedded CSS of bibtexbrowser */
function bibtexbrowserDefaultCSS() {
?>

/* title */
.bibtitle { font-weight:bold; }
/* author */
.bibauthor { /* nothing by default */ }
/* booktitle (e.g. proceedings title, journal name, etc )*/
.bibbooktitle { font-style:italic; }
/* publisher */
.bibpublisher { /* nothing by default */ }


.title {
  color: #003366;
  font-size: large;
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

.bibref {
  padding:7px;
  padding-left:15px;
  vertical-align:text-top; 
}

.result {
  padding:0px;
  border: 1px solid #000000;
  margin:0px;
  background-color: #ffffff;
  width:100%;
}
.result a {
  text-decoration: none;
  color: #469AF8;
}

.result a:hover {
  color: #ff6633;
}

.purebibtex {
  font-family: monospace;
  background-color:#FFFFFF;
  font-size: small;
  border: 1px solid #000000;
  white-space:pre;
  
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

.rsslink {
  text-decoration: none;
  color:#F88017;
/* could be fancy, see : http://www.feedicons.com/ for icons*/
  /*background-image: url("rss.png"); text-indent: -9999px;*/
}
<?php
} // end function bibtexbrowserDefaultCSS

/** A class to wrap contents in an HTML page
    withe <HTML><BODY> and TITLE */
class HTMLWrapper {
/**
 * $content: an object with a display() method
 * $title: title of the page
 */
function HTMLWrapper(&$content,$metatags=array()/* an array name=>value*/) {

// when we load a page with AJAX
// the HTTP header is taken into account, not the <meta http-equiv>
header('Content-type: text/html; charset='.ENCODING);
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'."\n";

?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo ENCODING ?>"/>
<meta name="generator" content="bibtexbrowser v__MTIME__" />
<?php if ($content->getRSS()!='') echo '<link rel="alternate" type="application/rss+xml" title="RSS" href="'.$content->getRSS().'&amp;rss" />'; ?>
<?php 

foreach($metatags as $item) {
  list($name,$value) = $item;
  echo '<meta name="'.$name.'" content="'.$value.'"/>'."\n"; 
} // end foreach  



// now the title
echo '<title>'.strip_tags($content->getTitle()).'</title>'; 
  
// now the CSS
echo '<style type="text/css"><!--  '."\n";
if (is_readable(dirname(__FILE__).'/bibtexbrowser.css')) {
  readfile(dirname(__FILE__).'/bibtexbrowser.css');
}
else {  bibtexbrowserDefaultCSS(); }
echo "\n".' --></style>';

?>
</head>
<body>
<?php $content->display();?>
</body>
</html>
<?php
//exit;
} // end constructor

}


/** NoWrapper calls method display() on the content. */
class NoWrapper {
  function NoWrapper(&$content) {
    @header('Content-type: text/html; charset='.ENCODING);
    echo $content->display();
  }
}

/** is used to create an subset of a bibtex file  */
class BibtexDisplay {

  /** an array of BibEbtry */
  var $results;

  /** the initial query to get the results */
  var $query;

  function BibtexDisplay(&$results, &$query) {
    $this->results=$results;
    $this->query=$query;
  }

  function display() {
    header('Content-type: text/plain; charset='.ENCODING);
    echo '% '.query2title($this->query)."\n";
    echo '% Encoding: '.ENCODING."\n";
    foreach($this->results as $bibentry) { echo $bibentry->getText()."\n"; }
    exit;
  }
}

/** is used to create an RSS feed */
class RSSDisplay {

  /** an array of BibEbtry */
  var $results;

  /** the initial query to get the results */
  var $query;

  function RSSDisplay(&$results, &$query) {
    $this->results=$results;
    $this->query=$query;
    $this->title = query2title($query);
  }

  /** tries to always output a valid XML/RSS string 
    * based on ENCODING, HTML tags, and the transformations 
    * that happened in latex2html */
  function text2rss($desc) {
    // first strip HTML tags 
    $desc = strip_tags($desc);
  
    // then decode characters encoded by latex2html
    $desc= html_entity_decode($desc);

    // some entities may still be here, we remove them
    // we replace html entities e.g. &eacute; by nothing
    // however XML entities are kept (e.g. &#53;)
    $desc = preg_replace('/&\w+;/','',$desc);
            
    // bullet proofing ampersand
    $desc = preg_replace('/&([^#])/','&#38;$1',$desc);

    // be careful of < 
    $desc = str_replace('<','&#60;',$desc);
    
    // final test with encoding:
    if (function_exists('mb_check_encoding')) { // (PHP 4 >= 4.4.3, PHP 5 >= 5.1.3)
      if (!mb_check_encoding($desc,ENCODING)) { 
        return 'encoding error: please check the content of ENCODING';
      }
    }
    
    return $desc;
  }
  
  
  function display() {
    header('Content-type: application/rss+xml');
    echo '<?xml version="1.0" encoding="'.ENCODING.'"?>';
//

?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
   <channel>
      <title><?php echo $this->title;?></title>
      <link>http://<?php echo $_SERVER['HTTP_HOST'].htmlentities($_SERVER['REQUEST_URI']);?></link>
      <atom:link href="http://<?php echo $_SERVER['HTTP_HOST'].htmlentities($_SERVER['REQUEST_URI']);?>" rel="self" type="application/rss+xml" />
      <description></description>
      <generator>bibtexbrowser v__MTIME__</generator>

<?php
      foreach($this->results as $bibentry) {
         ?>
         <item>
         <title><?php echo $this->text2rss($bibentry->getTitle());?></title>
         <link><?php echo htmlentities($bibentry->getURL());?></link>
         <description>
          <?php
            // we are in XML, so we cannot have HTML entitites
            // however the encoding is specified in preamble
            echo $this->text2rss(bib2html($bibentry)."\n".$bibentry->getAbstract());
          ?>
          </description>
         <guid isPermaLink="false"><?php echo urlencode($_GET[Q_FILE].'::'.$bibentry->getKey());?></guid>
         </item>
         <?php } /* end foreach */?>
   </channel>
</rss>

<?php
  //exit;
  }
}




class Dispatcher {

  /** this is the query */
  var $query = array();

  /** this is the result of the querw: an array of BibEbtry */
  var $selectedEntries = array();

  /** the displayer of selected entries. The default is a paged display.
    *  It could also be an RSSDisplay if the rss keyword is present
    */
  var $displayer = 'PagedDisplay';

  /** the wrapper of selected entries. The default is an HTML wrapper
    *  It could also be a NoWrapper when you include your pub list in your home page
    */
  var $wrapper = 'HTMLWrapper';

  function Dispatcher() {
    // are we in test mode, or libray mode
    // then this file is just a library
    if (isset($_GET['test']) || isset($_GET['library'])) {
      // we unset in  order to use the dispatcher afterwards
      unset($_GET['test']);
      unset($_GET['library']);
      return;
    }
    
    // first we set the database (load from disk or parse the bibtex file)
    setDB();
    
    // is the publication list included in another page?
    // strtr is used for Windows where __FILE__ contains C:\toto and SCRIPT_FILENAME contains C:/toto :-(
    // 2010-07-01: bug found by Marco: on some installation these two variables contain backslashes
    if (strtr(__FILE__,"\\","/")!=strtr($_SERVER['SCRIPT_FILENAME'],"\\","/")) $this->wrapper='NoWrapper';

    // first pass, we will exit if we encounter key or menu or academic
    // other wise we just create the $this->query
    foreach($_GET as $keyword=>$value) {
      if (method_exists($this,$keyword)) {
        // if the return value is END_DISPATCH, we finish bibtexbrowser (but not the whole PHP process in case we are embedded)
        if ($this->$keyword()=='END_DISPATCH') return; 
      }
    }


    
    if (count($this->query)>0) {

       // first test for inconsistent queries
       if (isset($this->query[Q_ALL]) && count($this->query)>1) {
         // we discard the Q_ALL, it helps in embedded mode
         unset($this->query[Q_ALL]);
       }
    
       $this->selectedEntries = $_GET[Q_DB]->multisearch($this->query);

       // required for PHP4 to have this intermediate variable
       $x = new $this->displayer($this->selectedEntries,$this->query);
       new $this->wrapper($x);
    }
    else { 
       // we send a redirection for having the frameset
       // if some contents have already been sent, for instance if we are included
       // this means doing nothing
       if ( headers_sent() == false ) { /* to avoid sending an unnecessary frameset */ 
         header("Location: ".$_SERVER['SCRIPT_NAME']."?frameset&bib=".$_GET[Q_FILE]);
       }
     }
  }

  function all() {
    $this->query[Q_ALL]=1;
  }

  function rss() {
    $this->displayer='RSSDisplay';
    $this->wrapper='NoWrapper';
  }

  function astext() {
    $this->displayer='BibtexDisplay';
    $this->wrapper='NoWrapper';
  }

  function search() {
    if (preg_match('/utf-?8/i',ENCODING)) {
      $_GET[Q_SEARCH] = urldecode($_GET[Q_SEARCH]);
    }
    $this->query[Q_SEARCH]=$_GET[Q_SEARCH];
  }

  function exclude() { $this->query[Q_EXCLUDE]=$_GET[Q_EXCLUDE]; }

  function year() {  $this->query[Q_YEAR]=$_GET[Q_YEAR]; }

  function keywords() { $this->query[Q_TAG]=$_GET[Q_TAG]; }

  function author() {
    // Friday, October 29 2010
    // changed fomr 'author' to '_author'
    // because '_author' is already formatted
    // doing so we can search at the same time "Joe Dupont" an "Dupont, Joe"
    $this->query['_author']=$_GET[Q_AUTHOR];
  }

  function type() { 
    // remarks KEN
    // "book" selects inbook, book, bookchapter
    // so we add the regexp modifiers
    if (strlen($_GET[Q_TYPE])>0) { $_GET[Q_TYPE] = '^'.$_GET[Q_TYPE].'$'; }
    $this->query[Q_TYPE]= $_GET[Q_TYPE];
  }

  function menu() {
    $menu = new MenuManager($_GET[Q_DB]);
    new $this->wrapper($menu,array(array('robots','noindex')));
    return 'END_DISPATCH';
  }

  /** the academic keyword in URLs switch from a year based viey to a publication type based view */
  function academic() {
     $this->displayer='AcademicDisplay';
     
     
     // backward compatibility with old GET API
     // this is deprecated
     // instead of academic=Martin+Monperrus
     // you should use author=Martin+Monperrus&academic
     // be careful of the semantics of === and !==
     // 'foo bar' == true is true
     // 123 == true is true (and whatever number different from 0
     // 0 == true is true
     // '1'!=1 is **false**
     if(!isset($_GET[Q_AUTHOR]) && $_GET[Q_ACADEMIC]!==true && $_GET[Q_ACADEMIC]!=='true' && $_GET[Q_ACADEMIC]!=1 && $_GET[Q_ACADEMIC]!='') {
      $_GET[Q_AUTHOR]=$_GET[Q_ACADEMIC];
      $this->query[Q_AUTHOR]=$_GET[Q_ACADEMIC];
     }

  }

  function key() {
    if ($_GET[Q_DB]->contains($_GET[Q_KEY])) {
      $bibdisplay = new BibEntryDisplay($_GET[Q_DB]->getEntryByKey($_GET[Q_KEY]));
      new $this->wrapper($bibdisplay,$bibdisplay->metadata());
      return 'END_DISPATCH';
    }
    else { new  NonExistentBibEntryError(); }
  }

  /** is used to remotely analyzed a situation */
  function diagnosis() {
    header('Content-type: text/plain');
    echo "php version: ".phpversion()."\n";
    echo "bibtexbrowser version: __MTIME__\n";
    echo "dir: ".decoct(fileperms(dirname(__FILE__)))."\n";
    echo "bibtex file: ".decoct(fileperms($_GET[Q_FILE]))."\n";
    exit;
  }

  function frameset() {    ?>


    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
    <html  xmlns="http://www.w3.org/1999/xhtml">
    <head>
    <meta name="generator" content="bibtexbrowser v__MTIME__" />
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo ENCODING ?>"/>
    <title>You are browsing <?php echo $_GET[Q_FILE]; ?> with bibtexbrowser</title>
    </head>
    <frameset cols="15%,*">
    <frame name="menu" src="<?php echo '?'.Q_FILE.'='. urlencode($_GET[Q_FILE]).'&amp;menu'; ?>" />
    <frame name="main" src="<?php echo '?'.Q_FILE.'='. urlencode($_GET[Q_FILE]).'&amp;'.Q_ALL; ?>" />
    </frameset>
    </html>

    <?php
    return 'END_DISPATCH';
}

} // end class Dispatcher

} // end if (!defined('BIBTEXBROWSER'))

new Dispatcher();

?>