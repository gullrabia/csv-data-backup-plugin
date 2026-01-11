<?php



/**
 * plugin Name: Csv data Backup plugin 
 * Description: It will export table data into .csv file 
 * 
 * Version:           1.10.3
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Rabia Gull
 * Author URI:        https://author.example.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       csv-data-backup 
 * 
 *
 * 
 * 
 * 
 * 
 */


// Plugin menu in admin menu panel


// have to create a wordpress page   having a button in it that export backup 



// Export All table data into .csv file 






add_action("admin_menu", "create_admin_menu");

function create_admin_menu(){

    add_menu_page("Csv Data Backup Plugin", 
    "Csv Data Backup",
     "manage_options", 
     "csv-data-backup",
      "export_form",
     "dashicons-database-export", 
     20
    );

}

// Form Layout
function export_form(){
  ob_start();

  include_once plugin_dir_path(__FILE__) . "/template/table-data-form-backup.php";

  $layout = ob_get_contents();

 ob_end_clean();
 echo $layout;



}

add_action('admin_init', 'handle_form_export');

function handle_form_export()
{
    if (!isset($_POST['export_button'])) {
        return;
    }

    if (!current_user_can('manage_options')) {
        return;
    }

    global $wpdb;

    $table_name = $wpdb->prefix . 'students_data';

    // FIXED SQL QUERY
    $students = $wpdb->get_results(
        "SELECT * FROM {$table_name}",
        ARRAY_A
    );

    //Proper empty check
    if (empty($students)) {
        wp_die('No data found to export.');
    }

    $filename = 'students_data_' . time() . '.csv';

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename);

    $output = fopen('php://output', 'w');

    // CSV column headers
    fputcsv($output, array_keys($students[0]));

    // CSV rows
    foreach ($students as $row) {
        fputcsv($output, $row);
    }

    fclose($output);
    exit;
}


?>