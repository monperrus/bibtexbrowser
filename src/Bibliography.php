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

    private $config = array();

    public function __construct($userConfig = array())
    {
        // (PHP 5 >= 5.3.0, PHP 7)
        $this->config = array_replace($this->defaultConfig, $userConfig);

        if (!isset($_GET['bib'])) {
            $_GET['bib'] = $this->config['bib'];
        }
    }

    public function print()
    {
        require_once "bibtexbrowser.php";
    }
}
