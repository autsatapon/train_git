<body>
<form method="post" action="<?php echo $submitURL; ?>" id="sendcpg">
	<input type="hidden" value="<?php echo $appId; ?>" name="app_id">
	<input type="hidden" value="<?php echo $xmlOrder; ?>" name="xml_order">
	<input type="hidden" value="<?php echo $chkSum; ?>" name="chkSum">
</form>
<script language="javascript">
	document.getElementById('sendcpg').submit();
</script>
</body>