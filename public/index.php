<?PHP

require_once "../vendor/autoload.php";

use Monperrus\BibtexBrowser\Bibliography;


$config = array("bib" => "bibacid-utf8.bib",
                "all" => 1,
                "author" => "",
                "academic" => 1
);

$browser = new Monperrus\BibtexBrowser\Bibliography($config);
$browser->print();
