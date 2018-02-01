<?PHP

namespace Monperrus\BibtexBrowser;

/** is a possible delegate for StateBasedBibParser.
    usage:
    see snippet of [[#StateBasedBibParser]]
*/
class XMLPrettyPrinter extends ParserDelegate
{
    public function beginFile() {
        header('Content-type: text/xml;');
        print '<?xml version="1.0" encoding="'.OUTPUT_ENCODING.'"?>';
        print '<bibfile>';
    }

    public function endFile() {
        print '</bibfile>';
    }

    public function setEntryField($finalkey,$entryvalue) {
        print "<data>\n<key>".$finalkey."</key>\n<value>".$entryvalue."</value>\n</data>\n";
    }

    public function setEntryType($entrytype) {
        print '<type>'.$entrytype.'</type>';
    }

    public function setEntryKey($entrykey) {
        print '<keyonly>'.$entrykey.'</keyonly>';
    }

    public function beginEntry() {
        print "<entry>\n";
    }

    public function endEntry($entrysource) {
        print "</entry>\n";
    }
} // end class XMLPrettyPrinter
