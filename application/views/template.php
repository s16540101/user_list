<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="<?= base_url('assets/bootstrap/css/bootstrap.min.css'); ?>">
	<link rel="stylesheet" type="text/css" href="<?= base_url('assets/bootstrap_dialog/css/bootstrap-dialog.min.css'); ?>">
	<script type="text/javascript" src="<?= base_url('assets/jquery/core/jquery-1.11.3.min.js'); ?>"></script>
	<script type="text/javascript" src="<?= base_url('assets/bootstrap/js/bootstrap.min.js'); ?>"></script>
	<script type="text/javascript" src="<?= base_url('assets/bootstrap_dialog/js/bootstrap-dialog.min.js'); ?>"></script>
	<script type="text/javascript" src="<?= base_url('assets/common/common.js'); ?>"></script>
	<script type="text/javascript" src="<?= base_url('assets/user/js/user.js'); ?>"></script>
	<script type='text/javascript'> 
		var base_url = function(){return '<?=base_url()?>'} 
	</script>
</head>
<body>
	<div class='container'><?=$content?></div>
</body>
</html>