<?php

class Migrate extends CI_Controller
{
    /**
	 * Migration Path
	 *
	 * @var string
	 */
	protected $migration_path = '';

    public function __construct($is_run = null)
    {
        parent::__construct();
        // if (PHP_SAPI !== 'cli') {
        //     if (!in_array(session("user_account"), array("admin", "superadmin")))
        //         exit('No web access allowed');
        // }

        $this->migration_path = APPPATH . "migrations/";

        if(!is_dir($this->migration_path)) {
            mkdir($this->migration_path);
        }

        $this->load->library('migration');
    }

    /**
    *  產生Migration 模板
    */
    public function generate_migration_file(){
        $this->show_cli_message('產生migration中...', 'NOTE');
        echo '請輸入migration檔案名稱(請勿重複): ';
        $name = trim(fgets(STDIN));

        if(is_null($name) || $name == ""){
            $this->show_cli_message('請輸入檔案名稱。', 'FAILURE');
            return;
        }

        if(!preg_match('/^[a-z0-9_]+$/i', $name)){
            $this->show_cli_message('不符合命名規則。', 'FAILURE');
            return;
        }

        $filename = date('YmdHis') . "_" . $name . ".php";
        $file_path = $this->migration_path . $filename;

        if(file_exists($file_path)){
            $this->show_cli_message('檔案名稱已存在。', 'FAILURE');
            return;
        }

        $data = array();
        $data['class_name'] = $name;

        $template = $this->load->view('migrate/migrate_class_template', $data, true);

        $file = fopen($file_path, 'w');
        $content = "<?php\n" . $template ."?>";
        fwrite($file, $content);
        fclose($file);
        $this->show_cli_message('檔案已建立:' . $file_path , 'SUCCESS');
    }

    /**
    *  將Migration 更新至指定時間戳記號
    */
    public function version($version = null){
        if(is_null($version)){
            $this->show_cli_message('請輸入時間戳記號。', 'FAILURE');
            return;
        }
        $migration = $this->migration->version($version);
        if(!$migration) {
            $this->show_cli_message($this->migration->error_string(), 'FAILURE');
        } else {
            $this->show_cli_message('Migration更新至 ' . $version , 'SUCCESS');
        }
    }

    /**
    *  將Migration 更新至最新版本
    */
    public function latest() {
        $this->migrations_log();
        $update_data = $this->migration->latest();
        if($update_data['data'] == false){
        }
        else{
            $this->show_cli_message('Migration 已經更新到最後一個版本。', 'SUCCESS');
            foreach($update_data['update_number'] as $key => $value){
                $this->db->where("version", $value);
                $this->db->set("is_run", 1);
                $this->db->set('is_run_error', 0);
                $this->db->set("is_delete", 0);
                $this->db->set("update_time", date("Y-m-d H:i:s"));
                $this->db->update("migrations_log");
            }
        }
    }

    /**
     * 補跑沒跑過的migrations
     * 請先執行完 lastest() 之後再執行此程式 比較沒問題
     * 執行前須考慮刪除欄位的問題
     */
    public function migrations_supplement_no_run(){
        $this->db->from("migrations_log");
        $this->db->where("is_run", 0);
        $this->db->where("is_delete", 0);
        $this->db->order_by("id");
        $all_no_run_migrations = $this->db->get()->result_array();
        foreach($all_no_run_migrations as $key => $value){
            $message = $this->migration->run_one_version($value['version']);
            $this->db->where("version", $value['version']);
            $this->db->set("is_run", 1);
            $this->db->set('is_run_error', 0);
            $this->db->set("is_delete", 0);
            $this->db->set("update_time", date("Y-m-d H:i:s"));
            $this->db->update("migrations_log");
        }
    }

    public function migrations_log(){
        $this->_check_database_table();
        $files = $this->migration->find_migrations();
        foreach($files as $key => $value){
            $version_status = $this->db->get_where("migrations_log", array("version" => $key));
            if($version_status->num_rows() <= 0){
                $this->db->set("version", $key);
                $this->db->set("is_run", 0);
                $this->db->set('is_run_error', 0);
                $this->db->set("is_delete", 0);
                $this->db->set("update_time", date("Y-m-d H:i:s"));
                $this->db->insert("migrations_log");
            }
        }
    }

    private function _check_database_table()
    {
        if ($this->db->table_exists('migrations_log')) {
            return;
        }

        $fields = array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ),
            'version' => array(
                'type' => 'VARCHAR',
                'constraint' => 128,
                'null' => false,
            ),

            'is_run' => array(
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0
            ),
            'is_run_error' => array(
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0
            ),
            'is_delete' => array(
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0
            ),
            'update_time' => array(
                'type' => 'DATETIME',
                'default' => '0000-00-00 00:00:00'
            )
        );

        $this->load->dbforge();
        $attributes = array('ENGINE' => 'InnoDB');
        $this->dbforge->add_key('id', true);
        $this->dbforge->add_key('versioin');
        $this->dbforge->add_field($fields);
        $this->dbforge->create_table('migrations_log', true, $attributes);
    }

    /**
     * @param string $message
     * @param string $status SUCCESS, FAILURE, WARNING, NOTE
     *
     * @throws Exception
     */
    function show_cli_message($message, $status='NOTE')
    {
        $iscli           = (bool)(PHP_SAPI == 'cli');
        $color_start_str = "";
        switch ($status) {
            case "SUCCESS":
                $color_start_str = ($iscli) ? chr(27) . "[0;32m" : "<span style='color:green;'>"; //Green background
                break;
            case "FAILURE":
                $color_start_str = ($iscli) ? chr(27) . "[0;31m" : "<span style='color:red;'>"; //Red background
                break;
            case "WARNING":
                $color_start_str = ($iscli) ? chr(27) . "[1;33m" : "<span style='color:yellow;'>"; //Yellow background
                break;
            case "NOTE":
                $color_start_str = ($iscli) ? chr(27) . "[1;34m" : "<span style='color:blue;'>"; //Blue background
                break;
            default:
                throw new Exception("Invalid status: " . $status);
        }

        $color_end_str = ($iscli) ? chr(27) . "[0m" : "</span>";

        echo "[" . date('Y-m-d H:i:s') . "] " . $color_start_str . $message . $color_end_str . ($iscli ? "\n" : '<br>');
    }
}
