</div>
</div>
<footer class="text-center" id="footer">
	&copy; Copyright 2013 - <?php echo date('Y'); ?> Shaunta's Boutique
</footer>

<script>
    jQuery(window).scroll(function () {
        var vscroll = jQuery(this).scrollTop();
        jQuery('#logo-text').css({
            "transform" : "translate(0px, "+vscroll/2+"px)"
        });

        var vscroll = jQuery(this).scrollTop();
        jQuery('#back-flower').css({
            "transform" : "translate("+vscroll/5+"px, -"+vscroll/12+"px)"
        });

        var vscroll = jQuery(this).scrollTop();
        jQuery('#fore-flower').css({
            "transform" : "translate(0px, -"+vscroll/2+"px)"
        });
    });
    
    function detailsmodal(id) {
        var data = {"id" : id};
        jQuery.ajax({
            url : '/Projects/InProgress/Boutique/includes/detailsmodal.php',
            method : "post",
            data : data,
            success : function (data) {
                if (jQuery('#details-modal').length){
                    jQuery('#details-modal').remove();
                }
               jQuery('body').append(data);
               jQuery('#details-modal').modal('toggle');
            },
            error : function () {
               alert("Something went wrong!");
            }
        });
    }

    // close alert box after some seconds
    window.setTimeout(function () {
        $(".alert-success").fadeTo(500, 0).slideUp(500, function () {
            $(this).remove();
        });
    }, 5000);

    function update_cart(mode, edit_id, edit_size) {
        var data = {"mode" : mode, "edit_id" : edit_id, "edit_size" : edit_size};
        jQuery.ajax({
            url : '/Projects/InProgress/Boutique/admin/parsers/update_cart.php',
            method : "post",
            data : data,
            success : function(){
                location.reload();
            },
            error : function(){
                alert("Something went wrong.");
            },
        });
    }

    function add_to_cart(){
        jQuery('#modal_errors').html("");
        var size = jQuery('#size').val();
        var quantity = jQuery('#quantity').val();
        // var available = jQuery('#available').val();
        var available = parseInt(jQuery('#available').val());
        var error = "";
        var data = jQuery('#add_product_form').serialize();
        // var data = jQuery('#add_product_form').serializeArray();
        if (size === '' || quantity === '' || quantity === 0) {
            error += '<p class="text-danger text-center">You must choose a size and quantity.</p>';
            jQuery('#modal_errors').html(error);
            return;
        } else if(quantity > available) {
            error += '<p class="text-danger text-center">There are only '+available+' available.</p>';
            jQuery('#modal_errors').html(error);
            return;
        } else {
            jQuery.ajax({
                url : '/Projects/InProgress/Boutique/admin/parsers/add_cart.php',
                method : 'post',
                data : data,
                success : function(){
                    location.reload();
                },
                error : function(){
                    alert("Something went wrong.");
                }
            });
        }
    }
</script>
</body>
</html>

