<body>
    <form method="post" action="<?php echo $postUrl; ?>" id="sendcpg">
    	<input type="hidden" value="<?php echo $order_id; ?>" name="order_id">
    </form>
    <script language="javascript">
    	document.getElementById('sendcpg').submit();
    </script>
</body>