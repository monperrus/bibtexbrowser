<?php
#From http://stackoverflow.com/a/15774700

echo "<html>
<body>
<h3>Available databases:</h3>";
echo "<ul>";

$i = 0;

    if ($handle = opendir('.')) {
        while (false !== ($entry = readdir($handle))) {
            if (strpos($entry,'.bib')) {
                echo "<li><a href=\"bibtexbrowser.php?bib=$entry\">$entry</a></li>\n";
                $i++;
            }
        }
        closedir($handle);
   }
if ($i == 0){
   echo "<p><strong>ERROR: </strong>No .bib files were found</p>";
}
echo "</ul>
</body>
</html>";
?>