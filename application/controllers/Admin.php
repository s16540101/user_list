<?php
require_once "Base.php";
class Admin extends Base{
    function __construct(){
        parent::__construct();
        if($this->session->userdata('is_login') !== TRUE)
            header("Location:".base_url());
    }

    public function index(){
        $user_list = $this->user_class->searchUser()->result_array();
        $data['user_list'] = $user_list;
        $this->layout->view("user_list", $data);
    }

    public function Logout(){
        $this->session->sess_destroy();
    }

    public function userResetPassword(){
        $request_method = $this->input->server(array('REQUEST_METHOD'));
        $account = null;
        //處理 REQUEST_METHOD 為 put部分
        if($request_method['REQUEST_METHOD'] == 'PUT'){
            $put = array();
            parse_str(file_get_contents("php://input"), $put);
            $account = (isset($put['account'])) ? $put['account'] : null;
        }
        $update_info = array("password" => md5($account));
        $message = $this->user_class->userUpdateInfo($account, $update_info);
        echo json_encode($message);
    }

    public function userDelete(){
        $request_method = $this->input->server(array('REQUEST_METHOD'));
        $account = null;
        //處理 REQUEST_METHOD 為 put部分
        if($request_method['REQUEST_METHOD'] == 'DELETE'){
            $delete = array();
            parse_str(file_get_contents("php://input"), $delete);
            $account = (isset($delete['account'])) ? $delete['account'] : null;
        }

        $message = $this->user_class->userDelete($account);

        echo json_encode($message);
    }

    public function outputUserExcel(){
        $this->load->library("excel");
        $user_list = $this->user_class->searchUser()->result_array();
        $excel = array();
        array_push($excel, array(array("data" => "帳號"), array("data" => "姓名"), array("data" => '性別'), array("data" => "生日"), array("data" => 'Email'), array("data" => "備註")));
        foreach($user_list as $key => $value){
            $col = array(
                array("data" => $value['account']),
                array("data" => $value['name']),
                array("data" => ($value['sex'] == 1) ? '男' : '女'),
                array("data" => $value['birthday']),
                array("data" => $value['email']),
                array("data" => $value['note'])
            );

            array_push($excel, $col);
        }

        $this->excel->arrayToExcel($excel, 'user_list');
    }

    /**
     * 使用者EXCEL匯入
     */
    public function userImportExcel(){
        $message = array('status' => 1, 'error_message' => '');
        if(isset($_FILES['excel_file']) && count((array) $_FILES['excel_file']) > 0){
            $this->load->library("excel");
            $file_path = $_FILES['excel_file']['tmp_name'];
            $objWorksheet = $this->excel->getExcelObj($file_path);
            $excel_check_title = array("帳號", "密碼", "姓名", "性別", "生日", "Email", "備註");
            $highestRow = $objWorksheet->getHighestRow();
            $highestColumn = $objWorksheet->getHighestColumn();
            $headingsArray = $objWorksheet->rangeToArray('A1:'.$highestColumn.'1',null, true, true, true);

            for ($row = 1; $row <= $highestRow; $row++) {
                $dataRow = $objWorksheet->rangeToArray('A'.$row.':'.$highestColumn.$row,null, true, true, true);
                switch($row){
                    case 1:
                        $check_data_key = 0;
                        foreach($dataRow[$row] as $key => $value) {
                            if($excel_check_title[$check_data_key] != $value){
                                $message['status'] = 0;
                                $message['error_message'] = '請確認匯入EXCEL第一行是否依序為'.implode(',', $excel_check_title);
                                break;
                            } 
                            $check_data_key++;
                        }
                    break;
                    default:
                        $excel_user_info = array(
                            'account' => trim($dataRow[$row]['A']),
                            'password' => trim($dataRow[$row]['B']),
                            'name' => trim($dataRow[$row]['C']),
                            'sex' => trim($dataRow[$row]['D']),
                            'birthday' => trim($dataRow[$row]['E']),
                            'email' => trim($dataRow[$row]['F']),
                            'note' => trim($dataRow[$row]['G'])
                        );
                        if($excel_user_info['account']){
                            $user_account = $excel_user_info['account'];
                            $excel_user_info['password'] = md5($excel_user_info['password']);
                            $excel_user_info['sex'] = ($excel_user_info['sex'] == '男') ? 1 : 0;
                            $excel_user_info['birthday'] = date('Y-m-d', strtotime($excel_user_info['birthday']));

                            $search_user_info = $this->user_class->searchUser($user_account);
                            if($search_user_info->num_rows() <= 0){
                                $this->user_class->insertUser($excel_user_info);
                            }
                            else{
                                unset($excel_user_info['account']);
                                $this->user_class->userUpdateInfo($user_account, $excel_user_info);
                            }
                        }
                        else{
                            $message['status'] = 0;
                            $message['error_message'] = '第'.$row."行帳號未輸入";
                        }
                }

                //檢查TITLE順序標題不對中斷迴圈
                if($message['status'] == 0)
                    break;
            }
        }
        else{
            $message['status'] = 0;
            $message['error_message'] = '上傳檔案失敗';
        }
        echo json_encode($message);
    }
}
?>