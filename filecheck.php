<?php

class DirectoryChecker{
    
    public static function get_file_names_in_directory($absolute_directory_path){
        $files = scandir($absolute_directory_path); // Get all the filename inside directory
        $files = array_diff($files, array(".", "..")); // Remove . and .. from filename
        $files = array_values($files); // Reset key index to normal array

        return $files;
    }

    public static function get_absolute_file_paths_in_directory_and_subdirectory($absolute_directory_path){
        $output = array();
        $file_names = DirectoryChecker::get_file_names_in_directory($absolute_directory_path);

        foreach($file_names as $file_name){

            $absolute_file_path = "$absolute_directory_path/$file_name";
            array_push($output, $absolute_file_path);

            if(is_dir($absolute_file_path)){
                
                $subdirectory_absolute_file_paths = DirectoryChecker::get_absolute_file_paths_in_directory_and_subdirectory($absolute_file_path);
                $output = array_merge($output, $subdirectory_absolute_file_paths);

            } 
        }

        return $output;
    }

};

class CSVFile{

    private $absolute_file_path;

    public function __construct($in_absolute_file_path){
        if(file_exists($in_absolute_file_path)){
            $this->absolute_file_path = $in_absolute_file_path;
        } else {
            throw new Exception("File not found");
        } 
    }

    public function append_row($in_row){
        $file_stream = fopen($this->absolute_file_path, "a");
        fputcsv($file_stream, $in_row);
        fclose($file_stream);
    }

    public function append_rows($in_rows){
        foreach($in_rows as $row){
            $this->append_row($row);
        }
    }

    public function get_header_row(){
        $all_rows = $this->get_all_rows();
        $header_row = array_shift($all_rows);
        return $header_row;
    }

    public function get_data_rows(){
        $all_rows = $this->get_all_rows();
        array_shift($all_rows);
        return $all_rows;
    }

    public function get_all_rows(){
        $output = array();
        $file_stream = fopen($this->absolute_file_path, "r");
        while(($row = fgetcsv($file_stream)) !== FALSE){
            array_push($output, $row);
        }
        fclose($file_stream);
        return $output;
    }

    public function clear_all_rows(){
        file_put_contents($this->absolute_file_path, "");
    }

    public function clear_data_rows(){
        $header_row = $this->get_header_row();
        $this->clear_all_rows();
        $this->append_row($header_row);
    }

    public function is_file_empty(){
        return file_get_contents($this->absolute_file_path) === "";
    }

};

class FileChecker{

    const CSV_FILE_EXISTENCE_HEADER = array("file_path","is_file_exist");

    public static function update_files_existence($csv_file_path){
        try{
            $csv_file = new CSVFile($csv_file_path);
            $csv_rows = $csv_file->get_data_rows();
            $updated_rows = FileChecker::get_updated_csv_rows_file_path_and_existence($csv_rows);
            $csv_file->clear_data_rows();
            $csv_file->append_rows($updated_rows);
        } catch (Exception $e){
            echo "Error: Exception $e->getMessage() <br>";
        }

    }

    private static function get_csv_row_of_file_path_and_existence($absolute_file_path){
        if(file_exists($absolute_file_path)){
            return array($absolute_file_path, "TRUE");
        } else {
            return array($absolute_file_path, "FALSE");
        }
    }

    private static function get_updated_csv_rows_file_path_and_existence($csv_rows){
        $bunch_of_updated_rows = array();
        foreach($csv_rows as $row){
            $file_path = $row[0];
            $updated_row = FileChecker::get_csv_row_of_file_path_and_existence($file_path);
            array_push($bunch_of_updated_rows, $updated_row);
        }
        return $bunch_of_updated_rows;
    }

    private static function is_files_existence_csv_file_valid($csv_file_path){
        if(file_exists($csv_file_path)){
            $csv_file = new CSVFile($csv_file_path);
            if($csv_file->get_header_row() === FileChecker::CSV_FILE_EXISTENCE_HEADER){
                return true;
            }
        }
        return false;
    }

    public static function export_directory_to_csv($absolute_directory_path, $empty_csv_file_path){
        try{
            $csv_file = new CSVFile($empty_csv_file_path);
            $csv_file->clear_all_rows();
            $file_paths_in_directory = DirectoryChecker::get_absolute_file_paths_in_directory_and_subdirectory($absolute_directory_path);
            $file_paths_as_row = array();
            foreach($file_paths_in_directory as $file_path){
                $row = array($file_path);
                array_push($file_paths_as_row,$row);
            }
    
            $csv_file->append_row(FileChecker::CSV_FILE_EXISTENCE_HEADER);
            $csv_file->append_rows($file_paths_as_row);
        } catch (Exception $e){
            echo "$e->getMessage() <br>";
        }

    }

}

$absolute_directory_path = "C:/xampp/htdocs/file-checker/house";
$csv_allowed_files_file_path = "C:/xampp/htdocs/file-checker/allowed_files.csv";

$wordpress_directory_path = "C:/xampp/htdocs/file-checker/wordpress";
$csv_export_file_path = "C:/xampp/htdocs/file-checker/export_directory.csv";



