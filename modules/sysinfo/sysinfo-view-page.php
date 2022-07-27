<?php
/*
if (isset($_POST['wowbs_maintenance_nonce'])) {
	$this->status_maintenance_save();
}
*/
global $current_screen;

?>
<style type="text/css">

#phpinfo body {background-color: #fff; color: #222; font-family: sans-serif;}
#phpinfo pre {margin: 0; font-family: monospace;}
#phpinfo a:link {color: #009; text-decoration: none; background-color: #fff;}
#phpinfo a:hover {text-decoration: underline;}
#phpinfo table {border-collapse: collapse; border: 0; width: 934px; box-shadow: 1px 2px 3px #ccc;}
#phpinfo .center {text-align: center;}
#phpinfo .center table {margin: 1em auto; text-align: left;}
#phpinfo .center th {text-align: center !important;}
#phpinfo td, #phpinfo th {border: 1px solid #666; font-size: 75%; vertical-align: baseline; padding: 4px 5px;}
#phpinfo th {position: sticky; top: 0; background: inherit;}
#phpinfo h1 {font-size: 150%;}
#phpinfo h2 {font-size: 125%;}
#phpinfo .p {text-align: left;}
#phpinfo .e {background-color: #ccf; width: 300px; font-weight: bold;}
#phpinfo .h {background-color: #99c; font-weight: bold;}
#phpinfo .v {background-color: #ddd; max-width: 300px; overflow-x: auto; word-wrap: break-word;}
#phpinfo .v i {color: #999;}
#phpinfo img {float: right; border: 0;}
#phpinfo hr {width: 934px; background-color: #ccc; border: 0; height: 1px;}
</style>
<?php
/*

#phpinfo body {background-color: #fff; color: #222; font-family: sans-serif;}
#phpinfo pre {margin: 0; font-family: monospace;}
#phpinfo a:link {color: #009; text-decoration: none; background-color: #fff;}
#phpinfo a:hover {text-decoration: underline;}
#phpinfo table {border-collapse: collapse; border: 0; width: 934px; box-shadow: 1px 2px 3px #ccc;}
#phpinfo .center {text-align: center;}
#phpinfo .center table {margin: 1em auto; text-align: left;}
#phpinfo .center th {text-align: center !important;}
#phpinfo td, #phpinfo th {border: 1px solid #666; font-size: 75%; vertical-align: baseline; padding: 4px 5px;}
#phpinfo th {position: sticky; top: 0; background: inherit;}
#phpinfo h1 {font-size: 150%;}
#phpinfo h2 {font-size: 125%;}
#phpinfo .p {text-align: left;}
#phpinfo .e {background-color: #ccf; width: 300px; font-weight: bold;}
#phpinfo .h {background-color: #99c; font-weight: bold;}
#phpinfo .v {background-color: #ddd; max-width: 300px; overflow-x: auto; word-wrap: break-word;}
#phpinfo .v i {color: #999;}
#phpinfo img {float: right; border: 0;}
#phpinfo hr {width: 934px; background-color: #ccc; border: 0; height: 1px;}

#phpinfo {}
#phpinfo pre {}
#phpinfo a:link {}
#phpinfo a:hover {}
#phpinfo table {}
#phpinfo .center {}
#phpinfo .center table {}
#phpinfo .center th {}
#phpinfo td, th {}
#phpinfo h1 {}
#phpinfo h2 {}
#phpinfo .p {}
#phpinfo .e {}
#phpinfo .h {}
#phpinfo .v {}
#phpinfo .vr {}
#phpinfo img {}
#phpinfo hr {}
*/

ob_start () ;
phpinfo () ;
$pinfo = ob_get_contents () ;
ob_end_clean () ;

// the name attribute "module_Zend Optimizer" of an anker-tag is not xhtml valide, so replace it with "module_Zend_Optimizer"
$pinf= str_replace ( "module_Zend Optimizer", "module_Zend_Optimizer", preg_replace ( '%^.*<body>(.*)</body>.*$%ms', '$1', $pinfo ) );
//$pinf= str_replace ( "module_Zend Optimizer", "module_Zend_Optimizer", $pinfo );

?>
<div class="wrap">
  <h2><?php echo WOW_MANAGER_TITLE ?>: Info Sistema</h2><!--pageID: <?php echo $current_screen->id ?> -->
  <div id="wow-main">
    <hr />
    <div id="phpinfo">
		<?php print($pinf); ?>
    </div>
  </div>
</div>
<script>
jQuery(function() {
//	jQuery( "#woowowopt" ).tabs();
});
</script> 
