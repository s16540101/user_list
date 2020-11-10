
<div class='col-md-12 div-login'>
    <div class="modal" style='display:block'>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">登入</h5>
                </div>
                <div class="modal-body">
                    <input type='text' class='form-control' placeholder='帳號' name='uesr_account'>
                    <p></p>
                    <input type='password' class='form-control' placeholder='密碼' name='user_passwd'>
                    <p></p>
                    <span class='float-right'>
                        <a href='<?=base_url('home/register')?>'>註冊帳號</a>
                    </span>        
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-login">登入</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script type='text/javascript'>
    $(function(){
        $(".btn-login").on("click", function(){
            let user = new User_manager();
            let account = $("input[name='uesr_account']").val();
            let password = $("input[name='user_passwd']").val();
            user.user_login(account, password, function(res){
                if(res.status == 1){
                    location.href = base_url() + 'admin';
                }
                else{
                    error_message(res.error_message);
                }
            });
        })
    });
</script>
