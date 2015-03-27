<?php

function go_die() {
	die(json_encode(array(
		"status" => "Error"
	)));
}

if( !isset($_POST['data']) ) {
	go_die();
}

$profile_content = file_get_contents($_POST['data']);
if( $profile_content === FALSE ) {
	go_die();
}

$png_filename = tempnam('tmp', 'PNG');
if( $png_filename === FALSE ) {
	go_die();
}

$gprof2dot_handle = popen('python gprof2dot.py | dot -Tpng -o '.$png_filename, "wb");
if( !$gprof2dot_handle ) {
	unlink($png_filename);
	go_die();
}
fwrite($gprof2dot_handle, $profile_content);
pclose($gprof2dot_handle);

$png_content = file_get_contents($png_filename);
unlink($png_filename);
if( $png_content === FALSE ) {
	go_die();
}

echo json_encode(array(
	"status" => "success",
	"data" => base64_encode($png_content)
));
