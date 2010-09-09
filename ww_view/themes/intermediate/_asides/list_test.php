<?php

$custom_list = array();

$custom_list[] = array('title' => 'first item','link' => '#');
$custom_list[] = array('title' => 'second item','link' => '#');
$custom_list[] = array('title' => 'third item','link' => '#');

echo build_snippet('List Test',$custom_list);

?>