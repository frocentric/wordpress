<tbody>
    <tr id="row_ninja_forms[<?php echo $data[ 'name' ]; ?>]" class="row-ninja-forms--<?php echo $data[ 'name' ]; ?>">
        <th scope="row">
            <label for="ninja_forms[<?php echo $data[ 'name' ]; ?>]"><?php echo $data[ 'label' ]; ?></label>
        </th>
        <td>

            <?php $view->get_part( 'setting-' . $data[ 'type' ], $data ); ?>

            <?php if( isset( $data[ 'desc' ] ) ): ?>
                <p class='description'><?php echo $data[ 'desc' ]; ?></p>
            <?php endif; ?>
        </td>
    </tr>
</tbody>