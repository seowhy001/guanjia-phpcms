<?php
    defined('IN_ADMIN') or exit('No permission resources.');
    include $this->admin_tpl('header','admin');
?>
<div class="pad_10">
    <form action="?m=guanjia&c=admin_guanjia&a=index" method="post">
        <table width="100%" class="table_form" cellspacing="0">
            <tr>
                <td width="100">Token</td>
                <td>
                    <input type="text" class="input-text" name="token" value="<?php echo $response['token']; ?>">
                    <div id="rmb_point_rateid" class="onShow">搜外内容管家对接的token</div>
                </td>
            </tr>
            <tr>
                <td width="100">插件版本</td>
                <td>
                    <div>V1.0.0
                    <div id="rmb_point_rateid" class="onShow">当前插件版本</div>
                </td>
            </tr>
            <tr>
                <td width="100">插件地址</td>
                <td>
                    <div><?php echo $response['url']; ?></div>
                </td>
            </tr>
        </table>
        <div class="bk15"></div>
        <input name="dosubmit" type="submit" id="dosubmit" value="<?php echo L('submit')?>" class="button">
    </form>
</div>