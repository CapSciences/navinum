<div class="dm_box  f_left <?php echo $category['class'] ?>" >
  <div class="content-box">
    <div class="content-box-header">
      <h3><?php echo $category['label'] ?></h3>
    </div>
    <div class="dashboard">
      <ul>
        <?php foreach ($category['items'] as $key => $item): ?>
          <?php if (mpDashboardReality::checkUserAccess($item, $sf_user)): ?>
        <li><a href="<?php echo url_for($item['url']) ?>" title="<?php echo $item['label'] ?>">
                <?php echo image_tag($item['image'], array('alt' => __($item['label']))); ?><br><?php echo __($item['label']); ?>
                <?php  if (isset($item['badge'])) :  ?>
            <span><?php echo $item['badge'] ?></span>
                <?php endif; ?>
          </a>
        </li>
          <?php endif; ?>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
</div>
