<?php echo $header; ?>
<div id="content">
    <div class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <?php echo $breadcrumb['separator']; ?><a
            href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
        <?php } ?>
    </div>
    <?php if ($error_warning) { ?>
    <div class="warning"><?php echo $error_warning; ?></div>
    <?php } ?>
    <div class="box">
        <div class="heading">
            <h1><img src="view/image/feed.png" alt=""/> <?php echo $heading_title; ?></h1>

            <div class="buttons"><a onclick="$('#form').submit();"
                                    class="button"><span><?php echo $button_save; ?></span></a><a
                    onclick="location = '<?php echo $cancel; ?>';"
                    class="button"><span><?php echo $button_cancel; ?></span></a></div>
        </div>

        <div class="content">
            <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
                <table class="form">
                    <tr>
                        <td><?php echo $entry_status; ?></td>
                        <td><select name="yandex_market_status">
                            <?php if ($yandex_market_status) { ?>
                            <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                            <option value="0"><?php echo $text_disabled; ?></option>
                            <?php } else { ?>
                            <option value="1"><?php echo $text_enabled; ?></option>
                            <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                            <?php } ?>
                        </select></td>
                    </tr>
                    <tr>
                        <td><?php echo $entry_shopname; ?></td>
                        <td><input name="yandex_market_shopname" type="text"
                                   value="<?php echo $yandex_market_shopname; ?>" size="40" maxlength="20"/></td>
                    </tr>
                    <tr>
                        <td><?php echo $entry_company; ?></td>
                        <td><input name="yandex_market_company" type="text"
                                   value="<?php echo $yandex_market_company; ?>" size="40"/></td>
                    </tr>
                    <tr>
                        <td><?php echo $entry_category; ?></td>
                        <td>
                            <div class="scrollbox">
                                <?php $class = 'odd'; ?>
                                <?php foreach ($categories as $category) { ?>
                                <?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
                                <div class="<?php echo $class; ?>">
                                    <?php if (in_array($category['category_id'], $yandex_market_categories)) { ?>
                                    <input type="checkbox" name="yandex_market_categories[]"
                                           value="<?php echo $category['category_id']; ?>" checked="checked"/>
                                    <?php echo $category['name']; ?>
                                    <?php } else { ?>
                                    <input type="checkbox" name="yandex_market_categories[]"
                                           value="<?php echo $category['category_id']; ?>"/>
                                    <?php echo $category['name']; ?>
                                    <?php } ?>
                                </div>
                                <?php } ?>
                            </div>
                            <a onclick="$(this).parent().find(':checkbox').attr('checked', true);"><?php echo $text_select_all; ?></a>
                            / <a
                                onclick="$(this).parent().find(':checkbox').attr('checked', false);"><?php echo $text_unselect_all; ?></a>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $entry_currency; ?></td>
                        <td><select name="yandex_market_currency">
                            <?php foreach ($currencies as $currency) { ?>
                            <?php if ($currency['code'] == $yandex_market_currency) { ?>
                            <option value="<?php echo $currency['code']; ?>"
                                    selected="selected"><?php echo '(' . $currency['code'] . ') ' . $currency['title']; ?></option>
                            <?php } else { ?>
                            <option value="<?php echo $currency['code']; ?>"><?php echo '(' . $currency['code'] . ') ' . $currency['title']; ?></option>
                            <?php } ?>
                            <?php } ?>
                        </select></td>
                    </tr>
                    <!-- BRANDS ADDED-->
                    <tr>
                        <td><?php echo $entry_stock_status; ?>
                            <select id="stock_status_id" name="stock_status_id">
                                <?php foreach ($stock_statuses as $stock_status) { ?>
                                <?php if ($stock_status['stock_status_id'] == $stock_status_id) { ?>
                                <option value="<?php echo $stock_status['stock_status_id']; ?>" selected="selected"><?php echo $stock_status['name']; ?></option>
                                <?php } else { ?>
                                <option value="<?php echo $stock_status['stock_status_id']; ?>"><?php echo $stock_status['name']; ?></option>
                                <?php } ?>
                                <?php } ?>
                            </select>

                        </td>
                        <td><div class="ajax-loader"><div id="brands_list" class="listBrandsCol"></div></div></td>
                    </tr>
                    <tr>
                        <td><?php echo $entry_data_feed; ?></td>
                        <td><i><?php echo $data_feed; ?></i></td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</div>
<?php echo $footer; ?>

<script id="source" language="javascript" type="text/javascript">
    $(function () {

        var _fc = false;
        var _ss_id = 0;
        var brandsList = $('#brands_list');

        function brandsSelectors(data){
            if(!data.results.length) return;
            var r = data.results,
                    t = data.translations;

            console.log(data)

            var i=ci=0,n=r.length,cn=3,c=Math.ceil(n/cn);
            html =  '<input type="checkbox" id="set_all_brands" value="1"';
            if(data.set_all){
                html += ' checked="checked"';
            }
            html += '/>';
            html += '<label for="set_all_brands"><b>' + t['entry_all_brands'] + '</b></label><br/>';
            html += '<div><ul>';
            for (i=0;i<n;i++){
                html += '<li>';
                html += '<input type="checkbox" name="yandex_market_stock_brands[]" id="brand_'+r[i]['id']+'" value="'+r[i]['id']+'"';
                if(r[i]['checked']||data.set_all){
                    html += ' checked="checked"';
                }
                if(data.set_all){
                    html += ' disabled="disabled"';
                }
                html += '/>';
                html += '<label class="brand_label" for="brand_'+r[i]['id']+'"';
                if(data.set_all){
                    html += ' style="color: #ccc"';
                }
                html += '>'+r[i]['name']+'</label>';
                html += '</li>';
                ci++;
                if((ci == c) && ((i+1) < n)){
                    html += '</ul>';
                    html += '<ul>';
                    ci=0;
                }
            }
            html += '</ul></div>';
            html += '<div style="clear: both; display: block; ">';
            html += '<input type="checkbox" name="products_available" id="products_available" value="1"';
            if(data.products_available){
                html += ' checked="checked"';
            }
            html += '/>';
            html += '<label for="products_available">' + t['entry_option_in_stock'] + '</label></div>';
            return html;
        }

        function saveFormData(url){
            $('div.ajax-loader').addClass('active');
            url = url || null;
            var data = $('input:checked',brandsList).serialize();
            if($('#set_all_brands:checked',brandsList).length){
                data = 'yandex_market_all_brands=1';
            }
            $.ajax({
                url: 'index.php?route=feed/yandex_market/save&token=<?php echo $token; ?>',
                type: 'POST',
                data: data
                        + '&stock_status_id='+_ss_id
                        + '&yandex_market_status='+$('select[name="yandex_market_status"]').val()
                        + '&products_available=' + ($('#products_available:checked').length ? 1 : 0),
                dataType: 'json',
                success: function(data) {
                    if(data == true){
                        if(url){
                            window.location.href = url;
                        }
                    }
                }
            });
        }

        $('input',brandsList).live('change',function(){_fc = true;});

        $('#set_all_brands').live('click',function(){
            if(this.checked){
                $('input',brandsList).not(this).not('#products_available')
                        .attr('disabled',  'disabled')
                        .attr('checked',  'checked');
                $('label.brand_label',brandsList).css('color',  '#ccc');

            }else{
                $('input',brandsList).not(this).not('#products_available')
                        .removeAttr('disabled')
                        .removeAttr('checked');
                $('label.brand_label',brandsList).css('color',  '#000');
            }
        });

        $('#form_save_button').click(function(){saveFormData('<?php echo $cancel; ?>');});

        $('#stock_status_id').change(function(){
            if(_fc){
                saveFormData();
                _fc = false;
            }
            _ss_id = $(this).val();
            $('div.ajax-loader').addClass('active');
            $.ajax({
                url: 'index.php?route=feed/yandex_market/brands&token=<?php echo $token; ?>',
                type: 'POST',
                data: {stock_status:_ss_id},
                dataType: 'json',
                success: function(data) {
                    if(data.results){
                        brandsList.html(brandsSelectors(data));
                        $('div.ajax-loader').removeClass('active');
                    }
                }
            });
        }).trigger('change');
    });
</script>