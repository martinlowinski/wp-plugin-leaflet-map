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
		// Store $form[$name==geojson] in a file
		file_put_contents(LEAFLET_MAP__PLUGIN_DIR . 'regions.js', stripslashes($form['geojson_data']));
	}
?>
<div class="notice notice-success is-dismissible">
	<p>Options Updated!</p>
</div>
<?php
} elseif (isset($_POST['reset'])) {
	$settings->reset();
?>
<div class="notice notice-success is-dismissible">
	<p>Options have been reset to default values!</p>
</div>
<?php
} elseif (isset($_POST['clear-geocoder-cache'])) {
	include_once(LEAFLET_MAP__PLUGIN_DIR . 'class.geocoder.php');
	Leaflet_Geocoder::remove_caches();
?>
<div class="notice notice-success is-dismissible">
	<p>Location caches have been cleared!</p>
</div>
<?php
}
?>
<div class="wrap">
	<div class="wrap">
	<form method="post">
	<input type='file' id='csv' name='csv'></input>
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

jQuery('#generate').click(function(e) {
	generate();
});

var inputElement = document.getElementById("csv");
inputElement.addEventListener("change", function(e) {
	loadCSV();
}, false);

// Create a packaged mapshapber lib from the node module
// browserify -r ./mapshaper.js:mapshaper > mapshaper-lib.js
var mapshaper = require('mapshaper');

function generate(csv) {
	var json = (function () {
		var json = null;
		jQuery.ajax({
		'async': false,
			'global': false,
			'url': '<?php echo plugin_dir_url( __FILE__ ); ?>' + '../postleitzahlen-gf.geojson',
			'dataType': "json",
			'success': function (data) {
				json = data;
			}
		});
		return json;
	})(); 
	console.log(json);
	console.log(csv);

	const input = {
	'postleitzahlen-gf.json': json,
		'postleitzahlen-gf.csv': csv,
	};

	mapshaper.applyCommands(
		'postleitzahlen-gf.json -join postleitzahlen-gf.csv keys=postcode,postcode -dissolve2 region -filter-slivers copy-fields=postcode,locality,pastor,subdomain,color,fillColor,fillOpacity,weight -filter \'region != null\' -simplify 15% -o format=geojson',
		input,
		function(err, output) {
			console.log(err);
			console.log(output);
			var data = jQuery.parseJSON(output['postleitzahlen-gf.json'].toString());
			jQuery('#geojson_data').val(output['postleitzahlen-gf.json'].toString());
			console.log(data);
		});

}

function loadCSV() {
	var file    = document.querySelector('input[type=file]').files[0];
	var reader  = new FileReader();
	var csv;

	reader.addEventListener("load", function () {
		generate(reader.result);
	}, false);

	if (file) {
		reader.readAsText(file);
	}
}

</script>

