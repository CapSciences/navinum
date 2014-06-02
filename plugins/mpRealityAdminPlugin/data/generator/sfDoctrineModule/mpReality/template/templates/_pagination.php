<div class="sf_admin_pagination">
  <a class="first button_nav" href="[?php echo url_for('@<?php echo $this->getUrlForAction('list') ?>') ?]?page=1"></a>

  <a class="previous button_nav" href="[?php echo url_for('@<?php echo $this->getUrlForAction('list') ?>') ?]?page=[?php echo $pager->getPreviousPage() ?]"></a>

  [?php foreach ($pager->getLinks() as $page): ?]
    [?php if ($page == $pager->getPage()): ?]
      <span class="graybutton pagelink active">[?php echo $page ?]</span>
    [?php else: ?]
      <a class="graybutton pagelink" href="[?php echo url_for('@<?php echo $this->getUrlForAction('list') ?>') ?]?page=[?php echo $page ?]">[?php echo $page ?]</a>
    [?php endif; ?]
  [?php endforeach; ?]

  <a class="next button_nav" href="[?php echo url_for('@<?php echo $this->getUrlForAction('list') ?>') ?]?page=[?php echo $pager->getNextPage() ?]"></a>

  <a class="last button_nav" href="[?php echo url_for('@<?php echo $this->getUrlForAction('list') ?>') ?]?page=[?php echo $pager->getLastPage() ?]"></a>
</div>

