<?php global $current_screen; ?>
<div class="wrap">
  <h1>WoW-Manager: Debug</h1>
  <?php wp_nonce_field('wowdebug', 'wowdebug_nonce'); ?>
	<div id="res">
      <ul id="res_link">
      <li><a href="#variabili"><span>Variabili</span></a></li><!--pageID: <?php echo $current_screen->id ?> -->
      </ul>
      <div id="variabili">
      </div>
    </div>
</div>
