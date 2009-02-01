<?php /* bibtexbrowser: a PHP script to browse and search bib entries from BibTex files

[[#Download]] | [[#Screenshot]] | [[#Features]] | [[#Related_tools]] | [[#Users]] | [[#Copyright]]

bibtexbrowser is a PHP script to browse and search bib entries from BibTex files. For instance, on the [[http://www.monperrus.net/martin/bibtexbrowser.php|bibtexbrowser demonstration site]], you can browse my main bibtex file.

For feature requests or bug reports, [[http://www.monperrus.net/martin/|please drop me an email ]].

Thanks to all [[#Users]] of bibtexbrowser :-)

=====Download=====

**[[http://www.monperrus.net/martin/bibtexbrowser.php.txt|Download bibtexbrowser]]**

=====Screenshot=====

<a href="bibtexbrowser-screenshot.png"><img height="500" src="bibtexbrowser-screenshot.png" alt="bibtexbrowser screenshot"/><br/></a>

=====Features=====

* [[http://www.monperrus.net/martin/bibtexbrowser.php|bibtexbrowser can display the menu and all entries without filtering from the $filename hardcoded in the script ]]
* [[http://www.monperrus.net/martin/bibtexbrowser.php?bib=biblio_monperrus.bib|bibtexbrowser can display the menu and all entries without filtering from the file name passed as parameter]]
* [[http://www.monperrus.net/martin/bibtexbrowser.php?bib=biblio_monperrus.bib&amp;all|bibtexbrowser can display all entries  out of a bibtex file]]
* [[http://www.monperrus.net/martin/bibtexbrowser.php?bib=biblio_monperrus.bib&amp;year=2004|bibtexbrowser can display all entries for a given year]]
* [[http://www.monperrus.net/martin/bibtexbrowser.php?bib=biblio_monperrus.bib&amp;author=Barbara+A.+Kitchenham|bibtexbrowser can display all entries for an author]]
* [[http://www.monperrus.net/martin/bibtexbrowser.php?bib=biblio_monperrus.bib&amp;key=monperrus08phd|bibtexbrowser can display a single entry]]
* [[http://www.monperrus.net/martin/bibtexbrowser.php?bib=biblio_monperrus.bib&amp;keywords=mda|bibtexbrowser can display all entries with a bib keyword]]
* [[http://www.monperrus.net/martin/bibtexbrowser.php?bib=biblio_monperrus.bib&amp;search=ocl|bibtexbrowser can display found entries with a search word (it can be in any bib field)]]
* bibtexbrowser allows multi criteria search, e.g. ?type=inproceedings&amp;year=2004
* bibtexbrowser outputs valid XHTML 1.0 Transitional
* bibtexbrowser in designed to be search engine friendly.
* You can include your publications list into your home page:
&#60;?php
session_start(); //to avoid reparsing the bib file; should be at the very top of the script
// the bib file
$_GET&#91;'bib'&#93;='mybib.bib';
// the request
$_GET&#91;'author'&#93;='Martin Monperrus';
include('bibtexbrowser.php');
?>
And tailor it with a CSS style!
&#60;style>
.date {
   background-color: blue;
   }

.rheader {
   font-size: large
   }


.bibline {
  padding:3px;
  padding-left:15px;
  vertical-align:top;
}
&#60;/style>

Warning: you may change the default iso-8859-1 encoding if your bib file is in utf-8 (define('ENCODING','iso-8859-1') below);

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
Current version: v__DATE__

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
define('Q_RESULT', 'result');

define('AUTHOR', 'author');
define('EDITOR', 'editor');
define('SCHOOL', 'school');
define('TITLE', 'title');
define('BOOKTITLE', 'booktitle');
define('YEAR', 'year');

// this constant may have already been initialized
// when using include('')
@define('SCRIPT_NAME','bibtexbrowser.php');

// for clean search engine links
// we disable url rewriting
// ... and hope that your php configuration will accept one of these
@ini_set("session.use_only_cookies",1);
@ini_set("session.use_trans_sid",0);
@ini_set("url_rewriter.tags","");

// we ensure that the pages won't get polluted
// if future versions of PHP change warning mechanisms...
@error_reporting(E_ERROR);

// we use sessions to avoid reparsing the bib file for each request
// the session may be already started
// by an external script that includes bibtexbrowser
@session_start();

// default bib file, if no file is specified in the query string.
global $filename;
$filename = "biblio_monperrus.bib";
// retrieve the filename sent as query or hidden data, if exists.
if (isset($_GET[Q_FILE])) {
  $filename = urldecode($_GET[Q_FILE]);
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

// parse a new bib file, if this file has not been already parsed
if (!isset($_SESSION[$filename]) ) {
  echo '<!-- parsing '.$filename.'-->';
  // we use serialize in order to be able to get a session correctly set up
  // without bibtexbrowser loaded in PHP
  $_SESSION[$filename]  = serialize(new DisplayManager(new BibDataBase($filename)));
}
$displaymanager=unserialize($_SESSION[$filename]);


////////////////////////////////////////////////////////


/**
 * Class to parse a bibtex file.
 */
class BibParser {

  /** A hashtable from IDs (number) to bib entries (BibEntry). */
  var $bibdb;

  /** Parses the given bibtex file and stores all entries to $bibdb. */
  //@ assignable $bibdb;
  function BibParser($filename) {
    $file = fopen($filename, 'r');
    $entry =$this->parseEntry($file);
    while ($entry) {
      $this->bibdb[$entry->getKey()] = $entry;
      //if ($entry->getId() >= 500) { return; } // !FIXME!
      $entry =$this->parseEntry($file);
      //print_r($entry);
    }
    fclose($file);
    //print_r($this->bibdb);
  }


  /** Returns the array of parsed bib entires. */
  function getEntries() {
    return $this->bibdb;
  }

  /** Parses and returns the next bib entry from the fiven file.  If
   * no more entry exist, NULL is returned. */
  function parseEntry($file) {
    // parse bib type, e.g., @BOOK{
    while (true) {
      $raw_line = $this->nextLine($file);
      //echo 'RAW: ' . $raw_line;
      if (!$raw_line) { // EOF?
 return NULL;
      }
      $line = trim($raw_line);
      if (ereg('^[[:space:]]*@.*\{[[:space:]]*([^,]*)', $line,$regs)) {
 //echo 'NEW: ' . $regs[1] . "\n";
 $type = trim(substr($line, 1, strpos($line,'{') - 1));
 $fields = array();
 $fields['key']= $regs[1];
 $raw_bib = $raw_line;
 break;
      }
    }

    // parse fields, if any
    $raw_line = $this->nextLine($file);
    while ($raw_line) {
      $raw_bib .= $raw_line;

      $line = trim($raw_line);
      if (ereg("^.*=", $line)) { // new field?
 //echo 'FIELD: ' . $line . "\n";
 //get the field type
 $ps = strpos($line, '=');
 $fkey = strtolower(trim(substr($line,0,$ps)));
 $fval = $this->extractFieldValue($line);
 if (strlen($fval)) {
   $fields[$fkey] = $fval;
 }
         if (ereg(",[:space:]*}$",$line)) return $this->makeBibEntry($type, $fields, $raw_bib);
         if (ereg("=[^{]*}$",$line))  return $this->makeBibEntry($type, $fields, $raw_bib);
      } else if ($line == "}") { // end of entry?
 //echo 'END: ' . $line . "\n";
 return $this->makeBibEntry($type, $fields, $raw_bib);
      } else { // continued field?
 $fval = $this->extractFieldValue($line);
 if (strlen($fval) > 0) {
   if (!isset($fields[$fkey])) { // no value seen so far?
     // remove starting " if exists
     if ($fval[0] == '"') {
       $fval = ltrim(substr($fval, 1));
     }
   }
   if (strlen($fval)) {
     if (isset($fields[$fkey])) {
       $fields[$fkey] = $fields[$fkey].' '.$fval;
     } else {
       $fields[$fkey] = $fval;
     }
   }
 }
      }

      $raw_line = $this->nextLine($file);
    }

    // entry ended without a closing brace
    return $this->makeBibEntry($type, $fields, $raw_bib);
  }

  /** Creates and return a bib entry by doing any postprogessing to
   * the arguments. E.g., canonical rep. of type names. */
  function makeBibEntry($type, &$fields, $raw_bib) {
    // remove a trailing comma, if exists.
    foreach ($fields as $name => $value) {
      $fields[$name] = rtrim($value, ',');
    }
    return new BibEntry($this->stdType($type), $fields, $raw_bib);
  }

  /** Returns the canonical representation of the given type name. */
  function stdType($type) {
    static $types = array();
    foreach ($types as $t) {
      if (strcasecmp($t, $type) == 0) {
 return $t;
      }
    }
    $type = ucfirst($type);
    $types[] = $type;
    return $type;
  }

  /** Extracts and returns a field value from the given line. */
  function extractFieldValue($line) {

    $result = ereg_replace("^[^=]*=[ :space:]*", '', $line);
    // clean out tex stuff
    $result = str_replace('}','', $result);
    $result = str_replace('{','', $result);
    // comas are important to recognize the author name format
    //$result = str_replace(',','', $result);
    $result = str_replace("\'", '', $result); // e.g., \'{e}
    $result = str_replace('\`', '', $result);
    $result = str_replace('\^', '', $result);
    $result = str_replace('"', '', $result);
    $result = str_replace('\~', '', $result);
    $result = str_replace('\.', '', $result);
    $result = str_replace('\u', '', $result);
    $result = str_replace('\v', '', $result);
    $result = str_replace('\H', '', $result);
    $result = str_replace('\t', '', $result);
    $result = str_replace('\c', '', $result);
    $result = str_replace('\d', '', $result);
    $result = str_replace('\b', '', $result);
    $result = str_replace('\i', 'i', $result);
    $result = str_replace('\j`', 'j', $result);
    $result = str_replace('\j`', 'j', $result);
    $result = str_replace('\ ', ' ',$result); // space

    return trim($result);
  }

  /** Returns the next non-empty line; returns NULL upon end-of-file. */
  function nextLine($file) {
    $rawline = fgets($file, READLINE_LIMIT);
    while (!feof($file)) {
      //echo "RAW: " . $rawline;
      $line = trim($rawline);
      if (strpos($line, '@string') === false // !FIXME!@string ignored!
   && strlen($line) != 0 && $line[0] != '%') {
 return $rawline;
      }
      $rawline = fgets($file, READLINE_LIMIT);
    }
    return NULL;
  }
}


// ----------------------------------------------------------------------
// BIB ENTRIES
// ----------------------------------------------------------------------

/**
 * Class to represent a bibliographic entry.
 */
class BibEntry {

  /** The type (e.g., article and book) of this bib entry. */
  var $type;

  /** The fields (fieldName -> value) of this bib entry. */
  var $fields;

  /** The verbatim copy (i.e., whole text) of this bib entry. */
  var $text;

  /** Creates a new bib entry. Each bib entry is assigned a unique
   * identification number. */
  function BibEntry($type, &$fields, &$text) {
    static $id = 0;
    $this->id = $id++;
    $this->type = $type;
    $this->fields =$fields;
    $this->text =$text;
  }

  /** Returns the type of this bib entry. */
  function getType() {
    return strtolower($this->type);
  }

  /** Has this entry the given field? */
  function hasField($name) {
    return array_key_exists($name, $this->fields);
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

  /** Returns the authors of this entry? */
  function getAuthors() {
    return $this->getField('author');
  }

  /** Returns the year of this entry? */
  function getYear() {
    return $this->getField('year');
  }

  /** Returns the value of the given field? */
  function getField($name) {
    if ($this->hasField($name))
 {return $this->fields[$name];}
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
   * in the given fields. The argument $fields is an array of
   * field names; if null, all fields are considered. */
  function hasPhrase($phrase, $fields = null) {
    if (!$fields) {
      return strpos(strtolower($this->getText()), $phrase) !== false;
    }
    foreach ($fields as $f) {
      if ($this->hasField($f) &&
   strpos(strtolower($this->getField($f)), $phrase) !== false) {
 return true;
      }
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
          foreach (explode(" and ", $this->getAuthors()) as $author) {
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

        if (($type=="inproceedings") || ($type=="incollection")) {
            echo " In <i>".$this->getField(BOOKTITLE)."</i>";
        }
        if ($type=="article") {
            echo " In <i>".$this->getField("journal")."</i>";
            echo ", volume ".$this->getField("volume");
        }

        if ($this->hasField('editor')) {
          $editors = array();
          foreach (explode(" and ", $this->getField("editor")) as $editor) {
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
 * Returns a compacted string form of author names by throwing away
 * all author names except for the first one and appending ", et al."
 */
function compactAuthor($author){
  $authors = explode(" and ", $author);
  $etal = count($authors) > 1 ? ', et al.' : '';
  return formatAuthor($authors[0]) . $etal;
}

/**
 * A class providing GUI views and controllers. In general, the views
 * are tables that can be incorporated into bigger GUI tables.
 */
class DisplayManager {

  /** The bibliographic database, an instance of class BibDataBase. */
  var $db;

  /** The result to display */
  var $result;

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
    <br/>
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
      $this->result->display();
  }

  /** Process the GET parameters */
  function processRequest() {

    global $filename;
    $this->result = null;

    if ($_GET[Q_KEY]!=''){

    if (isset($this->db->bibdb[$_GET[Q_KEY]])) {
    $this->result = new SingleResultDisplay(
      $this->db->getEntryByKey(
      urldecode($_GET[Q_KEY])));
      }
      else { header('HTTP/1.1 404 Not found'); $this->result = new  ErrorDisplay(); }
    } else if ($_GET[Q_SEARCH]!=''){  // search?
      $to_find = $_GET[Q_SEARCH];
      $searched = $this->db->search($to_find);
      if (count($searched)==1)
        $this->result = new SingleResultDisplay($searched[0]);
      else {
        $header = 'Search: ' . trim($to_find);
        $this->result = new ResultDisplay($searched, $header,array(Q_SEARCH => $to_find));
              }
    // clicking an author, a menu item from the authors menu?
      }  else if(isset($_GET[Q_ALL])) {
    $to_find = $_GET[Q_ALL];
    $searched = array_values($this->db->bibdb);
          $header = 'Bibtex entries';
          $this->result = new ResultDisplay($searched, $header,array(Q_ALL =>''));
      }
    else {
      $query = array();
      if ($_GET[Q_AUTHOR]!='') { $query[Q_AUTHOR]=$_GET[Q_AUTHOR]; }
      if ($_GET[Q_TAG]!='') { $query[Q_TAG]=$_GET[Q_TAG]; }
      if ($_GET[Q_YEAR]!='') { $query[Q_YEAR]=$_GET[Q_YEAR]; }
      if ($_GET[Q_TYPE]!='') { $query[Q_TYPE]=$_GET[Q_TYPE]; }
      //print_r($query); 
      if (count($query)<1) return false;
      $searched = $this->db->multisearch($query);
      $headers = array();
      foreach($query as $k=>$v) $headers[] = ucwords($k).': '.ucwords($v);
      $header = join(' &amp; ',$headers); 
      $this->result = new ResultDisplay($searched, $header, $query);
     }

     
    // adding the bibtex filename
    if (isset($this->result)) $this->result->header.=' in '.$filename;

    // requesting a different page of the result view?
    if (isset($this->result) && isset($_GET[Q_RESULT])) {
      $this->result->setPage($_GET[Q_RESULT]);
      // we add the page number to the title
      // in order to have unique titles
      // google prefers that
      $this->result->header.=' - page '.$_GET[Q_RESULT];
    }


    // return true if bibtexbrowser has found something to do
    return $this->result!==null;
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
class ResultDisplay {
  /** the bib entries to display. */
  var $result;

  /** the header string. */
  var $header;

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
  var $headerStrategy;

  /** Creates an instance with the given entries and header. */
  function ResultDisplay(&$result, $header,$filter) {
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

  /** Returns the powered by part */
  function poweredby() {
    $poweredby = "\n".'<div style="text-align:right;font-size: xx-small;opacity: 0.6;" class="poweredby">';
    $poweredby .= '<!-- If you like bibtexbrowser, thanks to keep the link :-) -->';
    $poweredby .= 'Powered by <a href="http://www.monperrus.net/martin/bibtexbrowser/">bibtexbrowser</a>';
    $poweredby .= '</div>'."\n";
    return $poweredby;
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
     if ($this->header!="")  echo "<div class=\"rheader\">{$this->header}</div>\n";

    $this->contentStrategy->display($this);
    echo '<br/>';
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
    echo '</center></div><br/>';
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

      }
    }
    ?>
    </table>
    <?php
  } // end function
} // end class


class ErrorDisplay  {

  function ErrorDisplay() {
    $this->header="Bib entry not found!";
  }

  /** Displays en error message */
  function display() {

    ?>
    <b>Sorry, this bib entry does not exist.</b>
    <a href="?">Back to bibtexbrowser</a>

    <?php
  }
}

class SingleBibEntryContentStrategy {
   function display($display) {
      $display->result->toEntryUnformatted();
      }
}


/** Class to display a single bibentry. */
class SingleResultDisplay extends ResultDisplay {

  /** Creates an instance with the given bib entry and header.
   * It the object is an instance of BibIndexEntry, it may be
   * mutated to read the rest of the fields.
   */
  function SingleResultDisplay(&$bibentry) {
    $this->result = $bibentry;
    global $filename;
    $this->header = 'Bibtex entry: '.$this->result->getTitle().' in '.$filename;
    $this->contentStrategy = new SingleBibEntryContentStrategy();
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
 $parser = new BibParser($filename);
 //print_r($parser);
  $this->bibdb =$parser->getEntries();
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
      $authors =explode(' and ', $bib->getAuthor());
      foreach($authors as $a){
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
      $tags =explode(' and ', $bib->getField("year"));
      foreach($tags as $a){
 $ta = trim($a);
   $result[$ta] = $ta;
      }
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

  /** Returns an array of bib entries (BibEntry) that 
   * satisfy the query
   * $query is an hash with entry type as key and searched fragment as value
   */

  function multisearch($query) {
    if (count($query)<1) {return array();}
    $result = array();

    foreach ($this->bibdb as $bib) {
        $entryisselected = true;
        foreach ($query as $field => $fragment) {
          if (($field!='type' && !$bib->hasPhrase(strtolower($fragment), array($field))) || ($field=='type' && $bib->getType()!=$fragment)) {
              $entryisselected = false;
          }
        }
        if ($entryisselected)  $result[] = $bib;
    }
    return $result;
  }

  /** Returns an array of bib entries (BibEntry) that contains the
   * given phrase.
   */
  function search($phrase) {
    $phrase = strtolower(trim($phrase));
    if (empty($phrase)) {
      return array();
    }

    $result = array();
    foreach ($this->bibdb as $bib) {
      if ($bib->hasPhrase(strtolower($phrase))) {
 $result[] = $bib;
      }
    }
    //print_r($result);
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
  margin: 0px;
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
  echo $displaymanager->searchView();
  echo $displaymanager->typeVC().'<br/>';
  echo $displaymanager->yearVC().'<br/>';
  echo $displaymanager->authorVC().'<br/>';
  echo $displaymanager->tagVC().'<br/>';
  echo '</body></html>';
} // end isset($_GET['menu']
else if ($displaymanager->processRequest()) {

    if (!$included) printHTMLHeaders($displaymanager->result->header);
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