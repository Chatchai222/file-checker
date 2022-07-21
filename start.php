<?php

require "filecheck.php";

function print_post(){
    echo "Printing _POST <br>";
    foreach($_POST as $index => $item){
        echo "$index -- $item <br>";
    }
}

print_post();

if(isset($_POST["update_file_existence_in_csv"])){
    echo "About to update file existence in CSV <br>";
    $absolute_csv_file_path = $_POST["csv_file_path_entry_bar"];

}

if(isset($_POST["export_directory_file_paths_to_empty_csv"])){
    echo "About to export directory file paths to CSV <br>";
    $absolute_directory_path = $_POST["directory_path_entry_bar"];
    $absolute_csv_file_path = $_POST["csv_file_path_entry_bar"];

}

?>



<html>
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Start Page</title>
        <style>
            input {
                width: 300px;
            }
        </style>
    </head>
    <body>

        <h1>Start Page</h1>
        <p>This is a page for checking if a file exist in the allowed_files</p>
        
        <form method="post" action="">

            <label for="directory_path">Directory path: </label>
            <input type="text" id="directory_path_entry_bar" name="directory_path_entry_bar">
            <br>

            <label for="csv_file_path">CSV file path: </label>
            <input type="text" id="csv_file_path_entry_bar" name="csv_file_path_entry_bar">
            <br>

            <input type="submit" name="update_file_existence_in_csv" value="Update file existence in CSV">
            <input type="submit" name="export_directory_file_paths_to_empty_csv" value="Export directory file paths to empty CSV">
        
        </form>

    </body>
</html>
