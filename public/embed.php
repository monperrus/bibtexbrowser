<?PHP

print <<<HTML
<!doctype html>
<html>
<head></head>
<body>
HTML;

$_GET['library']=1;
require_once('../src/bibtexbrowser.php');
$db = new BibDataBase();
$db->load('bibacid-utf8.bib');
$query = array('year'=>'1997');
$entries=$db->multisearch($query);
uasort($entries, 'compare_bib_entries');
foreach ($entries as $bibentry) {
    print $bibentry->toHTML()."<br/>";
}

print <<<HTML
</body>
</html>
HTML;
exit;
