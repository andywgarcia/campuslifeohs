<?php 

function get_result_as_arrays($result) {

	for ($i = 0; $i < $result->num_rows; $i++){
		if(!mysqli_data_seek($result,$i)){
			return false;
		}
		else {
			$data[] = mysqli_fetch_row($result);
		}
	}
	return $data;
}
function write_result_to_csv($filepath,$result){
	$data = get_result_as_arrays($result);
	if (!($handle = fopen($filepath,'w'))){
		return false;
	}
	else {
		foreach($data as $row){
			fputcsv($handle,$row);
		}
		fclose($handle);
	}

	return true;
}
?>