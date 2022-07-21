<?php

class DirectoryReader{

    public function get_file_names_in_directory($directory_path){
        $files = scandir($directory_path); 
        $files = array_diff($files, array(".", "..")); 
        $files = array_values($files); 

        return $files;
    }

    public function get_absolute_file_path_in_directory_and_subdirectory($directory_path){
        $output = array();
        $file_names = $this->get_file_names_in_directory($directory_path);

        foreach($file_names as $file_name){

            $absolute_file_path = "$directory_path/$file_name";
            array_push($output, $absolute_file_path);

            if(is_dir($absolute_file_path)){
                
                $subdirectory_absolute_file_paths = $this->get_absolute_file_path_in_directory_and_subdirectory($absolute_file_path);
                $output = array_merge($output, $subdirectory_absolute_file_paths);

            } 
        }

        return $output;
    }

}

class CSVFile{

    private $absolute_file_path;

    public function __construct($in_absolute_file_path){
        $this->absolute_file_path = $in_absolute_file_path;
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

    public function overwrite_data_rows($in_data_rows){
        $this->clear_data_rows();
        $this->append_rows($in_data_rows);
    }

};

class FileChecker{

    const FILE_EXISTENCE_HEADER = array("file_path", "is_file_exist");

    public static function update_file_existence_csv_file($file_existence_csv_file_path){
        if(FileChecker::is_file_existence_csv_file_valid($file_existence_csv_file_path)){

            
            $csv_file = new CSVFile($file_existence_csv_file_path);
            $csv_file_data_rows = $csv_file->get_data_rows();
            $overwrite_updated_rows = array();

            foreach($csv_file_data_rows as $each_data_row){
                // $each_updated_row = $each_data_row;
                // $file_path = $each_data_row[0];
                // if(file_exists($file_path)){
                //     $each_data_row[1] = "TRUE";
                // } else {
                //     $each_data_row[1] = "FALSE";
                // }
                // array_push($overwrite_updated_rows, $each_updated_row);
                print_r($each_data_row);
            }

            
        } else{
            echo "Error: update_file_existence has invalid csv file <br>";
        }
    }

    public static function export_directory_to_csv($absolute_directory_path, $absolute_empty_csv_file_path){
        
    }

    private static function is_file_existence_csv_file_header_match($absolute_csv_file_path){
        $csv_file = new CSVFile($absolute_csv_file_path);
        $csv_file_header = $csv_file->get_header_row();
        return FileChecker::FILE_EXISTENCE_HEADER === $csv_file_header;
    }

    private static function is_file_existence_csv_file_valid($absolute_csv_file_path){
        if(file_exists($absolute_csv_file_path)){
            if(FileChecker::is_file_existence_csv_file_header_match($absolute_csv_file_path)){
                return true;
            }
        }
        return false;
    }

}


FileChecker::update_file_existence_csv_file($csv_allowed_files_file_path);
echo "<br>" . __FILE__ . " has runned <br>";


?>