<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Pcms commands</title>
</head>
<body>
	<p style="color:red">{{ $error }}</p>

	<form action="/command" method="post">
		Cron: <input type="text" name="cron" value="" placeholder="* * * * *"/>
		Name: <input type="test" name="command" value=""/>
		<input type="submit" value="Add"/>
	</form>

	<h1>{{ date(DATE_RFC2822); }}</h1>
	<table>
		<tr>
			<th>Cron</th>
			<th>Command</th>
		</tr>

		<?php foreach ($commands as $command) : ?>
		    <?php if ($command->is_due) : ?>
			<tr style="color:green;">
			<?php else : ?>
			<tr style="color:red;">
			<?php endif; ?>

		    	<td><?php echo $command->cron; ?></td>
		    	<td><?php echo $command->name; ?></td>
		    </tr>
		<?php endforeach; ?>

	</table>


</body>
</html>