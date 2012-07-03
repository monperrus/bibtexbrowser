<?php /* bibtexbrowser: publication lists with bibtex and PHP
<!--this is version v__MTIME__ -->
URL: http://www.monperrus.net/martin/bibtexbrowser/ 
Feedback & Bug Reports: martin.monperrus@gmail.com

(C) 2006-2012 Martin Monperrus
(C) 2005-2006 The University of Texas at El Paso / Joel Garcia, Leonardo Ruiz, and Yoonsik Cheon
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License as
published by the Free Software Foundation; either version 2 of the
License, or (at your option) any later version.

*/

// it is be possible to include( 'bibtexbrowser.php' ); several times in the same script
// added on Wednesday, June 01 2011, bug found by Carlos Bras
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
@define('ENCODING','UTF-8');//@define('ENCODING','iso-8859-1');//define('ENCODING','windows-1252');
// number of bib items per page
// we use the same parameter 'num' as Google
@define('PAGE_SIZE',isset($_GET['num'])?(preg_match('/^\d+$/',$_GET['num'])?$_GET['num']:10000):14);
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

// the default view in {SimpleDisplay,AcademicDisplay,RSSDisplay,BibtexDisplay}
@define('BIBTEXBROWSER_DEFAULT_DISPLAY','SimpleDisplay');

// the target frame of menu links
@define('BIBTEXBROWSER_MENU_TARGET','main'); // might be define('BIBTEXBROWSER_MENU_TARGET','_self'); in bibtexbrowser.local.php 

@define('ABBRV_TYPE','index');// may be year/x-abbrv/key/none/index

// Wrapper to use when we are included by another script 
@define('BIBTEXBROWSER_EMBEDDED_WRAPPER', 'NoWrapper');
// default order functions
@define('ORDER_FUNCTION','compare_bib_entry_by_year_and_month');
// can be @define('ORDER_FUNCTION','compare_bib_entry_by_title');

// only displaying the n newest entries
@define('BIBTEXBROWSER_NEWEST',5);

// do we add [bibtex] links ?
// suggested by Sascha Schnepp
@define('BIBTEXBROWSER_BIBTEX_LINKS',true);

@define('BIBTEXBROWSER_DEBUG',false);

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
@define('Q_LATEST', 'latest');
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


/** parses $_GET[Q_FILE] and puts the result (an object of type BibDataBase) in $_GET[Q_DB].
See also zetDB().
  */
function setDB() {
  list($db, $parsed, $updated, $saved) = _zetDB(@$_GET[Q_FILE]);
  $_GET[Q_DB] = $db;
  return $updated;
}

/** parses the $bibtex_filenames (usually semi-column separated) and returns an object of type BibDataBase.
See also setDB()
*/
function zetDB($bibtex_filenames) {
  list($db, $parsed, $updated, $saved) = _zetDB($bibtex_filenames);
  return $db;
}

/** @nodoc */
function _zetDB($bibtex_filenames) {

  $db = null;
  // Check if magic_quotes_runtime is active
  if(get_magic_quotes_runtime())
  {
      // Deactivate
      // otherwise it does not work
      set_magic_quotes_runtime(false);
  }
  
  // default bib file, if no file is specified in the query string.
  if (!isset($bibtex_filenames) || $bibtex_filenames == "") {
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
  // $bibtex_filenames can be urlencoded for instance if they contain slashes
  // so we decode it
  $bibtex_filenames = urldecode($bibtex_filenames);

  // ---------------------------- HANDLING unexistent files
  foreach(explode(MULTIPLE_BIB_SEPARATOR, $bibtex_filenames) as $bib) {
  
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
  foreach(explode(MULTIPLE_BIB_SEPARATOR, $bibtex_filenames) as $bib) {
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
  $compiledbib = 'bibtexbrowser_'.md5($bibtex_filenames).'.dat';

  $parse=false;
  foreach(explode(MULTIPLE_BIB_SEPARATOR, $bibtex_filenames) as $bib) {
  // do we have a compiled version ?
  if (is_file($compiledbib) 
     && is_readable($compiledbib) 
     && filesize($compiledbib)>0
   ) {

    $f = fopen($compiledbib,'r');
    //we use a lock to avoid that a call to bibbtexbrowser made while we write the object loads an incorrect object
    if (flock($f,LOCK_EX)) { 
      $s = filesize($compiledbib);
      $ser = fread($f,$s);
      $db = @unserialize($ser);
      flock($f,LOCK_UN);
    } else { die('could not get the lock'); }
    fclose($f);
    // basic test
    // do we have an correct version of the file
    if (!is_a($db,'BibDataBase')) {
      unlink($compiledbib);
      if (BIBTEXBROWSER_DEBUG) { die('$db not a BibDataBase. please reload.'); }      
      $parse=true;
    }
  } else {$parse=true;}
  } // end for each

  // we don't have a compiled version
  if ($parse) {
    //echo '<!-- parsing -->';
    // then parsing the file
    $db = createBibDataBase();
    foreach(explode(MULTIPLE_BIB_SEPARATOR, $bibtex_filenames) as $bib) {
      $db->load($bib);
    }    
  }
  
  $updated = false; 
  // now we may update the database
  if (!file_exists($compiledbib)) {
    @touch($compiledbib);
    $updated = true; // limit case
  } else foreach(explode(MULTIPLE_BIB_SEPARATOR, $bibtex_filenames) as $bib) {
      // is it up to date ? wrt to the bib file and the script
    // then upgrading with a new version of bibtexbrowser triggers a new compilation of the bib file
    if (filemtime($bib)>filemtime($compiledbib) || filemtime(__FILE__)>filemtime($compiledbib)) {
      //echo "updating...";
      $db->update($bib);
      $updated = true;
    }
  }
  
  //echo var_export($parse);
  //echo var_export($updated);

  $saved = false;
  // are we able to save the compiled version ?
  // note that the compiled version is saved in the current working directory
  if ( ($parse || $updated ) && is_writable($compiledbib)) {
    // we use 'a' because the file is not locked between fopen and flock
    $f = fopen($compiledbib,'a');
    //we use a lock to avoid that a call to bibbtexbrowser made while we write the object loads an incorrect object
    if (flock($f,LOCK_EX)) { 
//       echo '<!-- saving -->';
      ftruncate($f,0);
      fwrite($f,serialize($db));
      flock($f,LOCK_UN);
      $saved = true;
    } else { die('could not get the lock'); }
    fclose($f);
  } // end saving the cached verions
  //else echo '<!-- please chmod the directory containing the bibtex file to be able to keep a compiled version (much faster requests for large bibtex files) -->';
  

  return array(&$db, $parse, $updated, $saved);
} // end function setDB


// factories
// may be overridden in bibtexbrowser.local.php
if (!function_exists('createBibDataBase')) {
  /** factory method for openness @nodoc */
  function createBibDataBase() { $x = new BibDataBase(); return $x;}
}
if (!function_exists('createBibEntry')) {
  /** factory method for openness @nodoc */
  function createBibEntry() { $x = new BibEntry(); return $x;}
}
if (!function_exists('createBibDBBuilder')) {
  /** factory method for openness @nodoc */
  function createBibDBBuilder() { $x = new BibDBBuilder(); return $x;}
}
if (!function_exists('createBasicDisplay')) {
  /** factory method for openness @nodoc */
  function createBasicDisplay() { $x = new SimpleDisplay(); return $x;}
}
if (!function_exists('createBibEntryDisplay')) {
  /** factory method for openness @nodoc */
  function createBibEntryDisplay() { $x = new BibEntryDisplay(); return $x;}
}
if (!function_exists('createMenuManager')) {
  /** factory method for openness @nodoc */
  function createMenuManager() { $x = new MenuManager(); return $x;}
}


////////////////////////////////////////////////////////

/** is a generic parser of bibtex files.
usage:
<pre>
  $delegate = new XMLPrettyPrinter();// or another delegate such as BibDBBuilder
  $parser = new StateBasedBibtexParser($delegate);
  $parser->parse('foo.bib');
</pre>
notes:
 - It has no dependencies, it can be used outside of bibtexbrowser
 - The delegate is expected to have some methods, see classes BibDBBuilder and XMLPrettyPrinter
 */
class StateBasedBibtexParser {
  
  var $delegate;
 
  function StateBasedBibtexParser(&$delegate) {
    $this->delegate = &$delegate;
  }

  function parse($bibfilename) {
    $delegate = &$this->delegate;
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
    @define('GETVALUEDELIMITEDBYCURLYBRACKETS_3NESTEDLEVEL',13);
    @define('GETVALUEDELIMITEDBYCURLYBRACKETS_3NESTEDLEVEL_ESCAPED',14);


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
    //$d = &$this->delegate;print_r($d);
  } // end function
} // end class

/** is a delegate for StateBasedBibParser.
usage:
see snippet of [[#StateBasedBibParser]]
*/
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

/** builds arrays of BibEntry objects from a bibtex file.
usage:
<pre>
  $empty_array = array();
  $db = new BibDBBuilder(); // see also factory method createBibDBBuilder
  $db->build('foo.bib'); // parses foo.bib
  print_r($db->builtdb);// an associated array key -> BibEntry objects
  print_r($db->stringdb);// an associated array key -> strings representing @string
</pre>
notes: 
 method build can be used several times, bibtex entries are accumulated in the builder
*/
class BibDBBuilder {

  /** A hashtable from keys to bib entries (BibEntry). */
  var $builtdb  = array();

  /** A hashtable of constant strings */
  var $stringdb = array();

  var $filename;

  var $currentEntry;

  function setData(&$builtdb, &$stringdb) {
    $this->builtdb = $builtdb;
    $this->stringdb = $stringdb;
    return $this;
  }

  function build($filename) {
    $this->filename = $filename;    
    $parser = new StateBasedBibtexParser($this);
    $parser->parse($filename);
    //print_r(array_keys(&$this->builtdb));
    //print_r(&$this->builtdb);
  }
  

  function getBuiltDb() {
    //print_r($this->builtdb);
    return $this->builtdb;
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
    //print_r($this->builtdb);
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
    $this->currentEntry->setKey($entrykey);
  }

  function beginEntry() {
    $this->currentEntry = createBibEntry();
    $this->currentEntry->setFile($this->filename);
  }

  function endEntry($entrysource) {
  
    // we add a timestamp
    $this->currentEntry->setTimestamp();
    
    // we set the fulltext
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




/** is an extended version of the trim function, removes linebreaks, tabs, etc.
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

/** encapsulates the conversion of a single latex chars to the corresponding HTML entity.
It expects a **lower-case** char.
*/
function char2html($line,$latexmodifier,$char,$entitiyfragment) {
  $line = str_replace('\\'.$latexmodifier.$char,'&'.$char.''.$entitiyfragment.';', $line);
  $line = str_replace('\\'.$latexmodifier.'{'.$char.'}','&'.$char.''.$entitiyfragment.';', $line);
  $line = str_replace('\\'.$latexmodifier.strtoupper($char),'&'.strtoupper($char).''.$entitiyfragment.';', $line);
  $line = str_replace('\\'.$latexmodifier.'{'.strtoupper($char).'}','&'.strtoupper($char).''.$entitiyfragment.';', $line);
  return $line;
}

/** converts latex chars to HTML entities.
(I still look for a comprehensive translation table from late chars to html, better than [[http://isdc.unige.ch/Newsletter/help.html]])
 */
function latex2html($line) {
  $line = preg_replace('/([^\\\\])~/','\\1&nbsp;', $line);

  // performance increases with this test
  // bug found by Serge Barral: what happens if we have curly braces only (typically to ensure case in Latex)
  // added && strpos($line,'{')===false
  if (strpos($line,'\\')===false && strpos($line,'{')===false) return $line;
  
  // we should better replace this before the others
  // in order not to mix with the HTML entities coming after (just in case)
  $line = str_replace('\\&','&amp;', $line);

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
  $line = str_replace('\\aa','&aring;', $line);
  $line = str_replace('\\AA','&Aring;', $line);

  // clean out extra tex curly brackets, usually used for preserving capitals
  $line = str_replace('}','', $line);
  $line = str_replace('{','', $line);

  return $line;
}

/** encodes strings for Z3988 URLs. Note that & are encoded as %26 and not as &amp. */
function s3988($s) {return urlencode(utf8_encode($s));}

/**
see BibEntry->formatAuthor($author) 
@deprecated 
@nodoc
*/
function formatAuthor() {
  die('Sorry, this function does not exist anymore, however, you can simply use $bibentry->formatAuthor($author) instead.');
}

// ----------------------------------------------------------------------
// BIB ENTRIES
// ----------------------------------------------------------------------

/** represents a bibliographic entry.
usage:
<pre>
  $db = zetDB('metrics.bib');
  $entry = $db->getEntryByKey('Schmietendorf2000');
  echo bib2html($entry);
</pre>
notes:
- BibEntry are usually obtained with getEntryByKey or multisearch
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

  /** A timestamp to trace when entries have been created */
  var $timestamp;
  
  /** The name of the file containing this entry */
  var $filename;
  
  /** The short name of the entry (parameterized by ABBRV_TYPE) */
  var $abbrv;
  
  /** The index in a list of publications (e.g. [1] Foo */
  var $index = '';
  
  /** Creates an empty new bib entry. Each bib entry is assigned a unique
   * identification number. */
  function BibEntry() {
    $this->fields = array();
    $this->constants = array();
    $this->text ='';
  }

  /** Sets the name of the file containing this entry */
  function setFile($filename) {  
    $this->filename = $filename;
    return $this;
  }
  
  /** Adds timestamp to this object */
  function setTimestamp() {
    $this->timestamp = time();
  }
  /** Returns the timestamp of this object */
  function getTimestamp() {
    return $this->timestamp;
  }

  /** Returns the type of this bib entry. */
  function getType() {
    // strtolower is important to be case-insensitive
    return strtolower($this->getField(Q_TYPE));
  }

  /** Sets the key of this bib entry. */
  function setKey($value) {
    $this->setField('key',$value);
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
  
  function setIndex($index) { $this->index = $index; }

  /** Tries to build a good URL for this entry */
  function getURL() {
    if (defined('BIBTEXBROWSER_URL_BUILDER')) {
      $f = BIBTEXBROWSER_URL_BUILDER;
      return $f($this);
    }
//     echo $this->filename;
//     echo $this->getKey();
    return BIBTEXBROWSER_URL.'?'.createQueryString(array('key'=>$this->getKey(), Q_FILE=>$this->filename));
  }

  /** Tries to build a good absolute URL for this entry */
  function getAbsoluteURL() {
    if (defined('BIBTEXBROWSER_URL_BUILDER')) {
      $f = BIBTEXBROWSER_URL_BUILDER;
      return $f($this);
    }
    return "http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']).'/'.$this->getURL();
  }

  /** returns a "[pdf]" link if relevant */
  function getUrlLink() {
    if ($this->hasField('url')) return ' <a'.(BIBTEXBROWSER_BIB_IN_NEW_WINDOW?' target="_blank" ':'').' href="'.$this->getField('url').'">[pdf]</a>';
    return '';
  }

  /** Reruns the abstract */
  function getAbstract() {
    if ($this->hasField('abstract')) return $this->getField('abstract');
    else return '';
  }

  /**
    * Returns the last name of an author name.
    */
  function getLastName($author){
      list($firstname, $lastname) = splitFullName($author);
      return $lastname;
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
      $editors[]=$this->addHomepageLink($this->formatAuthor($editor));
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
    
  /** Returns the abbreviation. */
  function getAbbrv() {
    if (ABBRV_TYPE == 'index') return $this->index;
    if (ABBRV_TYPE == 'none') return '';
    if (ABBRV_TYPE == 'key') return '['.$this->getKey().']';
    if (ABBRV_TYPE == 'year') return '['.$this->getYear().']';
    if (ABBRV_TYPE == 'x-abbrv') {
      if ($this->hasField('x-abbrv')) {return $this->getField('x-abbrv');}
      return $this->abbrv;
    }
    
    // otherwise it is a user-defined function in bibtexbrowser.local.php
    $f = ABBRV_TYPE;
    return $f($this);
  }


  /** Sets the abbreviation (e.g. [OOPSLA] or [1]) */
  function setAbbrv($abbrv) {
    //if (!is_string($abbrv)) { throw new Exception('Illegal argument'); }
    $this->abbrv = $abbrv;
    return $this;
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
        echo '<td  class="bibref">';
        //echo '<a name="'.$this->getId().'"></a>';
 
        echo $this->getAbbrv().'</td> ';
        echo '<td class="bibitem">';
        echo bib2html($this);

        $href = 'href="'.$this->getURL().'"';

        if (BIBTEXBROWSER_BIBTEX_LINKS) {
          // we add biburl and title to be able to retrieve this important information
          // using Xpath expressions on the XHTML source
          echo " <a".(BIBTEXBROWSER_BIB_IN_NEW_WINDOW?' target="_blank" ':'')." class=\"biburl\" title=\"".$this->getKey()."\" {$href}>[bibtex]</a>";
        }
        
        // returns an empty string if no url present
        echo $this->getUrlLink();

        if ($this->hasField('doi')) {
            echo ' <a href="http://dx.doi.org/'.$this->getField("doi").'">[doi]</a>';
        }
        
        // Google Scholar ID
        if ($this->hasField('gsid')) {
            echo ' <a href="http://scholar.google.com/scholar?cites='.$this->getField("gsid").'">[cites]</a>';
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
    $url_parts[]='rfr_id='.s3988('info:sid/'.$_SERVER['HTTP_HOST'].':'.@$_GET[Q_FILE]);

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

/** encapsulates the user-defined sections. @nodoc */
function _DefaultBibliographySections() {
  $function = BIBLIOGRAPHYSECTIONS;
  return $function();
}

/** compares two instances of BibEntry by modification time
 */
function compare_bib_entry_by_mtime($a, $b)
{
  return -($a->getTimestamp()-$b->getTimestamp());
}

/** compares two instances of BibEntry by year
 */
function compare_bib_entry_by_year_and_month($a, $b)
{
  $year_cmp =  -strcmp($a->getYear(),$b->getYear());
  if ($year_cmp==0) { return compare_bib_entry_by_month($a, $b);}
  return $year_cmp;
}

/** compares two instances of BibEntry by title
 */
function compare_bib_entry_by_title($a, $b)
{
  return strcmp($a->getTitle(),$b->getTitle());
}


/** compares two instances of BibEntry by month 
 * @author Jan Geldmacher
 */
function compare_bib_entry_by_month($a, $b)
{  
  // this was the old behavior
  // return strcmp($a->getKey(),$b->getKey());
  
  //bibkey which is used for sorting
  $sort_key = 'month';
  //desired order of values
  $sort_order_values = array('jan','january','feb','february','mar','march','apr','april','may','jun','june','jul','july','aug','august','sep','september','oct','october','nov','november','dec','december');
  //order: 1=as specified in $sort_order_values  or -1=reversed
  $order = -1;


  //first check if the search key exists
  if (!array_key_exists($sort_key,$a->fields)  && !array_key_exists($sort_key,$b->fields)) {
    //neither a nor b have the key -> we compare the keys
    $retval=strcmp($a->getKey(),$b->getKey());
  }
  elseif (!array_key_exists($sort_key,$a->fields)) {
    //only b has the field -> b is greater
    $retval=-1;
  }
  elseif  (!array_key_exists($sort_key,$b->fields)) {
    //only a has the key -> a is greater
    $retval=1;
  }
  else {
    //both have the key, sort using the order given in $sort_order_values

    $val_a = array_search(strtolower($a->fields[$sort_key]), $sort_order_values);
    $val_b = array_search(strtolower($b->fields[$sort_key]), $sort_order_values);
    
    if (($val_a === FALSE && $val_b === FALSE) || ($val_a === $val_b)) {
      //neither a nor b are in the search array or a=b -> both are equal
      $retval=0;
    }
    elseif (($val_a === FALSE) || ($val_a < $val_b)) {
      //a is not in the search array or a<b -> b is greater
      $retval=-1;
    }
    elseif (($val_b === FALSE) || (($val_a > $val_b))){
      //b is not in the search array or a>b -> a is greater
      $retval=1;
    }   
  }
 
  return $order*$retval;
}

/** is the default sectioning for AcademicDisplay (books, articles, proceedings, etc. ) */
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


/** transforms a $bibentry into an HTML string.
  It is called by function bib2html if the user did not choose a specific style
  the default usable CSS styles are
  .bibtitle { font-weight:bold; }
  .bibbooktitle { font-style:italic; }
  .bibauthor { }
  .bibpublisher { }
*/
function DefaultBibliographyStyle(&$bibentry) {
  $title = $bibentry->getTitle();
  $type = $bibentry->getType();

  // later on, all values of $entry will be joined by a comma
  $entry=array();

  // title
  // usually in bold: .bibtitle { font-weight:bold; }
  $title = '<span class="bibtitle">'.$title.'</span>';
  if ($bibentry->hasField('url')) $title = ' <a'.(BIBTEXBROWSER_BIB_IN_NEW_WINDOW?' target="_blank" ':'').' href="'.$bibentry->getField("url").'">'.$title.'</a>';
  

  // author
  if ($bibentry->hasField('author')) {
    $coreInfo = $title . ' <span class="bibauthor">('.$bibentry->getFormattedAuthorsImproved().')</span>';}
  else $coreInfo = $title;

  // core info usually contains title + author
  $entry[] = $coreInfo;

  // now the book title
  $booktitle = '';
  if ($type=="inproceedings") {
      $booktitle = 'In '.$bibentry->getField(BOOKTITLE); }
  if ($type=="incollection") {
      $booktitle = 'Chapter in '.$bibentry->getField(BOOKTITLE);}
  if ($type=="inbook") {
      $booktitle = 'Chapter in '.$bibentry->getField('chapter');}
  if ($type=="article") {
      $booktitle = 'In '.$bibentry->getField("journal");}

  //// we may add the editor names to the booktitle
  $editor='';
  if ($bibentry->hasField(EDITOR)) {
    $editor = $bibentry->getFormattedEditors();
  }
  if ($editor!='') $booktitle .=' ('.$editor.')';
  // end editor section

  // is the booktitle available
  if ($booktitle!='') {
    $entry[] = '<span class="bibbooktitle">'.$booktitle.'</span>';
  }


  $publisher='';
  if ($type=="phdthesis") {
      $publisher = 'PhD thesis, '.$bibentry->getField(SCHOOL);
  }
  if ($type=="mastersthesis") {
      $publisher = 'Master\'s thesis, '.$bibentry->getField(SCHOOL);
  }
  if ($type=="bachelorsthesis") {
      $publisher = 'Bachelor\'s thesis, '.$bibentry->getField(SCHOOL);
  }
  if ($type=="techreport") {
      $publisher = 'Technical report, '.$bibentry->getField("institution");
  }
  
  if ($type=="misc") {
      $publisher = $bibentry->getField('howpublished');
  }

  if ($bibentry->hasField("publisher")) {
    $publisher = $bibentry->getField("publisher");
  }

  if ($publisher!='') $entry[] = '<span class="bibpublisher">'.$publisher.'</span>';


  if ($bibentry->hasField('volume')) $entry[] =  "volume ".$bibentry->getField("volume");


  if ($bibentry->hasField(YEAR)) $entry[] = $bibentry->getYear();

  $result = implode(", ",$entry).'.';

  // some comments (e.g. acceptance rate)?
  if ($bibentry->hasField('comment')) {
      $result .=  " (".$bibentry->getField("comment").")";
  }
  if ($bibentry->hasField('note')) {
      $result .=  " (".$bibentry->getField("note").")";
  }

  // add the Coin URL
  $result .=  "\n".$bibentry->toCoins();

  return $result;
}



/** is the Bibtexbrowser style contributed by Janos Tapolcai. It looks like the IEEE transaction style.
usage:
Add the following line in "bibtexbrowser.local.php"
<pre>
include( 'bibtexbrowser-style-janos.php' );
define('BIBLIOGRAPHYSTYLE','JanosBibliographyStyle');
</pre>
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
  if ($bibentry->hasField('url')) $title = ' <a'.(BIBTEXBROWSER_BIB_IN_NEW_WINDOW?' target="_blank" ':'').' href="'.$bibentry->getField("url").'">'.$title.'</a>';
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
    $editor = $bibentry->getFormattedEditors();
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






// ----------------------------------------------------------------------
// DISPLAY MANAGEMENT
// ----------------------------------------------------------------------


/** creates a query string given an array of parameter, with all specifities of bibtexbrowser_ (such as adding the bibtex file name &bib=foo.bib
 */
function createQueryString($array_param) {
  if (isset($_GET[Q_FILE]) && !isset($array_param[Q_FILE])) {
    // first we add the name of the bib file
    $array_param[Q_FILE] = urlencode($_GET[Q_FILE]);
  }
 // then a simple transformation and implode
 foreach ($array_param as $key => $val) {
      if($key == '_author') { $key = 'author'; }
      $array_param[$key]=$key .'='. urlencode($val);
 }
 return implode("&amp;",$array_param);
}

/** returns a href string of the form: href="?bib=testing.bib&search=JML.
Based on createQueryString.
@nodoc
 */
function makeHref($query = NULL) {
  return 'href="?'. createQueryString($query) .'"';
}


/** returns the splitted name of an author name as an array. The argument is assumed to be
 "FirstName LastName" or "LastName, FirstName".
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


/** outputs an horizontal  year-based menu
usage:
<pre>
  $_GET['library']=1;
  $_GET['bib']='metrics.bib';
  $_GET['all']=1;
  include( 'bibtexbrowser.php' );
  setDB();
  new IndependentYearMenu();
</pre>
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

/** Returns the powered by part. @nodoc */
function poweredby() {
  $poweredby = "\n".'<div style="text-align:right;font-size: xx-small;opacity: 0.6;" class="poweredby">';
  $poweredby .= '<!-- If you like bibtexbrowser, thanks to keep the link :-) -->';
  $poweredby .= 'Powered by <a href="http://www.monperrus.net/martin/bibtexbrowser/">bibtexbrowser</a><!--v__MTIME__-->';
  $poweredby .= '</div>'."\n";
  return $poweredby;
  }


/** ^^adds a touch of AJAX in bibtexbrowser to display bibtex entries inline.
   It uses the HIJAX design pattern: the Javascript code fetches the normal bibtex HTML page
   and extracts the bibtex.
   In other terms, URLs and content are left perfectly optimized for crawlers 
   Note how beautiful is this piece of code thanks to JQuery.^^
  */
function javascript() {
  // we use jquery with the official content delivery URLs
  // Microsoft and Google also provide jquery with their content delivery networks
?><script type="text/javascript" src="http://code.jquery.com/jquery-1.5.1.min.js"></script> 
<script type="text/javascript" ><!--
// Javascript progressive enhancement for bibtexbrowser
$('a.biburl').each(function() { // for each url "[bibtex]"
  var biburl = $(this);
  if (biburl.attr('bibtexbrowser') === undefined)
  {
  biburl.click(function(ev) { // we change the click semantics
    ev.preventDefault(); // no open url
    if (biburl.nextAll('pre').length == 0) { // we don't have yet the bibtex data
      var bibtexEntryUrl = $(this).attr('href');
      $.ajax({url: bibtexEntryUrl,  dataType: 'xml', success: function (data) { // we download it
        // elem is the element containing bibtex entry, creating a new element is required for Chrome and IE
        var elem = $('<pre class="purebibtex"/>');         
        elem.text($('.purebibtex', data).text()); // both text() are required for IE
        // we add a link so that users clearly see that even with AJAX
        // there is still one URL per paper (which is important for crawlers and metadata)
        elem.append(
           $('<div>%% Bibtex entry URL: <a href="'+bibtexEntryUrl+'">'+bibtexEntryUrl+'</a></div>')
           ).appendTo(biburl.parent());
      }, error: function() {window.location.href = biburl.attr('href');}});
    } else {biburl.nextAll('pre').toggle();}  // we toggle the view    
  });
  biburl.attr('bibtexbrowser','done');
  } // end if biburl.bibtexbrowser;
});


--></script><?php
} // end function javascript



/** is used for creating menus (by type, by year, by author, etc.).
usage:
<pre>
  $db = zetDB('metrics.bib');
  $menu = new MenuManager();
  $menu->setDB($db);
  $menu->year_size=100;// should display all years :)
  $menu->display();
</pre>
 */
class MenuManager {

  /** The bibliographic database, an instance of class BibDataBase. */
  var $db;

  var $type_size = TYPES_SIZE;
  var $year_size = YEAR_SIZE;
  var $author_size = AUTHORS_SIZE;
  var $tag_size = TAGS_SIZE;
  
  function MenuManager() {
  }
  
  /** sets the database that is used to create the menu */
  function setDB(&$db) {
    $this->db =$db;
    return $this;
  }

  function getTitle() {
    return '';
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
    <form action="?" method="get" target="<?php echo BIBTEXBROWSER_MENU_TARGET;?>">
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
    foreach ($this->db->getTypes() as $type) {
      $types[$type] = $type;
    }
    $types[''] = 'all types';
    // retreive or calculate page number to display
    if (isset($_GET[Q_TYPE_PAGE])) {
      $page = $_GET[Q_TYPE_PAGE];
    }
    else $page = 1;

    $this->displayMenu('Types', $types, $page, $this->type_size, Q_TYPE_PAGE, Q_TYPE);
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


    $this->displayMenu('Authors', $authors, $page, $this->author_size, Q_AUTHOR_PAGE,
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


    if (count($tags)>0) $this->displayMenu('Keywords', $tags, $page, $this->tag_size, Q_TAG_PAGE,
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


    $this->displayMenu('Years', $years, $page, $this->year_size, Q_YEAR_PAGE,
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
          <tr class="btb-nav-title">
            <td><b><?php echo $title; ?></b></td>
            <td class="btb-nav"><b>
                <?php echo $this->menuPageBar($pageKey, $numEntries, $page,
           $pageSize, $startIndex, $endIndex);?></b></td>
          </tr>
        </table>
        </td>
      </tr>
      <tr>
        <td class="btb-menu-items">
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

    // (1 page) reverse (<)
    if ($start > 0) {
      $href = makeHref(array($queryKey => $page - 1,'menu'=>''));//menuPageBar
      $result .= '<a '. $href ."><b>[prev]</b></a>\n";
    }

    // (1 page) forward (>)
    if ($end < $numEntries) {
      $href = makeHref(array($queryKey => $page + 1,'menu'=>''));//menuPageBar
      $result .= '<a '. $href ."><b>[next]</b></a>\n";
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
 $href = makeHref(array($queryKey => $key));
 echo '<a '. $href .' target="'.BIBTEXBROWSER_MENU_TARGET.'">'. $item ."</a>\n";
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


/** displays the latest modified bibtex entries.
usage:
<pre>
  $db = zetDB('metrics.bib');
  $d = new NewEntriesDisplay();
  $d->setDB($db);
  $d->setN(7);// optional 
  $d->display();
</pre>
 */
class NewEntriesDisplay {
  var $n=5;
  var $db;
  
  function setDB(&$bibdatabase) {
    $this->db = $bibdatabase;
  }
  
  function setN($n) {$this->n = $n;return $this;}

  /** sets the entries to be shown */
  function setEntries(&$entries) {
    $this->db = createBibDataBase();
    $this->db->bibdb = $entries;
  }

    /** Displays a set of bibtex entries in an HTML table */
  function display() {
    $array = $this->db->getLatestEntries($this->n);
    $delegate = createBasicDisplay();
    $delegate->setEntries($array);
    $delegate->display();
  }
}


/** displays the entries by year in reverse chronological order. 
usage:
<pre>
  $db = zetDB('metrics.bib');
  $d = new YearDisplay();
  $d->setDB($db);
  $d->display();
</pre>
*/
class YearDisplay {
  
  /** is an array of strings representing years */
  var $yearIndex;

  function setDB(&$bibdatabase) {
    $this->setEntries($bibdatabase->bibdb);
  }

  /** creates a YearDisplay */
  function setOptions(&$options) {}

  function getTitle() {return '';}
  
  /** sets the entries to be shown */
  function setEntries(&$entries) {
    $this->entries = $entries;
    $db= createBibDataBase();
    $db->bibdb = $entries;
    $this->yearIndex = $db->yearIndex();    
  }

  /** Displays a set of bibtex entries in an HTML table */
  function display() {   
    $delegate = createBasicDisplay();
    $delegate->setEntries($this->entries);
    $index = count($this->entries);
    foreach($this->yearIndex as $year) {
      $x = array();
      uasort($x,'compare_bib_entry_by_month');
      foreach($this->entries as $e) {
        if ($e->getYear() == $year) {
          $x[] = $e;
        }
      }
      
      if (count($x)>0) {
        echo '<div  class="btb-header">'.$year.'</div>';
        $delegate->setEntries($x);
        $delegate->display();
      }
      
      $index = $index - count($x);
    }
  }
}


/** displays the summary information of all bib entries.
usage:
<pre>
  $db = zetDB('metrics.bib');
  $d = new SimpleDisplay();
  $d->setDB($db);
  $d->display();
</pre>
  */
class SimpleDisplay  {
 
  var $options = array();
  
  function setDB(&$bibdatabase) {
    $this->setEntries($bibdatabase->bibdb);
  }

  /** sets the entries to be shown */
  function setEntries(&$entries) {
    $this->entries = $entries;
  }

  function indexUp() {
    $index=1;
    foreach ($this->entries as $bib) {      
      $bib->setAbbrv((string)$index++);
    } // end foreach
    return $this->entries;
  }
  
  function newest(&$entries) {
    return array_slice($entries,0,BIBTEXBROWSER_NEWEST);
  }

  function indexDown() {
    $index=count($this->entries);
    foreach ($this->entries as $bib) {      
      $bib->setAbbrv((string)$index--);
    } // end foreach
    return $this->entries;
  }
  
  function setTitle($title) { $this->title = $title; return $this; }
  function getTitle() { return @$this->title ; }
  
  /** Displays a set of bibtex entries in an HTML table */
  function display() {
   
    uasort($this->entries, ORDER_FUNCTION);

    if ($this->options) {
      foreach($this->options as $fname=>$opt) {
        $this->$fname($opt,$entries);
      }
    }

    if (BIBTEXBROWSER_DEBUG) {
      echo 'Style: '.BIBLIOGRAPHYSTYLE.'<br/>';
      echo 'Order: '.ORDER_FUNCTION.'<br/>';
      echo 'Abbrv: '.ABBRV_TYPE.'<br/>';
      echo 'Options: '.@implode(',',$this->options).'<br/>';      
    }
    
    ?>
    
    <table class="result" >
    <?php
    $count = count($this->entries);
    $i=0;
    foreach ($this->entries as $bib) {    
      // by default, index are in decrasing order
      // so that when you add a publicaton recent , the indices of preceding publications don't change
      $bib->setIndex('['.($count-($i++)).']');
      $bib->toTR();
    } // end foreach
    ?>
    </table>
    <?php    
  } // end function
} // end class


/** returns an HTTP 404 and displays en error message. */
function nonExistentBibEntryError() {
  header('HTTP/1.1 404 Not found');
  ?>
  <b>Sorry, this bib entry does not exist.</b>
  <a href="?">Back to bibtexbrowser</a>
  <?php
  exit;
}


/** displays the publication records sorted by publication types (as configured by constant BIBLIOGRAPHYSECTIONS).
usage:
<pre>
  $db = zetDB('metrics.bib');
  $d = new AcademicDisplay();
  $d->setDB($db);
  $d->display();
</pre>
  */
class AcademicDisplay  {

  function getTitle() { return $this->title; }
  function setTitle($title) { $this->title = $title; return $this; }

  function setDB(&$bibdatabase) {
    $this->setEntries($bibdatabase->bibdb);
  }
  
  /** sets the entries to be shown */
  function setEntries(&$entries) {
    $this->entries = $entries;
  }

  /** transforms a query to HTML 
   * $ query is an array (e.g. array(Q_TYPE=>'book'))
   * $title is a string, the title of the section
   */
  function search2html($query, $title) {
    $entries = $this->db->multisearch($query);
    uasort($entries, ORDER_FUNCTION);
    if (count($entries)>0) {
    echo "\n".'<div class="btb-header">'.$title.'</div>'."\n";
    echo '<table class="result">'."\n";

    // by default the abbreviation is incermented over all 
    // searches
    
    // since we don't know before hand all section, we can not index in decreasing order
    static $count;
    if ($count == NULL) { $count = 1; } // init
    $id = $count;
    foreach ($entries as $bib) {
        $bib->setIndex('['.($id++).']');
        $bib->toTR();
    } // end foreach
    $count = @$count + count($entries);
    echo '</table>';
    }

  }
  
  function display() {
    $this->db = createBibDataBase();
    $this->db->bibdb = $this->entries;


    foreach (_DefaultBibliographySections() as $section) {
      $this->search2html($section['query'],$section['title']);
    }

    echo poweredby();
    
  }

}




/** displays a single bib entry.
usage:
<pre>
  $db = zetDB('metrics.bib');
  $dis = new BibEntryDisplay($db->getEntryByKey('Schmietendorf2000'));
  $dis->display();
</pre>
notes:
- the top-level header (usually &lt;H1>) must be done by the caller.  
- this view is optimized for Google Scholar
 */
class BibEntryDisplay {

  /** the bib entry to display */
  var $bib;

  function BibEntryDisplay($bib=null) {
    $this->bib = $bib;
  }
  
  function setEntries(&$entries) {
    $this->bib = $entries[0];
    //$this->title = $this->bib->getTitle().' (bibtex)'.$this->bib->getUrlLink();
  }

  /** returns the title */
  function getTitle() {
    return $this->bib->getTitle().' (bibtex)';
  }
  
  /** 2011/10/02: new display, inspired from Tom Zimmermann's home page */
  function displayOnSteroids() {
      $subtitle = '<div class="bibentry-by">by '.$this->bib->getFormattedAuthorsImproved().'</div>';

      $abstract = '';
      if ($this->bib->hasField('abstract')) {
        $abstract = '<div class="bibentry-label">Abstract:</div><div class="bibentry-abstract">'.$this->bib->getAbstract().'</div>';
      }
      
      $download = '';
      if ($this->bib->hasField('url')) {
        $download = '<div class="bibentry-document-link"><a href="'.$this->bib->getField('url').'">View PDF</a></div>';
      }
      $reference= '<div class="bibentry-label">Reference:</div><div class="bibentry-reference">'.strip_tags(bib2html($this->bib)).'</div>';

      $bibtex = '<div class="bibentry-label">Bibtex Entry:</div>'.$this->bib->toEntryUnformatted().'';
      return $subtitle.$abstract.$download.$reference.$bibtex.$this->bib->toCoins();
  }

  function display() {
    // we encapsulate everything so that the output of display() is still valid XHTML
    echo '<div>';
    //echo $this->display_old();
    echo $this->displayOnSteroids();
    echo poweredby();
    echo '</div>';
  }

  // old display
  function display_old() {
    return $this->bib->toCoins().$this->bib->toEntryUnformatted();
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
    $result[] = array('citation_publication_date',$this->bib->getYear());

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
    $result[] = array('DC.Issued',$this->bib->getYear());
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

/** represents a bibliographic database that contains a set of bibliographic entries.
usage:
<pre>
$db = new BibDataBase();
$db->load('metrics.bib');
$query = array('author'=>'martin', 'year'=>2008);
foreach ($db->multisearch($query) as $bibentry) { echo $bibentry->getTitle(); }
</pre>
*/
class BibDataBase {
  /** A hash table from keys (e.g. Goody1994) to bib entries (BibEntry instances). */
  var $bibdb;

  /** A hashtable of constant strings */
  var $stringdb;

  /** Creates a new database by parsing bib entries from the given
   * file. (backward compatibility) */
  function load($filename) {
    $this->update($filename);
  }
  
  /** Updates a database (replaces the new bibtex entries by the most recent ones) */ 
  function update($filename) {
  
    $empty_array = array();
    $db = createBibDBBuilder();
    $db->setData($empty_array, $this->stringdb);
    $db->build($filename);
    
    $this->stringdb = $db->stringdb;
    $result = $db->builtdb;
    
    
    foreach ($result as $b) {
       
      // new entries:
      if (!isset($this->bibdb[$b->getKey()])) {
        //echo 'adding...<br/>';
        $this->bibdb[$b->getKey()] = $b;
      }
      // update entry
      else if (isset($this->bibdb[$b->getKey()]) && ($b->getText() !== $this->bibdb[$b->getKey()]->getText())) {
        //echo 'replacing...<br/>';
        $this->bibdb[$b->getKey()] = $b;      
      }
    }
    
    // some entries have been removed
    foreach ($this->bibdb as $e) {
      if (!isset($result[$e->getKey()])) {
        //echo 'deleting...<br/>';
        unset($this->bibdb[$e->getKey()]);
      }
    }
      
  }

  /** Creates a new empty database */
  function BibDataBase() {
    $this->bibdb = array();
    $this->stringdb = array();
  }

  /** Returns the $n latest modified bibtex entries/ */
  function getLatestEntries($n) {
    $order='compare_bib_entry_by_mtime';
    $array = $this->bibdb; // array passed by value
    uasort($array, $order);
    $result = array_slice($array,0,$n);
    return $result;
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
        @$result[$bib->getLastName($a)][$bib->formatAuthor($a)]++;
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
        if ($entryisselected) {
          $result[] = $bib;
        }
      }
      
      return $result;
  }
} // end class

/** returns the default CSS of bibtexbrowser */
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

.btb-header {
  background-color: #995124;
  color: #FFFFFF;
  padding: 1px 2px 1px 2px;
}

.btb-nav-title {   
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

.purebibtex {
  font-family: monospace;
  font-size: small;
  border: 1px solid #DDDDDD;
  white-space:pre;
  background: none repeat scroll 0 0 #F5F5F5;  
  padding:10px;
  overflow:auto;
  clear:both;
}
.bibentry-by { font-style: italic; }
.bibentry-abstract { margin:15px; }
.bibentry-label { margin-top:15px; }
.bibentry-reference { margin-bottom:15px; padding:10px; background: none repeat scroll 0 0 #F5F5F5; border: 1px solid #DDDDDD; }

.btb-nav { text-align: right; }

<?php
} // end function bibtexbrowserDefaultCSS

/** encapsulates the content of a delegate into full-fledged HTML (&lt;HTML>&lt;BODY> and TITLE)
usage:
<pre>
  $db = zetDB('metrics.bib');
  $dis = new BibEntryDisplay($db->getEntryByKey('Schmietendorf2000'));
  new HTMLWrapper($dis);
</pre>
*/
class HTMLWrapper {
/**
 * $content: an object with methods
      display() 
      getRSS()
      getTitle()
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
<?php 
// if ($content->getRSS()!='') echo '<link rel="alternate" type="application/rss+xml" title="RSS" href="'.$content->getRSS().'&amp;rss" />'; 
?>
<?php 

foreach($metatags as $item) {
  list($name,$value) = $item;
  echo '<meta name="'.$name.'" content="'.$value.'"/>'."\n"; 
} // end foreach  



// now the title
if (method_exists($content, 'getTitle')) {
  echo '<title>'.strip_tags($content->getTitle()).'</title>'; 
} 

// now the CSS
echo '<style type="text/css"><!--  '."\n";

if (method_exists($content, 'getCSS')) {
  echo $content->getCSS(); 
} else if (is_readable(dirname(__FILE__).'/bibtexbrowser.css')) {
  readfile(dirname(__FILE__).'/bibtexbrowser.css');
}
else {  bibtexbrowserDefaultCSS(); }

echo "\n".' --></style>';

?>
</head>
<body>
<?php 
if (method_exists($content, 'getTitle')) {
  echo "<div class=\"rheader\">" . $content->getTitle() . "</div>";
}
?>
<?php 
  $content->display();

  if (BIBTEXBROWSER_USE_PROGRESSIVE_ENHANCEMENT) {
    javascript();
  }


?>
</body>
</html>
<?php
//exit;
} // end constructor

}


/** does nothing but calls method display() on the content. 
usage:
<pre>
  $db = zetDB('metrics.bib');
  $dis = new SimpleDisplay($db);
  new NoWrapper($dis);
</pre>
*/
class NoWrapper {
  function NoWrapper(&$content) {
    echo $content->display();
  }
}

/** is used to create an subset of a bibtex file.
usage:
<pre>
  $db = zetDB('metrics.bib');
  $query = array('year'=>2005);
  $dis = new BibtexDisplay()->setEntries($db->multisearch($query));
  $dis->display();
</pre>
*/
class BibtexDisplay {

  function BibtexDisplay() {}
  
  function setTitle($title) { $this->title = $title; return $this; }

  /** sets the entries to be shown */
  function setEntries(&$entries) {
    $this->entries = $entries;
  }

  function setWrapper($x) { $x->wrapper = 'NoWrapper'; }
  
  function display() {
    header('Content-type: text/plain; charset='.ENCODING);
    echo '% generated by bibtexbrowser <http://www.monperrus.net/martin/bibtexbrowser/>'."\n";
    echo '% '.@$this->title."\n";
    echo '% Encoding: '.ENCODING."\n";
    foreach($this->entries as $bibentry) { echo $bibentry->getText()."\n"; }
    exit;
  }
}

/** creates paged output, e.g: [[http://localhost/bibtexbrowser/testPagedDisplay.php?page=1]]
usage:
<pre>
  $_GET['library']=1;
  include( 'bibtexbrowser.php' );
  $db = zetDB('metrics.bib');
  $pd = new PagedDisplay();
  $pd->setEntries($db->bibdb);
  $pd->display();
</pre>
*/
class PagedDisplay {

  var $query = array();
  
  function PagedDisplay() {
    $this->setPage();
  }
  
    /** sets the entries to be shown */
  function setEntries(&$entries) {
    uasort($entries, ORDER_FUNCTION);
    $this->entries = array_values($entries);
  }

  /** sets $this->page from $_GET, defaults to 1 */
  function setPage() {
    $this->page = 1;
    if (isset($_GET['page'])) {
      $this->page = $_GET['page'];
    }  
  }
  
  function setQuery($query = array()) {
    $this->query = $query;
  }
    
  function getTitle() {
    return query2title($this->query). ' - page '.$this->page;  
  }
  
  function display() {      
    $less = false;
    
    if ($this->page>1) {$less = true;} 
    
    $more = true;
    
    // computing $more
    $index = ($this->page)*PAGE_SIZE;      
    if (!isset($this->entries[$index])) {
      $more = false;
    }
    
    $this->menu($less, $more);    
    echo '<table class="result" >';
    for ($i = 0; $i < PAGE_SIZE; $i++) { 
      $index = ($this->page-1)*PAGE_SIZE + $i;
      if (isset($this->entries[$index])) {
        $bib = $this->entries[$index];       
        $bib->toTR(); 
        
      } else {
        //break;
      }
    } // end foreach
    echo '</table>';
    
    $this->menu($less, $more);    
  }
  
  function menu($less, $more) {
  
    echo '<span class="nav-menu">';
    
    $prev = $this->query;
    $prev['page'] = $this->page-1;
    if ($less == true) { echo '<a '.makeHref($prev).'">Prev Page</a>'; }
    
    if ($less && $more) { echo '&nbsp;|&nbsp;'; }
    
    $next = $this->query;
    $next['page'] = $this->page+1;
    if ($more == true) { echo '<a '.makeHref($next).'">Next Page</a>'; }    
    echo '</span>';

  }
}

/** is used to create an RSS feed.
usage:
<pre>
  $db = zetDB('metrics.bib');
  $query = array('year'=>2005);
  $rss = new RSSDisplay();
  $entries = $db->getLatestEntries(10);
  $rss->setEntries($entries);
  $rss->display();
</pre>
*/
class RSSDisplay {

  var $title = 'RSS produced by bibtexbrowser';
  
  function RSSDisplay() {
    // nothing by default    
  }

  function setTitle($title) { $this->title = $title; return $this; }

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
  
  /** sets the entries to be shown */
  function setEntries(&$entries) {
    $this->entries = $entries;
  }

  function setWrapper($x) { $x->wrapper = 'NoWrapper'; }

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
      foreach($this->entries as $bibentry) {
         ?>
         <item>
         <title><?php echo $this->text2rss($bibentry->getTitle());?></title>
         <link><?php echo $bibentry->getAbsoluteURL();?></link>
         <description>
          <?php
            // we are in XML, so we cannot have HTML entitites
            // however the encoding is specified in preamble
            echo $this->text2rss(bib2html($bibentry)."\n".$bibentry->getAbstract());
          ?>
          </description>
         <guid isPermaLink="false"><?php echo urlencode(@$_GET[Q_FILE].'::'.$bibentry->getKey());?></guid>
         </item>
         <?php } /* end foreach */?>
   </channel>
</rss>

<?php
  //exit;
  }
}



/** is responsible for transforming a query string of $_GET[..] into a publication list.
usage:
<pre>
  $_GET['library']=1;
  @require('bibtexbrowser.php');
  // simulating ?bib=metrics.bib&year=2009
  $_GET['bib']='metrics.bib';
  $_GET['year']='2006';
  new Dispatcher();
</pre>
*/
class Dispatcher {

  /** this is the query */
  var $query = array();

  /** the displayer of selected entries. The default is set in BIBTEXBROWSER_DEFAULT_DISPLAY.
    *  It could also be an RSSDisplay if the rss keyword is present
    */
  var $displayer = '';

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
    // strtr is used for Windows where __FILE__ contains C:\toto and SCRIPT_FILENAME contains C:/toto (bug reported by Marco)
    // realpath is required if the path contains sym-linked directories (bug found by Mark Hereld)    
    if (strtr(realpath(__FILE__),"\\","/")!=strtr(realpath($_SERVER['SCRIPT_FILENAME']),"\\","/")) $this->wrapper=BIBTEXBROWSER_EMBEDDED_WRAPPER;

    // first pass, we will exit if we encounter key or menu or academic
    // other wise we just create the $this->query
    foreach($_GET as $keyword=>$value) {
      if (method_exists($this,$keyword)) {
        // if the return value is END_DISPATCH, we finish bibtexbrowser (but not the whole PHP process in case we are embedded)
        if ($this->$keyword()=='END_DISPATCH') return; 
      }
    }

    // at this point, we may have a query
    
    if (count($this->query)>0) {

       // first test for inconsistent queries
       if (isset($this->query[Q_ALL]) && count($this->query)>1) {
         // we discard the Q_ALL, it helps in embedded mode
         unset($this->query[Q_ALL]);
       }
    
       $selectedEntries = $_GET[Q_DB]->multisearch($this->query);
       
       // default order
       uasort($selectedEntries, ORDER_FUNCTION);
       $selectedEntries = array_values($selectedEntries);
       
       //echo '<pre>';print_r($selectedEntries);echo '</pre>';
       
       if ($this->displayer=='') {
         $this->displayer = BIBTEXBROWSER_DEFAULT_DISPLAY;
       }
    } // otherwise the query is left empty

    // do we have a displayer?
    if ($this->displayer!='') {
    
      $options = array();
      if (isset($_GET['dopt'])) {
        $options = json_decode($_GET['dopt'],true);
      }     
       
      // required for PHP4 to have this intermediate variable
      $x = new $this->displayer();
      
      if (method_exists($x,'setEntries')) {
        $x->setEntries($selectedEntries);
      }
      
      if (method_exists($x,'setTitle')) {
        $x->setTitle(query2title($this->query));
      }

      if (method_exists($x,'setQuery')) {
        $x->setQuery($this->query);
      }

      if (method_exists($x,'setWrapper')) {
        $x->setWrapper($this);
      }

      // should call method display() on $x
      new $this->wrapper($x);
      
      $this->clearQuery();
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

  /** clears the query string in $_GET so that bibtexbrowser can be called multiple times */
  function clearQuery() {
    $params= array(Q_ALL,'rss', 'astext', Q_SEARCH, Q_EXCLUDE, Q_YEAR, EDITOR, Q_TAG, Q_AUTHOR, Q_TYPE, Q_ACADEMIC, Q_KEY);
    foreach($params as $p) { unset($_GET[$p]); }    
  }
  
  function all() {
    $this->query[Q_ALL]=1;
  }

  function display() {
    $this->displayer=$_GET['display'];  
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

  function year() {  
    // we may want the latest
    if ($_GET[Q_YEAR]=='latest') {
      $years = $_GET[Q_DB]->yearIndex();
      $_GET[Q_YEAR]=array_shift($years);
    }
    $this->query[Q_YEAR]=$_GET[Q_YEAR];
  }
  
  function editor() {  $this->query[EDITOR]=$_GET[EDITOR]; }

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
    if (strlen($_GET[Q_TYPE])>0 && !preg_match('/^\^.*\$$/',$_GET[Q_TYPE])) { $_GET[Q_TYPE] = '^'.$_GET[Q_TYPE].'$'; }
    $this->query[Q_TYPE]= $_GET[Q_TYPE];
  }

  function menu() {
    $menu = createMenuManager();
    $menu->setDB($_GET[Q_DB]);
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
      $bibentry = $_GET[Q_DB]->getEntryByKey($_GET[Q_KEY]);
      $entries = array($bibentry);
      if (isset($_GET['astext'])) {
        $bibdisplay = new BibtexDisplay();
        $bibdisplay->setEntries($entries);
        $bibdisplay->display();
      } else {
        $bibdisplay = createBibEntryDisplay();
        $bibdisplay->setEntries($entries);
        new $this->wrapper($bibdisplay,$bibdisplay->metadata());
      }
      return 'END_DISPATCH';
    }
    else { nonExistentBibEntryError(); }
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
    <frame name="main" src="<?php echo '?'.Q_FILE.'='. urlencode($_GET[Q_FILE]).'&amp;year=latest'?>" />
    </frameset>
    </html>

    <?php
    return 'END_DISPATCH';
}

} // end class Dispatcher

} // end if (!defined('BIBTEXBROWSER'))

new Dispatcher();

?>