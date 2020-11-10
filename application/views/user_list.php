
<div class='col-md-12 div-user-list'>
    <div class="modal" style='display:block;'>
        <div class="modal-dialog" style='width:80%'>
            <div class="modal-content">
                <div class="modal-header">
                    <a class='logout pull-right' href='javascript:logout()'>登出</a>
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
                                <tr data-account='<?=$value['account']?>'>
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
    var user_manager = new User_manager();
    $(function(){
        //重製密碼
        $(".btn-user-update").on("click", function(){
            let account = $(this).parents("tr:first").attr('data-account');
            confirm_message('更新提醒', '是否確定要重製密碼?', null, function(res){
                if(res){
                    user_manager.user_reset({'account':account}, function(res){
                        if(res.status == 1){
                            normal_message("重製密碼", "已重製密碼完成(密碼與帳號一樣)");
                        }
                        else{
                            error_message(res.error_message);
                        }
                    });
                }
            });
        });
        //刪除使用者
        $(".btn-user-delete").on("click", function(){
            let obj = this;
            let account = $(this).parents("tr:first").attr('data-account');
            confirm_message('更新提醒', '是否確定要刪除該使用者?', null, function(res){
                if(res){
                    user_manager.user_delete(account, function(res){
                        if(res.status == 1){
                            $(obj).parents("tr:first").remove();
                        }
                        else{
                            error_message(res.error_message);
                        }
                    });
                }
            });
        });
    });

    function logout(){
        $.ajax({
            type:'post',
            url:base_url() + 'admin/logout',
            error:function(){
                error_message("登出錯誤");
            },
            success:function(res){
                location.href=location.href; 
            }
        });
    }
</script>