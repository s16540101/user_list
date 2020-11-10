<div class='col-md-12 div-login'>
    <div class="modal" style='display:block'>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">帳號註冊</h5>
                </div>
                <div class="modal-body">
                    <input type='text' class='form-control' placeholder='帳號' name='uesr_account'>
                    <p></p>
                    <input type='password' class='form-control' placeholder='密碼' name='user_passwd'>
                    <p></p>
                    <input type='text' class='form-control' placeholder='姓名' name='user_name'>
                    <p></p>
                    <select class='form-control' name='user_sex'>
                        <option value="<?=USER_SEX_MAN?>">男</option>
                        <option value="<?=USER_SEX_WOMAN?>">女</option>
                    </select>
                    <p></p>
                    <input type='date' class='form-control' placeholder='生日' name='user_birthday'>
                    <p></p>
                    <input type='text' class='form-control' placeholder='信箱' name='user_email'>
                    <p></p>
                    <textarea class='form-control' name='user_note' placeholder='備註'></textarea>
                    <p></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" onclick="location.href='<?=base_url()?>'">取消</button>
                    <button type="button" class="btn btn-primary" onclick='register_user()'>註冊</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script type='text/javascript'>
    
    function register_user(){
        let user_manager = new User_manager();
        let user_data = {
            account:$("input[name='uesr_account']").val(),
            password:$("input[name='user_passwd']").val(),
            user_name:$("input[name='user_name']").val(),
            user_sex:$("select[name='user_sex']").val(),
            user_birthday:$("input[name='user_birthday']").val(),
            user_email:$("input[name='user_email']").val(),
            user_note:$("textarea[name='user_note']").val(),
        }
        confirm_message('註冊帳號', "是否確定註冊新帳號?", null, function(res){
            if(res){
                user_manager.user_insert(user_data, function(res){
                    if(res.status == 1){
                        normal_message("新增使用者", "已新增完成", function(){
                            location.href=base_url();
                        });
                    }
                    else{
                        error_message(res.error_message);
                    }
                });
            }
        })
    }
</script>