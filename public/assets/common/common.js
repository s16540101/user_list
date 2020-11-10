var error_message = function (messages, callback) {
    if ((typeof(callback) == 'undefined') && (typeof(callback) !== "function")) {
        callback = function (result) {
        }
    }

    BootstrapDialog.show({
        type: BootstrapDialog.TYPE_DANGER,
        title: '錯誤訊息！',
        message: messages,
        buttons: [{
            label: '確定',
            action: function (dialogRef) {
                dialogRef.close();
                callback();
            }
        }],
        onhide: function() {
            if($('.modal:visible').length)
            {
                $('body').addClass('modal-open');
            }
        }
    });
};

var normal_message = function (title, messages, callback) {
    if ((typeof(callback) == 'undefined') && (typeof(callback) !== "function")) {
        callback = function (result) {
        }
    }

    BootstrapDialog.show({
        type: BootstrapDialog.TYPE_PRIMARY,
        title: title,
        message: messages,
        buttons: [{
            label: '確定',
            cssClass: 'btn btn-primary',
            action: function (dialogRef) {
                dialogRef.close();
                callback();
            }
        }],
        onhide: function() {
            callback();
            if($('.modal:visible').length)
            {
                $('body').addClass('modal-open');
            }
        }
    });
};

var confirm_message = function (title, messages, confirm_btn_label, callback) {
    if ((typeof(confirm_btn_label) == 'undefined') || (confirm_btn_label == null)) {
        confirm_btn_label = "<i class='icon-ok'></i>確定!";
    }

    if ((typeof(callback) == 'undefined') && (typeof(callback) !== "function")) {
        callback = function (result) {
        }
    }

    BootstrapDialog.confirm({
        title: title,
        message: messages,
        type: BootstrapDialog.TYPE_WARNING,
        closable: false,
        draggable: true,
        btnCancelLabel: "<i class='icon-cancel'></i>取消!",
        btnOKLabel: confirm_btn_label,
        callback: callback,
        onhide: function() {
            if($('.modal:visible').length)
            {
                $('body').addClass('modal-open');
            }
        }
    });
};