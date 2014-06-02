<div class="sf_admin_list">
  [?php if (!$pager->getNbResults()): ?]
  <p>[?php echo __('No result', array(), 'sf_admin') ?]</p>
  [?php else: ?]
  <div class="paginate_top">
       <div class="sf_admin_actions f_left">
                        [?php include_partial('<?php echo $this->getModuleName() ?>/list_batch_actions', array('helper' => $helper)) ?]
                       
                    </div>
    
  [?php if ($pager->haveToPaginate()): ?]
    [?php include_partial('<?php echo $this->getModuleName() ?>/pagination', array('pager' => $pager)) ?]
 [?php endif; ?]
  </div>
  <table cellspacing="0">
    <thead>
      <tr>
        <?php if ($this->configuration->getValue('list.batch_actions')): ?>
          <th id="sf_admin_list_batch_actions">
            <input type="checkbox" id="sf_admin_list_batch_checkbox"  onclick="checkAll();" />
            <label class="" for="sf_admin_list_batch_checkbox"></label>
          </th>
        <?php endif; ?>
          [?php include_partial('<?php echo $this->getModuleName() ?>/list_th_<?php echo $this->configuration->getValue('list.layout') ?>', array('sort' => $sort)) ?]
        <?php if ($this->configuration->getValue('list.object_actions')): ?>
            <th id="sf_admin_list_th_actions">[?php echo __('Actions', array(), 'sf_admin') ?]</th>
        <?php endif; ?>
          </tr>
        </thead>
        <tfoot>
          <tr>
            <th colspan="<?php echo count($this->configuration->getValue('list.display')) + ($this->configuration->getValue('list.object_actions') ? 1 : 0) + ($this->configuration->getValue('list.batch_actions') ? 1 : 0) ?>">
              [?php if ($pager->haveToPaginate()): ?]
              [?php include_partial('<?php echo $this->getModuleName() ?>/pagination', array('pager' => $pager)) ?]
              [?php endif; ?]
              <div class="f_left list_results">
              [?php echo format_number_choice('[0] no result|[1] 1 result|(1,+Inf] %1% results', array('%1%' => $pager->getNbResults()), $pager->getNbResults(), 'sf_admin') ?]
            
              [?php include_partial('<?php echo $this->getModuleName() ?>/pagination_list_select', array( 'pager' => $pager)) ?]
                [?php if ($pager->haveToPaginate()): ?]
              [?php echo __(' (%%page%%/%%nb_pages%%)', array('%%page%%' => $pager->getPage(), '%%nb_pages%%' => $pager->getLastPage()), 'sf_admin') ?]
              [?php endif; ?]
              </div>
                <div class="clearfix"></div>
               [?php include_partial('<?php echo $this->getModuleName() ?>/list_actions', array('helper' => $helper)) ?]
            </th>
          </tr>
        </tfoot>
        
	
	<tbody class='{toggle_url: "[?php echo url_for($helper->getRouteArrayForAction('toggleBoolean', '<?php echo $this->getModuleName() ?>')) ?]"}'>
          [?php foreach ($pager->getResults() as $i => $<?php echo $this->getSingularName() ?>): $odd = fmod(++$i, 2) ? 'odd' : 'even' ?]
          <tr class="sf_admin_row [?php echo $odd ?] {pk: [?php echo $<?php echo $this->getSingularName() ?>->getPrimaryKey() ?]}">
        <?php if ($this->configuration->getValue('list.batch_actions')): ?>
          [?php include_partial('<?php echo $this->getModuleName() ?>/list_td_batch_actions', array('<?php echo $this->getSingularName() ?>' => $<?php echo $this->getSingularName() ?>, 'helper' => $helper, 'key' => $i )) ?]
        <?php endif; ?>
          [?php include_partial('<?php echo $this->getModuleName() ?>/list_td_<?php echo $this->configuration->getValue('list.layout') ?>', array('<?php echo $this->getSingularName() ?>' => $<?php echo $this->getSingularName() ?>)) ?]
        <?php if ($this->configuration->getValue('list.object_actions')): ?>
          [?php include_partial('<?php echo $this->getModuleName() ?>/list_td_actions', array('<?php echo $this->getSingularName() ?>' => $<?php echo $this->getSingularName() ?>, 'helper' => $helper)) ?]
        <?php endif; ?>
      </tr>
      [?php endforeach; ?]
    </tbody>
  </table>
  [?php endif; ?]
</div>

<script type="text/javascript">
  /* <![CDATA[ */
  function checkAll()
  {
    var allInputs = $('.sf_admin_batch_checkbox');

    allInputs.each(function(intIndex)
    {
      $(this).attr('checked', $('#sf_admin_list_batch_checkbox').is(':checked'));
      $(this).trigger("updateState");
    });
    //var boxes = document.getElementsByTagName('input'); for(var index = 0; index < boxes.length; index++) { box = boxes[index]; if (box.type == 'checkbox' && box.className == 'sf_admin_batch_checkbox') box.checked = document.getElementById('sf_admin_list_batch_checkbox').checked } return true;
  }
  /* ]]> */
</script>
