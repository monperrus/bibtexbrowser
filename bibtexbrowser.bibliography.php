<?php

/* TODO: improve by loading bibtexbrowser DB first, then cite() checks whether the entry is in the DB (prints "?" if not found). this also allows for using other ABBRV than indices.
*/

@define('LAYOUT','list');
@define('USEBIBTHUMBNAIL',0);
@define('BIBLIOGRAPHYSTYLE','MGBibliographyStyle');

// Keep track of all citations and their reference numbers (order of appearance)
$citations = array();

// Create the link to the bibtex entry in reference list
function linkify($a) {
  return '<a href="#' . $a . '">' . $a . '</a>' ;
}

// Create citations from bibtex entries. One argument per bibtex entry.
/* Example:  As shown in <?php cite("MyBibtexEntry2013","MyOtherRef2013");?> , one can use bibtex within HTML/PHP.
*/
function cite() {
    global $citations;
    $entries = func_get_args(); // list of bibtex entries
    $refs = array(); // corresponding references
    foreach ($entries as $entry) {
        if ( array_key_exists ( $entry , $citations ) ) {
            $ref = $citations[$entry] ;
        } else {
            $ref = count( $citations ) + 1 ;
            $citations[$entry] = $ref ;
        }
        $refs[] = $ref;
    }
    sort( $refs );
    $links = array_map( 'linkify', $refs );
    echo "[" . implode(",",$links) . "]" ;
}

// prepare bibtexbrowser query
function make_bibtexbrowser_bibliography_keys() {
    global $citations;
    return json_encode(array_flip($citations)) ;
}

function make_bibliography() {
    global $_GET;
    $_GET = array();
    $_GET['bib']='mg.bib';
    $_GET['bibliography']=1; // sets assoc_keys
    $_GET['keys']=make_bibtexbrowser_bibliography_keys();
    //print_r($_GET);
    include( 'bibtexbrowser.php' );
}

?>
