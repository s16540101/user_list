
<div class='col-md-12 div-user-list'>
    <div class="modal" style='display:block;'>
        <div class="modal-dialog" style='width:80%'>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">使用者列表</h5>
                </div>
                <div class="modal-body">
                    <table class='table table-bordered'>
                        <thead>
                            <tr>
                                <th>使用者ID</th>
                                <th>帳號</th>
                                <th>姓名</th>
                                <th>性別</th>
                                <th>生日</th>
                                <th>電子信箱</th>
                                <th>備註</th>
                                <th>
                                    <input type='button' class='btn btn-primary btn-excel-upload btn-sm' value='EXCEL匯入'>
                                    <input type='button' class='btn btn-success btn-excel-output btn-sm' value='EXCEL匯出'>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?foreach($user_list as $key => $value):?>
                                <tr>
                                    <td><?=$value['user_id'];?></td>
                                    <td><?=$value['account'];?></td>
                                    <td><?=$value['name'];?></td>
                                    <td><?=$value['sex'] == 1 ? '男' : '女';?></td>
                                    <td><?=date('Y年m月d日s', strtotime($value['birthday']));?></td>
                                    <td><?=$value['email'];?></td>
                                    <td><?=$value['note'];?></td>
                                    <td>
                                        <input type='button' class='btn btn-primary btn-user-update btn-sm' value='重製密碼'>
                                        <input type='button' class='btn btn-danger btn-user-delete btn-sm' value='刪除'>
                                    </td>
                                </tr>
                            <?endforeach;?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script type='text/javascript'>
    var usr_manager = new User_manager();
    $(function(){
        
        $(".btn-user-update").on("click", function(){

        });
    });
</script>