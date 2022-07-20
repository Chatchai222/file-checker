const export_directory_to_csv_button = document.getElementById("export_directory_to_csv_button");
const update_file_existence_in_csv_button = document.getElementById("update_file_existence_in_csv_button");
const directory_path_entry_bar = document.getElementById("directory_path_entry_bar");
const csv_file_path_entry_bar = document.getElementById("csv_file_path_entry_bar");

function click_export_directory_to_csv_button(){
    console.log("Clicked on export_directory_to_csv button");
    console.log(get_directory_path_entry_bar_value());
    console.log(get_csv_file_path_entry_bar_value());
}

function click_update_file_existence_in_csv_button(){
    console.log("Clicked on update_file_existence_in_csv button");
}

function get_directory_path_entry_bar_value(){
    return directory_path_entry_bar.value;
}

function get_csv_file_path_entry_bar_value(){
    return csv_file_path_entry_bar.value;
}

