
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
                                <th style='width:10%'>
                                    <input type='button' class='btn btn-danger btn-select-delete-user' value='選取刪除'>
                                </th>
                                <th style='width:5%'>ID</th>
                                <th>帳號</th>
                                <th>姓名</th>
                                <th>性別</th>
                                <th>生日</th>
                                <th>電子信箱</th>
                                <th>備註</th>
                                <th>
                                    <input type='file' name='excel_file' style='display:none;'>
                                    <input type='button' class='btn btn-primary btn-excel-upload btn-sm' value='匯入' onclick='file_select()'>
                                    <input type='button' class='btn btn-success btn-excel-output btn-sm' value='匯出' onclick="location.href='<?=base_url("admin/outputUserExcel")?>'">
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?foreach($user_list as $key => $value):?>
                                <tr data-account='<?=$value['account']?>'>
                                    <td><input type='checkbox' class="checkbox-user"></td>
                                    <td><?=$value['user_id'];?></td>
                                    <td><?=$value['account'];?></td>
                                    <td><?=$value['name'];?></td>
                                    <td><?=$value['sex'] == 1 ? '男' : '女';?></td>
                                    <td><?=date('Y年m月d日', strtotime($value['birthday']));?></td>
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

        //EXCLE檔案上傳
        $("input[name='excel_file']").on("change", function(){
            let obj = this;
            confirm_message("匯入使用者", "是否確定匯入使用者?", null, function(res){
                if(res){
                    let check_file_ext = new Array(".xlsx", ".xls");
                    if(obj.files.length > 0){
                        let file = obj.files[0];
                        let file_ext = file.name.substring(file.name.lastIndexOf('.'));
                        if (check_file_ext.indexOf(file_ext) < 0) {
                            error_message("Excel上傳只支援.xlsx，.xls");
                            $(obj).val('');
                        }
                        else{
                            var form_data = new FormData();
                            form_data.append("excel_file", file);
                            $.ajax({
                                type:'post',
                                url:base_url() + 'admin/userImportExcel',
                                data:form_data,
                                dataType:'json',
                                processData: false, 
                                contentType: false,
                                error:function(){
                                    error_message("上傳失敗");
                                    $(obj).val('');
                                },
                                success:function(res){
                                    if(res.status == 1){
                                        normal_message('匯入使用者', "已匯入完成", function(){
                                            location.href=location.href;
                                        });
                                    }
                                    else{
                                        error_message(res.error_message);
                                    }
                                    $(obj).val('');
                                }
                            })       
                        }
                    }
                }
                else{
                    $(obj).val('');
                }
            });
        });

        $(".btn-select-delete-user").on("click", function(){
            let account = new Array();
            $("input.checkbox-user:checked").each(function(k){
                account[k] = $(this).parents("tr:first").data("account");
            });
            if(account.length > 0){
                confirm_message("刪除使用者", "是否確定刪除使用者?", null, function(res){
                    user_manager.batch_delete_account(account, function(res){
                        if(res.status == 1){
                            normal_message('刪除使用者', "已刪除完成", function(){
                                location.href=location.href;
                            });
                        }
                        else{
                            error_message(res.error_message);
                        }
                    });
                });
            }
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

    function file_select(){
        $("input[name='excel_file']").click();
    }
</script>