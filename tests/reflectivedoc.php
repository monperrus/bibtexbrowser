<?php /*  
Provides ways to manipulate and print API documentation of PHP programs

author: Martin Monperrus

*/


global $diffs;
$diffs = array();

function get_functions_in($phpfile) {
  return load($phpfile)['functions'];
}

function load($phpfile) {
  global $diffs;

  if (!isset($diffs[$phpfile])) { 
    $beforef=get_defined_functions()['user'];
    $beforec=get_declared_classes();
  } else {
    return $diffs[$phpfile];
  } 

  // prevent problems if they is one exit in the included script
  // register_shutdown_function('printNewFunctions',$beforef);

  // we don't want the output
  ob_start();
  require($phpfile);
  // this does not work because the include is not executed
  //eval('return true; include("'.$_GET['file'].'");');
  ob_end_clean();

  $afterf=get_defined_functions()['user'];
  $afterc=get_declared_classes();

  $new_functions = array();
  foreach($afterf as $k) {
    if (!in_array($k,$beforef)) {
      $new_functions[] = $k;
    }
  }
  $diffs[$phpfile]['functions'] = $new_functions;

  $new_classes = array();
  foreach($afterc as $k) {
    if (!in_array($k,$beforec)) {
      $new_classes[] = $k;
    }
  }
  $diffs[$phpfile]['classes'] = $new_classes;

  return $diffs[$phpfile];
}

/** returns a list of new classes */
function get_classes_in($phpfile) {
  return load($phpfile)['classes'];
}

/** print only documented classes and methods */
function printDocumentedClasses($file) {
  $res = '';
  foreach (get_classes_in($file) as $klass) {
    $res .= printAPIDocClass($klass, true);
  }
  return $res;
}


?><?php /* pp4php: including ./gakowiki-syntax.php (in reflectivedoc.php:73) */ ?><?php /** 

A wiki parser implemented in a neat way.

Author: Martin Monperrus
Public domain

Usage: echo gk_wiki2html("foo **bar*")

*/

/** returns an HTML version of the wiki text of $text, according to the syntax of [[http://www.monperrus.net/martin/gakowiki-syntax]] */
function gk_wiki2html($text) {
  global $parser;
  if ($parser==null) $parser = create_wiki_parser();
  return  $parser->parse($text);
}


?><?php /* pp4php: including ./gakoparser.php (in ./gakowiki-syntax.php:20) */ ?><?php /* Gakoparser

Gakoparser parses a family of markup language

Sunday, October 30 2011
impossible to handle both ===== sdf ===== and ==== sdfsdf ====
handling of space different

handling of >\n different

 */

if (defined('gakoparser.php')) return;
define('gakoparser.php',true);

class Delimiter{}

class GakoParserException extends Exception {}

/** provides a parametrizable parser. The main method is "parse" */
class GakoParser {
  
  /** is the PHP5 constructor. */
  function __construct() {
    // an array of Delimiter objects
    $this->start_delimiters = array();
    $this->end_delimiters = array();
    $this->nonesting = array();
    $this->delegate = array();
  }
  
  /** specifies that $tag can not contain nested tags */
  function noNesting($tag) {
    $this->nonesting[] = $tag;
    return $this;
  }

  function setDelegate($obj) {
    $this->delegate = $obj;
    return $this;
  }

  function addDelim($name, $delim) {
    return $this->addDelimX($name, $delim, $delim);
  }

  /** adds a trigger $tag -> execution of function $name */
  function addTag($name, $tag) {
    $start = substr($tag,0,strlen($tag)-1);
    $end = substr($tag,strlen($tag)-1);
    return $this->addDelimX($name, $start, $end);
  }

  /** setDelegate must be called before this method */
  function addDelimX($name, $start, $end) {
    if (!method_exists($this->delegate,$name)) {throw new GakoParserException('no method '.$method.' in delegate!');}

    $x = new Delimiter();
    $x->value = $start;
//     $x->type = 'start';
    $x->action = $name;
    
    $y = new Delimiter();
    $y->value = $end;
//     $y->type = 'end';
    $y->action = $name;
    
    // crossing links
    $y->startDelim = $x;
    $x->endDelim = $y;
    
    if (in_array($start, $this->get_start_delimiters())) {
      throw new GakoParserException("delimiter ".$start." already exists");
    }
    
    $this->start_delimiters[$start] = $x;
    $this->end_delimiters[$end] = $y;
    return $this;
  }

  function get_start_delimiters() {
    return array_keys($this->start_delimiters);
  }
  
  function get_end_delimiters() {
    return array_keys($this->end_delimiters);
  }
  
  function array_preg_quote($x) {
    $result = array();
    foreach($x as $k) { $result[] = preg_quote($k, '/'); }
    return $result;
  }
  
  function addDelimXML($name, $delim) {
    return $this->addDelimX($name, '<'.$delim.'>', '</'.$delim.'>');
  }

  function getCandidateDelimiters_man($str) {
    $result = array();
    
    for ( $i=0; $i < strlen( $str ); $i++) { 
      foreach(array_merge($this->get_start_delimiters(),$this->get_end_delimiters()) as $v) {
        $new_fragment = substr($str, $i, strlen($v));
        if ($new_fragment === $v) {
          $x = array();
          $x[0] = array();
          $x[0][0] = $v;
          $x[0][1] = $i;
          $result[] = $x;
        }
      }
    }
    //print_r($result);
    return $result;
  }

  function getCandidateDelimiters($str) {
    //return $this->getCandidateDelimiters_man($str);
    return $this->getCandidateDelimiters_preg($str);
  }

  function getCandidateDelimiters_preg($str) {
    // getting the start delimiters
    preg_match_all('/'.implode('|',array_merge($this->array_preg_quote($this->get_start_delimiters()),$this->array_preg_quote($this->get_end_delimiters()))).'/', $str, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE );
    return $matches;
  }
  
  function parse($str) {
//     echo "---------parse $str\n";
    $method = '__pre';
    if (method_exists($this->delegate,$method)) {
      $str = $this->delegate->$method($str);
    }
    
    $matches = $this->getCandidateDelimiters($str); 

//     print_r($matches);
    
    //echo 'parse '.$str.'<br>';
    // the stack contains the current markup environment
    $init = new Delimiter(); $init->action = '__init__'; $init->value = '__init__'; $init->endDelim = $init;
    $stack = array($init);
    $strings = array(1=>'');
    $last = 0;
    
//     if (count($matches) == 0) return $str;
    
//     print_r($matches);
    
//     $strings[1] = substr($str, 0, $matches[0][0][1]);
    
    // for each tags found
    foreach ( $matches as $_ ) { 
    
        list($v, $pos) = $_[0];
        
//         echo "studying ".$v." at ".$pos."  (last=".$last.")\n";

        // if $_ is a start delimiter,
        // we'll get '' in $k
        //echo $stack[0];
        if (isset($this->end_delimiters[$v]) 
//              && $stack[0] != '__init__'
             && $stack[0]->endDelim->value == $v  
             && $pos>=$last
            ) {
          $k = $stack[0]->endDelim->action;
        
          $strings[count($stack)] .= substr($str,$last, $pos-$last);

//           echo "popping ".$k." at ".$pos." with ".$v." (last=".$last.", stack=".count($stack).")\n";

          $closedtag = array_shift($stack);

          $method = $k;
//                  echo $method." ".$value. "\n";
          $value = $strings[count($stack)+1];
          $transformed = $this->delegate->$method($value);
//             echo $transformed ."---".$value." \n";              
          $strings[count($stack)] .= $transformed;
            
          $strings[count($stack)+1] = '';            
          $last = $pos+strlen($v);

        }
      
      // certain tags do not support nesting
      else if (!in_array($stack[0]->action, $this->nonesting)
                && in_array($v, $this->get_start_delimiters()) && $pos >= $last) 
      {      
        $delim = $this->start_delimiters[$v];
        $k = $delim->action;
        
//         echo "putting ".$k." at ".$pos." with ".$last."<br/>\n";
            
        if ($pos>$last) {
          $strings[count($stack)] .= substr($str, $last, $pos-$last);
        } 
        array_unshift($stack, $delim);
        // init the new stack
        $strings[count($stack)] = ''; 
        $last = $pos+strlen($v);
//             print_r($strings);
                       
      } else {
        //die('oops');
      }
      
    } // end foreach
    
    
    if ($stack[0]->action!="__init__") { 
      //print_r($strings); 
      throw new GakoParserException("parsing error: ending with ".$stack[0]->action. " ('".$strings[1]."')"); 
    }
    
    $result = $strings[count($stack)];
    
    // adding the rest
    if ($last<strlen($str)) $result .= substr($str,$last,strlen($str)-$last);
    
    $method = '__post';
    if (method_exists($this->delegate,$method)) {
      $result = $this->delegate->$method($result);
    } 
    //echo "$$$$$ ".$result."\n";
    
    return $result;
    
  }
}


?><?php 

/** Defines a set of functions for interpreting a markup language in HTML.
Maintains a state to build the table of contents.
Delegate class for Gakoparser.
<pre>

// high level
$parser = create_wiki_parser();
$html_text = $parser->parse($wiki_text);

// low level
  $parser = new Gakoparser();
  $parser->setDelegate(new MarkupInterpreter());
  $parser->addDelim('bold','**');
  echo $parser->parse('hello **world**');

</pre>
 */


class GakowikiMarkupToHTMLTranslator {

  var $toc = array();

  var $references = array();

  /** replaces all line breaks by "__newline__" that are meant to replaced back by a call to __post() */
  function __pre($str) {
    $result = $str;

    // we often use nelines to have pretty HTML code
    // such as in tables
    // however, they are no "real" newlines to be transformed in <br/>
    $result = preg_replace("/>\s*(\n|\r\n)/",'>__newline__',$result);
    return $result;
  }

  function bib($str) {
    $this->references[] = $str;
    return '<a name="ref'.count($this->references).'">['.count($this->references).']</a> '.$str;
  }

  function cite($str) {
    return "@@@".$str."@@@";
  }

  function escape_newline($str) {
    return preg_replace("/(\n|\r\n)/","__newline__",$str);
  }

  function toc($str) {
    return '+++TOC+++';
  }

  function __post($str) {
    $result = $str;
    $result = preg_replace("/(\n|\r\n)/","<br/>\n",$result);

    // workaround to support the semantics change in pre mode
    // and the semantics of embedded HTML
    $result = preg_replace("/__newline__/","\n",$result);// must be at the end

    // cleaning the additional <br>
    // this is really nice
    $result = preg_replace("/(<\/h.>)<br\/>/i","\\1 ",$result);

    // adding the table of contents
    $result = str_replace($this->toc(''),implode('<br/>',$this->toc),$result);

    // adding the references
    $citeregexp = '/@@@(.*?)@@@/';
    if (preg_match_all($citeregexp,$result,$matches)) {
      foreach($matches[1] as $m) {
          $theref = '';
          foreach ($this->references as $k => $ref) {
            if (preg_match('/'.preg_quote($m).'/i', $ref)) {
//echo $m.' '.$ref;
              // if we have already a match it is not deterministic
              if ($theref!='') $result = "undeterministic citation: ".$m;
              $theref = $ref;
              $result = preg_replace('/@@@'.preg_quote($m).'@@@/i', '<a href="#ref'.($k+1).'">['.($k+1).']</a>', $result);
            }
          }
      }
    }

    return $result;
  }

  /** adds <pre> tags and prevents newline to be replaced by <br/> by __post */
  function pre($str) {
    return '<pre>'.$this->escape_newline($str).'</pre>';
  }

  /** prevents newline to be replaced by <br/> by __post */
  function unwrap($str) {
    return $this->escape_newline($str);
  }

  /** adds <b> tags */
  function bold($str) {
    return '<b>'.$str.'</b>';
  }
  
  /** adds <i> tags */
  function italic($str) {
    return '<i>'.$str.'</i>';
  }

  function table($str) {
    $result = '';
    foreach(preg_split('/\n/',$str) as $line) {
      if (strlen(trim($line))>0) {
      $result .= '<tr>';
      foreach(preg_split('/&&/',$line) as $field) {
        $result .= '<td>'.$field.'</td>';

      }
      $result .= '</tr>';
      }
   }

    return '<table border="1">'.$result.'</table>';
  }


  function __create_anchor($m) {
    return preg_replace("/[^a-zA-Z]/","",$m);
  }
  function h2($str) {
    $tag = $this->__create_anchor($str);   
    $this->toc[] = "<a href=\"#".$tag."\">".$str."</a>";
    return '<a name="'.$tag.'"></a>'.'<h2>'.$str."</h2>";
  }

  function h3($str) {
    $tag = $this->__create_anchor($str);
    $this->toc[] = "&nbsp;&nbsp;<a href=\"#".$tag."\">".$str."</a>";
    return '<a name="'.$tag.'"></a>'.'<h3>'.$str."</h3>";
  }

  function monotype($str) {
    return '<code>'.str_replace('<','&lt;',$str).'</code>';
  }

  function link($str) {
    
    if (preg_match('/(.*)\|(.*)/',$str, $matches)) {
      $rawurl =  $matches[1];
      $text =  $matches[2];
    } else {$rawurl=$str;$text=$str;}

    $url=$rawurl;

    if (!preg_match("/(#|^http|^mailto)/",$rawurl)) {
      if (function_exists('logical2url')) {
        $url=logical2url($rawurl);
      } else {
        $url=$rawurl;

      }
    } 

    return '<a href="'.trim($url).'">'.trim($text).'</a>';
  }

  function phpcode($str) {
      ob_start();
      eval($str);
      return $this->escape_newline(ob_get_clean());
   }

  function phpcode2($str) {
      return gk_wiki2html($this->phpcode($str));
   }

  function a($str) {
      return '<a'.$str.'</a>';
   }

  function script($str) {
      return '<script'.$this->escape_newline($str).'</script>';
   }

  function img($str) {
      return '<img src="'.$this->escape_newline($str).'"/>';
   }

  function img2($str) {
      return '<img'.$str.'/>';
   }


  function html($str) {
      return '<'.$str.'>';
   }

  function iframe($str) {
      return '<iframe'.$str.'</iframe>';
   }


  function comment($str) {
      return ''; // comments are discarded
  }

  function silent($str) {
      return '';
  }

} // end class


/** returns a parser object to parse wiki syntax.

The returned object may be used with the parse method:
<pre>
$parser = create_wiki_parser();
$html_text = $parser->parse($wiki_text);
</pre>
*/
function create_wiki_parser() {
  $x = new Gakoparser();
  return $x->setDelegate(new GakowikiMarkupToHTMLTranslator())
      ->addDelimX('comment','<!--','-->')->noNesting('comment')

     ->addDelim('bold','**')
     ->addDelim('italic','//')//->noNesting('italic')
     ->addDelim('bold',"'''")
     ->addDelim('monotype',"''")->noNesting('monotype')
     ->addDelim('h2',"=====") // the longest comes before, it has the highest priority
     ->addDelim('h3',"====")
     ->addDelim('table',"|||")
     ->addDelimXML('pre','pre')->noNesting('pre') // this is essential otherwise you have infinite loops      
     ->addDelimX('pre','{{{','}}}')->noNesting('pre2') // à la Google Code wiki syntax      
     ->addDelimX('link','[[',']]')->noNesting('link')
     ->addDelimX('phpcode2','<?php2wiki','?>')->noNesting('phpcode2')
     ->addDelimX('phpcode','<?php','?>') ->noNesting('phpcode')
     ->addDelimX('img2','<img','/>')->noNesting('img2')
     ->addDelim('img','%%')->noNesting('img')// huge bug when I did this for 1000 index :(
     ->addDelimX('script','<script','</script>') ->noNesting('script')
     ->addDelimX('unwrap','^^','^^')
     ->addTag('toc','+++TOC+++')

     ->addDelimX('a','<a','</a>')->noNesting('a') // important to support cross tags

     ->addDelimX('iframe','<iframe','</iframe>')->noNesting('iframe')

     // Dec 30 2012
     ->addDelimX('bib','\bib{','}')
     ->addDelimX('cite','\cite{','}')

      // this one is really not good
      //->addDelimX('html','<','>')->noNesting('html') // a link often contains // (e.g. http:// which clash with italics
      ;
} // end create_wiki_parser

function gakowiki__doc() {
?>
<a href="http://www.monperrus.net/martin/gakowiki-syntax">Syntax specification</a>:<br/>
**<b>this is bold</b>**<br/>
//<i>this is italic</i>//<br/>
''<code>this is code</code>''<br/>
[[link to a page on this wiki]],[[http://www.google.fr|link to google]]<br/>
<h2>=====Section=====</h2>
<h3>====Subsection====</h3>
<?php
} // end function gakowiki__doc()





// end file
?><?php 

function printGk($comment) {
  try {
    $result = htmlentities($comment);
    $result = str_replace('&lt;pre&gt;', '<pre>', $result);
    $result = str_replace('&lt;/pre&gt;', '</pre>', $result);
    // removes lines prefixed "*" often used to have nice API comments
    $result = preg_replace('/^.*?\*/m', '', $result);
    return  '<pre>'.$result.'</pre>';
    //return  gk_wiki2html($comment);
  } catch (GakoParserException $e) {return '<pre>'.$comment.'</pre>';}
}

/** outputs the API doc of the function called $fname */
function printDocFuncName($fname, $prefix='') {
  $funcdeclared = new ReflectionFunction($fname);
  return printDocFuncObj($funcdeclared, $prefix);
}

function getComment($funcdeclared) {
  $comment = trim(substr($funcdeclared->getDocComment(),3,-2));
  return $comment;
} 
function printDocFuncObj($funcdeclared, $prefix='', $documented = true) {
  $comment = trim(substr($funcdeclared->getDocComment(),3,-2));  
  if ($documented && strlen($comment)<1) { return ''; } 
  $res = "";
  $res .= '<div>';
  $res .=  '<b>'.$prefix.$funcdeclared->getName().'</b>';
  $res .=  '<i>('.implode(', ',array_map('f',$funcdeclared->getParameters())).')</i> ';
  $res .=  printGk($comment);
  $res .=  '</div>';
  return $res;
}

// Anonymous functions are available only since PHP 5.3.0
function f($x){return '$'.$x->getName();}


/** this is printNewFunctions
 the main limitation is that this does not fully work if there is an exit/die in the included script
*/
function printNewFunctions($beforef) {
  $afterf=get_defined_functions();


  foreach($afterf['user'] as $fname ) {
      $funcdeclared = new ReflectionFunction($fname);
      if (!in_array($fname,$beforef['user']) && $funcdeclared->getFileName()==realpath($_GET['file'])) {
          printDocFunc($funcdeclared);
      }
  }
}

/** outputs an HTML representation of the API doc of the class called $cname */
function printAPIDocClass($cname, $documented = true) {
  $res = '';
  $cdeclared = new ReflectionClass($cname);
  //if ($cdeclared->getFileName()!=realpath($_GET['file'])) {continue;}
  $res .=  '<b>'.$cdeclared->getName().'</b> ';
  $comment = trim(substr($cdeclared->getDocComment(),3,-2));
  if ($documented && strlen($comment)<1) { return '';}
  $res .=  printGk($comment);
  foreach($cdeclared->getMethods() as $method) {
    $f = printDocFuncObj($method, "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"/*,$cname.'.'*/, true);
    if (strlen($f)>0) {
      $res .= $f; 
    }
  }
  return "<div>".$res."</div><hr/>";
}

function getCodeSnippetsInClass($cname) {
  $res = array();
  $cdeclared = new ReflectionClass($cname);
  $res[] = _getCodeSnippet($cdeclared);
  foreach($cdeclared->getMethods() as $method) {
    $res[] = _getCodeSnippet($method);
  }
  return $res;
}

/** returns the  snippet of a function */
function getCodeSnippet($function_name) {
  $funcdeclared = new ReflectionFunction($function_name);
  return _getCodeSnippet($funcdeclared);
}


function _getCodeSnippet($obj) {
  $comment = getComment($obj);
  if (preg_match('/<pre>(.*)<\/pre>/is', $comment, $matches)) {
      return $matches[1];
  }  
  return "";  
}


function getAllSnippetsInFile($file) {
  $res = array();
  foreach (get_functions_in($file) as $f) {
    $x=getCodeSnippet($f);
    if (strlen($x)>0) $res[] = $x;
  }

  foreach (get_classes_in($file) as $klass) {
    foreach (getCodeSnippetsInClass($klass) as $x) {
      if (strlen($x)>0)  $res[] = $x;
    }
  }
  return $res;
}




?>
