<?php
/** PhPUnit tests for bibtexbrowser

To run them:
$ phpunit BibtexbrowserTest.php

With coverage:
$ phpunit --coverage-html ./coverage BibtexbrowserTest.php

(be sure that xdebug is enabled: /etc/php5/cli/conf.d# ln -s ../../mods-available/xdebug.ini)
*/

// backward compatibility
if (!class_exists('PHPUnit_Framework_TestCase')) {
    class_alias('\PHPUnit\Framework\TestCase', 'PHPUnit_Framework_TestCase');
}

function exception_error_handler($severity, $message, $file, $line) {
    if ($severity != E_ERROR) {
	//trigger_error($message);
        return;
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
}
error_reporting(E_ALL);

// setup
@copy('bibtexbrowser.local.php','bibtexbrowser.local.php.bak');
@unlink('bibtexbrowser.local.php');

class Nothing {
  function main() {}
}
define('BIBTEXBROWSER_MAIN','Nothing');
//require_once('bibtexbrowser.php');

set_error_handler("exception_error_handler");
if (!is_file('reflectivedoc.php')) {
  die("to run the bibtexbrowser tests, download this file first:\ncurl -L -o reflectivedoc.php https://www.monperrus.net/martin/reflectivedoc.php.txt\n");
}
require('reflectivedoc.php');
$nsnippet=0;
foreach(getAllSnippetsInFile('bibtexbrowser.php') as $snippet) {
    ob_start();
    eval($snippet);
    ob_get_clean();
    unset($_GET['bib']);
    $nsnippet++;
}
if ($nsnippet!=19) {
  die('oops '.$nsnippet);
}
restore_error_handler();

class SimpleDisplayExt extends SimpleDisplay {
  function setIndices() {
    $this->setIndicesInIncreasingOrderChangingEveryYear();
  }
}


class BibtexbrowserTest extends PHPUnit_Framework_TestCase {

    public function setUp():void
    {
        // resetting the default link style
        bibtexbrowser_configure('BIBTEXBROWSER_LINK_STYLE','bib2links_default');
        bibtexbrowser_configure('ABBRV_TYPE','index');
        bibtexbrowser_configure('BIBTEXBROWSER_USE_PROGRESSIVE_ENHANCEMENT', false);
    }

  function test_checkdoc() {
    if(!is_file('gakowiki-syntax.php')) { return; }
    if (!function_exists('gk_wiki2html')) { include('gakowiki-syntax.php'); }
    $result = create_wiki_parser()->parse(file_get_contents('bibtexbrowser-documentation.wiki'));
    $this->assertEquals(1, strpos($result,"bibtexbrowser is a PHP script that creates publication lists from Bibtex files"));
  }

  function createDB() {
    return $this->_createDB("@book{aKey,title={A Book},author={Martin Monperrus},publisher={Springer},year=2009}\n"
    ."@book{aKey/withSlash,title={Slash Dangerous for web servers},author={Ap Ache},publisher={Springer},year=2010}\n"
    ."@article{aKeyA,title={An Article},author={Foo Bar and Jane Doe},volume=5,journal=\"New Results\",year=2009,pages={1-2}}\n");
  }

  function _createDB($content, $fakefilename="inline") {
    $test_data = fopen('php://memory','x+');
    fwrite($test_data, $content);
    fseek($test_data,0);
    $btb = new BibDataBase();
    $btb->update_internal($fakefilename, $test_data);
    return $btb;
  }


  function test_bibentry_to_html_book() {
    $btb = $this->createDB();
    $first_entry=$btb->getEntryByKey('aKey');

    // default style
    $this->assertEquals("A Book (Martin Monperrus), Springer, 2009. [bibtex]",strip_tags($first_entry->toHTML()));
    $this->assertEquals('<span itemscope="" itemtype="http://schema.org/ScholarlyArticle"><span class="bibtitle"  itemprop="name">A Book</span> (<span class="bibauthor"><span itemprop="author" itemtype="http://schema.org/Person">Martin Monperrus</span></span>), <span class="bibpublisher">Springer</span>, <span itemprop="datePublished">2009</span>.<span class="Z3988" title="ctx_ver=Z39.88-2004&amp;rft_val_fmt=info%3Aofi%2Ffmt%3Akev%3Amtx%3Abook&amp;rft.btitle=A+Book&amp;rft.genre=book&amp;rft.pub=Springer&amp;rfr_id=info%3Asid%2F%3A&amp;rft.date=2009&amp;rft.au=Martin+Monperrus"></span></span> <span class="bibmenu"><a class="biburl" title="aKey" href="bibtexbrowser.php?key=aKey&amp;bib=inline">[bibtex]</a></span>',$first_entry->toHTML());

    // IEEE style
    bibtexbrowser_configure('BIBLIOGRAPHYSTYLE','JanosBibliographyStyle');
    $this->assertEquals("Martin Monperrus, \"A Book\", Springer, 2009.\n [bibtex]",strip_tags($first_entry->toHTML()));

    // Vancouver style
    bibtexbrowser_configure('BIBLIOGRAPHYSTYLE','VancouverBibliographyStyle');
    $this->assertEquals("Martin Monperrus. A Book. Springer; 2009.\n [bibtex]",strip_tags($first_entry->toHTML()));

    // other methods
    $this->assertEquals('<span class="bibmenu"><a class="biburl" title="aKey" href="bibtexbrowser.php?key=aKey&amp;bib=inline">[bibtex]</a></span>',$first_entry->bib2links());
    $this->assertEquals('<a class="bibanchor" name=""></a>',$first_entry->anchor());
  }

  function extract_css_classes($str) {
    $xml = new SimpleXMLElement($str);
    $css_classes = array();
    foreach($xml->xpath('//node()/@class') as $v) {
       $css_classes[] = $v->__toString();
    };
    sort($css_classes);
    return $css_classes;
  }

  function test_bibentry_to_html_article() {
    $btb = $this->createDB();
    $first_entry=$btb->getEntryByKey('aKeyA');
    $this->assertEquals("1-2",$first_entry->getField("pages"));
    $this->assertEquals("1",$first_entry->getPages()[0]);
    $this->assertEquals("2",$first_entry->getPages()[1]);

    // default style
    bibtexbrowser_configure('BIBLIOGRAPHYSTYLE','DefaultBibliographyStyle');
    bibtexbrowser_configure('BIBTEXBROWSER_LINK_STYLE','nothing');
    $this->assertEquals("An Article (Foo Bar and Jane Doe), In New Results, volume 5, 2009. ",strip_tags($first_entry->toHTML()));
    $this->assertEquals('<span itemscope="" itemtype="http://schema.org/ScholarlyArticle"><span class="bibtitle"  itemprop="name">An Article</span> (<span class="bibauthor"><span itemprop="author" itemtype="http://schema.org/Person">Foo Bar</span> and <span itemprop="author" itemtype="http://schema.org/Person">Jane Doe</span></span>), <span class="bibbooktitle">In <span itemprop="isPartOf">New Results</span></span>, volume 5, <span itemprop="datePublished">2009</span>.<span class="Z3988" title="ctx_ver=Z39.88-2004&amp;rft_val_fmt=info%3Aofi%2Ffmt%3Akev%3Amtx%3Ajournal&amp;rft.atitle=An+Article&amp;rft.jtitle=New+Results&amp;rft.volume=5&amp;rft.issue=&amp;rft.pub=&amp;rfr_id=info%3Asid%2F%3A&amp;rft.date=2009&amp;rft.au=Foo+Bar&amp;rft.au=Jane+Doe"></span></span> ',$first_entry->toHTML());

    // listing the CSS classes
    $css_classes_before = $this->extract_css_classes($first_entry->toHTML());
    
    // IEEE style
    bibtexbrowser_configure('BIBLIOGRAPHYSTYLE','JanosBibliographyStyle');
    $this->assertEquals("Foo Bar and Jane Doe, \"An Article\", In New Results, vol. 5, pp. 1-2, 2009.\n ",strip_tags($first_entry->toHTML()));
    $css_classes_after = $this->extract_css_classes($first_entry->toHTML());
    // contract: make sure the Janos style and default style use the same CSS classes
    $this->assertEquals($css_classes_before, $css_classes_after);

    // Vancouver style
    bibtexbrowser_configure('BIBLIOGRAPHYSTYLE','VancouverBibliographyStyle');
    $this->assertEquals("Foo Bar and Jane Doe. An Article. New Results. 2009;5:1-2.\n ",strip_tags($first_entry->toHTML()));

    // changing the target
    bibtexbrowser_configure('BIBLIOGRAPHYSTYLE','DefaultBibliographyStyle');
    bibtexbrowser_configure('BIBTEXBROWSER_LINKS_TARGET','_top');
    $this->assertEquals('<span itemscope="" itemtype="http://schema.org/ScholarlyArticle"><span class="bibtitle"  itemprop="name">An Article</span> (<span class="bibauthor"><span itemprop="author" itemtype="http://schema.org/Person">Foo Bar</span> and <span itemprop="author" itemtype="http://schema.org/Person">Jane Doe</span></span>), <span class="bibbooktitle">In <span itemprop="isPartOf">New Results</span></span>, volume 5, <span itemprop="datePublished">2009</span>.<span class="Z3988" title="ctx_ver=Z39.88-2004&amp;rft_val_fmt=info%3Aofi%2Ffmt%3Akev%3Amtx%3Ajournal&amp;rft.atitle=An+Article&amp;rft.jtitle=New+Results&amp;rft.volume=5&amp;rft.issue=&amp;rft.pub=&amp;rfr_id=info%3Asid%2F%3A&amp;rft.date=2009&amp;rft.au=Foo+Bar&amp;rft.au=Jane+Doe"></span></span> ',$first_entry->toHTML());

    // testing ABBRV_TYPE
    bibtexbrowser_configure('ABBRV_TYPE','year');
    $this->assertEquals("[2009]",$first_entry->getAbbrv());
    bibtexbrowser_configure('ABBRV_TYPE','key');
    $this->assertEquals("[aKeyA]",$first_entry->getAbbrv());
    bibtexbrowser_configure('ABBRV_TYPE','index');
    $this->assertEquals("[]",$first_entry->getAbbrv());
    $first_entry->setIndex('foo');
    $this->assertEquals("[foo]",$first_entry->getAbbrv());
    bibtexbrowser_configure('ABBRV_TYPE','none');
    $this->assertEquals("",$first_entry->getAbbrv());

  }

  function testMultiSearch() {
    $btb = $this->createDB();
    $q=array(Q_AUTHOR=>'monperrus');
    $results=$btb->multisearch($q);
    $entry = $results[0];
    $this->assertTrue(count($results) == 1);
    $this->assertTrue($entry->getTitle() == 'A Book');
  }

  function testMultiSearch2() {
    $btb = $this->createDB();
    $q=array(Q_AUTHOR=>'monperrus|ducasse');
    $results=$btb->multisearch($q);
    $entry = $results[0];
    $this->assertTrue(count($results) == 1);
    $this->assertTrue($entry->getTitle() == 'A Book');
  }

  function test_config_value() {
    // default value
    $this->assertFalse(config_value('BIBTEXBROWSER_NO_DEFAULT'));

    // setting to true
    bibtexbrowser_configure('BIBTEXBROWSER_NO_DEFAULT', true);
    $this->assertTrue(config_value('BIBTEXBROWSER_NO_DEFAULT'));
    ob_start();
    default_message();
    $this->assertEquals('', ob_get_clean());

    // setting to false
    bibtexbrowser_configure('BIBTEXBROWSER_NO_DEFAULT', false);
    $this->assertFalse(config_value('BIBTEXBROWSER_NO_DEFAULT'));
    ob_start();
    default_message();
    $this->assertStringContainsString('Congratulations', ob_get_clean());
  }


  function testInternationalization() {
    $btb = $this->createDB();
    global $BIBTEXBROWSER_LANG;
    $BIBTEXBROWSER_LANG=array();
    $BIBTEXBROWSER_LANG['Refereed Conference Papers']="foo";
    $this->assertEquals("foo",__("Refereed Conference Papers"));

    $BIBTEXBROWSER_LANG['Books']="Livres";
    $d = new AcademicDisplay();
    $d->setDB($btb);
    ob_start();
    $d->display();
    $data = ob_get_clean();
    $this->assertStringContainsString('Livres', $data);
  }


  function testNoSlashInKey() {
    $btb = $this->createDB();
    $q=array(Q_SEARCH=>'Slash');
    $results=$btb->multisearch($q);
    $this->assertTrue(count($results) == 1);
    $entry = $results[0];
    $this->assertStringContainsString("aKey-withSlash",$entry->toHTML());

    $q=array(Q_KEY=>'aKey-withSlash');
    $results=$btb->multisearch($q);
    $entry2 = $results[0];
    $this->assertSame($entry2,$entry);
  }

  function test_string_should_be_deleted_after_update() {
    $test_data = fopen('php://memory','x+');
    fwrite($test_data, "@book{aKey,title={A Book},author={Martin Monperrus},publisher={Springer},year=2009}\n".
    "@String{x=2008}\n"
    );
    fseek($test_data,0);
    $btb = new BibDataBase();
    $btb->update_internal("inline", $test_data);
//     print_r($btb->stringdb);
    $this->assertEquals(1,count($btb->stringdb));

    // replacing the existing one
    $test_data = fopen('php://memory','x+');
    fwrite($test_data, "@book{aKey2,title={A Book},author={Martin Monperrus},publisher={Springer},year=2009}\n".
    "@String{x=2009}\n"
    );
    fseek($test_data,0);
    $btb = new BibDataBase();
    $btb->update_internal("inline2", $test_data);
//     print_r($btb->stringdb);
    $this->assertEquals(1,count($btb->stringdb));
    $this->assertEquals("2009",$btb->stringdb['x']->value);//

    // now adding another one and removing the string
    $test_data2 = fopen('php://memory','x+');
    fwrite($test_data2, "@book{aKey,title={A Book},author={Martin Monperrus},publisher={Springer},year=2009}\n".
    "@String{y=2010}\n"
    );
    fseek($test_data2,0);
    $btb->update_internal("inline2", $test_data2);
    $this->assertEquals(1,count($btb->stringdb));//
    $this->assertEquals("2010",$btb->stringdb['y']->value);//

  }

  function test_mastersthesis() {
    $test_data = fopen('php://memory','x+');
    fwrite($test_data, "@mastersthesis{aKey,title={A Thing},author={Martin Monperrus},year=2009,school={School of Nowhere}}\n".
    "@String{x=2008}\n"
    );
    fseek($test_data,0);
    $db = new BibDataBase();
    $db->update_internal("inline", $test_data);
    $this->assertEquals("A Thing (Martin Monperrus), Master's thesis, School of Nowhere, 2009. [bibtex]",strip_tags($db->getEntryByKey('aKey')->toHTML()));
  }

  function test_google_scholar_metadata() {
    bibtexbrowser_configure('METADATA_GS', true);
    $test_data = fopen('php://memory','x+');
    fwrite($test_data, "@article{aKey,title={A Book},author={Martin Monperrus},publisher={Springer},year=2009,pages={42--4242},number=1}\n".
    "@String{x=2008}\n"
    );
    fseek($test_data,0);
    $db = new BibDataBase();
    $db->update_internal("inline", $test_data);
    $dis = new BibEntryDisplay($db->getEntryByKey('aKey'));
    $metadata = $dis->metadata_dict();
    //print_r($metadata);
    $this->assertEquals("A Book",$metadata['citation_title']);
    $this->assertEquals("2009",$metadata['citation_date']);
    $this->assertEquals("2009",$metadata['citation_year']);
    $this->assertEquals("42",$metadata['citation_firstpage']);
    $this->assertEquals("4242",$metadata['citation_lastpage']);
    $this->assertEquals("1",$metadata['citation_issue']);

  }

    function test_metadata_opengraph() {
    $test_data = fopen('php://memory','x+');
    fwrite($test_data, "@article{aKey,title={A Book},author={Martin Monperrus},url={http://foo.com/},publisher={Springer},year=2009,pages={42--4242},number=1}\n".
    "@String{x=2008}\n"
    );
    fseek($test_data,0);
    $db = new BibDataBase();
    $db->update_internal("inline", $test_data);
    $dis = new BibEntryDisplay($db->getEntryByKey('aKey'));
    $metadata = $dis->metadata_dict();

    //print_r($metadata);
    $this->assertEquals("A Book",$metadata['og:title']);
    $this->assertEquals("article",$metadata['og:type']);
    $this->assertTrue(1 == preg_match("/http:.*author=Martin\+Monperrus/",$metadata['og:author']));
    $this->assertEquals("2009",$metadata['og:published_time']);
  }


  function test_math_cal() {
    $test_data = fopen('php://memory','x+');
    fwrite($test_data, "@book{aKey,title={{A {Book} $\mbox{foo}$ tt $\boo{t}$}} ,author={Martin Monperrus},publisher={Springer},year=2009}\n".
    "@String{x=2008}\n"
    );
    fseek($test_data,0);
    $btb = new BibDataBase();
    $btb->update_internal("inline", $test_data);
    $first_entry=$btb->bibdb[array_keys($btb->bibdb)[0]];
//    $this->assertTrue(strpos('A Book{} $\mbox{foo}$',$first_entry->toHTML());
    $this->assertEquals('A Book $\mbox{foo}$ tt $\boo{t}$',$first_entry->getTitle());
  }

  function test_link_configuration() {
    bibtexbrowser_configure('BIBTEXBROWSER_LINKS_TARGET','_self');
    $test_data = fopen('php://memory','x+');
    fwrite($test_data, "@book{aKey,pdf={myarticle.pdf}}\n@book{bKey,url={myarticle.pdf}}\n@book{cKey,url={myarticle.xyz}}\n"
    );
    fseek($test_data,0);
    $btb = new BibDataBase();
    $btb->update_internal("inline", $test_data);
    $first_entry=$btb->bibdb[array_keys($btb->bibdb)[0]];
    $this->assertEquals('<a href="myarticle.pdf">[pdf]</a>',$first_entry->getLink('pdf'));
    $this->assertEquals('<a href="myarticle.pdf">[pdf]</a>',$first_entry->getPdfLink());
    $this->assertEquals('<a href="myarticle.pdf"><img class="icon" src="pdficon.png" alt="[pdf]" title="pdf"/></a>',$first_entry->getLink('pdf','pdficon.png'));
    $this->assertEquals('<a href="myarticle.pdf">[see]</a>',$first_entry->getLink('pdf',NULL,'see'));
    $second_entry=$btb->bibdb[array_keys($btb->bibdb)[1]];
    $this->assertEquals('<a href="myarticle.pdf">[pdf]</a>',$second_entry->getPdfLink());
    $third_entry=$btb->bibdb[array_keys($btb->bibdb)[2]];
    $this->assertEquals('<a href="myarticle.xyz">[url]</a>',$third_entry->getPdfLink());
  }

  // see https://github.com/monperrus/bibtexbrowser/pull/14
  function test_zotero() {
    bibtexbrowser_configure('BIBTEXBROWSER_LINKS_TARGET','_self');
    $test_data = fopen('php://memory','x+');
    fwrite($test_data, "@book{aKey,file={myarticle.pdf}}\n"
    );
    fseek($test_data,0);
    $btb = new BibDataBase();
    $btb->update_internal("inline", $test_data);
    $first_entry=$btb->bibdb[array_keys($btb->bibdb)[0]];
    $this->assertEquals('<a href="myarticle.pdf">[pdf]</a>',$first_entry->getPdfLink());
  }

  // https://github.com/monperrus/bibtexbrowser/issues/40
  function test_doi_url() {
    bibtexbrowser_configure('BIBTEXBROWSER_LINKS_TARGET','_self');
    $test_data = fopen('php://memory','x+');
    fwrite($test_data, "@Article{Baldwin2014Quantum,Doi={10.1103/PhysRevA.90.012110},Url={http://link.aps.org/doi/10.1103/PhysRevA.90.012110}}"
    );
    fseek($test_data,0);
    $btb = new BibDataBase();
    $btb->update_internal("inline", $test_data);
    $first_entry=$btb->bibdb[array_keys($btb->bibdb)[0]];
    $this->assertEquals('<pre class="purebibtex">@Article{Baldwin2014Quantum,Doi={<a href="https://doi.org/10.1103/PhysRevA.90.012110">10.1103/PhysRevA.90.012110</a>},Url={<a href="http://link.aps.org/doi/10.1103/PhysRevA.90.012110">http://link.aps.org/doi/10.1103/PhysRevA.90.012110</a>}}</pre>',$first_entry->toEntryUnformatted());
  }

  function test_filter_view() {
    $test_data = fopen('php://memory','x+');
    fwrite($test_data, "@article{aKey,title={A Book},author={Martin M\'e},publisher={Springer},year=2009,pages={42--4242},number=1}\n");
    fseek($test_data,0);
    $db = new BibDataBase();
    $db->update_internal("inline", $test_data);
    $dis = $db->getEntryByKey('aKey');
    $this->assertEquals("@article{aKey,title={A Book},author={Martin M\'e},publisher={Springer},year=2009,pages={42--4242},number=1}",$dis->getText());

    // now ith option
    bibtexbrowser_configure('BIBTEXBROWSER_BIBTEX_VIEW', 'reconstructed');
    bibtexbrowser_configure('BIBTEXBROWSER_BIBTEX_VIEW_FILTEREDOUT', 'pages|number');
    $this->assertEquals("@article{aKey,\n title = {A Book},\n author = {Martin M\'e},\n publisher = {Springer},\n year = {2009},\n}\n",    $dis->getText());
  }

  function test_BIBTEXBROWSER_USE_LATEX2HTML() {
    $bibtex = "@article{aKey,title={\`a Book},author={J\'e Lo},publisher={Springer},year=2009,pages={42--4242},number=1}\n";

    bibtexbrowser_configure('BIBTEXBROWSER_USE_LATEX2HTML', true);
    $test_data = fopen('php://memory','x+');
    fwrite($test_data, $bibtex);
    fseek($test_data,0);
    $db = new BibDataBase();
    $db->update_internal("inline", $test_data);
    $dis = $db->getEntryByKey('aKey');
    $this->assertEquals("à Book",$dis->getTitle());
    $this->assertEquals("Jé Lo",$dis->getFormattedAuthorsString());

    // ensure that it is comma separated, used for metadata
    $this->assertEquals("Lo, Jé",$dis->getArrayOfCommaSeparatedAuthors()[0]);

    bibtexbrowser_configure('BIBTEXBROWSER_USE_LATEX2HTML', false);
    $test_data = fopen('php://memory','x+');
    fwrite($test_data, $bibtex);
    fseek($test_data,0);
    $db = new BibDataBase();
    $db->update_internal("inline", $test_data);
    $dis = $db->getEntryByKey('aKey');
    $this->assertEquals("\`a Book",$dis->getTitle());
    $this->assertEquals("J\'e Lo",$dis->getFormattedAuthorsString());
  }


    function test_PagedDisplay() {
        $PAGE_SIZE = 4;
        bibtexbrowser_configure('BIBTEXBROWSER_DEFAULT_DISPLAY', 'PagedDisplay');
        bibtexbrowser_configure('PAGE_SIZE', $PAGE_SIZE);
        $db = new BibDataBase();
        $db->load('bibacid-utf8.bib');
        $d = new PagedDisplay();
        $d->setEntries($db->bibdb);
        ob_start();
        $d->display();
        $content = "<div>".ob_get_clean()."</div>";
        $xml = new SimpleXMLElement($content);
        $result = $xml->xpath('//td[@class=\'bibref\']');
        $this->assertEquals($PAGE_SIZE,count($result));
    }

    function test_getKeywords() {
        $bibtex = "@article{aKey,title={\`a Book},keywords={foo,bar},author={Martin Monperrus},publisher={Springer},year=2009,pages={42--4242},number=1}\n";

        bibtexbrowser_configure('BIBTEXBROWSER_USE_LATEX2HTML', true);
        $test_data = fopen('php://memory','x+');
        fwrite($test_data, $bibtex);
        fseek($test_data,0);
        $db = new BibDataBase();
        $db->update_internal("inline", $test_data);
        $dis = $db->getEntryByKey('aKey');
        $this->assertEquals(2,count($dis->getKeywords()));
    }

    # https://github.com/monperrus/bibtexbrowser/pull/51
    function test_emptyGetPdfLink() {
        bibtexbrowser_configure('BIBTEXBROWSER_LINKS_TARGET','_self');
        $bibtex = "
        @article{aKey,
            title={\`a Book},
            author={Martin Monperrus},
            publisher={Springer},
            year=2009,
            pages={42--4242},
            number=1
        }
        @article{bKey,
            url={magic.pdf},
        }
        @article{cKey,
            pdf={magic2.pdf},
            url={magic3}
        }";
        $test_data = fopen('php://memory','x+');
        fwrite($test_data, $bibtex);
        fseek($test_data,0);
        $db = new BibDataBase();
        $db->update_internal("inline", $test_data);

        $dis = $db->getEntryByKey('aKey');
        $this->assertEquals("",$dis->getPdfLink());

        $dis = $db->getEntryByKey('bKey');
        $this->assertEquals('<a href="magic.pdf">[pdf]</a>',$dis->getPdfLink());

        $dis = $db->getEntryByKey('cKey');
        $this->assertEquals('<a href="magic2.pdf">[pdf]</a>',$dis->getPdfLink());
    }

    function test_formatting() {

        $bibtex = "@article{aKey61,title={An article Book},author = {Meyer, Heribert  and   {Advanced Air and Ground Research Team} and Foo Bar}}\n@article{bKey61,title={An article Book},author = {Meyer, Heribert and Foo Bar}}\n";
        $test_data = fopen('php://memory','x+');
        fwrite($test_data, $bibtex);
        fseek($test_data,0);
        $db = new BibDataBase();
        $db->update_internal("inline", $test_data);
        $entry = $db->getEntryByKey('aKey61');

        // test with formatting with default options same as getRawAuthors()
        $authors = $entry->getFormattedAuthorsArray();
        $this->assertEquals(3, count($authors));
        $this->assertEquals("Meyer, Heribert", $authors[0]);
        $this->assertEquals("Advanced Air and Ground Research Team", $authors[1]);
        $this->assertEquals("Foo Bar", $authors[2]);
        $this->assertEquals("Meyer, Heribert, Advanced Air and Ground Research Team and Foo Bar", $entry->getFormattedAuthorsString());

        // test with formatting (first name before)
        bibtexbrowser_configure('USE_COMMA_AS_NAME_SEPARATOR_IN_OUTPUT', true);
        $authors = $entry->getFormattedAuthorsArray();
        $this->assertEquals(3, count($authors));
        $this->assertEquals("Meyer, Heribert", $authors[0]);
        $this->assertEquals("Team, Advanced Air and Ground Research", $authors[1]);
        $this->assertEquals("Bar, Foo", $authors[2]);
        $this->assertEquals("Meyer, Heribert; Team, Advanced Air and Ground Research and Bar, Foo", $entry->getFormattedAuthorsString());

        // test with formatting (with initials) formatAuthorInitials
        bibtexbrowser_configure('USE_COMMA_AS_NAME_SEPARATOR_IN_OUTPUT', false);
        bibtexbrowser_configure('USE_INITIALS_FOR_NAMES', true);
        $authors = $entry->getFormattedAuthorsArray();
        $this->assertEquals(3, count($authors));
        $this->assertEquals("Meyer H", $authors[0]);
        $this->assertEquals("Team AAand GR", $authors[1]);
        $this->assertEquals("Bar F", $authors[2]);
        $this->assertEquals("Meyer H, Team AAand GR and Bar F", $entry->getFormattedAuthorsString());

        // test with first_name last_name formatAuthorCanonical
        bibtexbrowser_configure('USE_COMMA_AS_NAME_SEPARATOR_IN_OUTPUT', false);
        bibtexbrowser_configure('USE_INITIALS_FOR_NAMES', false);
        bibtexbrowser_configure('USE_FIRST_THEN_LAST', true);
        $authors = $entry->getFormattedAuthorsArray();
        $this->assertEquals(3, count($authors));
        $this->assertEquals("Heribert Meyer", $authors[0]);
        $this->assertEquals("Advanced Air and Ground Research Team", $authors[1]);
        $this->assertEquals("Foo Bar", $authors[2]);
        $this->assertEquals("Heribert Meyer, Advanced Air and Ground Research Team and Foo Bar", $entry->getFormattedAuthorsString());
        
        // test Oxford comma with default options
        bibtexbrowser_configure('USE_COMMA_AS_NAME_SEPARATOR_IN_OUTPUT', false);
        bibtexbrowser_configure('USE_INITIALS_FOR_NAMES', false);
        bibtexbrowser_configure('USE_FIRST_THEN_LAST', false);
        bibtexbrowser_configure('USE_OXFORD_COMMA', true);
        $this->assertEquals("Meyer, Heribert, Advanced Air and Ground Research Team, and Foo Bar", $entry->getFormattedAuthorsString());
        $entry = $db->getEntryByKey('bKey61');
        $this->assertEquals("Meyer, Heribert and Foo Bar", $entry->getFormattedAuthorsString());
        bibtexbrowser_configure('USE_OXFORD_COMMA', false);
    }

    function test_parsing_author_list() {
        // specify parsing of author list

        bibtexbrowser_configure('USE_COMMA_AS_NAME_SEPARATOR_IN_OUTPUT', false);
        bibtexbrowser_configure('USE_FIRST_THEN_LAST', false);

        // default case: one authors
        $bibtex = "@article{aKey61,title={An article Book},author = {Meyer, Heribert}}\n";
        $test_data = fopen('php://memory','x+');
        fwrite($test_data, $bibtex);
        fseek($test_data,0);
        $db = new BibDataBase();
        $db->update_internal("inline", $test_data);
        $entry = $db->getEntryByKey('aKey61');
        $authors = $entry->getRawAuthors();
        $this->assertEquals(1,count($authors));
        $this->assertEquals("Meyer, Heribert", $authors[0]);

        // default case: no sub list
        $bibtex = "@article{aKey61,title={An article Book},author = {Meyer, Heribert     and  Foo Bar}}\n";
        $test_data = fopen('php://memory','x+');
        fwrite($test_data, $bibtex);
        fseek($test_data,0);
        $db = new BibDataBase();
        $db->update_internal("inline", $test_data);
        $entry = $db->getEntryByKey('aKey61');
        $authors = $entry->getRawAuthors();
        $this->assertEquals(2,count($authors));
        $this->assertEquals("Meyer, Heribert", $authors[0]);
        $this->assertEquals("Meyer, Heribert and Foo Bar", $entry->getFormattedAuthorsString());

        // Github issue 61
        $bibtex = "@article{aKey61,title={An article Book},author = {Meyer, Heribert  and   {Advanced Air and Ground Research Team} and Foo Bar and J{\'e} Ko and J{\'e} Le and Fd L{\'e}}}\n";
        // wrong parsing of author names
        $test_data = fopen('php://memory','x+');
        fwrite($test_data, $bibtex);
        fseek($test_data,0);
        $db = new BibDataBase();
        $db->update_internal("inline", $test_data);
        $entry = $db->getEntryByKey('aKey61');
        $authors = $entry->getRawAuthors();
        $this->assertEquals(6, count($authors));
        $this->assertEquals("Meyer, Heribert", $authors[0]);
        $this->assertEquals("Advanced Air and Ground Research Team", $authors[1]);
        $this->assertEquals("Foo Bar", $authors[2]);
        $this->assertEquals("J{\'e} Ko", $authors[3]);
        $this->assertEquals("J{\'e} Le", $authors[4]);
        $this->assertEquals("Fd L{\'e}", $authors[5]);
    }

    function test_latex2html() {
        $this->assertEquals('"', latex2html("``"));
        $this->assertEquals('"', latex2html("''"));
        $this->assertEquals('&eacute;', latex2html("\'e"));
        $this->assertEquals('&eacute;', latex2html("{\'e}"));
    }

    function test_homepage_link() {
        bibtexbrowser_configure('USE_COMMA_AS_NAME_SEPARATOR_IN_OUTPUT', false);
        bibtexbrowser_configure('USE_FIRST_THEN_LAST', false);
        $bibtex = "@string{hp_MartinMonperrus={http://www.monperrus.net/~martin},hp_FooAcé={http://example.net/}},@article{aKey61,title={An article Book},author = {Martin Monperrus and Foo Acé and Monperrus, Martin}}\n";
        $test_data = fopen('php://memory','x+');
        fwrite($test_data, $bibtex);
        fseek($test_data,0);
        $db = new BibDataBase();
        $db->update_internal("inline", $test_data);
        $entry = $db->getEntryByKey('aKey61');
        $authors = $entry->getFormattedAuthorsArray();
        $this->assertEquals('<a href="http://www.monperrus.net/~martin">Martin Monperrus</a>', $authors[0]);
        $this->assertEquals('<a href="http://example.net/">Foo Acé</a>', $authors[1]);
        $this->assertEquals('<a href="http://www.monperrus.net/~martin">Monperrus, Martin</a>', $authors[2]);
    }

    function test_author_index() {
        bibtexbrowser_configure('USE_FIRST_THEN_LAST', true);

        $bibtex = "@string{hp_MartinMonperrus={http://www.monperrus.net/martin},hp_FooAcé={http://example.net/}},@article{aKey61,title={An article Book},author = {Martin Monperrus and Foo Ac\'e and Monperrus, Martin}}\n";
        $test_data = fopen('php://memory','x+');
        fwrite($test_data, $bibtex);
        fseek($test_data,0);
        $db = new BibDataBase();
        $db->update_internal("inline", $test_data);

        $index = var_export($db->authorIndex(), true);
        $this->assertEquals("array (\n  'Foo Acé' => 'Foo Acé',\n  'Martin Monperrus' => 'Martin Monperrus',\n)", $index);
    }

    function test_string_entries() {
        $btb = new BibDataBase();
        $btb->load('bibacid-utf8.bib');
        $this->assertEquals(5, count($btb->stringdb));
        $this->assertEquals("@string{foo={Foo}}",$btb->stringdb['foo']->toString());
    }

    function test_indices() {
        $btb = $this->createDB();
        $this->assertEquals("[]", $btb->getEntryByKey('aKey')->getAbbrv());

        $d = new SimpleDisplay();
        $d->setDB($btb);
        ob_start();
        $d->display();
        ob_get_clean();

        // the indices have been set by SimpleDisplay
        $this->assertEquals("[1]", $btb->getEntryByKey('aKey')->getAbbrv());
        $this->assertEquals("[3]", $btb->getEntryByKey('aKey-withSlash')->getAbbrv());

        // SimpleDisplayExt sets the indices differently, using setIndicesInIncreasingOrderChangingEveryYear
        $d = new SimpleDisplayExt();
        $d->setDB($btb);
        ob_start();
        $d->display();
        ob_get_clean();
        $this->assertEquals("[2]", $btb->getEntryByKey('aKey')->getAbbrv());
        $this->assertEquals("[1]", $btb->getEntryByKey('aKey-withSlash')->getAbbrv());
        
    }

    function test_identity() {
        $btb = new BibDataBase();
        $btb->load('bibacid-utf8.bib');
        bibtexbrowser_configure('BIBTEXBROWSER_BIBTEX_VIEW_FILTEREDOUT', '');

        // computing the representation
        $d = new SimpleDisplay();
        $d->setDB($btb);
        ob_start();
        $d->display();
        $rep = ob_get_clean();

        $nref = count($btb->bibdb);
        $bibtex = $btb->toBibtex();
        
        // reparsing the new content
        $btb2 = $this->_createDB($bibtex, 'bibacid-utf8.bib');
        $d->setDB($btb2);
        ob_start();
        $d->display();
        $rep2 = ob_get_clean();
        // there is the same number of entries
        $this->assertEquals($nref, count($btb2->bibdb));
        $this->assertEquals($bibtex, $btb2->toBibtex());
        $this->assertEquals($rep, $rep2);
    }

    function test_cli() {
        $test_file="test_cli.bib";
        copy('bibacid-utf8.bib', $test_file);
        system('php bibtexbrowser-cli.php '.$test_file." --id classical --set-title \"a new title\"");
        $db = new BibDataBase();
        $db->load($test_file);
        $this->assertEquals("a new title", $db->getEntryByKey('classical')->getField('title'));

        // multiple changes
        system('php bibtexbrowser-cli.php '.$test_file." --id classical --set-title \"a new title\" --id with_abstract --set-title \"a new title\" --set-year 1990");
        $db = new BibDataBase();
        $db->load($test_file);
        $this->assertEquals("a new title", $db->getEntryByKey('classical')->getField('title'));
        $this->assertEquals("a new title", $db->getEntryByKey('with_abstract')->getField('title'));
        $this->assertEquals("1990", $db->getEntryByKey('with_abstract')->getField('year'));

        unlink($test_file);
    }

    function test_removeField() {
        $btb = $this->createDB();
        $first_entry=$btb->getEntryByKey('aKey');
        $this->assertTrue($first_entry->hasField('author'));
        $first_entry->removeField('author');
        $this->assertFalse($first_entry->hasField('author'));
    }

    function testdefaultkey() {
        bibtexbrowser_configure('BIBTEXBROWSER_BIBTEX_VIEW', 'original');
        $bibtex = "@article{title={An article Book},author = {Martin Monperrus and Foo Ac\'e and Monperrus, Martin}}";
        $key = md5($bibtex);
        $test_data = fopen('php://memory','x+');
        fwrite($test_data, $bibtex);
        fseek($test_data,0);
        $db = new BibDataBase();
        $db->update_internal("inline", $test_data);
        $entry=$db->getEntryByKey($key);
        $this->assertEquals($bibtex, $entry->getText());
    }

    function testscholarlink() {
        bibtexbrowser_configure('BIBTEXBROWSER_LINKS_TARGET','_self');
        bibtexbrowser_configure('BIBTEXBROWSER_BIBTEX_VIEW', 'original');
        $bibtex = "@article{key,title={An article Book},gsid={1234},author = {Martin Monperrus and Foo Ac\'e and Monperrus, Martin}}";
        $test_data = fopen('php://memory','x+');
        fwrite($test_data, $bibtex);
        fseek($test_data,0);
        $db = new BibDataBase();
        $db->update_internal("inline", $test_data);
        $entry=$db->getEntryByKey("key");
        $this->assertStringContainsString('<a href="https://scholar.google.com/scholar?cites=1234">[citations]</a>', $entry->toHTML());
    }


    function test_before() {
        $bibtex = "@article{doe2000,title={An article},author={Jane Doe},journal={The Wordpress Journal},year=2000}@book{doo2001,title={A book},author={Jane Doe},year=2001}";
        $test_data = fopen('php://memory','x+');
        fwrite($test_data, $bibtex);
        fseek($test_data,0);
        $db = new BibDataBase();
        $_GET[Q_FILE] = 'sample.bib';
        $db->update_internal("inline", $test_data);

        $d = new SimpleDisplay();
        $d->setDB($db);
        ob_start();
        NoWrapper($d);
        $output = ob_get_clean();
        $res = eval("return ".file_get_contents('reference-output-wp-publications.txt').";");
        $this->assertEquals(strip_tags($res['rendered']), "&#091;wp-publications bib=sample.bib all=1&#093; gives:\n".strip_tags($output)."\n");
    }

    function test80() {
        // entries without year are at the top
        $bibtex = "@article{keyWithoutYear,title={First article},author = {Martin}},@article{key2,title={Second article},author = {Martin}, year=2007}";
        $test_data = fopen('php://memory','x+');
        fwrite($test_data, $bibtex);
        fseek($test_data,0);
        $db = new BibDataBase();
        $db->update_internal("inline", $test_data);

        $d = new SimpleDisplay();
        $d->setDB($db);
        ob_start();
        $d->display();
        $output = ob_get_clean();
//         print($output);
        $this->assertEquals("keyWithoutYear", $d->entries[0]->getKey());
        $this->assertEquals("key2", $d->entries[1]->getKey());
        // the indices have been set by SimpleDisplay, by default the one at the top is the one withuut year (the first in $d->entries)
        $this->assertEquals("[2]", $db->getEntryByKey('keyWithoutYear')->getAbbrv());
        $this->assertEquals("[1]", $db->getEntryByKey('key2')->getAbbrv());
    }

    function test_bug_201808() {
        $btb = new BibDataBase();
        $btb->load('bibacid-utf8.bib');
        $this->assertEquals(4,count($btb->bibdb['arXiv-1807.05030']->getRawAuthors()));
        $this->assertEquals(4,count($btb->bibdb['arXiv-1807.05030']->getFormattedAuthorsArray()));
        $this->assertEquals("Oscar Luis Vera-Pérez, Benjamin Danglot, Martin Monperrus and Benoit Baudry",$btb->bibdb['arXiv-1807.05030']->getAuthor());

        bibtexbrowser_configure('BIBTEXBROWSER_LINK_STYLE','nothing');
        bibtexbrowser_configure('BIBLIOGRAPHYSTYLE','JanosBibliographyStyle');
        $this->assertEquals("Oscar Luis Vera-Pérez, Benjamin Danglot, Martin Monperrus and Benoit Baudry,  \"A Comprehensive Study of Pseudo-tested Methods\", Technical report, arXiv 1807.05030, 2018.\n ",strip_tags($btb->bibdb['arXiv-1807.05030']->toHTML()));

    }


    function test_multiple_table() {
        ob_start();

        $btb = new BibDataBase();
        $btb->load('bibacid-utf8.bib');

        $display = new SimpleDisplay($btb, array(Q_YEAR => '1997'));
        $display->display();

        $display = new SimpleDisplay($btb, array(Q_YEAR => '2010'));
        $display->display();

        $output = ob_get_clean();

        // assertion: we have two tables in the output
        $xml = new SimpleXMLElement("<doc>".$output."</doc>");
        $result = $xml->xpath('//table');
        $this->assertEquals(2,count($result));

    }


} // end class

@copy('bibtexbrowser.local.php.bak','bibtexbrowser.local.php');

?>
