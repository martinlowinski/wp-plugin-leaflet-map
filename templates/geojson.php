<?php
function option_label ($opt) {
    $opt = explode('_', $opt);
    
    foreach($opt as &$v) {
        $v = ucfirst($v);
    }
    echo implode(' ', $opt);
}
?>
<div class="wrap">
	<h1>GeoJSON Editor</h1>
<?php
if (isset($_POST['submit'])) {
	/* copy and overwrite $post for checkboxes */
	$form = $_POST;

	foreach ($settings->options as $name => $option) {
		file_put_contents(LEAFLET_MAP__PLUGIN_DIR . 'regions.js', stripslashes($form['geojson_data']));
	}
?>
<div class="notice notice-success is-dismissible">
	<p>Regions generated!</p>
</div>
<?php
} elseif (isset($_POST['reset'])) {
	$settings->reset();
?>
<div class="notice notice-success is-dismissible">
	<p>Options have been reset to default values!</p>
</div>
<?php
}
?>
<div class="wrap">
	<div class="wrap">
	<form method="post">
	<?php
	foreach ($settings->options as $name => $option) {
		if (!$option->type) continue;
	?>
	<div class="container">
		<label>
			<span class="label"><?php option_label($name); ?></span>
			<span class="input-group">
			<?php
			$option->widget($name, $settings->get($name));
			?>
			</span>
		</label>

		<?php
		if ($option->helptext) {
		?>
		<div class="helptext">
			<p class="description"><?php 
				echo $option->helptext; 
			?></p>
		</div>
		<?php
		}
		?>
	</div>
	<?php
	}
	?>
	<div class="submit">
		<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
		<input type="submit" name="reset" id="reset" class="button button-secondary" value="Reset to Defaults">
	</div>

	</form>
	</div>
</div>

<script type="text/javascript">
// Load Postleitzahlen GeoJSON
var json = (function () {
	var json = null;
	jQuery.ajax({
	'async': false,
		'global': false,
		'url': '<?php echo plugins_url('postleitzahlen-gf.geojson', LEAFLET_MAP__PLUGIN_FILE); ?>',
		'dataType': "json",
		'success': function (data) {
			json = data;
		}
	});
	return json;
})();
</script>
