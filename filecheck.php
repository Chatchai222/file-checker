<?php

class DirectoryReader{

    public function __construct(){
        ;
    }

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
    private static $instance = null;
    private $latest_status_message = "stud message";

    private function __construct(){
        
    }

    public static function get_instance(){
        if(self::$instance == null){
            self::$instance = new FileChecker();
        }
        return self::$instance;
    }

    public function update_file_existence_csv_file($file_existence_csv_file_path){
        if($this->is_file_existence_csv_file_valid($file_existence_csv_file_path)){

            $csv_file = new CSVFile($file_existence_csv_file_path);
            $csv_file_data_rows = $csv_file->get_data_rows();
            $incoming_rows_to_overwrite = $this->get_updated_file_existence_data_rows($csv_file_data_rows);
            $csv_file->overwrite_data_rows($incoming_rows_to_overwrite);

            $this->latest_status_message = "Sucessfully update file existence in a csv file <br>";
        } else{
            $this->latest_status_message = "Error: update_file_existence has invalid csv file <br>";
        }
    }

    public function export_directory_to_csv($absolute_directory_path, $absolute_empty_csv_file_path){
        if(is_dir($absolute_directory_path) and file_exists($absolute_empty_csv_file_path)){
            $directory_reader = new DirectoryReader();
            $empty_csv_file = new CSVFile($absolute_empty_csv_file_path);

            
            $bunch_of_file_paths = $directory_reader->get_absolute_file_path_in_directory_and_subdirectory($absolute_directory_path);
            $data_rows_to_csv_file = $this->get_file_paths_as_data_rows($bunch_of_file_paths);

            $empty_csv_file->clear_all_rows();
            $empty_csv_file->append_row($this::FILE_EXISTENCE_HEADER);
            $empty_csv_file->append_rows($data_rows_to_csv_file);

            $this->latest_status_message = "Sucessfully exported directory file path to a csv file <br>";
        } else {
            $this->latest_status_message = "Error: Invalid directory or csv file path for exporting <br>";
        }
    }

    public function get_latest_status_message(){
        return $this->latest_status_message;
    }

    private function get_file_paths_as_data_rows($bunch_of_file_paths){
        $output = array();

        foreach($bunch_of_file_paths as $file_path){
            $data_row = array($file_path);
            array_push($output, $data_row);
        }

        return $output;
    }

    private function is_file_existence_csv_file_header_match($absolute_csv_file_path){
        $csv_file = new CSVFile($absolute_csv_file_path);
        $csv_file_header = $csv_file->get_header_row();
        return $this::FILE_EXISTENCE_HEADER === $csv_file_header;
    }

    private function is_file_existence_csv_file_valid($absolute_csv_file_path){
        if(file_exists($absolute_csv_file_path)){
            if($this->is_file_existence_csv_file_header_match($absolute_csv_file_path)){
                return true;
            }
        }
        return false;
    }

    private function get_updated_file_existence_single_data_row($single_file_existence_data_row){
        $output = $single_file_existence_data_row;
        $file_path = $output[0];
        if(file_exists($file_path)){
            $output[1] = "TRUE";
        } else {
            $output[1] = "FALSE";
        }
        return $output;
    }

    private function get_updated_file_existence_data_rows($bunch_of_file_existence_data_row){
        $output = array();
        foreach($bunch_of_file_existence_data_row as $data_row){
            $updated_row = $this->get_updated_file_existence_single_data_row($data_row);
            array_push($output, $updated_row);
        }
        return $output;
    }



}

// $file_existence_csv = "C:/xampp/htdocs/file-checker/file_existence.csv";
// $example_directory = "C:/xampp/htdocs/file-checker/house";

// $directory_reader = new DirectoryReader();
// $file_paths_in_example_directory = $directory_reader->get_absolute_file_path_in_directory_and_subdirectory($example_directory);
// foreach($file_paths_in_example_directory as $each_file_path){
//     echo "$each_file_path <br>";
// }
// $my_file_checker = FileChecker::get_instance();
// $my_file_checker->update_file_existence_csv_file("C:/xampp/htdocs/file-checker/file_existence.csv");
// $my_file_checker->export_directory_to_csv($example_directory, "C:/xampp/htdocs/file-checker/export_directory.csv");

?>