<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    
    $files = Storage::disk('local')->files('csv');
    $data = [];
    foreach($files as $file){

        if(!Str::contains($file, ['Equip', 'Effect', 'Correct', 'Weapon', 'Protector', 'Goods', 'Magic', 'Bullet', 'Atk', 'Sword', 'Shop'])){
            // if file isn't one of these kinds, skip it
            continue;
        }
        
        $filepath = storage_path('app/'.$file);
        $filename = pathinfo($file, PATHINFO_FILENAME);
        $names_file = storage_path('app/Yapped-Rune-Bear_2_00/Paramdex/ER/Names/'.$filename.'.txt');

        dump($filename);
        
        $contents = file_get_contents($filepath);
        $rows_data = explode("\r\n", $contents);

        foreach( $rows_data as $i => $row_data){
            if($i === 0){
                // first row is headings 
                $data [$filename]['headings']= explode(";", $row_data);
                continue;
            }
            $row = explode(";", $row_data);
            // make sure is same size as headings
            // -- the \r\n on end adds an empty array item 
            array_pop($row);

            if(empty($row)){
                continue;
            }

            // use headings as keys
            $data [$filename]['rows'][] = array_combine($data [$filename]['headings'], $row);
        }
    
        if(file_exists($names_file)) {
            // get row names 
            $name_contents = file_get_contents($names_file);
            $name_rows = explode("\n", $name_contents);
            
            foreach ( $name_rows as $i => $name_row_data ) {
                $data [$filename]['names'][]= explode(" ", $name_row_data, 2);
            }

        } // endif names
        
        dump($data[$filename]);
//        dump(json_encode($data[$filename], JSON_PRETTY_PRINT));
    }

    return view('welcome');
});

