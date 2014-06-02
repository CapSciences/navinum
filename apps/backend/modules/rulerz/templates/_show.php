<?php /** @var $rulerz Rulerz */ ?>
<?php if (has_component_slot('show_header')) include_component_slot('show_header') ?>
<?php include_partial('rulerz/show_header', array('rulerz' => $rulerz, 'helper' => $helper)) ?>


<div class="sf_admin_form">

    <div class="content-box">
        <div class="content-box-header">
            <h3>Rulerz : <?php echo $rulerz->getLibelle() ?></h3>

            <h1></h1>
        </div>

        <div style="padding: 10px;">
            <p><strong>Action :</strong></p>
            <pre style="padding: 20px 5px; border: 1px solid black;"><?php echo $rulerz->getAction() ?></pre>
        </div>

        <div style="text-align: center">
            <?php echo $helper->linkToEdit($rulerz, array(  'params' =>   array(  ),  'class_suffix' => 'edit',  'label' => 'Modifier la règle',)) ?>
        </div>
    </div>

</div>

<div class="sf_admin_form">

    <div class="content-box">
        <div class="content-box-header">
            <h3>Ecouteurs</h3>
        </div>

        <div style="padding: 10px;">
            <table>
                <thead>

                </thead>
                <tbody>
                <tr>
                    <form action="<?php echo url_for('default', array('module' => 'rulerz', 'action' => 'addListener', 'guid' => $rulerz->getGuid())) ?>"
                          method="post">
                        <td>
                            <select name="select_event">
                                <?php foreach ($models as $model): ?>
                                <option value="<?php echo $model ?>"><?php echo $model ?></option>
                                <?php endforeach ?>
                            </select>
                        </td>
                        <td>
                            <button type="submit">Ajouter</button>
                        </td>
                    </form>
                </tr>
                <?php foreach ($rulerz->getListeners() as $listener): ?>
                    <?php /** @var $listener RulerzListener */ ?>
                <tr>
                    <td><?php echo $listener->getEvent() ?></td>
                    <td><?php echo link_to('Delete', url_for('default', array('module' => 'rulerz', 'action' => 'deleteListener', 'guid' => $rulerz->getGuid(), 'lguid' => $listener->getGuid())), array('method' => 'delete', 'confirm' => 'Are you sure?')) ?></td>
                </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>

    </div>

</div>

<div class="sf_admin_form">

    <div class="content-box">
        <div class="content-box-header">
            <h3>Executions</h3>
        </div>

        <div style="padding: 10px;">
            <div class="sf_admin_list">
                <table>
                    <thead>
                    <tr>
                        <th>Execution le :</th>
                        <th>Ecouteur</th>
                        <th>guid</th>
                        <th>Statut</th>
                        <th>Données</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($executions as $execution): ?>
                        <?php /** @var $execution RulerzExecution */ ?>
                    <tr>
                        <td><?php $date = new DateTime($execution->get('created_at')); echo $date->format('d/m/Y H:i:s') ?></td>
                        <td><?php echo $execution->getEvent() ?></td>
                        <td><?php echo $execution->getEntityUid() ?></td>
                        <td><?php echo $execution->getStatus() ?></td>
                        <td>
                            <pre><?php echo $execution->getExecutionData() ?></pre>
                        </td>
                    </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>


<?php include_partial('rulerz/show_footer', array('rulerz' => $rulerz, 'helper' => $helper)) ?>
<?php if (has_component_slot('show_footer')) include_component_slot('show_footer') ?>
