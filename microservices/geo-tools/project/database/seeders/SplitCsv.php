<?php

CONST MAX_ROWS=2000;

$current = getcwd();
$path_origin = 'data';
$file_origin='Cities.csv'; # scrivi qui il file da convertire
$file_path_origin=$path_origin.'/'.$file_origin;

# pendiamo il match del nome del file in modo da generare 
# lo stesso nome di file ma con estensione diversa
# si poteva usare l'explode 
preg_match("/^(.+)\.csv$/i", $file_path_origin, $match_csv);

# $fp file csv da leggere
# $fp2 file csv da scrivere
$fp_r = fopen($file_path_origin, 'r');

$count=0;   //conta le righe
$nfile=0;  //numera file

while ($row = fgets($fp_r)) {
    if ($count==0) {
        $ret = is_dir("{$match_csv[1]}") || mkdir("{$match_csv[1]}");
        $foutput = "$match_csv[1]/temp.csv"; 
        $fp_w = fopen($foutput, 'w');
    }
    fputs($fp_w, $row);
    $count++;
    if ($count==MAX_ROWS OR feof($fp_r)) {
        echo "\ninizio scrittura file php";
        fclose($fp_w);
        INCLUDE ("ConvertiCsvToArray.php");
        echo "\nfine scrittura file php";
        unlink("{$foutput}");
        echo "\neliminazione file csv temporaneo";
        $nfile++;
        echo "\nnfile ={$nfile}       count={$count}";
        $count=0;
    }
}
fclose($fp_r);
