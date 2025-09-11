"use strict";
$(function() {
    var update_cart;
    var rate, quantity, max, product_id, amount_text;
    $(".quantity").change(function(event) {
        var quantity = $(this).val();
        max = $(this).attr('max');
        if (quantity <= 0 || !$.isNumeric(quantity)) {
            alert_float("danger","Quantity Must Be Greater Than 0 ");
            $(this).val($(this).data('old-qty'));
            return false;
        }
        if (parseInt(quantity) > parseInt(max)) {
            alert_float("danger",`Only ${max} Items are in stock for this Product`);
            $(this).val($(this).data('old-qty'));
            return false;
        }
        if (update_cart) {
            update_cart.abort();
        }
        product_id = $(this).data('product_id');
        update_cart = $.post(site_url+'products/client/add_cart', {quantity: quantity, product_id: product_id}, function(data, textStatus, xhr) {
        });
            rate = $(this).data('rate');
        $(this).data('old-qty',quantity);
        amount_text = rate * quantity;
        $(this).parents('td').children('.order_qty').children('input').val(quantity);
        $(this).parents('tr').children('.amount').text(format_money(amount_text));
        var total = 0; 
        $(".quantity").each(function(index, el) {
            rate = $(this).data('rate');
            total += rate * $(this).val();
        });
        $(".subtotal,.total").text(format_money(total));
        $("input[name='subtotal']").val(total);
        $("input[name='total']").val(total);
        $("input[name='sub_total_disabled']").val(total);
    });
    $(".remove_cart").on('click', function(event) {
        var button;
        product_id = $(this).data('product_id');
        button = $(this);
        $.post(site_url+'products/client/remove_cart', {product_id: product_id}, function(data, textStatus, xhr) {
            data = $.parseJSON(data);
            if (data.status == false) {
                location.reload();
            }
            var next_tr = button.parents('tbody').children('tr');
            if (next_tr.length != 0) {
                console.log(next_tr.find('.quantity'));
                button.parents('tr').remove();
                next_tr.find('.quantity').change();
                alert_float("success","Item Removed from Cart");
            }
        });
    });
});
init_currency();
function format_money(total, excludeSymbol) {
    if (typeof(excludeSymbol) != 'undefined' && excludeSymbol) {
        return accounting.formatMoney(total, { symbol: '' });
    }
    return accounting.formatMoney(total);
}
function init_currency() {
    $.get(site_url + 'products/client/get_currency/'+base_currency)
        .done(function(currency) {
            currency = $.parseJSON(currency);
            accounting.settings.currency.decimal = currency.decimal_separator;
            accounting.settings.currency.thousand = currency.thousand_separator;
            accounting.settings.currency.symbol = currency.symbol;
            accounting.settings.currency.format = currency.placement == 'after' ? '%v %s' : '%s%v';
        });
}