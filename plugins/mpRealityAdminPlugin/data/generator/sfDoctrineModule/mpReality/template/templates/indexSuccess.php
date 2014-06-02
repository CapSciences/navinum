[?php use_helper('I18N', 'Date') ?]
[?php include_partial('<?php echo $this->getModuleName() ?>/assets') ?]
[?php include_partial('<?php echo $this->getModuleName() ?>/flashes') ?]

<div id="sf_admin_container">

    <div class="title">
        <h1>[?php echo <?php echo $this->getI18NString('list.title') ?> ?]</h1>
       [?php include_partial('<?php echo $this->getModuleName() ?>/list_action_new', array('helper' => $helper)) ?]
    </div>
<?php if ($this->configuration->hasFilterForm()): ?>
  <div id="sf_admin_bar">
    [?php include_partial('<?php echo $this->getModuleName() ?>/filters', array('form' => $filters, 'configuration' => $configuration)) ?]
  </div>
  <?php endif; ?>

        <div class="content-box">
            <div class="content-box-header">


           [?php  include_partial('<?php echo $this->getModuleName() ?>/search');?]

            </div>
            <div id="sf_admin_header">
                [?php include_partial('<?php echo $this->getModuleName() ?>/list_header', array('pager' => $pager)) ?]
            </div>



            <div id="sf_admin_content">
            <?php if ($this->configuration->getValue('list.batch_actions')): ?>
                    <div class="sf_admin_table_content">
                     <form action="[?php echo url_for('<?php echo $this->getUrlForAction('collection') ?>', array('action' => 'batch')) ?]" method="post">
                    <?php endif; ?>
                    <div id="list_bloc_content">
                        [?php include_partial('<?php echo $this->getModuleName() ?>/list', array('pager' => $pager, 'sort' => $sort, 'helper' => $helper)) ?]
                    </div>
                   
                    <?php if ($this->configuration->getValue('list.batch_actions')): ?>
                    </form>
                </div>
            <?php endif; ?>
                    </div>
                </div>
                <div id="sf_admin_footer">
                    [?php include_partial('<?php echo $this->getModuleName() ?>/list_footer', array('pager' => $pager)) ?]
                </div>
</div>
