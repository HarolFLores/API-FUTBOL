<?php

$files = glob(__DIR__ . '/database/migrations/*.php');

foreach ($files as $file) {
    if (strpos($file, 'create_permission_tables') !== false) {
        $content = file_get_contents($file);
        $content = str_replace("bigIncrements('id')", "increments('id')", $content);
        $content = str_replace('unsignedBigInteger', 'unsignedInteger', $content);
        file_put_contents($file, $content);
        continue;
    }

    $content = file_get_contents($file);
    $content = str_replace('$table->id();', "\$table->increments('id');", $content);
    
    // Pattern: $table->foreignId('col_id')->constrained('table')->onDelete('cascade');
    $content = preg_replace_callback('/\$table->unsignedInteger\(\'([^\']+)\'\)(->nullable\(\))?\n +;\n +\$table->foreign\(\'[^\']+\'\)->references\(\'id\'\)(?:->on\(\'([^\']+)\'\))?(->cascadeOnDelete\(\))?;/', function($m) {
        // Just in case I need to rerun, but the previous script already modified it to unsignedInteger. 
        return $m[0];
    }, $content);
    
    // Actually, the previous regex already ran and converted the files incorrectly. 
    // I need to search and replace the incorrect table name `game_matchs` with `game_matches`.
    $content = str_replace("->on('game_matchs')", "->on('game_matches')", $content);
    
    // also `match_results` was handled as `match_results`? Wait: `match_result_id` -> `match_results` because `str_replace('_id', 's')` = `match_results`. But game_match_id -> game_matchs.
    
    file_put_contents($file, $content);
}

echo "Fixed game_matches table names.\n";
