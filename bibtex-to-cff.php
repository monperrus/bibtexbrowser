<?php
// create CITATION.cff file for Github from a bibtex file
// reference documentation: https://docs.github.com/en/repositories/managing-your-repositorys-settings-and-features/customizing-your-repository/about-citation-files#about-citation-files
// script part of https://github.com/monperrus/bibtexbrowser/
//
// Usage
// $ php bibtexbrowser-cff.php test_cli.bib --id classical 
//     -> this creates a file CITATION.cff for @xx{classical,

$_GET['library']=1;
require('bibtexbrowser.php');

function bibtexbrowser_cff($arguments) {
    $db = new BibDataBase();
    $db->load($arguments[1]);
    $current_entry=NULL;
    $current_field=NULL;
    for ($i=2;$i<count($arguments); $i++) {
        $arg=$arguments[$i];
        if ($arg=='--id') {
            $current_entry = $db->getEntryByKey($arguments[$i+1]);
        }
    }
    // now we have $current_entry
    echo "cff-version: 1.2.0"."\n";
    echo "# CITATION.cff created with https://github.com/monperrus/bibtexbrowser/"."\n";
    echo "preferred-citation:"."\n";
    echo "  title: \"".$current_entry->getTitle()."\""."\n";
    if ($current_entry->hasField("doi")) {
        echo "  doi: \"".$current_entry->getField("doi")."\""."\n";
    }
    if ($current_entry->hasField("year")) {
        echo "  year: \"".$current_entry->getField("year")."\""."\n";
    }
    if ($current_entry->hasField("journal")) {
        echo "  type: article\n";
        echo "  journal: \"".$current_entry->getField("journal")."\""."\n";
    }
    if ($current_entry->hasField("booktitle")) {
        echo "  type: conference-paper\n";
        echo "  conference: \"".$current_entry->getField("booktitle")."\""."\n";
    }
    echo "  authors:"."\n";
    foreach ($current_entry->getFormattedAuthorsArray() as $author) {
        $split = splitFullName($author);
        echo "    - family-names: ".$split[1]."\n";
        echo "      given-names: ".$split[0]."\n";
    }
}

bibtexbrowser_cff($argv);

?>
