<?php 
function WoW_ungz_file($filein, $pathin, $fileout, $dest, $out=true) {
	$bUnpack=false;
	$filename=$pathin.'/'.$filein;
	if (is_file($filename)) {
		$outfile=$dest.'/'.$fileout;
		@chmod($outfile,0644);
		$out=fopen($outfile,"w");
		if ($out) {
			// open file and write content
	//		$fcontents=gzfile($filename);
			$zd = gzopen ($filename, "r");
			while ($contents = gzread ($zd, 100000)) {
				fwrite($out,$contents);
				if (gzeof($zd)) {
					continue;
				}
			}
			gzclose ($zd);
			fflush($out);
			fclose($out);
		} else {
			if ($out) echo "<p>Impossibile creare il file '$fileout'</p>";
			return false;
		}
		if (is_file($outfile)) {
	//		unlink($filename);
			$bUnpack=true;
			return true;
		}
	} else {
		if ($out) echo "<p>File di input '$filein' non presente.</p>";
		return false;
	}
}
?>
