jQuery('#generate').click(function(e) {
	generate();
});

var inputElement = document.getElementById("csv_file");
inputElement.addEventListener("change", function(e) {
	loadCSV();
}, false);

// Create a packaged mapshapber lib from the node module
// browserify -r ./mapshaper.js:mapshaper > mapshaper-lib.js
var mapshaper = require('mapshaper');

function generate(csv) {
	const input = {
		'postleitzahlen-gf.json': json,
		'postleitzahlen-gf.csv': csv,
	};

	// Mapshaper commands:
	// - Join: Add additional data from the CSV to the GeoJSON
	// - Dissolve: Group to regions based on CSV data
	// - Filter: Remove regions that are ungrouped
	// - Simplify: Reduce the dataset
	mapshaper.applyCommands(
		'postleitzahlen-gf.json -join postleitzahlen-gf.csv keys=postcode,postcode -dissolve2 region -filter-slivers copy-fields=postcode,locality,pastor,subdomain,color,fillColor,fillOpacity,weight -filter \'region != null\' -simplify 15% -o format=geojson',
		input,
		function(err, output) {
			var data = output['postleitzahlen-gf.json'].toString();
			var data_json = jQuery.parseJSON(data);
			jQuery('#geojson_data').val(data);
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

