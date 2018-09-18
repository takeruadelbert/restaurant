<?php
echo $this->element(_TEMPLATE_DIR . "/{$template}/filter/order");
?>
<div class="panel panel-default">
    <div class="panel-body">
        <div class="block-inner text-danger">
            <h6 class="heading-hr"><?= __("DATA ORDER") ?>
                <div class="pull-right">
                </div>
                <small class="display-block"></small>
            </h6>
        </div>
        <div class="table-responsive pre-scrollable stn-table">
            <form id="checkboxForm" method="post" name="checkboxForm" action="<?php echo Router::url('/' . $this->params['prefix'] . '/' . $this->params['controller'] . '/multiple_delete', true); ?>">
                <table width="100%" class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <th width="50">No</th>
                            <th><?= __("No. Order") ?></th>
                            <th><?= __("Meja") ?></th>
                            <th><?= __("Tanggal") ?></th>
                            <th><?= __("Device") ?></th>
                            <th colspan="2"><?= __("Total") ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $limit = intval(isset($this->params['named']['limit']) ? $this->params['named']['limit'] : 10);
                        $page = intval(isset($this->params['named']['page']) ? $this->params['named']['page'] : 1);
                        $i = ($limit * $page) - ($limit - 1);
                        if (empty($data['rows'])) {
                            ?>
                            <tr>
                                <td class = "text-center" colspan = 8>Tidak Ada Data</td>
                            </tr>
                            <?php
                        } else {
                            foreach ($data['rows'] as $item) {
                                ?>
                                <tr>
                                    <td class="text-center"><?= $i ?></td>
                                    <td class="text-center"><a target="_blank" href="<?= Router::url("/admin/orders/view/{$item['Order']['id']}") ?>"><?= $item['Order']['no_order'] ?></a></td>
                                    <td class="text-center"><?= $item['Table']['name'] ?></td>
                                    <td class="text-center"><?= $this->Html->cvtWaktu($item['Order']['created']) ?></td>
                                    <td class="text-center"><?= $item['Device']['full_label'] ?></td>
                                    <td class="text-center" style = "border-right-style:none !important" width = "50">Rp.</td>                                    
                                    <td class="text-right" style = "border-left-style:none !important" width = "150"><?= ic_rupiah($item['Order']['grand_total']) ?></td>
                                </tr>
                                <?php
                                $i++;
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
    <?php echo $this->element(_TEMPLATE_DIR . "/{$template}/pagination") ?>
</div>

