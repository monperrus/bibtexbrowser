#!/usr/bin/env phpunit
<?php
/** PhPUnit tests for bibtexbrowser

To run them:
$ phpunit BibtexbrowserTest.php

With coverage:
$ phpunit --coverage-html ./coverage BibtexbrowserTest.php
$ XDEBUG_MODE=coverage phpunit --coverage-filter bibtexbrowser.php --coverage-html=foo.html BibtexbrowserTest.php 

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

  public function setUp():void {
        // resetting the default link style
        bibtexbrowser_configure('BIBTEXBROWSER_LINK_STYLE','bib2links_default');
        bibtexbrowser_configure('ABBRV_TYPE','index');
        bibtexbrowser_configure('BIBTEXBROWSER_USE_PROGRESSIVE_ENHANCEMENT', false);
  }

  function createDB() {
    return $this->_createDB("@book{aKey,title={A Book},author={Martin Monperrus},publisher={Springer},year=2009}\n"
    ."@book{aKey/withSlash,title={Slash Dangerous for web servers},author={Ap Ache},editor={Martin Monperrus},publisher={Springer},year=2010}\n"
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
    $this->assertEquals('<span itemscope="" itemtype="http://schema.org/ScholarlyArticle"><span class="bibtitle"  itemprop="name">A Book</span> (<span class="bibauthor"><span itemprop="author" itemtype="http://schema.org/Person">Martin Monperrus</span></span>), <span class="bibpublisher">Springer</span>, <span itemprop="datePublished">2009</span>.<span class="Z3988" title="ctx_ver=Z39.88-2004&amp;rft_val_fmt=info%3Aofi%2Ffmt%3Akev%3Amtx%3Abook&amp;rft.btitle=A+Book&amp;rft.genre=book&amp;rft.pub=Springer&amp;rft.date=2009&amp;rft.au=Martin+Monperrus"></span></span> <span class="bibmenu"><a class="biburl" title="aKey" href="bibtexbrowser.php?key=aKey&amp;bib=inline">[bibtex]</a></span>',$first_entry->toHTML());

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
    $this->assertEquals('<span itemscope="" itemtype="http://schema.org/ScholarlyArticle"><span class="bibtitle"  itemprop="name">An Article</span> (<span class="bibauthor"><span itemprop="author" itemtype="http://schema.org/Person">Foo Bar</span> and <span itemprop="author" itemtype="http://schema.org/Person">Jane Doe</span></span>), <span class="bibbooktitle">In <span itemprop="isPartOf">New Results</span></span>, volume 5, <span itemprop="datePublished">2009</span>.<span class="Z3988" title="ctx_ver=Z39.88-2004&amp;rft_val_fmt=info%3Aofi%2Ffmt%3Akev%3Amtx%3Ajournal&amp;rft.atitle=An+Article&amp;rft.jtitle=New+Results&amp;rft.volume=5&amp;rft.issue=&amp;rft.pub=&amp;rft.date=2009&amp;rft.au=Foo+Bar&amp;rft.au=Jane+Doe"></span></span> ',$first_entry->toHTML());

    // listing the CSS classes
    $css_classes_before = $this->extract_css_classes($first_entry->toHTML());
    
    // IEEE style
    bibtexbrowser_configure('BIBLIOGRAPHYSTYLE','JanosBibliographyStyle');
    $this->assertEquals("Foo Bar and Jane Doe, \"An Article\", New Results, vol. 5, pp. 1-2, 2009.\n ",strip_tags($first_entry->toHTML()));
    $css_classes_after = $this->extract_css_classes($first_entry->toHTML());
    // contract: make sure the Janos style and default style use the same CSS classes
    $this->assertEquals($css_classes_before, $css_classes_after);

    // Vancouver style
    bibtexbrowser_configure('BIBLIOGRAPHYSTYLE','VancouverBibliographyStyle');
    $this->assertEquals("Foo Bar and Jane Doe. An Article. New Results. 2009;5:1-2.\n ",strip_tags($first_entry->toHTML()));

    // changing the target
    bibtexbrowser_configure('BIBLIOGRAPHYSTYLE','DefaultBibliographyStyle');
    bibtexbrowser_configure('BIBTEXBROWSER_LINKS_TARGET','_top');
    $this->assertEquals('<span itemscope="" itemtype="http://schema.org/ScholarlyArticle"><span class="bibtitle"  itemprop="name">An Article</span> (<span class="bibauthor"><span itemprop="author" itemtype="http://schema.org/Person">Foo Bar</span> and <span itemprop="author" itemtype="http://schema.org/Person">Jane Doe</span></span>), <span class="bibbooktitle">In <span itemprop="isPartOf">New Results</span></span>, volume 5, <span itemprop="datePublished">2009</span>.<span class="Z3988" title="ctx_ver=Z39.88-2004&amp;rft_val_fmt=info%3Aofi%2Ffmt%3Akev%3Amtx%3Ajournal&amp;rft.atitle=An+Article&amp;rft.jtitle=New+Results&amp;rft.volume=5&amp;rft.issue=&amp;rft.pub=&amp;rft.date=2009&amp;rft.au=Foo+Bar&amp;rft.au=Jane+Doe"></span></span> ',$first_entry->toHTML());

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

  function testMultiSearch_name_match() {
    $btb = $this->createDB();
    $q=array(Q_NAME=>'Martin Monperrus');
    $results=$btb->multisearch($q);
    $this->assertTrue(count($results) == 2);
  }

  function testMultiSearch_author_name_match() {
    $btb = $this->createDB();
    $q=array(Q_AUTHOR_NAME=>'Martin Monperrus');
    $results=$btb->multisearch($q);
    $entry = $results[0];
    $this->assertTrue(count($results) == 1);
    $this->assertTrue($entry->getTitle() == 'A Book');
  }

  function testMultiSearch_editor_name_match() {
    $btb = $this->createDB();
    $q=array(Q_EDITOR_NAME=>'Martin Monperrus');
    $results=$btb->multisearch($q);
    $entry = $results[0];
    $this->assertTrue(count($results) == 1);
    $this->assertTrue($entry->getTitle() == 'Slash Dangerous for web servers');
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
        $this->assertEquals("[3]", $btb->getEntryByKey('aKey')->getAbbrv());
        $this->assertEquals("[1]", $btb->getEntryByKey('aKey-withSlash')->getAbbrv());

        // SimpleDisplayExt sets the indices differently, using setIndicesInIncreasingOrderChangingEveryYear
        $d = new SimpleDisplayExt();
        $d->setDB($btb);
        ob_start();
        $d->display();
        ob_get_clean();
        $this->assertEquals("[1]", $btb->getEntryByKey('aKey')->getAbbrv());
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
        // contract: the ordering is chronological
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
        // contract: entries without year are at the top
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

    function test_cff() {
        
        $btb = new BibDataBase();
        $btb->load('bibacid-utf8.bib');
        $entry = $btb->bibdb['arXiv-1807.05030'];
        $expected = "cff-version: 1.2.0\n".
        "# CITATION.cff created with https://github.com/monperrus/bibtexbrowser/\n".
        "preferred-citation:\n".
        "  title: \"A Comprehensive Study of Pseudo-tested Methods\"\n".
        "  year: \"2018\"\n".
        "  authors:\n".
        "    - family-names: Vera-Pérez\n".
        "      given-names: Oscar Luis\n".
        "    - family-names: Danglot\n".
        "      given-names: Benjamin\n".
        "    - family-names: Monperrus\n".
        "      given-names: Martin\n".
        "    - family-names: Baudry\n".
        "      given-names: Benoit\n";
        
        $this->assertEquals($expected,$entry->toCFF());
        
    }

    function test_uppercase() {
  print(preg_replace_callback('/\\\\uppercase\{(.*)\}/',"strtolowercallback", "$\uppercase{B}"));

        $bibtex = "@article{doe2000,title={Preferential polarization and its reversal in polycrystalline $\uppercase{B}i\uppercase{F}e\uppercase{O}_{3}$/$\uppercase{L}a_{0. 5}\uppercase{S}r_{0.5} \uppercase{C}o\uppercase{O}_{3}$ heterostructures},author={Jane Doe},journal={The Wordpress Journal},year=2000}";
        $test_data = fopen('php://memory','x+');
        fwrite($test_data, $bibtex);
        fseek($test_data,0);
        $db = new BibDataBase();
        $_GET[Q_FILE] = 'sample.bib';
        $db->update_internal("inline", $test_data);
        $this->assertEquals('Preferential polarization and its reversal in polycrystalline $BiFeO_{3}$/$La_{0. 5}Sr_{0.5} CoO_{3}$ heterostructures', $db->bibdb['doe2000']->getTitle());
    }

    function test_sorting_year() {
        $bibtex = "@article{doe2004,year=2004},@article{doe1999,year=1999},@article{doe2000,year=2000}";
        $test_data = fopen('php://memory','x+');
        fwrite($test_data, $bibtex);
        fseek($test_data,0);
        $db = new BibDataBase();
        $db->update_internal("inline", $test_data);
//         print_r($db);        
        $data = array_values($db->bibdb);
        $this->assertEquals("2004", $data[0]->getYear());
        $this->assertEquals("1999", $data[1]->getYear());
        $this->assertEquals("2000", $data[2]->getYear());
        usort($data, 'compare_bib_entry_by_year');

        $this->assertEquals("1999", $data[0]->getYear());
        $this->assertEquals("2000", $data[1]->getYear());
        $this->assertEquals("2004", $data[2]->getYear());

        $bibtex = "@article{doe2004,year=2004},@article{doe1999,year=1999},@article{doe2000,year=2000}";

    }

    function test_sorting_month() {
        $bibtex = "@article{doe2004,year=2004, month={may}},@article{doe1999,year=2004,month={jan}},@article{doe2000,year=2000, month={dec}}";
        $test_data = fopen('php://memory','x+');
        fwrite($test_data, $bibtex);
        fseek($test_data,0);
        $db = new BibDataBase();
        $db->update_internal("inline", $test_data);
//         print_r($db);        
        $data = array_values($db->bibdb);
        $this->assertEquals("may", $data[0]->getField("month"));
        usort($data, 'compare_bib_entry_by_month');

        $this->assertEquals("jan", $data[0]->getField("month"));
        $this->assertEquals("may", $data[1]->getField("month"));
        $this->assertEquals("dec", $data[2]->getField("month"));

    }

    function test_misc() {
        bibtexbrowser_configure('BIBLIOGRAPHYSTYLE','DefaultBibliographyStyle');
        $bibtex = "@misc{doe2004,title={foo bar title}, publisher={publisher}, howpublished={howpublished}}";
        $test_data = fopen('php://memory','x+');
        fwrite($test_data, $bibtex);
        fseek($test_data,0);
        $db = new BibDataBase();
        $db->update_internal("inline", $test_data);
        //         print_r($db);        
        $data = array_values($db->bibdb);
        $this->assertEquals('foo bar title, howpublished. [bibtex]', strip_tags($data[0]->toHTML()));
    }
    
    function test_note() {
      // fix https://github.com/monperrus/bibtexbrowser/issues/131
      bibtexbrowser_configure('BIBLIOGRAPHYSTYLE','DefaultBibliographyStyle');      
      bibtexbrowser_configure('BIBTEXBROWSER_BIBTEX_LINKS',false);
      bibtexbrowser_configure('BIBTEXBROWSER_PDF_LINKS',false);
      $bibtex = "
      @inproceedings{exampleEntry,
      author = {Aurora Macías and Elena Navarro and Carlos E. Cuesta and Uwe Zdun},
      title = {Architecting Digital Twins Using a Domain-Driven Design-Based Approach},
      booktitle = {XXVII Jornadas de Ingenier'{\i}a del Software y Bases de Datos (JISBD 2023)},
      month = {September},
      note = {handle: 11705/JISBD/2023/7321},
      year = {2023},
      url = {https://hdl.handle.net/11705/JISBD/2023/7321},
      month = {September},
      pages = {1--1},
      publisher = {SISTEDES},
      editor = {Amador Dur'{a}n Toro},
    }";
      $test_data = fopen('php://memory','x+');
      fwrite($test_data, $bibtex);
      fseek($test_data,0);
      $db = new BibDataBase();
      $db->update_internal("inline", $test_data);
      //         print_r($db);        
      $data = array_values($db->bibdb);
      $this->assertEquals(' Architecting Digital Twins Using a Domain-Driven Design-Based Approach (Aurora Macías, Elena Navarro, Carlos E. Cuesta and Uwe Zdun), In XXVII Jornadas de Ingenier\'ia del Software y Bases de Datos (JISBD 2023) (Amador Dur\'an Toro, ed.), SISTEDES, 2023, handle: 11705/JISBD/2023/7321. ', strip_tags($data[0]->toHTML()));
    }
    
    
    function test_keyword() {
      bibtexbrowser_configure('BIBLIOGRAPHYSTYLE','DefaultBibliographyStyle');
      $bibtex = "@misc{doe2004,title={foo bar title}, publisher={publisher}, howpublished={howpublished}} @misc{doe2,title={baz}, keywords={bar}, howpublished={howpublished}}";
      $test_data = fopen('php://memory','x+');
      fwrite($test_data, $bibtex);
      fseek($test_data,0);
      $db = new BibDataBase();
      $db->update_internal("inline", $test_data);
      //         print_r($db);  

      // no keyword
      $entry = array_values($db->bibdb)[0];
      $entry->addKeyword("foo");
      $this->assertEquals('foo', $entry->getField("keywords"));
      
      // already one keyword
      $entry = array_values($db->bibdb)[1];
      $entry->addKeyword("foo");
      $this->assertEquals('bar;foo', $entry->getField("keywords"));
      
    }
    
    public function testYearDisplay() {
      // Create test entries
      $entry2020 = new RawBibEntry();
      $entry2020->setField('year', '2020');
      $entry2020->setField('title', 'Paper 2020');
      $entry2020->setField('author', 'Author A');
      
      // assert getKey
      $this->assertEquals('97c46f8bdf428ce28cfa05cdedba2ec1', $entry2020->getKey());

      $entry2019a = new RawBibEntry(); 
      $entry2019a->setField('year', '2019');
      $entry2019a->setField('title', 'Paper 2019 A');
      $entry2019a->setField('author', 'Author B');

      $entry2019b = new RawBibEntry();
      $entry2019b->setField('year', '2019'); 
      $entry2019b->setField('title', 'Paper 2019 B');
      $entry2019b->setField('author', 'Author C');

      // Create YearDisplay
      $display = new YearDisplay();
      
      // Set test entries
      $entries = array(
          'key2020' => $entry2020,
          'key2019a' => $entry2019a,
          'key2019b' => $entry2019b
      );
      $display->setEntries($entries);

      // Get year index and verify
      $yearIndex = $display->yearIndex;
      $this->assertEquals("2020", $yearIndex[2020]);

      // Capture output
      ob_start();
      $display->display();
      $output = ob_get_clean();

      // Verify output contains years and titles
      // split output by line
      $lines = explode("\n", $output);
      $this->assertEquals('<div  class="theader">2020</div><table class="result">', $lines[0]);
  }
  
  public function testXMLPrettyPrinter() {
    // Create sample BibTeX entry with various fields to test
    $bibtex = "@article{test2023,
        author = {Test Author},
        title = {A Test Title with Special Characters: & < >}, 
        journal = {Test Journal},
        year = {2023},
        volume = {1},
        number = {2},
        pages = {100--200}
    }";

    // Set up input stream with BibTeX content
    $stream = fopen('php://memory', 'r+');
    fwrite($stream, $bibtex);
    rewind($stream);

    // Create XMLPrettyPrinter and parse
    $printer = new XMLPrettyPrinter();
    $printer->header = false;
    $parser = new StateBasedBibtexParser($printer);

    // Capture output
    ob_start();
    $parser->parse($stream);
    $output = ob_get_clean();

    // Verify XML structure and content
    $this->assertStringContainsString('<?xml version="1.0"', $output);
    $this->assertStringContainsString('<bibfile>', $output); 
    $this->assertStringContainsString('</bibfile>', $output);
    
    // Verify entry type
    $this->assertStringContainsString('<type>article</type>', $output);
    
    // Verify fields are present
    $this->assertStringContainsString('<key>author</key>', $output);
    $this->assertStringContainsString('<value>Test Author</value>', $output);
    
    // Verify special characters are escaped
    $this->assertStringContainsString('&amp;', $output);
    $this->assertStringContainsString('&lt;', $output);
    $this->assertStringContainsString('&gt;', $output);

    fclose($stream);
  }    

  public function testBibtexDisplay() {
    // Create test entries
    $entry1 = new RawBibEntry();
    $entry1->setType('article');
    $entry1->setField('title', 'Test Title');
    $entry1->setField('author', 'Test Author');
    $entry1->setField('year', '2023');
    $entry1->setField('journal', 'Test Journal');
    
    $entry2 = new RawBibEntry(); 
    $entry2->setType('inproceedings');
    $entry2->setField('title', 'Another Title with Special Chars: é');
    $entry2->setField('author', 'Another Author');
    $entry2->setField('year', '2022');
    $entry2->setField('booktitle', 'Test Conference');

    // Create BibtexDisplay instance
    $display = new BibtexDisplay();
    $display->header = false;
    $display->setTitle('Test Export');
    $display->setEntries(array($entry1, $entry2));
    
    // assert entries in $display
    $this->assertEquals(2, count($display->entries));

    // set BIBTEXBROWSER_BIBTEX_VIEW to reconstructed
    bibtexbrowser_configure('BIBTEXBROWSER_BIBTEX_VIEW', 'reconstructed');

    // Capture output
    ob_start();
    $display->display();
    $output = ob_get_clean();

    // Verify output contains expected elements
    // Header comments
    $this->assertStringContainsString('% generated by bibtexbrowser', $output);
    $this->assertStringContainsString('% Test Export', $output);
    $this->assertStringContainsString('% Encoding: UTF-8', $output);
    
    // Entry 1 content
    $this->assertStringContainsString('@article{', $output);
    $this->assertStringContainsString('title = {Test Title}', $output);
    $this->assertStringContainsString('author = {Test Author}', $output);
    $this->assertStringContainsString('year = {2023}', $output);
    $this->assertStringContainsString('journal = {Test Journal}', $output);
    
    // Entry 2 content  
    $this->assertStringContainsString('@inproceedings{', $output);
    $this->assertStringContainsString('title = {Another Title with Special Chars: é}', $output);
    $this->assertStringContainsString('author = {Another Author}', $output);
    $this->assertStringContainsString('year = {2022}', $output);
    $this->assertStringContainsString('booktitle = {Test Conference}', $output);
    
  }

  public function testCreateBibEntryDisplay() {
    // Test instance creation
    $display = createBibEntryDisplay();
    $this->assertInstanceOf('BibEntryDisplay', $display);
    
    // Create test entry
    $entry = new RawBibEntry();
    $entry->setType('article');
    $entry->setField('title', 'Test Title');
    $entry->setField('author', 'Test Author');
    $entry->setField('year', '2023');
    $entry->setField('journal', 'Test Journal');
    $entry->setField('abstract', 'Test Abstract');
    $entry->setField('url', 'http://example.com');
    
    // assertion on toString
    $this->assertEquals('article 4a9ba73904f4f22a7b37e18750ee5454', $entry->__toString());

    // Test display with entry
    $display->setEntries(array($entry));
    
    // Capture output
    ob_start();
    $display->display();
    $output = ob_get_clean();
    
    // Verify display output contains expected elements
    $this->assertStringContainsString('Test Title', $output);
    $this->assertStringContainsString('Test Author', $output);
    $this->assertStringContainsString('Test Journal', $output);
    $this->assertStringContainsString('Test Abstract', $output);
    
    // Test metadata generation
    $metadata = $display->metadata();
    $this->assertIsArray($metadata);

    // Verify metadata contains essential fields
    $foundTitle = false;
    $foundAuthor = false;
    foreach($metadata as $meta) {
        if($meta[0] == 'citation_title' && $meta[1] == 'Test Title') {
            $foundTitle = true;
        }
        if($meta[0] == 'citation_author' && $meta[1] == 'Author, Test') {
            $foundAuthor = true; 
        }
    }
    $this->assertTrue($foundTitle);
    $this->assertTrue($foundAuthor);
    
    // Test title generation
    $this->assertEquals('Test Title (bibtex)', $display->getTitle());
  }

  public function testParserDelegate() {
    // Create test delegate
    $delegate = new TestParserDelegate();
    
    // Create parser with delegate
    $parser = new StateBasedBibtexParser($delegate);
    
    // Create test bibtex content
    $bibtex = "@article{test2023,
        author = {Test Author},
        title = {Test Title},
        journal = {Test Journal},
        year = {2023},
        abstract = {Test Abstract}
    }";
    
    // Create memory stream with test content
    $stream = fopen('php://memory', 'r+');
    fwrite($stream, $bibtex);
    rewind($stream);
    
    // Parse the content
    $parser->parse($stream);
    
    // Verify event sequence
    $expected = array(
        'beginFile',
        'beginEntry',
        'setEntryType:article',
        'setEntryKey:test2023', 
        'setEntryField:author:Test Author',
        'setEntryField:title:Test Title',
        'setEntryField:journal:Test Journal', 
        'setEntryField:year:2023',
        'setEntryField:abstract:Test Abstract',
        'endEntry',
        'endFile'
    );
    
    $this->assertEquals($expected, $delegate->events);
    
    fclose($stream);
  }

  public function testRSSDisplay() {
    // Create test entries with various data to test RSS generation
    $entry1 = new RawBibEntry();
    $entry1->setType('article');
    $entry1->setField('title', 'Test Title with Special Chars: & < >');
    $entry1->setField('author', 'Test Author');
    $entry1->setField('year', '2023');
    $entry1->setField('journal', 'Test Journal');
    $entry1->setField('abstract', 'Test Abstract with HTML <b>tags</b>');
    $entry1->setField('url', 'http://example.com');
    
    $entry2 = new RawBibEntry();
    $entry2->setType('inproceedings');
    $entry2->setField('title', 'Another Test Title');
    $entry2->setField('author', 'Another Author'); 
    $entry2->setField('year', '2022');
    $entry2->setField('booktitle', 'Test Conference');
    
    // Create RSSDisplay instance
    $display = new RSSDisplay();
    $display->header = false;
    $display->setTitle('Test RSS Feed');
    $display->setEntries(array($entry1, $entry2));
    
    // Capture output
    ob_start();
    $display->display();
    $output = ob_get_clean();
    
    // Verify XML declaration
    $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?>', $output);
    
    // Verify RSS structure
    $this->assertStringContainsString('<rss version="2.0"', $output);
    $this->assertStringContainsString('<channel>', $output);
    $this->assertStringContainsString('<title>Test RSS Feed</title>', $output);
    
    // Verify entries
    $this->assertStringContainsString('<item>', $output);
    $this->assertStringContainsString('Test Title with Special Chars: &#38; &#60; &#62;', $output);
    $this->assertStringContainsString('Another Test Title', $output);
    
    // Verify text encoding
    $rss = $display->text2rss('Test & Text <b>bold</b> with HTML &eacute;');
    $this->assertEquals('Test &#38; Text bold with HTML ', $rss);
    
    // Verify metadata handling
    $this->assertStringContainsString('<description>', $output);
    $this->assertStringContainsString('<link>', $output);
    $this->assertStringContainsString('<guid', $output);
    
    // Verify valid XML
    $xml = new SimpleXMLElement($output);
    $this->assertInstanceOf('SimpleXMLElement', $xml);
  }

  public function testHTMLTemplate() {
    // Create mock content object that implements required methods
    $mockContent = new class {
        public function display() {
            echo '<div class="test-content">Test Content</div>';
        }
        
        public function getTitle() {
            return 'Test Title';
        }
        
        public function metadata() {
            return array(
                array('description', 'Test Description'),
                array('keywords', 'test, bibtex')
            );
        }
    };

    // Capture output
    bibtexbrowser_configure('BIBTEXBROWSER_USE_PROGRESSIVE_ENHANCEMENT', true);
    ob_start();
    HTMLTemplate($mockContent, false);
    $output = ob_get_clean();
    
    // Verify basic HTML structure
    $this->assertStringContainsString('<!DOCTYPE html PUBLIC', $output);
    $this->assertStringContainsString('<html xmlns="http://www.w3.org/1999/xhtml">', $output);
    $this->assertStringContainsString('</html>', $output);
    
    // Verify HEAD section
    $this->assertStringContainsString('<meta http-equiv="Content-Type" content="text/html; charset=', $output);
    $this->assertStringContainsString('<meta name="generator" content="bibtexbrowser', $output);
    
    // Verify meta tags
    $this->assertStringContainsString('<meta name="description" property="description" content="Test Description"', $output);
    $this->assertStringContainsString('<meta name="keywords" property="keywords" content="test, bibtex"', $output);
    
    // Verify title
    $this->assertStringContainsString('<title>Test Title</title>', $output);
    
    // Verify CSS inclusion
    $this->assertStringContainsString('<style type="text/css">', $output);
    
    // Verify content display
    $this->assertStringContainsString('<div class="test-content">Test Content</div>', $output);
    
    // Verify JavaScript includes when progressive enhancement enabled
    $this->assertStringContainsString(JQUERY_URI, $output);
    
  }  

    function test_hasPhrase() {
        // Create test entries with various content to test phrase matching
        $bibtex = "@article{entry1,title={Security Analysis of Web Applications},author={John Smith},year=2023,abstract={This paper discusses security vulnerabilities in modern web applications including XSS and CSRF attacks.}}
                  @inproceedings{entry2,title={Machine Learning for Cybersecurity},author={Jane Doe},year=2022,keywords={AI, security, neural networks},booktitle={International Conference on Security}}";
                  
        $test_data = fopen('php://memory','x+');
        fwrite($test_data, $bibtex);
        fseek($test_data,0);
        $db = new BibDataBase();
        $db->update_internal("inline", $test_data);
        
        $entries = array_values($db->bibdb);
        $entry1 = $entries[0]; // Security Analysis paper
        $entry2 = $entries[1]; // Machine Learning paper
        
        // Test basic phrase matching
        $this->assertTrue($entry1->hasPhrase('security'));
        $this->assertTrue($entry1->hasPhrase('web applications'));
        $this->assertTrue($entry2->hasPhrase('machine learning'));
        
        // Test case insensitivity
        $this->assertTrue($entry1->hasPhrase('SECURITY'));
        $this->assertTrue($entry2->hasPhrase('Machine LEARNING'));
        
        // Test specific field searching
        $this->assertTrue($entry1->hasPhrase('security', 'title'));
        $this->assertTrue($entry2->hasPhrase('cybersecurity', 'title'));
        $this->assertFalse($entry1->hasPhrase('cybersecurity', 'title')); // Should not match
        
        // Test matching in different fields
        $this->assertTrue($entry1->hasPhrase('smith', 'author'));
        $this->assertTrue($entry2->hasPhrase('neural', 'keywords'));
        $this->assertFalse($entry1->hasPhrase('neural', 'keywords')); // No keywords field in entry1
        
        // Test advanced patterns with regex
        $this->assertTrue($entry1->hasPhrase('XSS.*CSRF')); // Testing regex across words
        $this->assertTrue($entry2->hasPhrase('AI.*security')); // Testing regex with multiple terms
        $this->assertTrue($entry1->hasPhrase('vulnerab')); // Partial word matching
        
        // Test non-matching phrases
        $this->assertFalse($entry1->hasPhrase('blockchain')); // Not in any field
        $this->assertFalse($entry2->hasPhrase('database', 'title')); // Not in title
        
        // Test special characters
        $bibtex_special = "@article{special,title={Testing special chars: a/b (c) [d] {e}}}";
        $test_data_special = fopen('php://memory','x+');
        fwrite($test_data_special, $bibtex_special);
        fseek($test_data_special, 0);
        $db_special = new BibDataBase();
        $db_special->update_internal("inline", $test_data_special);
        
        $entry_special = array_values($db_special->bibdb)[0];
        
        // Test that special chars are properly handled in the search phrase
        $this->assertTrue($entry_special->hasPhrase('a/b'));
        $this->assertTrue($entry_special->hasPhrase('(c)'));
        $this->assertTrue($entry_special->hasPhrase('[d]'));
        $this->assertTrue($entry_special->hasPhrase('e')); // Curly braces get stripped in the stored fields
        
        // Test edge cases
        $this->assertFalse($entry1->hasPhrase('')); // Empty phrase should not match
    }

    function testMultiSearchWithSpecialChars() {
      // Create test entries with C/C++ related content
      $bibtex = "@article{cpp2023,
          title={Advances in C/C++ Programming Language},
          author={Jane Programmer},
          journal={Journal of Programming Languages},
          year=2023,
          keywords={C/C++, programming languages, compilers}
      }
      @inproceedings{java2023,
          title={Java vs Python: Performance Comparison},
          author={John Coder},
          booktitle={International Conference on Software Engineering},
          year=2023,
          keywords={Java, Python, performance}
      }";
      
      $test_data = fopen('php://memory','x+');
      fwrite($test_data, $bibtex);
      fseek($test_data, 0);
      $db = new BibDataBase();
      $db->update_internal("inline", $test_data);
      
      // Test searching for C/C++ (which contains special characters / and +)
      $q = array(Q_SEARCH => 'C/C++');
      $results = $db->multisearch($q);
      
      // Should match only the first entry
      $this->assertEquals(1, count($results));
      $this->assertEquals('Advances in C/C++ Programming Language', $results[0]->getTitle());
      
      // Test searching with escaped slashes
      $q = array(Q_SEARCH => 'C/C\+\+');
      $results = $db->multisearch($q);
      $this->assertEquals(1, count($results));
      
      // Test with partial match
      $q = array(Q_SEARCH => 'C/C');
      $results = $db->multisearch($q);
      $this->assertEquals(1, count($results));
      
      // Test with keywords field specifically
      $q = array(Q_TAG => 'C/C++');
      $results = $db->multisearch($q);
      $this->assertEquals(1, count($results));
      
      // Test searching for something that doesn't exist
      $q = array(Q_SEARCH => 'Rust');
      $results = $db->multisearch($q);
      $this->assertEquals(0, count($results));
    }
    

} // end class

// Test implementation that records events
class TestParserDelegate extends ParserDelegate {
  public $events = array();
  
  function beginFile() {
      $this->events[] = 'beginFile';
  }
  
  function endFile() {
      $this->events[] = 'endFile'; 
  }
  
  function setEntryField($key, $value) {
      $this->events[] = "setEntryField:".trim($key).":".trim($value);
  }
  
  function setEntryType($type) {
      $this->events[] = "setEntryType:$type";
  }
  
  function setEntryKey($key) {
      $this->events[] = "setEntryKey:$key";
  }
  
  function beginEntry() {
      $this->events[] = 'beginEntry';
  }
  
  function endEntry($source) {
      $this->events[] = 'endEntry';
  }
  }



@copy('bibtexbrowser.local.php.bak','bibtexbrowser.local.php');

?>
