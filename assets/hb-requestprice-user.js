window.addEventListener('load', (event) =>{

    if(document.getElementById('hbrequestprice')){

        var requestprice_name = document.getElementById('hb-requestprice-name'),
            requestprice_connection = document.getElementById('hb-requestprice-connection'),
            hbrequestprice = document.getElementById('hbrequestprice'),
            requestpricemessage_word = document.getElementById('requestpricemessage_word'),
            requestpricename_word = document.getElementById('requestpricename_word'),
            requestpriceconnection_word = document.getElementById('requestpriceconnection_word'),
            requestprice_requestpricemessage = document.getElementById('hb-requestprice-message');

        requestpricename_word.innerText = requestprice_name.value.length;
        requestpriceconnection_word.innerText = requestprice_connection.value.length;
        requestpricemessage_word.innerText = requestprice_requestpricemessage.value.length;


        hbrequestprice.addEventListener('submit', (event) => {

            if (requestprice_name.value.length < 1) {
                requestprice_name.style.borderColor = 'red';
                event.preventDefault();
                return;
            }
            if (requestprice_connection.value.length < 1) {
                requestprice_connection.style.borderColor = 'red';
                event.preventDefault();
                return;
            }
            if (requestprice_requestpricemessage.value.length < 1) {
                requestprice_requestpricemessage.style.borderColor = 'red';
                event.preventDefault();
                return;
            }



            if (requestprice_name.value.length > 10) {
                alert('error');
                event.preventDefault();
                return;
            }
            if (requestprice_connection.value.length > 30) {
                alert('error');
                event.preventDefault();
                return;
            }
            if (requestprice_requestpricemessage.value.length > 250) {
                alert('error');
                event.preventDefault();
                return;
            }


        });


        //hb-requestprice-name
        requestprice_name.addEventListener('keyup', (event) => {

            requestpricename_word.innerText = event.target.value.length;

            if (event.target.value.length > 10) {
                requestprice_name.value = requestprice_name.value.substring(0, 10);
                requestpricename_word.innerText = event.target.value.length;
            }

        });

        //hb-requestprice-connection
        requestprice_connection.addEventListener('keyup', (event) => {

            requestpriceconnection_word.innerText = event.target.value.length;

            if (event.target.value.length > 30) {
                requestprice_connection.value = requestprice_connection.value.substring(0, 30);
                requestpriceconnection_word.innerText = event.target.value.length;
            }

        });


        //hb-requestprice-message
        requestprice_requestpricemessage.addEventListener('keyup', (event) => {

            requestpricemessage_word.innerText = event.target.value.length;

            if (event.target.value.length > 250) {
                requestprice_requestpricemessage.value = requestprice_requestpricemessage.value.substring(0, 250);
                requestpricemessage_word.innerText = event.target.value.length;

            }

        });

    }
});