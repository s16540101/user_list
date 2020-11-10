var User_manager = function(){
    var user_default = {account:null, password:null, user_name:null, user_sex:null, user_birthday:null, user_email:null, user_note:null}
    this.user_insert = function(user_register_option, callback){
        let insert_data = $.extend(user_default, user_register_option);
        let status = this._check_save(insert_data)
        if(status.status == 0){
            let error_str = '';
            for(let i in status.error_message){
                error_str += status.error_message[i] + "<br/>";
            }
            error_message(error_str);
        }
        else{
            $.ajax({
                type:'post',
                url:base_url() + 'home/userInsert',
                dataType:'json',
                data:insert_data,
                error:function(res){
                    error_message("發生未知錯誤");
                },
                success:function(res){
                    if(typeof(callback) == 'function'){
                        callback(res);
                    }
                }
            });
        }
        
    },
    this.user_reset = function(user_update_option, callback){
        let update_data = $.extend(user_default, user_update_option);
        if(!update_data.account){
            error_message("無法確定帳號");
            return false;
        }
        $.ajax({
            type:'put',
            url:base_url() + 'admin/userResetPassword',
            dataType:'json',
            data:update_data,
            error:function(){
                error_message("發生未知錯誤");
            },
            success:function(res){
                if(typeof(callback) == 'function'){
                    callback(res);
                }
            }
        });
    },

    this.user_search = function(){

    },

    this.user_delete = function(account, callback){
        if(!account){
            error_message("無法確定帳號");
            return false;
        }
        $.ajax({
            type:'delete',
            url:base_url() + 'admin/userDelete',
            dataType:'json',
            data:{'account' : account},
            error:function(){
                error_message("發生未知錯誤");
            },
            success:function(res){
                if(typeof(callback) == 'function'){
                    callback(res);
                }
            }
        });
    },
    this.user_login = function(account, password, callback){
        if(!account || !password){
            error_message("請正確輸入帳號或密碼");
        }
        else{
            $.ajax({
                type:"post",
                url:base_url() + 'home/Login',
                data:{account:account, password:password},
                dataType:'json',
                error:function(){
                    error_message("登入發生未知錯誤");
                },
                success:function(res){
                    if(typeof(callback) == 'function'){
                        callback(res);
                    }
                }
            })
        }
    },
    this._check_save = function(check_data){
        let account_rule = /^([a-zA-Z]+\d+|\d+[a-zA-Z]+)[a-zA-Z0-9]*$/;
        let email_rule = /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z]+$/;
        let check_message = {status:1, error_message:[]};
        if(!check_data.account){
            check_message.status = 0;
            check_message.error_message.push('帳號未填');
        }
        else if(!account_rule.test(check_data.account)){
            check_message.status = 0;
            check_message.error_message.push('帳號請用英文加數字');
        }
        if(!check_data.password){
            check_message.status = 0;
            check_message.error_message.push('密碼未填');
        }
        if(!check_data.user_name){
            check_message.status = 0;
            check_message.error_message.push('姓名未填');
        }

        if(!check_data.user_email){
            check_message.status = 0;
            check_message.error_message.push('EMAIL未填');
        }
        else{
            if(!email_rule.test(check_data.user_email)){
                check_message.status = 0;
                check_message.error_message.push('EMAIL格式錯誤');
            }
        }
        return check_message;
    }
}