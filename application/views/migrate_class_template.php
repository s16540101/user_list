defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_<?= $class_name;?> extends CI_Migration {

    /**
    * 資料表名稱
    *
    * @var string
    */
    protected $table = "";

    /**
    * 當資料表不存在時建立
    *
    * @var boolean
    */
    protected $if_not_exists = true;

    /**
    * 建立資料表參數
    *
    * @var boolean
    */
    protected $attributes  = array(
        'ENGINE' => 'InnoDB',
        'CHARSET' => 'utf8',
        'COLLATE' => 'utf8_unicode_ci'
    );

    /**
    * migration更新主執行點
    */
    public function up()
    {

    }

    /**
    * migration還原執行點 (不使用)
    */
    public function down()
    {

    }

    /**
    *   建立資料表與欄位
    *
    * @param string $table_name 資料表名稱
    * @param string $primary_key 主鍵名稱
    * @param array  $fields 欄位(請參照註解欄位格式)
    *
    *       array(
    *           'filed_name' => array(
    *               'type' => 'INT | VARCHAR | TEXT | etc.',
    *               'constraint' => 長度,
    *               'default' => value,
    *               'unsigned' => TRUE | FALSE,
    *               'auto_increment' => TRUE | FALSE,
    *               'null' => TRUE | FALSE,
    *               'comment' => '註解'
    *           ),
    *       )
    *
    */
    private function _create_table($table_name, $primary_key, $fields){
        /***********************************
        $fields = array(
            'id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'auto_increment' => TRUE
            )
        );
        ************************************/

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key($primary_key, TRUE); // primary key(注意id要改)
        $this->dbforge->create_table($table_name, $this->if_not_exists, $this->attributes);
    }

    /**
    *   在資料表新增欄位
    *
    * @param string $table_name 資料表名稱
    * @param array $fields 欄位(請參照註解欄位格式)
    *
    *   array(
    *       'fields_name' => array(
    *           'type' => 'INT | VARCHAR | TEXT | etc.',
    *           'constraint' => 長度,
    *           'default' => value,
    *           'null' => TRUE | FALSE,
    *           'after' => 'another_field',
    *       )
    *   )
    */
    private function _add_fields($table_name, $fields){
        /******************
        $fields  = array(

        );
        ******************/

        $this->dbforge->add_column($table_name, $fields);
    }

    /**
    *   在資料表修改欄位
    *
    * @param string $table_name 資料表名稱
    * @param array $fields 欄位(請參照註解欄位格式)
    *
    *
    *   array(
    *       'old_name' => array(
    *           'name' => 'new_name',
    *           'type' => 'TEXT',
    *       )
    *   )
    *
    */
    private function _modify_fields($table_name, $fields){
        /****************
        $fields  = array(

        );
        ****************/
        $this->dbforge->modify_column($table_name, $fields);
    }

    /**
    *   刪除資料表(慎用)
    *
    * @param $table_name 資料表名稱
    */
    private function _drop_table($table_name){
        $this->dbforge->drop_table($table_name);
    }
}
