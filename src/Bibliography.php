<?PHP

namespace Monperrus\BibtexBrowser;

use Monperrus\BibtexBrowser\Definitions;

class Bibliography
{
    private $defaultConfig = array("bib" => null,
                                   "all" => null,
                                   "author" => null,
                                   "library" => null,
                                   "academic" => null);

    public function __construct($userConfig = array())
    {
        // (PHP 5 >= 5.3.0, PHP 7)
        // $userConfig precedent over defaultConfig; $_GET over $userConfig
        $_GET = array_replace($this->defaultConfig, $userConfig, $_GET);
    }

    public function print()
    {
        require_once "bibtexbrowser.php";
    }
}
