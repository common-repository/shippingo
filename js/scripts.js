
jQuery(document).ready(function ($) {    



    const loader = `<div class="shippingo-dsb-spinner"><div></div><div></div><div></div><div></div></div>`;
    function loaderToEl(el,action='') {

        if (action=='') {
            el.append(loader);
        } else {
            el.find(".shippingo-dsb-spinner").remove();
        }

    }

    function openIframe(order_id) {
        let modal = $(".shippingo-iframe");
        modal.find("iframe").attr("src","");

        order_id = order_id.toString();

        //NEW2024
        let type = 'shipment';
        if (order_id.includes(",")) {
            type = 'shipments';
        }

        modal.find("iframe").attr("src",`${shippingo_data.iframe_url}#/${type}/${order_id}`)
        modal.css("display","flex")
    }


    $(".shippingo-bulk.print").on("click",function(){
        let selectedList = [];
        $(".type-shop_order").find(".check-column input[name='post[]']:checked").each(function() {
            selectedList.push( $(this).val() )
        })
        console.log('selectedList',selectedList)

        if (selectedList.length>0) {

            var href = shippingo_data.print_link + selectedList.join(",")
            printPopup(href)

        } else {

            alert('יש לסמן את הזמנות שברצונכם להדפיס');

        }
    });



    $(document).on('click',".shippingo-dso-con-box-close", function (e) {

        let modal = $(".shippingo-iframe");
        modal.css("display","none")

    });


    $(document).on('click',".shippingo_open_iframe", function (e) {

        let el = $(this);
        loaderToEl(el)

        // NEW2024
        let order_id = '';
        if (el.hasClass('shippingo-bulk')) {
            
            let selectedList = [];

            $(".type-shop_order").find(".check-column input[name='post[]']:checked").each(function() {
                selectedList.push( $(this).val() )
            })

            order_id = selectedList.join(",");

        } else {

            order_id = $(this).closest(".shippingo-orders-colum,.shippingo-shipping-box").data("order-id");

        } 

        console.log('order_id',order_id)


        let data = {
            action : 'shippingo_add_order',
            order_id : order_id,
            nonce: shippingo_data.nonce,
        }

        $.ajax(shippingo_data.ajax_url, {
            type: 'POST',  // http method
            data: data,  // data to submit
            success: function (data, status, xhr) {                

                if (data.success) {
                    openIframe(order_id)
                }

                loaderToEl(el,1)

            }
        });

    });



    $(".shippingo-dso-bulk-send-modal").on("click",".shippingo_bulk.submit", async function(e){

        let ordersList = {};

        let modal = $(".shippingo-dso-bulk-send-modal");

        modal.find("tr").each(function(){

            let order_id = $(this).data("order-id")
            if (order_id!='') {

                ordersList[order_id] = { 
                    order_id : order_id
                }
            }

        });

        if (Object.keys(ordersList).length>0) {   

            let orders_list = ordersList.join(",");
            
            openIframe(orders_list)

        } else {

            loaderToEl(el,1)

            let modal = $(".shippingo-dso-error-modal");
            modal.find(".shippingo-dso-con-box .shippingo-dso-error-modal-box").html('לא בחרתם הזמנות לשידור! יש לבחור הזמנות ולנסות שנית');
            modal.css("display","flex")

        }

    });


    $(".shippingo-dso-modal").on("click",".shippingo-dso-con-title span",function(){

        let modal = $(".shippingo-dso-modal");
        modal.hide(0)

    })

    $(".shippingo-orders-colum").on("click",".shippingo-dso-row-btn.print",function(){

        var href = $(this).data("label")
        var token = $(this).data("token")

        printPopup(href,token);


    })

    $(document).on("click",".shippingo_submit.print_label",function(){

        var href = $(this).data("label")
        var token = $(this).data("token")
        printPopup(href,token);

    })

               

    function printPopup(href,token='') {

        window.open(href, '_blank', 'location=yes,height=570,width=520,scrollbars=yes,status=yes');

    }



});
