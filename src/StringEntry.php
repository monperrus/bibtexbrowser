<?PHP

namespace Monperrus\BibtexBrowser;

/** represents @string{k=v} */
class StringEntry
{
    public function __construct($key, $value, $filename)
    {
        $this->name=$key;
        $this->value=$value;
        $this->filename=$filename;
    }

    public function toString()
    {
        return '@string{'.$this->name.'={'.$this->value.'}}';
    }
} // end class StringEntry
