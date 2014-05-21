<?php d(@$exception); ?>

<h2>404 Missing page.</h2>

<p>
    If you think it's problem about routing, this url call controller is <b><?php echo $router->currentRouteAction(); ?></b>
    <br><br>
    If you see "<u>@missingMethod</u>", this mean router found controller but it don\'t found method.
</p>

<h3>Exception detail.</h3>
<table border="1">
    <tr>
        <td width="50px;">Cause: </td>
        <td><?php echo $exception->getMessage(); ?></td>
    </tr>
    <tr>
        <td>On file: </td>
        <td>
            Line: <?php echo $exception->getLine(); ?><br>
            File: <?php echo $exception->getFile(); ?>
        </td>
    </tr>
</table>