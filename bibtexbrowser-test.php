<?php
/** PhPUnit tests for bibtexbrowser

To run them:
$ phpunit bibtexbrowser-test.php 

With coverage:
$ phpunit --coverage-html ./coverage btb-test.php 

(be sure that xdebug is enabled: /etc/php5/cli/conf.d# ln -s ../../mods-available/xdebug.ini)
*/

function exception_error_handler($severity, $message, $file, $line) {
    if ($severity != E_ERROR) {
	//trigger_error($message);
        return;
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
}
error_reporting(E_ALL);

@copy('bibtexbrowser.local.php','bibtexbrowser.local.php.bak');
@unlink('bibtexbrowser.local.php');

if(is_file('reflectivedoc.php')) { 
  set_error_handler("exception_error_handler");
  require('reflectivedoc.php');
  $_GET['library'] = 1;
  foreach(getAllSnippetsInFile('bibtexbrowser.php') as $snippet) {
    //echo $snippet;
    ob_start();
    eval($snippet);
    ob_get_clean();
    unset($_GET['bib']);
  }
  restore_error_handler();
} else {
  $_GET['library']=1;
  require_once ('bibtexbrowser.php');
}


class BTBTest extends PHPUnit_Framework_TestCase {

  function test_checkdoc() {
    if(!is_file('gakowiki-syntax.php')) { return; }
    if (!function_exists('gk_wiki2html')) { include('gakowiki-syntax.php'); }
    create_wiki_parser()->parse(file_get_contents('bibtexbrowser-documentation.wiki'));
  }
    
  function createDB() {
    return $this->_createDB("@book{aKey,title={A Book},author={Martin Monperrus},publisher={Springer},year=2009}\n"
    ."@book{aKey/withSlash,title={Slash Dangerous for web servers},author={Ap Ache},publisher={Springer},year=2009}\n"
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

  function test_bibentry_to_html_article() {
    $btb = $this->createDB();
    $first_entry=$btb->getEntryByKey('aKeyA');
    $this->assertEquals("1-2",$first_entry->getField("pages"));
    $this->assertEquals("1",$first_entry->getPages()[0]);
    $this->assertEquals("2",$first_entry->getPages()[1]);
    
    // default style
    $this->assertEquals("An Article (Foo Bar and Jane Doe), In New Results, volume 5, 2009. [bibtex]",strip_tags($first_entry->toHTML()));
    $this->assertEquals('<span itemscope="" itemtype="http://schema.org/ScholarlyArticle"><span class="bibtitle"  itemprop="name">An Article</span> (<span class="bibauthor"><span itemprop="author" itemtype="http://schema.org/Person">Foo Bar</span> and <span itemprop="author" itemtype="http://schema.org/Person">Jane Doe</span></span>), <span class="bibbooktitle">In <span itemprop="isPartOf">New Results</span></span>, volume 5, <span itemprop="datePublished">2009</span>.<span class="Z3988" title="ctx_ver=Z39.88-2004&amp;rft_val_fmt=info%3Aofi%2Ffmt%3Akev%3Amtx%3Ajournal&amp;rft.atitle=An+Article&amp;rft.jtitle=New+Results&amp;rft.volume=5&amp;rft.issue=&amp;rft.pub=&amp;rfr_id=info%3Asid%2F%3A&amp;rft.date=2009&amp;rft.au=Foo+Bar&amp;rft.au=Jane+Doe"></span></span> <span class="bibmenu"><a class="biburl" title="aKeyA" href="bibtexbrowser.php?key=aKeyA&amp;bib=inline">[bibtex]</a></span>',$first_entry->toHTML());

    // IEEE style
    bibtexbrowser_configure('BIBLIOGRAPHYSTYLE','JanosBibliographyStyle');
    $this->assertEquals("Foo Bar and Jane Doe, \"An Article\", In New Results, vol. 5, pp. 1-2, 2009.\n [bibtex]",strip_tags($first_entry->toHTML()));
    
    // Vancouver style
    bibtexbrowser_configure('BIBLIOGRAPHYSTYLE','VancouverBibliographyStyle');
    $this->assertEquals("Foo Bar and Jane Doe. An Article. New Results. 2009;5:1-2.\n [bibtex]",strip_tags($first_entry->toHTML()));
    
    // changing the target
    bibtexbrowser_configure('BIBLIOGRAPHYSTYLE','DefaultBibliographyStyle');
    bibtexbrowser_configure('BIBTEXBROWSER_LINKS_TARGET','_top');
    $this->assertEquals('<span itemscope="" itemtype="http://schema.org/ScholarlyArticle"><span class="bibtitle"  itemprop="name">An Article</span> (<span class="bibauthor"><span itemprop="author" itemtype="http://schema.org/Person">Foo Bar</span> and <span itemprop="author" itemtype="http://schema.org/Person">Jane Doe</span></span>), <span class="bibbooktitle">In <span itemprop="isPartOf">New Results</span></span>, volume 5, <span itemprop="datePublished">2009</span>.<span class="Z3988" title="ctx_ver=Z39.88-2004&amp;rft_val_fmt=info%3Aofi%2Ffmt%3Akev%3Amtx%3Ajournal&amp;rft.atitle=An+Article&amp;rft.jtitle=New+Results&amp;rft.volume=5&amp;rft.issue=&amp;rft.pub=&amp;rfr_id=info%3Asid%2F%3A&amp;rft.date=2009&amp;rft.au=Foo+Bar&amp;rft.au=Jane+Doe"></span></span> <span class="bibmenu"><a target="_top" class="biburl" title="aKeyA" href="bibtexbrowser.php?key=aKeyA&amp;bib=inline">[bibtex]</a></span>',$first_entry->toHTML());
    
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
    $this->assertContains('Congratulations', ob_get_clean());
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
    $this->assertContains('Livres', $data);
  }

  
  function testNoSlashInKey() {
    $btb = $this->createDB();
    $q=array(Q_SEARCH=>'Slash');
    $results=$btb->multisearch($q);
    $this->assertTrue(count($results) == 1);
    $entry = $results[0];
    $this->assertContains("aKey-withSlash",$entry->toHTML());

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

  function test_google_scholar_metadata() {
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
    $test_data = fopen('php://memory','x+');
    fwrite($test_data, "@book{aKey,pdf={myarticle.pdf}}\n"
    );
    fseek($test_data,0);
    $btb = new BibDataBase();
    $btb->update_internal("inline", $test_data);    
    $first_entry=$btb->bibdb[array_keys($btb->bibdb)[0]];
    $this->assertEquals('<a href="myarticle.pdf">[pdf]</a>',$first_entry->getLink('pdf'));    
    $this->assertEquals('<a href="myarticle.pdf">[pdf]</a>',$first_entry->getPdfLink());    
    $this->assertEquals('<a href="myarticle.pdf"><img class="icon" src="pdficon.png" alt="[pdf]" title="pdf"/></a>',$first_entry->getLink('pdf','pdficon.png'));
    $this->assertEquals('<a href="myarticle.pdf">[see]</a>',$first_entry->getLink('pdf',NULL,'see'));    
  }

  // see https://github.com/monperrus/bibtexbrowser/pull/14
  function test_zotero() {
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
      $test_data = fopen('php://memory','x+');
    fwrite($test_data, "@Article{Baldwin2014Quantum,Doi={10.1103/PhysRevA.90.012110},Url={http://link.aps.org/doi/10.1103/PhysRevA.90.012110}}"
    );
    fseek($test_data,0);
    $btb = new BibDataBase();
    $btb->update_internal("inline", $test_data);    
    $first_entry=$btb->bibdb[array_keys($btb->bibdb)[0]];
    $this->assertEquals('<pre class="purebibtex">@Article{Baldwin2014Quantum,Doi={<a href="http://dx.doi.org/10.1103/PhysRevA.90.012110">10.1103/PhysRevA.90.012110</a>},Url={<a href="http://link.aps.org/doi/10.1103/PhysRevA.90.012110">http://link.aps.org/doi/10.1103/PhysRevA.90.012110</a>}}</pre>',$first_entry->toEntryUnformatted());    
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
        $PAGE_SIZE = 3;
        bibtexbrowser_configure('BIBTEXBROWSER_DEFAULT_DISPLAY', 'PagedDisplay');
        bibtexbrowser_configure('PAGE_SIZE', $PAGE_SIZE);
        $_GET['bib'] = 'bibacid-utf8.bib';
        $_GET['all'] = 1;
        $d = new Dispatcher();
        ob_start();
        $d->main();
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

        $bibtex = "@article{aKey61,title={An article Book},author = {Meyer, Heribert  and   {Advanced Air and Ground Research Team} and Foo Bar}}\n";
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

    }

    function test_parsing_author_list() {
        // specify parsing of author list

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
        $bibtex = "@string{hp_MartinMonperrus={http://www.monperrus.net/martin},hp_FooAcé={http://example.net/}},@article{aKey61,title={An article Book},author = {Martin Monperrus and Foo Acé and Monperrus, Martin}}\n";
        $test_data = fopen('php://memory','x+');
        fwrite($test_data, $bibtex);
        fseek($test_data,0);
        $db = new BibDataBase();
        $db->update_internal("inline", $test_data);
        $entry = $db->getEntryByKey('aKey61');
        $authors = $entry->getFormattedAuthorsArray();
        $this->assertEquals('<a href="http://www.monperrus.net/martin">Martin Monperrus</a>', $authors[0]);
        $this->assertEquals('<a href="http://example.net/">Foo Acé</a>', $authors[1]);
        $this->assertEquals('<a href="http://www.monperrus.net/martin">Monperrus, Martin</a>', $authors[2]);
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
    
    function test_identity() {
        $btb = new BibDataBase();
        $btb->load('bibacid-utf8.bib');
        
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
    
} // end class

@copy('bibtexbrowser.local.php.bak','bibtexbrowser.local.php');

?>
