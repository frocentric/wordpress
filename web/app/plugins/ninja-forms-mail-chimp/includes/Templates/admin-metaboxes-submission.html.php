<ul>
    <?php foreach( $data as $label => $value ):?>
    <li>
        <strong><?php echo $label; ?></strong>
        <br /><?php echo $value; ?>
    </li>
    <?php endforeach; ?>
</ul>