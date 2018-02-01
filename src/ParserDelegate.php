<?PHP

namespace Monperrus\BibtexBrowser;

/** a default empty implementation of a delegate for StateBasedBibtexParser */
class ParserDelegate {

    function beginFile() {}

    function endFile() {}

    function setEntryField($finalkey,$entryvalue) {}

    function setEntryType($entrytype) {}

    function setEntryKey($entrykey) {}

    function beginEntry() {}

    function endEntry($entrysource) {}

    /** called for each sub parts of type {part} of a field value
     * for now, only CURLYTOP and CURLYONE events
     */
    function entryValuePart($key, $value, $type) {}

} // end class ParserDelegate
