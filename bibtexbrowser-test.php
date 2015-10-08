<?php
/** PhPUnit tests for bibtexbrowser

To run them:
$ phpunit bibtexbrowser-test.php 

With coverage:
$ phpunit --coverage-html ./coverage btb-test.php 

(be sure that xdebug is enabled: /etc/php5/cli/conf.d# ln -s ../../mods-available/xdebug.ini)
*/

$_GET['library']=1;

copy('bibtexbrowser.local.php','bibtexbrowser.local.php.bak');
unlink('bibtexbrowser.local.php');

require_once ('bibtexbrowser.php');

error_reporting(E_ALL);

class BTBTest extends PHPUnit_Framework_TestCase {

  function setUp() {
    $test_data = fopen('php://memory','x+');
    fwrite($test_data, "@book{aKey,title={A Book},author={Martin Monperrus},publisher={Springer},year=2009}\n".
    "@book{aKey/withSlash,title={Slash Dangerous for web servers},author={Ap Ache},publisher={Springer},year=2009}\n"
    );
    fseek($test_data,0);
    $this->btb = new BibDataBase();
    $this->btb->update_internal("inline", $test_data);
  }


  function test_bibentry_to_html() {
    $first_entry=$this->btb->bibdb[array_keys($this->btb->bibdb)[0]];
    $this->assertEquals('<span class="bibmenu"><a class="biburl" title="aKey" href="bibtexbrowser.php?key=aKey&amp;bib=inline">[bibtex]</a></span>',$first_entry->bib2links());
    $this->assertEquals('<a class="bibanchor" name=""></a>',$first_entry->anchor());
  }

  function testMultiSearch() {
    $q=array(Q_AUTHOR=>'monperrus');
    $results=$this->btb->multisearch($q);
    $entry = $results[0];
    $this->assertTrue(count($results) == 1);
    $this->assertTrue($entry->getTitle() == 'A Book');
  }
  
  function testMultiSearch2() {
    $q=array(Q_AUTHOR=>'monperrus|ducasse');
    $results=$this->btb->multisearch($q);
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
    $this->assertEquals('', ob_get_flush());

    // setting to false
    bibtexbrowser_configure('BIBTEXBROWSER_NO_DEFAULT', false);
    $this->assertFalse(config_value('BIBTEXBROWSER_NO_DEFAULT'));      
    ob_start();
    default_message();
    $this->assertContains('Congratulations', ob_get_flush());
  }

  function testInternationalization() {
    global $BIBTEXBROWSER_LANG;
    $BIBTEXBROWSER_LANG=array();
    $BIBTEXBROWSER_LANG['Refereed Conference Papers']="foo";
    $this->assertEquals("foo",__("Refereed Conference Papers"));
    
    $BIBTEXBROWSER_LANG['Books']="Livres";
    $d = new AcademicDisplay();
    $d->setDB($this->btb);
    ob_start();
    $d->display();
    $data = ob_get_flush();
    $this->assertContains('Livres', $data);
  }

  
  function testNoSlashInKey() {
    $q=array(Q_SEARCH=>'Slash');
    $results=$this->btb->multisearch($q);
    $this->assertTrue(count($results) == 1);
    $entry = $results[0];
    $this->assertContains("aKey-withSlash",$entry->toHTML());

    $q=array(Q_KEY=>'aKey-withSlash');
    $results=$this->btb->multisearch($q);
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
  
  function test_math_cal() {
    $test_data = fopen('php://memory','x+');
    fwrite($test_data, "@book{aKey,title={{A Book{} $\mbox{foo}$ tt $\boo{t}$}} ,author={Martin Monperrus},publisher={Springer},year=2009}\n".
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
    $this->assertEquals('<a href="myarticle.pdf">[pdf]</a>',$first_entry->getUrlLink());    
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
    $this->assertEquals('<a href="myarticle.pdf">[pdf]</a>',$first_entry->getUrlLink());    
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
    fwrite($test_data, "@article{aKey,title={A Book},author={Martin Monperrus},publisher={Springer},year=2009,pages={42--4242},number=1}\n");
    fseek($test_data,0);
    $db = new BibDataBase();
    $db->update_internal("inline", $test_data);
    $dis = $db->getEntryByKey('aKey');
    $this->assertEquals("@article{aKey,title={A Book},author={Martin Monperrus},publisher={Springer},year=2009,pages={42--4242},number=1}",$dis->getText());
    
    // now ith option
    bibtexbrowser_configure('BIBTEXBROWSER_BIBTEX_VIEW', 'reconstructed');
    bibtexbrowser_configure('BIBTEXBROWSER_BIBTEX_VIEW_FILTEREDOUT', 'pages|number');
    $this->assertEquals("@article{aKey,\n title = {A Book},\n author = {Martin Monperrus},\n publisher = {Springer},\n year = {2009},\n}\n",    $dis->getText());
  }


} // end class

?>
