<?PHP

namespace Monperrus\BibtexBrowser;

/** builds arrays of BibEntry objects from a bibtex file.
    usage:
    <pre>
    $empty_array = array();
    $db = new BibDBBuilder(); // see also factory method createBibDBBuilder
    $db->build('bibacid-utf8.bib'); // parses bib file
    print_r($db->builtdb);// an associated array key -> BibEntry objects
    print_r($db->stringdb);// an associated array key -> strings representing @string
    </pre>
    notes:
    method build can be used several times, bibtex entries are accumulated in the builder
*/
class BibDBBuilder extends ParserDelegate {

    /** A hashtable from keys to bib entries (BibEntry). */
    var $builtdb  = array();

    /** A hashtable of constant strings */
    var $stringdb = array();

    var $filename;

    var $currentEntry;

    function build($bibfilename, $handle = NULL) {

        $this->filename = $bibfilename;
        if ($handle == NULL) {
            $handle = fopen($bibfilename, "r");
        }

        if (!$handle) die ('cannot open '.$bibfilename);

        $parser = new StateBasedBibtexParser($this);
        $parser->parse($handle);
        fclose($handle);
        //print_r(array_keys($this->builtdb));
        //print_r($this->builtdb);
    }


    function getBuiltDb() {
        //print_r($this->builtdb);
        return $this->builtdb;
    }

    function beginFile() {
    }

    function endFile() {
        // resolving crossrefs
        // we are careful with PHP 4 semantics
        foreach (array_keys($this->builtdb) as $key) {
            $bib = $this->builtdb[$key];
            if ($bib->hasField('crossref')) {
                if (isset($this->builtdb[$bib->getField('crossref')])) {
                    $crossrefEntry = $this->builtdb[$bib->getField('crossref')];
                    $bib->crossref = $crossrefEntry;
                    foreach($crossrefEntry->getFields() as $k => $v) {
                        // copying the fields of the cross ref
                        // only if they don't exist yet
                        if (!$bib->hasField($k)) {
                            $bib->setField($k,$v);
                        }
                    }
                }
            }
        }
        //print_r($this->builtdb);
    }

    function setEntryField($fieldkey,$entryvalue) {
        $fieldkey=trim($fieldkey);
        // support for Bibtex concatenation
        // see http://newton.ex.ac.uk/tex/pack/bibtex/btxdoc/node3.html
        // (?<! is a negative look-behind assertion, see http://www.php.net/manual/en/regexp.reference.assertions.php
        $entryvalue_array=preg_split('/(?<!\\\\)#/', $entryvalue);
        foreach ($entryvalue_array as $k=>$v) {
            // spaces are allowed when using # and they are not taken into account
            // however # is not itself replaced by a space
            // warning: @strings are not case sensitive
            // see http://newton.ex.ac.uk/tex/pack/bibtex/btxdoc/node3.html
            $stringKey=strtolower(trim($v));
            if (isset($this->stringdb[$stringKey]))
            {
                // this field will be formated later by xtrim and latex2html
                $entryvalue_array[$k]=$this->stringdb[$stringKey]->value;

                // we keep a trace of this replacement
                // so as to produce correct bibtex snippets
                $this->currentEntry->constants[$stringKey]=$this->stringdb[$stringKey]->value;
            }
        }
        $entryvalue=implode('',$entryvalue_array);

        $this->currentEntry->setField($fieldkey,$entryvalue);
    }

    function setEntryType($entrytype) {
        $this->currentEntry->setType($entrytype);
    }

    function setEntryKey($entrykey) {
        //echo "new entry:".$entrykey."\n";
        $this->currentEntry->setKey($entrykey);
    }

    function beginEntry() {
        $this->currentEntry = createBibEntry();
        $this->currentEntry->setFile($this->filename);
    }

    function endEntry($entrysource) {

        // we add a timestamp
        $this->currentEntry->timestamp();

        // we add a key if there is no key
        if (!$this->currentEntry->hasField(Q_KEY) && $this->currentEntry->getType()!='string') {
            $this->currentEntry->setField(Q_KEY,md5($entrysource));
        }

        // we set the fulltext
        $this->currentEntry->text = $entrysource;

        // we format the author names in a special field
        // to enable search
        if ($this->currentEntry->hasField('author')) {
            $this->currentEntry->setField(Q_INNER_AUTHOR,$this->currentEntry->getFormattedAuthorsString());

            foreach($this->currentEntry->getCanonicalAuthors() as $author) {
                $homepage_key = $this->currentEntry->getHomePageKey($author);
                if (isset($this->stringdb[$homepage_key])) {
                    $this->currentEntry->homepages[$homepage_key] = $this->stringdb[$homepage_key]->value;
                }
            }
        }

        // ignoring jabref comments
        if (($this->currentEntry->getType()=='comment')) {
            /* do nothing for jabref comments */
        }

        // we add it to the string database
        else if ($this->currentEntry->getType()=='string') {
            foreach($this->currentEntry->fields as $k => $v) {
                $k!=Q_INNER_TYPE and $this->stringdb[$k] = new StringEntry($k,$v,$this->filename);
            }
        }

        // we add it to the database
        else {
            $this->builtdb[$this->currentEntry->getKey()] = $this->currentEntry;
        }
    }

} // end class BibDBBuilder
