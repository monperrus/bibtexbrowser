<?php

// Load the database without doing anything else
function load_DB() {
    $_GET['library']=1;
    include( 'bibtexbrowser.php' );
    setDB();
    return $_GET[Q_DB];
}
$DB = load_DB();

// Keep track of all citations and their reference numbers (order of appearance)
$citations = array();

// Function to create a link for a bibtex entry
function linkify($a) {
  if ( empty($a) ) { return "<b>?</b>"; }
  return '<a href="#' . $a . '">' . $a . '</a>' ;
}

// Create citations from bibtex entries. One argument per bibtex entry.
/* Example:  As shown in <?php cite("MyBibtexEntry2013","MyOtherRef2013");?> , one can use bibtex within HTML/PHP.
*/
function cite() {
    global $citations;
    global $DB;
    $entries = func_get_args(); // list of bibtex entries
    $refs = array(); // corresponding references
    foreach ($entries as $entry) {
          $bib = $DB->getEntryByKey($entry);
          if ( empty($bib) ) {
             $ref = array(); // empty ref for detection by linkify, while getting last with sort()
             $refs[] = $ref;
             continue;
          }
          if (ABBRV_TYPE != 'index') {
              $ref = $bib->getAbbrv();
              $citations[$entry] = $ref;
          } else {
            if ( array_key_exists ( $entry , $citations ) ) {
                $ref = $citations[$entry] ;
            } else {
                $ref = count( $citations ) + 1 ;
                $citations[$entry] = $ref ;
            }
          }
          $refs[] = $ref;
    }
    sort( $refs );
    $links = array_map( 'linkify', $refs );
    echo "[" . implode(",",$links) . "]" ;
}

// Function to print out the table/list of references
function make_bibliography() {
    global $citations;
    $bibfile = $_GET[Q_FILE]; // save bibfilename before resetting $_GET
    $_GET = array();
    $_GET['bib'] = $bibfile;
    $_GET['bibliography']=1; // also sets $_GET['assoc_keys']=1
    $_GET['keys'] = json_encode(array_flip($citations));
    //print_r($_GET);
    include( 'bibtexbrowser.php' );
}

?>
