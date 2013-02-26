<?php

// Add the 'thumbnail' option for rendering
@define('USEBIBTHUMBNAIL',0);
@define('BIBTHUMBNAIL','thumbnail');

function MGBibliographyStyle(&$bibentry) {
  $title = $bibentry->getTitle();
  $type = $bibentry->getType();

  // later on, all values of $entry will be joined by a comma
  $entry=array();


  // thumbnail
  if (USEBIBTHUMBNAIL && $bibentry->hasField(BIBTHUMBNAIL)) {
    $thumb = '<img class="thumbnail" src="'.$bibentry->getField(BIBTHUMBNAIL).'" alt=""/>';}
  else $thumb = '';

  // title
  // usually in bold: .bibtitle { font-weight:bold; }
  $title = '<span class="bibtitle">'.$title.'</span><br/>'."\n";
  if ($bibentry->hasField('url')) $title = '<a'.(BIBTEXBROWSER_BIB_IN_NEW_WINDOW?' target="_blank" ':'').' href="'.$bibentry->getField("url").'">'.$title.'</a>';
  

  // author
  if ($bibentry->hasField('author')) {
    $coreInfo = $title . '<span class="bibauthor">'.$bibentry->getFormattedAuthorsImproved().'</span>';}
  else $coreInfo = $title;

  // core info usually contains title + author
  $entry[] = $thumb.$coreInfo;

  // now the book title
  $booktitle = '';
  if ($type=="inproceedings") {
      $booktitle = 'In '.$bibentry->getField(BOOKTITLE); }
  if ($type=="incollection") {
      $booktitle = 'Chapter in '.$bibentry->getField(BOOKTITLE);}
  if ($type=="inbook") {
      $booktitle = 'Chapter in '.$bibentry->getField('chapter');}
  if ($type=="article") {
      $booktitle = 'In '.$bibentry->getField("journal");}

  //// we may add the editor names to the booktitle
  $editor='';
  if ($bibentry->hasField(EDITOR)) {
    $editor = $bibentry->getFormattedEditors();
  }
  if ($editor!='') $booktitle .=' ('.$editor.')';
  // end editor section

  // is the booktitle available
  if ($booktitle!='') {
    $entry[] = '<br/><span class="bibbooktitle">'.$booktitle.'</span>';
  }


  $publisher='';
  if ($type=="phdthesis") {
      $publisher = 'PhD thesis, '.$bibentry->getField(SCHOOL);
  }
  if ($type=="mastersthesis") {
      $publisher = 'Master\'s thesis, '.$bibentry->getField(SCHOOL);
  }
  if ($type=="bachelorsthesis") {
      $publisher = 'Bachelor\'s thesis, '.$bibentry->getField(SCHOOL);
  }
  if ($type=="techreport") {
      $publisher = 'Technical report, '.$bibentry->getField("institution");
  }
  
  if ($type=="misc") {
      $publisher = $bibentry->getField('howpublished');
  }

  if ($bibentry->hasField("publisher")) {
    $publisher = $bibentry->getField("publisher");
  }

  if ($publisher!='') $entry[] = '<span class="bibpublisher">'.$publisher.'</span>';


  if ($bibentry->hasField('volume')) $entry[] =  "volume ".$bibentry->getField("volume");


  if ($bibentry->hasField(YEAR)) $entry[] = $bibentry->getYear();

  $result = implode(", ",$entry).'.';

  // some comments (e.g. acceptance rate)?
  if ($bibentry->hasField('comment')) {
      $result .=  " (".$bibentry->getField("comment").")";
  }
  if ($bibentry->hasField('note')) {
      $result .=  " (".$bibentry->getField("note").")";
  }

  // add the Coin URL
  //$result .=  "\n".$bibentry->toCoins();
  $result .=  "<br/>\n";

  // we add biburl and title to be able to retrieve this important information
  // using Xpath expressions on the XHTML source
  $result .= $bibentry->getBibLink();
  // returns an empty string if no pdf present
  $result .= $bibentry->getLink('pdf');
  // returns an empty string if no url present
  $result .= $bibentry->getLink('url');
  // returns an empty string if no slides present
  $result .= $bibentry->getLink('slides');
  // returns an empty string if no poster present
  $result .= $bibentry->getLink('poster');
  // Google Scholar ID. empty string if no gsid present
  $result .= $bibentry->getGSLink();
  // returns an empty string if no doi present
  $result .= $bibentry->getDoiLink();

  $result .= '<hr style="visibility: hidden; height:0; clear:both;"/>';

  return $result;
}

/** Class to display a bibliography of a page. */
class BibliographyDisplay  {
  /** the bib entries to display. */
  var $result;

  /** the content strategy (cf. pattern strategy) */
  var $contentStrategy;

  /** the query to reinject in links to different pages */
  var $filter;

  /** Creates an instance with the given entries and header. */
  function BibliographyDisplay(&$result, $filter) {
    $this->result = $result;
    $this->filter = $filter;
    // requesting a different page of the result view?
    $this->setTitle();
    $this->contentStrategy = new BibliographyContentStrategy();
  }

  /** sets the $this->title of BibtexBrowserDisplay based on the $filter */
  function setTitle() {
    $this->title = query2title($this->filter);
  }

  /** overrides */
  function  formatedHeader() { return '<div class="rheader">'.$this->title.' '.createRSSLink($this->filter).'</div>';}

  /** overrides */
  function getURL() { return '?'.createQueryString($this->filter);}

  /** overrides */
  function getRSS() { return BIBTEXBROWSER_URL.'?'.createQueryString($this->filter).'&amp;rss';}

  /** Displays the entries preceded with the header. */
  function display() {
    // print error message if no entry.
    if (empty($this->result)) {
      echo "No references.\n";
      return;
    }
    $this->contentStrategy->display($this);
    echo $this->poweredby();
    if (BIBTEXBROWSER_USE_PROGRESSIVE_ENHANCEMENT) {
      $this->javascript();
    }
  }


  function poweredby() {
    $poweredby = "\n".'<div style="text-align:right;font-size: xx-small;opacity: 0.6;" class="poweredby">';
    $poweredby .= '<!-- If you like bibtexbrowser, thanks to keep the link :-) -->';
    $poweredby .= 'Powered by <a href="http://www.monperrus.net/martin/bibtexbrowser/">bibtexbrowser</a><!--v20111211-->';
    $poweredby .= '</div>'."\n";
    return $poweredby;
   }

  /** Adds a touch of AJAX in bibtexbrowser to display bibtex entries inline.
   * It uses the HIJAX design pattern: the Javascript code fetches the normal bibtex HTML page
   * and extracts the bibtex.
   * In other terms, URLs and content are left perfectly optimized for crawlers 
   * Note how beautiful is this piece of code thanks to JQuery.
   */
  function javascript() {
  // we use jquery with the official content delivery URLs
  // Microsoft and Google also provide jquery with their content delivery networks
?><script type="text/javascript" src="http://code.jquery.com/jquery-1.5.1.min.js"></script> 
<script type="text/javascript" ><!--
// Javascript progressive enhancement for bibtexbrowser
$('a.biburl').each(function() { // for each url "[bib]"
  var biburl = $(this);
  if (biburl.attr('bibtexbrowser') === undefined)
  {
  biburl.click(function(ev) { // we change the click semantics
    ev.preventDefault(); // no open url
    if (biburl.nextAll('pre').length == 0) { // we don't have yet the bibtex data
      var bibtexEntryUrl = $(this).attr('href');
      $.ajax({url: bibtexEntryUrl,  dataType: 'xml', success: function (data) { // we download it
        var elem = $('<pre class="purebibtex"/>'); // the element containing bibtex entry, creating a new element is required for Chrome and IE
        elem.text($('.purebibtex', data).text()); // both text() are required for IE
        // we add a link so that users clearly see that even with AJAX
        // there is still one URL per paper (which is important for crawlers and metadata)
        elem.append(
           $('<div>%% Bibtex entry URL: <a href="'+bibtexEntryUrl+'">'+bibtexEntryUrl+'</a></div>')
           ).appendTo(biburl.parent());
      }, error: function() {window.location.href = biburl.attr('href');}});
    } else {biburl.nextAll('pre').toggle();}  // we toggle the view    
  });
  biburl.attr('bibtexbrowser','done');
  } // end if biburl.bibtexbrowser;
});


--></script><?php
  }

}

class BibliographyContentStrategy  {

  /** $display: an instance of PagedDisplay */
  function display(&$display) {
    switch(LAYOUT) { /* MG: added switch for different layouts */
      case 'list':
        ?>
        <ol class="result">
        <?php
        break;
      case 'table':
        ?>
        <table class="result">
        <?php
        break;
      case 'deflist':
        ?>
        <div class="result">
        <?php
        break;
    }

    $entries = $display->result;
    $refnum = count($display->result);

    foreach ($entries as $value => $bib) {
          $bib->setAbbrv($value);
          switch(LAYOUT) {
             case 'list': $bib->toLI(); break;
             case 'table': $bib->toTR(); break;
             case 'deflist': $bib->toDD(); break;
          }
    } // end foreach
    
      switch(LAYOUT) {
      case 'list':
        ?>
        </ol>
        <?php
        break;
      case 'table':
        ?>
        </table>
        <?php
        break;
      case 'deflist':
        ?>
        </div>
        <?php
        break;
    }
  } // end function
} // end class



?>

