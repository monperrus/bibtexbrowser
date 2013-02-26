<?php

// Keep track of all citations and their reference numbers (order of appearance)
$citations = array();

// Create the link to the bibtex entry in reference list
function linkify($a) {
  return '<a href="#' . $a . '">' . $a . '</a>' ;
}

// Create citations from bibtex entries. One argument per bibtex entry.
/* Example:  As shown in <?php cite("MyBibtexEntry2013");?> , one can cite 
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

function make_bibtexbrowser_bibliography() {
    global $citations;
    return json_encode($citations) ;
}

?>
