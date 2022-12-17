<?php
// create CITATION.cff file for GitHub from a bibtex file
// reference documentation: https://docs.github.com/en/repositories/managing-your-repositorys-settings-and-features/customizing-your-repository/about-citation-files#about-citation-files
// script part of https://github.com/monperrus/bibtexbrowser/
//
// Usage
// $ php bibtex-to-cff.php test_cli.bib --id classical
//     -> this creates a file CITATION.cff for @xx{classical,

$_GET['library']=1;
require('bibtexbrowser.php');

function bibtexbrowser_cff($arguments) {
    $db = new BibDataBase();
    $db->load($arguments[1]);
    $current_entry=NULL;
    for ($i=2;$i<count($arguments); $i++) {
        $arg=$arguments[$i];
        if ($arg=='--id') {
            $current_entry = $db->getEntryByKey($arguments[$i+1]);
        }
    }
    if (is_null($current_entry)) {
        echo "Did not find entry specified in the --id argument.";
        return;
    }
    echo $current_entry->toCFF();
}

bibtexbrowser_cff($argv);

?>
