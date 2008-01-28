<?/* bibtexbrowser : a PHP script to browse and search bib entries from BibTex files.
* This script is a refactored, extended and repackaged version from an excellent script 
* of the University of Texas at El Paso.
* Demonstration site: https://www.ensieta.fr/~monperma/bibtexbrowser.php
* Version : __VERSION__
*
* (C) 2005-2006 The University of Texas at El Paso / Joel Garcia, Leonardo Ruiz, and Yoonsik Cheon
* (C) 2006-2007 Martin Monperrus
* This program is free software; you can redistribute it and/or
* modify it under the terms of the GNU General Public License as
* published by the Free Software Foundation; either version 2 of the
* License, or (at your option) any later version.
*
* Related works:
*
* bibhtml, bib2html, bibtohtml, bibtex2html, bibtex2web
* Unlike them, bibtexbrowser is dynamic.
* Thus, you do not need to regenerate the static HTML files each time the bib file changes,
* and you can search any string in it. 
*
* "PHP BibTeX Database Manager", bibadmin
* Unlike them, bibtexbrowser does not need a MySQL database. Furthermore, they are made for 
* managing bibliographies and not only browsing them.
*
* SimplyBibtex has the same spirit, makes different architectural and presentation choices, has
* editing features
*
* Misc: a matlab script is similar !! http://www.sat.ltu.se/publications/publications.m
*
*
* Warning : the parser has some limitations
* please follow the best practices for bib entries 
*  @book{discours-de-la-methode,
*      author = "René Descartes",                       %% one attribute per line
*      title = "Discours de la M{\'{e}}thode",
*      }                                                                    %% closed with a  } alone on a line
* The next release will be based on a regular, comprehensive parser
*
*  USAGE: 
*
* <URL-to-directory>/bibtexbrowser.php
* displays the menu and all entries without filtering from the $filename hardcoded in the script
*
* <URL-to-directory>/bibtexbrowser.php?bib=bibfile.bib
* displays the menu and all entries without filtering from the file bibfile.bib
*
* <URL-to-directory>/bibtexbrowser.php?bib=bibfile.bib&all
* displays all entries
*
* <URL-to-directory>/bibtexbrowser.php?bib=bibfile.bib&year=2004
* displays all entries from year 2004
*
* <URL-to-directory>/bibtexbrowser.php?bib=bibfile.bib&author="James+Russel"
* displays all entries from author James Russel
*
* <URL-to-directory>/bibtexbrowser.php?bib=bibfile.bib&tag=france
* displays all entries with the keyword france
* @book{discours-de-la-methode,
*      author = "René Descartes",
*      title = "Discours de la M{\'{e}}thode",
*      year = 1637,
*      keywords="france and seventeenth century"
*      }
*/

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
define('Q_TAG', 'tag');
define('Q_TAG_PAGE', 'tag_page');

define('Q_TYPE', 'type');
define('Q_TYPE_PAGE', 'type_page');


define('Q_ALL', 'all');
define('Q_ENTRY', 'entry');
define('Q_SEARCH', 'search');
define('Q_RESULT', 'result');

define('AUTHOR', 'author');
define('EDITOR', 'editor');
define('TITLE', 'title');
define('YEAR', 'year');

error_reporting(E_ERROR);

session_start();

// default bib file, if no file is specified in the query string.
$filename = "uml.bib";

// retrieve the filename sent as query or hidden data, if exists.
if (isset($_GET[Q_FILE])) {
  $filename = urldecode($_GET[Q_FILE]);
}

// parse a new bib file, if requested
if (isset($_SESSION[Q_FILE]) && isset($_SESSION['main']) && ($filename ==  $_SESSION[Q_FILE])) {
  $_SESSION['main']  = $_SESSION['main'];
} else {
  $_SESSION['main']  = new DisplayManager(new BibDataBase($filename));
}

$_SESSION[Q_FILE] = $filename;

if (isset($_GET[Q_ENTRY])) {//__removeme__
        $headers=getallheaders();//__removeme__
        if (!eregi("googlebot|slurp|msnbot|fast",$headers['User-Agent'])) {//__removeme__
          $headers['date'] = time();//__removeme__
          $entry = $_SESSION['main']->db->getEntry($_GET[Q_ENTRY]);//__removeme__
          $headers['file'] = $_GET[Q_FILE].'#'.$entry->getTitle();//__removeme__
          $headers['ip'] = $_SERVER["REMOTE_ADDR"];//__removeme__
          $file  = fopen ("log-bibtexbrowser.txt", "a");//__removeme__
          fputs($file,serialize($headers)."\n");//__removeme__
          fclose($file);//__removeme__
	}//__removeme__
}//__removeme__


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
      $this->bibdb[$entry->getId()] = $entry;
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
      if (ereg("^@.*{", $line)) {
	//echo 'NEW: ' . $line . "\n";
	$type = trim(substr($line, 1, strpos($line,'{') - 1));
	$fields = array();
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
    $result = str_replace(',','', $result);
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
    return $this->type;
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

  /** Returns the title of this entry? */
  function getTitle() {
    return $this->getField('title');
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

  /** Returns the verbatim text of this bib entry. */
  function getText() {
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
  return 'href="'. $_SERVER['PHP_SELF'] .'?'. $qstring .'"';
}

/**
 * Returns the formated author name. The argument is assumed to be
 * <FirstName LastName>, and the return value is <LastName, FirstName>.
 */
function formatAuthor($author){
  $author = trim($author);
  return trim(!strrchr($author, ' ') ? 
	      $author :
	      strrchr($author, ' ') . ', ' 
	      .substr($author, 0, strrpos($author, " ")));
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
        <td class="title">Generated from <?= $filename ?></td>
      </tr>
    </table>
    <?php
  }

  /** Displays the search view in a form. */
  function searchView() {
    global $filename;
    ?>
    <form action="<?= $_SERVER['PHP_SELF'] ?>" method="get" target="main">
      <input type="text" name="<?= Q_SEARCH ?>" class="input_box" size="18"/>
      <input type="hidden" name="<?= Q_FILE ?>" value="<?= $filename ?>"/>
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
          <a <?= $yearHref ?>>By year</a>
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


  /** Displays and controls the main contents (or result) view. */
  function mainVC() {
      $result = null;
     if (isset($_GET[Q_ENTRY])){	
      $result = new SingleResultDisplay(
        $this->db->getEntry(
        $_GET[Q_ENTRY]));
     } else if (isset($_GET[Q_SEARCH])){  // search?
	$to_find = $_GET[Q_SEARCH];
	$searched = $this->db->search($to_find);
	if (count($searched)==1) $result = new SingleResultDisplay($searched[0]);
	else {
	  $header = 'Search: ' . trim($to_find);
          $result = new ResultDisplay($searched, $header,array(Q_SEARCH => $to_find));
        }
	// clicking an author, a menu item from the authors menu?
     } else if (isset($_GET[Q_AUTHOR])) {  
	$to_find = urldecode($_GET[Q_AUTHOR]);
	$searched = $this->db->search($to_find, array('author'));
	$header = 'Publications of ' . ucwords($to_find);
        $result = new ResultDisplay($searched, $header,array(Q_AUTHOR => $to_find));
	// clicking a type, a menu item from the types menu?
      } else if(isset($_GET[Q_TAG])) {
	$to_find = $_GET[Q_TAG];
	$searched = $this->db->search($to_find, array('keywords'));
	$header = 'Keyword: ' . ucwords($to_find);
        $result = new ResultDisplay($searched, $header,array(Q_TAG => $to_find));
      } 
	else if(isset($_GET[Q_YEAR])) {
	$to_find = $_GET[Q_YEAR];
	$searched = $this->db->search($to_find, array('year'));
	$header = 'Year: ' . ucwords($to_find);
        $result = new ResultDisplay($searched, $header,array(Q_YEAR => $to_find));
      } 
	else if(isset($_GET[Q_TYPE])) {
	$to_find = $_GET[Q_TYPE];
	$searched = $this->db->searchType($to_find); 
	$header = 'Type: ' . ucwords($to_find);
        $result = new ResultDisplay($searched, $header,array(Q_TYPE => $to_find));
      }
      else if(isset($_GET[Q_ALL])) {
	$to_find = $_GET[Q_ALL];
	$searched = array_values($this->db->bibdb);
        $header = 'All';
        $result = new ResultDisplay($searched, $header,array(Q_ALL =>''));
      }

    // requesting a different page of the result view?
    if (isset($_GET[Q_RESULT])) {
      $result->setPage($_GET[Q_RESULT]);
      // requesting a differen page of type or author menus?
    }

    // display
    return $result;
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
            <td class="header"><b><?= $title ?></b></td>
            <td class="header" align="right"><b>
                <?= $this->menuPageBar($pageKey, $numEntries, $page,
				       $pageSize, $startIndex, $endIndex)?></b></td>
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

  /** the original filter author, year, etc */
  var $filter;


  /** Creates an instance with the given entries and header. */
  function ResultDisplay(&$result, $header,$filter) {
    $this->result = $result;
    $this->header = $header;
    $this->page = 1;
    $this->filter = $filter;
  }

  /** Sets the page number to display. */
  function setPage($page) {
    $this->page = $page;
  }

  /** Displays the entries preceded with the header. */
  function display() {
    $this->displayHeader($this->header);
    $this->displayContents();
  }

  /** Displays the header stringg. */
  function displayHeader() {
    $header = $this->header ? $this->header : "&nbsp;";
    echo "<div class=\"rheader\"><b>$header</b></div><br/>\n";
  }

  /** 
   * Displays the summary information of each bib entries of the
   * current page. For each entry, this method displays the author,
   * title, and type; the bib entries are displayed grouped by the
   * publication years. If the bib list is empty, an error message is
   * displayed.
   */
  function displayContents() {
    $page = $this->page;

    // print error message if no entry.
    if (empty($this->result)) {
      echo "<b>No match found!</b>\n";
      return;
    }

    // print a page bar, a list of clickable page numbers
    $pageSize = PAGE_SIZE; // no. of entries per page
    $noPages = ceil(count($this->result) / $pageSize);
    
    if ($noPages>1) $this->displayPageBar($noPages, $page);

    // create a year -> entries map to display by years
    $years = array();
    foreach ($this->result as $e) {
      $y =  trim($e->getField(YEAR));
      $years[$y][] = $e;
    }
    krsort($years);

    $startIndex = ($page - 1) * $pageSize;
    $endIndex = $startIndex + $pageSize;
    $index = 0;
    ?>

    <table class="result" >
    <?php
    foreach ($years as $year => $entries) {
      if ($index >= $endIndex) { break; }

      if ($index >= $startIndex && $index < $endIndex) {
      ?>
      <tr>
        <td colspan="2" class="header"><?= $year ?></td>
      </tr>
      <?php
      }
      foreach ($entries as $bib) {
	if ($index >= $startIndex && $index < $endIndex) {
	  $author = compactAuthor($bib->getAuthor());
	  $id = $bib->getId();
	  $title = $bib->getField(TITLE);
	  $type = $bib->getType();
	  $href = makeHref(array(Q_ENTRY => $id));
      ?>
      <tr>
        <td class="a_name">
	    <?= $author?>
        </td>
        <td><a <?= $href ?>><?= $title ?></a><br/>
	    (<?= $type  ?>)
            <div class="mini_se"></div>
        </td>
      </tr>
      <?php
	}
	$index++;
      }
    }
    ?>
    </table>
    <?php
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
      $this->filter[Q_RESULT] = $i;
      $href = makeHref($this->filter);
      if ($i == $page) {
	echo '<a '. $href .'><b>['. $i .']</b></a>';
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


/** Class to display a single bibentry. */
class SingleResultDisplay extends ResultDisplay {

  /** Creates an instance with the given bib entry and header.
   * It the object is an instance of BibIndexEntry, it may be
   * mutated to read the rest of the fields.
   */
  function SingleResultDisplay(&$bibentry) {
    $this->result = $bibentry;
    $this->header = $this->result->getTitle();
  }

  /** Displays the bib entry, both in the formatted and raw text. 
   * The object may be mutated. */
  function displayContents() {
    if ($this->result) {
      $this->displayEntryFormatted($this->result);
      echo '<br/><div class="header">BibTeX entry:</div>';
      $this->displayEntryUnformatted($this->result);
    }
  }

  /** 
   * Displays a unformated (verbatim) text of the given bib entry.
   * The text is displayed in <pre><code></code></pre> tag. 
   * The object may be mutated to read the rest of the fields. 
   */
  function displayEntryUnformatted(&$entry) {
    $text =$entry->getText();
    ?>
    <!-- Note that the indentation does matter in the PRE tag -->
<pre><code><?=$text?></code></pre>
    <?php
   }

  /** 
   * Displays a formatted text of the given bib entry. The text is
   * displayed in a table. The object may be mutated to read the
   * rest of fields.
   */
  function displayEntryFormatted(&$bib) {
    $author = $bib->getAuthor();
    $type = $bib->getType();

    // display heading: author (type)
    ?>
    <table class="result">
      <tr>
        <td colspan="2">
          <span class="bit_big"><b><?= $author ?></b></span> (<?= $type?>)
        </td>
      </tr>

    <?php
    // display all fields: name and value pairs
    foreach ($bib->getFields() as $name => $value) {
      if ($name == 'key') { continue; } // skip the key field
      // make href if URL
      $dval = $name == 'url' ? "<a href=\"$value\">$value</a>" : $value;
    ?>
    <tr>
      <td class="header"><?php echo ucwords($name); ?></td>
      <td><?= $dval ?></td>
    </tr>
    <?php
    } // foreach
    ?>
    </table>
    <?php
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
  /** A hash table from IDs (number) to bib entries (BibEntry). */
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

  /** Given its ID, return the bib entry. */
  function getEntry($id) {
    return $this->bibdb[$id];
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
	$ta = trim($a);
	if (!array_key_exists($ta, $result)) {
	  $result[$ta] = formatAuthor($ta);
	}
      }	 	       
    }
    asort($result);
    return $result;
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

  /** Returns an array of bib entries (BibEntry) that contains the
   * given phrase in the given fields. If the fields are empty, all
   * fields are searched. */
  function search($phrase, $fields = NULL) {
    $phrase = strtolower(trim($phrase));
    if (empty($phrase)) {
      return array();
    }
    
    $result = array();
    foreach ($this->bibdb as $bib) {
      if ($bib->hasPhrase($phrase, $fields)) {
	$result[] = $bib;
      }
    }
    //print_r($result);
    return $result;
  }
}



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
<meta name="generator" content="bibtexbrowser v__VERSION__" />
<style type="text/css">
<!--
body {
  font-family: Geneva, Verdana, Arial, Helvetica, sans-serif;
  font-size: small;
  margin: 0px;
  padding: 10px;
}
td {
  font-size: xx-small;
  vertical-align:top;
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
  background-color: #003366;
  color: #ffffff;
  padding: 2px;
  margin: 0px;
  border-bottom: #ff6633 2px solid;

}
.menu {
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

.poweredby{
  text-align:right;
  font-size: x-small;
  margin-top : 5px;
}
-->
</style>

<? 
$result = $_SESSION['main']->mainVC();
?>

<title>
<?
if ($result != null) echo $result->header.' in '.$filename;
else echo 'You are browsing '.$filename.' with bibtexbrowser v__VERSION__';
?>
</title>

</head>

<?
if (isset($_GET['menu']))
{
  echo '<body>';
  echo $_SESSION['main']->searchView(); 
  echo $_SESSION['main']->typeVC().'<br/>';
  echo $_SESSION['main']->yearVC().'<br/>';
  echo $_SESSION['main']->authorVC().'<br/>';
  echo $_SESSION['main']->tagVC().'<br/>';
  echo '</body>';
} // end isset($_GET['menu']
else {
  if ($result != null) {
    echo '<body>';
    $result->display();
    $poweredby = '<div class="poweredby">';
    $poweredby .= '<a href="pub/bibtexbrowser.php.txt">';//__removeme__
    $poweredby .= ' Powered by bibtexbrowser';
    $poweredby .= '</a>';//__removeme__
    $poweredby .= '</div>';
    echo $poweredby;
    echo '</body>';
  }
  else {
    ?>
    <frameset cols="15%,*">
    <frame name="menu" src="<?= $_SERVER['PHP_SELF'] .'?'.Q_FILE.'='. urlencode($filename).'&menu' ?>" />
    <frame name="main" src="<?= $_SERVER['PHP_SELF'] .'?'.Q_FILE.'='. urlencode($filename).'&all' ?>" />
    </frameset>
    <?
  } 
} ?>

</html>
