function buyItem($buyLink){
    if (!$buyLink.parent().hasClass('selected')) {
        $.get($buyLink.attr('href'),function (response){
            $($buyLink).parent().addClass('selected');
            $("div.cart").html(response);
        });
    }
    return false;
}

function incItem($incLink){
    $.get($incLink.attr('href'),function (response){
        $("div.cart").html(response);
    });
    return shiftItem($incLink, +1);
}

function decItem($decLink){
    $.get($decLink.attr('href'),function (response){
        $("div.cart").html(response);
    });
    return shiftItem($decLink, -1);
}

function shiftItem($shiftLink, shift){
    var $row = $shiftLink.parents('tr:first');
    var $qntInput = $row.find('input[name^=quantity]');
    var $priceInput = $row.find('input[name^=price]');
    var qnt = parseInt($qntInput.val());
    var price = parseInt($priceInput.val());
    var $qntCell = $row.find('td').eq(2);
    var $costCell = $row.find('td').eq(3);
    
    qnt = qnt + shift;
    
    if (qnt > 0) {
        $qntInput.val(qnt);
        $qntCell.find('span').html(qnt);
        $costCell.html(formatRouble(qnt * price));
        
        updateCart();
    }
    
    return false;
}

function updateCart(){
    var totalQnt = 0; var totalSum = 0;
    $('form.cart-form').find('input[name^=quantity]').each(function(){
        var $qntInput = $(this);
        var $priceInput = $qntInput.parent().find('input[name^=price]');
        var qnt = parseInt($qntInput.val());
        var price = parseInt($priceInput.val());
        totalQnt += qnt;
        totalSum += qnt * price;
    });
    
    var $totalRow = $('form.cart-form').find('tr.total');
    var $totalQntCell = $totalRow.find('td').eq(2);
    var $totalSumCell = $totalRow.find('td').eq(3);
    $totalQntCell.html(totalQnt);
    $totalSumCell.html(formatRouble(totalSum));
}

function callback() {
    $.get('/callback', function (response){
        $(response).modal({
            opacity: 30,
            overlayClose: true,
            closeHTML: '<a class="modalCloseImg" title="Закрыть"></a>'
        });
    });
    return false;
}

function formatRouble(sum){
    return Intl.NumberFormat().format(sum) + ' р.';
}

$(function () {
    $.supersized({
        slides: [
            {image: '/image/background01.jpg'}, {image: '/image/background02.jpg'},
            {image: '/image/background03.jpg'}, {image: '/image/background04.jpg'}
        ],
        slide_interval: 15000
    });
    
    $('.teaser-slideshow').cycle();
    
    $('.product').each(function () {
        $(this).find('.product-hover').hover(function () {
            $(this).find('.product-description').show();
        }, function () {
            $(this).find('.product-description').hide();
        });
    });
    
    $('.card-tab a').click(function() {
        if (!$(this).hasClass('selected')) {
            $('.card-content').children().hide(300);
            $('#' + $(this).attr('for')).show(300);
            
            $('.card-tab a').removeClass('selected');
            $(this).addClass('selected');
        }
        return false;
    });
    
    $('input[href]').bind('click', function(e) {
        if (!$(this).attr('confirm') || confirm($(this).attr('confirm'))) {
            location.href = $(this).attr('href');
        }
    });
});
