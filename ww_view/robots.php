<?php
header("Content-type: text/plain");
include_once('../ww_config/model_functions.php');
echo "
User-agent: *
Disallow: 
Disallow: /ww_config/
Disallow: /ww_edit/
Disallow: /ww_files/
Sitemap: ".WW_WEB_ROOT."/sitemap.xml";
?>