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
function linkify($txt,$a) {
  if ( empty($a) ) { return '<b><abbr title="'.$txt.'">?</abbr></b>'; }
  return '<a href="#' . $a . '" class="bibreflink"><abbr title="'.$txt.'">' . $a . '</abbr></a>' ;
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
             $txt = "Unknown key &#171;$entry&#187;";
             $refs[$txt] = $ref;
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
          $txt = $bib->getVeryCompactedAuthors() . ", &#171;" . $bib->getTitle() . "&#187;, " . $bib->getYear() ;
          $refs[$txt] = $ref;
    }
    asort( $refs );
    $links = array();
    foreach ( $refs as $txt => $ref ) {
        $links[] = linkify($txt,$ref);
    }
    echo "[" . implode(",",$links) . "]" ;
}

// Function to print out the table/list of references
function make_bibliography() {
    global $citations;
    $bibfile = $_GET[Q_FILE]; // save bibfilename before resetting $_GET
    $_GET = array();
    $_GET['bib'] = $bibfile;
    $_GET['bibliography'] = 1; // also sets $_GET['assoc_keys']=1
    $_GET['keys'] = json_encode(array_flip($citations));
    //print_r($_GET);
    include( 'bibtexbrowser.php' );
?>
<script type="text/javascript" ><!--
updateCitation = function () { //detect hash change
    var hash = window.location.hash.slice(1); //hash to string
    $('.bibline').each(function() {$(this).removeClass("bibline-active");});
    if (hash) {
        $('[name='+hash+']').parents('.bibline').each(function() {$(this).addClass("bibline-active");});
    }
};
$(window).bind('hashchange',updateCitation);
updateCitation();
--></script>
<?php
}

?>
