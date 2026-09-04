<?php
# sostituisce ogni singolo elemento di $replace
# con l'elemento con lo stesso indice in $with

$replace = ["\n", "\r", "'", '"'];
$with = ['', '', '\\\'', '\\\''];

$file_path = "{$foutput}"; # scrivi qui il file da convertire

preg_match("/^(.+)\.csv$/i", $file_path, $match);

$foutput_php = "{$match[1]}_{$nfile}.php";

$fp_csv = fopen($file_path, 'r');
$fp2_php = fopen($foutput_php, 'w');

$row = fgets($fp_csv);

$indexes = isset($indexes) ? $indexes : explode(";", $row);

foreach ($indexes as &$index) {
    $index = str_replace($replace, $with, $index);
}

fputs($fp2_php, "<?php\n\t\$rows = \n\t[\n");
$count = 0;
while ($row = fgets($fp_csv)) {
    $values = explode(";", $row);

    $row = [];
    for ($i=0; $i<count($indexes); $i++)  {
        $row[$indexes[$i]]=str_replace($replace, $with, $values[$i]);
    }
    
    fputs($fp2_php, "\t\t[");

    foreach ($row as $index => $value) {
        fputs($fp2_php, "'{$index}' => '{$value}',");
    }

    fputs($fp2_php, "],\n");
    $count++;
    echo '.';
}

fclose($fp_csv);
fputs($fp2_php, "];");
fclose($fp2_php);






/*
[
[
"indice" => "valore",    "nazione_code" => 'IT'
"indice2" => "valore2"   "nazione" => 'ITALIA'
]
]
*/