
<div id="sf_admin_container">

<?php if ($sf_user->hasFlash('notice')): ?>
  <div class="notice"><?php echo __($sf_user->getFlash('notice'), array(), 'sf_admin') ?></div>
<?php endif; ?>

<?php if ($sf_user->hasFlash('error')): ?>
  <div class="error"><?php echo __($sf_user->getFlash('error'), array(), 'sf_admin') ?></div>
<?php endif; ?>


    <div class="title">
				<h1>Import Csv Rfid</h1>
    </div>
        <div class="content-box">
            <div class="content-box-header"></div>
            <div id="sf_admin_header"></div>

            <div id="sf_admin_content">
				<form enctype="multipart/form-data" action="<?php echo url_for('rfid/batchImport') ?>" method="POST">
				  <table>
				    <?php echo $form ?>
				    <tr>
				      <td colspan="2">
				        <input type="submit" />
				      </td>
				    </tr>
				  </table>
				</form>
            </div>
		</div>
        <div id="sf_admin_footer"></div>
</div>


