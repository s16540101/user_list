<?
class Allautoload{
    /**
     * MyClass path
     */
    private $my_class_path = APPPATH.'libraries/MyClass/';
    /**
     * MyClass底下所有檔案
     */
    private $all_files = array();

    function __construct(){
        $this->_loadAllFile();
        $this->_includeAllClass();
    }

    private function _loadAllFile($load_path = null){
        $load_path = ($load_path == null) ? $this->my_class_path : $load_path;
        if(is_dir($load_path)){
            $dir_file = opendir($load_path);
            while($files = readdir($dir_file)){
                if($files == '.' || $files == '..')
                    continue;
                if(is_file($load_path.$files)){
                    array_push($this->all_files, $load_path.$files);
                }
                elseif(is_dir($load_path.$files)){
                    $this->_loadAllFile($load_path.$files.'/');
                }
            }
            closedir($dir_file);
        }
    }

    private function _includeAllClass(){
        foreach($this->all_files as $key => $value){
            require_once $value;
        }
    }
}