<?PHP

namespace Monperrus\BibtexBrowser;

// the encoding of your bibtex file
@define('BIBTEX_INPUT_ENCODING','UTF-8');//@define('BIBTEX_INPUT_ENCODING','iso-8859-1');//define('BIBTEX_INPUT_ENCODING','windows-1252');
// the encoding of the HTML output
@define('OUTPUT_ENCODING','UTF-8');

// print a warning if deprecated variable is used
if (defined('ENCODING')) {
    echo 'ENCODING has been replaced by BIBTEX_INPUT_ENCODING and OUTPUT_ENCODING';
}

// number of bib items per page
// we use the same parameter 'num' as Google
@define('PAGE_SIZE',isset($_GET['num'])?(preg_match('/^\d+$/',$_GET['num'])?$_GET['num']:10000):14);

// bibtexbrowser uses a small piece of Javascript to improve the user experience
// see http://en.wikipedia.org/wiki/Progressive_enhancement
// if you don't like it, you can be disable it by adding in bibtexbrowser.local.php
// @define('BIBTEXBROWSER_USE_PROGRESSIVE_ENHANCEMENT',false);
@define('BIBTEXBROWSER_USE_PROGRESSIVE_ENHANCEMENT',true);
@define('BIBLIOGRAPHYSTYLE','DefaultBibliographyStyle');// this is the name of a function
@define('BIBLIOGRAPHYSECTIONS','DefaultBibliographySections');// this is the name of a function
@define('BIBLIOGRAPHYTITLE','DefaultBibliographyTitle');// this is the name of a function

// shall we load MathJax to render math in $…$ in HTML?
@define('BIBTEXBROWSER_RENDER_MATH', true);
@define('MATHJAX_URI', '//cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS_HTML.js');

// the default jquery URI
@define('JQUERY_URI', '//code.jquery.com/jquery-1.5.1.min.js');

// can we load bibtex files on external servers?
@define('BIBTEXBROWSER_LOCAL_BIB_ONLY', true);

// the default view in {SimpleDisplay,AcademicDisplay,RSSDisplay,BibtexDisplay}
@define('BIBTEXBROWSER_DEFAULT_DISPLAY','SimpleDisplay');

// the default template
@define('BIBTEXBROWSER_DEFAULT_TEMPLATE','HTMLTemplate');

// the target frame of menu links
@define('BIBTEXBROWSER_MENU_TARGET','main'); // might be define('BIBTEXBROWSER_MENU_TARGET','_self'); in bibtexbrowser.local.php

@define('ABBRV_TYPE','index');// may be year/x-abbrv/key/none/index/keys-index

// are robots allowed to crawl and index bibtexbrowser generated pages?
@define('BIBTEXBROWSER_ROBOTS_NOINDEX',false);

//the default view in the "main" (right hand side) frame
@define('BIBTEXBROWSER_DEFAULT_FRAME','year=latest'); // year=latest,all and all valid bibtexbrowser queries

// Wrapper to use when we are included by another script
@define('BIBTEXBROWSER_EMBEDDED_WRAPPER', 'NoWrapper');

// Main class to use
@define('BIBTEXBROWSER_MAIN', 'Dispatcher');

// default order functions
// Contract Returns < 0 if str1 is less than str2; > 0 if str1 is greater than str2, and 0 if they are equal.
// can be @define('ORDER_FUNCTION','compare_bib_entry_by_title');
// can be @define('ORDER_FUNCTION','compare_bib_entry_by_bibtex_order');
@define('ORDER_FUNCTION','compare_bib_entry_by_year');
@define('ORDER_FUNCTION_FINE','compare_bib_entry_by_month');

// only displaying the n newest entries
@define('BIBTEXBROWSER_NEWEST',5);

@define('BIBTEXBROWSER_NO_DEFAULT', false);

// BIBTEXBROWSER_LINK_STYLE defines which function to use to display the links of a bibtex entry
@define('BIBTEXBROWSER_LINK_STYLE','bib2links_default'); // can be 'nothing' (a function that does nothing)

// do we add [bibtex] links ?
@define('BIBTEXBROWSER_BIBTEX_LINKS',true);
// do we add [pdf] links ?
@define('BIBTEXBROWSER_PDF_LINKS',true);
// do we add [doi] links ?
@define('BIBTEXBROWSER_DOI_LINKS',true);
// do we add [gsid] links (Google Scholar)?
@define('BIBTEXBROWSER_GSID_LINKS',true);

// should pdf, doi, url, gsid links be opened in a new window?
@define('BIBTEXBROWSER_LINKS_TARGET','_self');// can be _blank (new window), _top (with frames)

// should authors be linked to [none/homepage/resultpage]
// none: nothing
// their homepage if defined as @strings
// their publication lists according to this bibtex
@define('BIBTEXBROWSER_AUTHOR_LINKS','homepage');

// BIBTEXBROWSER_LAYOUT defines the HTML rendering layout of the produced HTML
// may be table/list/ordered_list/definition/none (for <table>, <ol>, <dl>, nothing resp.).
// for list/ordered_list, the abbrevations are not taken into account (see ABBRV_TYPE)
// for ordered_list, the index is given by HTML directly (in increasing order)
@define('BIBTEXBROWSER_LAYOUT','table');

// should the original bibtex be displayed or a reconstructed one with filtering
// values: original/reconstructed
// warning, with reconstructed, the latex markup for accents/diacritics is lost
@define('BIBTEXBROWSER_BIBTEX_VIEW','original');
// a list of fields that will not be shown in the bibtex view if BIBTEXBROWSER_BIBTEX_VIEW=reconstructed
@define('BIBTEXBROWSER_BIBTEX_VIEW_FILTEREDOUT','comment|note|file');

// should Latex macros be executed (e.g. \'e -> é
@define('BIBTEXBROWSER_USE_LATEX2HTML',true);

// Which is the first html <hN> level that should be used in embedded mode?
@define('BIBTEXBROWSER_HTMLHEADINGLEVEL', 2);

@define('BIBTEXBROWSER_ACADEMIC_TOC', false);

@define('BIBTEXBROWSER_DEBUG',false);

// how to print authors names?
// default => as in the bibtex file
// USE_COMMA_AS_NAME_SEPARATOR_IN_OUTPUT = true => "Meyer, Herbert"
// USE_INITIALS_FOR_NAMES = true => "Meyer H"
// USE_FIRST_THEN_LAST => Herbert Meyer
@define('USE_COMMA_AS_NAME_SEPARATOR_IN_OUTPUT',false);// output authors in a comma separated form, e.g. "Meyer, H"?
@define('USE_INITIALS_FOR_NAMES',false); // use only initials for all first names?
@define('USE_FIRST_THEN_LAST',false); // use only initials for all first names?
@define('FORCE_NAMELIST_SEPARATOR', ''); // if non-empty, use this to separate multiple names regardless of USE_COMMA_AS_NAME_SEPARATOR_IN_OUTPUT
@define('LAST_AUTHOR_SEPARATOR',' and ');

@define('TYPES_SIZE',10); // number of entry types per table
@define('YEAR_SIZE',20); // number of years per table
@define('AUTHORS_SIZE',30); // number of authors per table
@define('TAGS_SIZE',30); // number of keywords per table
@define('READLINE_LIMIT',1024);
@define('Q_YEAR', 'year');
@define('Q_YEAR_PAGE', 'year_page');
@define('Q_YEAR_INPRESS', 'in press');
@define('Q_YEAR_ACCEPTED', 'accepted');
@define('Q_YEAR_SUBMITTED', 'submitted');
@define('Q_FILE', 'bib');
@define('Q_AUTHOR', 'author');
@define('Q_AUTHOR_PAGE', 'author_page');
@define('Q_TAG', 'keywords');
@define('Q_TAG_PAGE', 'keywords_page');
@define('Q_TYPE', 'type');// used for queries
@define('Q_TYPE_PAGE', 'type_page');
@define('Q_ALL', 'all');
@define('Q_ENTRY', 'entry');
@define('Q_KEY', 'key');
@define('Q_KEYS', 'keys'); // filter entries using a url-encoded, JSON-encoded array of bibtex keys
@define('Q_SEARCH', 'search');
@define('Q_EXCLUDE', 'exclude');
@define('Q_RESULT', 'result');
@define('Q_ACADEMIC', 'academic');
@define('Q_DB', 'bibdb');
@define('Q_LATEST', 'latest');
@define('Q_RANGE', 'range');
@define('AUTHOR', 'author');
@define('EDITOR', 'editor');
@define('SCHOOL', 'school');
@define('TITLE', 'title');
@define('BOOKTITLE', 'booktitle');
@define('YEAR', 'year');
@define('BUFFERSIZE',100000);
@define('MULTIPLE_BIB_SEPARATOR',';');
@define('METADATA_GS',true);
@define('METADATA_DC',true);
@define('METADATA_OPENGRAPH',true);
@define('METADATA_EPRINTS',false);

// define sort order for special values in 'year' field
// highest number is sorted first
// don't exceed 0 though, since the values are added to PHP_INT_MAX
@define('ORDER_YEAR_INPRESS', -0);
@define('ORDER_YEAR_ACCEPTED', -1);
@define('ORDER_YEAR_SUBMITTED', -2);
@define('ORDER_YEAR_OTHERNONINT', -3);


// in embedded mode, we still need a URL for displaying bibtex entries alone
// this is usually resolved to bibtexbrowser.php
// but can be overridden in bibtexbrowser.local.php
// for instance with @define('BIBTEXBROWSER_URL',''); // links to the current page with ?
@define('BIBTEXBROWSER_URL',basename(__FILE__));

// *************** END CONFIGURATION

define('Q_INNER_AUTHOR', '_author');// internally used for representing the author
define('Q_INNER_TYPE', 'x-bibtex-type');// used for representing the type of the bibtex entry internally
@define('Q_INNER_KEYS_INDEX', '_keys-index');// used for storing indices in $_GET[Q_KEYS] array
