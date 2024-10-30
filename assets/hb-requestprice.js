
window.addEventListener('load', (event) =>{


    var HBdone = document.getElementsByClassName('HB-done');

    for(let i = 0; i < HBdone.length; i++) {
        HBdone[i].addEventListener("click", function(event) {
            event.preventDefault();


            if (confirm('Are you sure to complete this?') == true) {

                var url_string      =   HBdone[i].getAttribute("href");
                var url_string      =   "https://piglet.me/"+url_string;
                var url             =   new URL(url_string);
                var hbRequestId     =   url.searchParams.get("hbRequestId");
                var wpnonce         =   url.searchParams.get("_wpnonce");

                var formData = new FormData();
                formData.append( 'action', 'hb_done_requestPrice_action' );
                formData.append( 'hbRequestId', hbRequestId );
                formData.append( 'wpnonce', wpnonce );

                (async () => {
                    const rawResponse = await fetch('/wp-admin/admin-ajax.php', {
                        method: 'POST',
                        body: formData
                    })
                        .then( res => res.text() )
                        .then( function(data) {

                            console.log(data);
                            if(data==0){
                                alert('Success');
                                location.reload();
                                return;
                            }

                            alert('Error');
                        })
                        .catch(function() {
                            alert('Error');
                        });

                })();
                return true;
            } else {
                alert('You have canceled confirmation!');
                return false;
            }




        });
    }

    var HBsend = document.getElementsByClassName('HB-Send');

    for(let i = 0; i < HBsend.length; i++) {
        HBsend[i].addEventListener("click", function(event) {
            event.preventDefault();


            let person = prompt("Please enter message:");
            if (person == null || person == "") {
                alert('Cancelled');
            } else {
                var url_string      =   HBsend[i].getAttribute("href");
                var url_string      =   "https://piglet.me/"+url_string;
                var url             =   new URL(url_string);
                var hbRequestId     =   url.searchParams.get("hbRequestId");
                var wpnonce         =   url.searchParams.get("_wpnonce");
                var reload         =   url.searchParams.get("reload");

                var formData = new FormData();
                formData.append( 'action', 'hb_send_requestPrice_action' );
                formData.append( 'hbRequestId', hbRequestId );
                formData.append( 'wpnonce', wpnonce );
                formData.append( 'message', person );

                (async () => {
                    const rawResponse = await fetch('/wp-admin/admin-ajax.php', {
                        method: 'POST',
                        body: formData
                    })
                        .then( res => res.text() )
                        .then( function(data) {
                            if(data==0){
                                alert('Success');
                                if(reload=='true'){
                                    location.reload();
                                }
                                return;
                            }else if(data==100){
                                alert('Success');
                                location.reload();
                                return;
                            }

                            alert('Error');
                        })
                        .catch(function() {
                            alert('Error');
                        });

                })();
                return true;
            }
        });
    }



    var HBcheckboxALL = document.getElementsByClassName('hb-request-a-question-all');

        if(HBcheckboxALL){

            var HBcheckbox = document.getElementsByName('hbrequest[]');


            for(let i = 0; i < HBcheckboxALL.length; i++) {

                HBcheckboxALL[i].addEventListener("click", function(event) {

                    for (var i = 0; i < HBcheckboxALL.length; i++) {

                        if(this.checked == true ){
                            if(HBcheckboxALL[i].checked==false)
                                HBcheckboxALL[i].checked = true;
                        }else{
                            HBcheckboxALL[i].checked = false;
                        }

                    }



                    for (var i = 0; i < HBcheckbox.length; i++) {
                        if (HBcheckbox[i] != this)
                            HBcheckbox[i].checked = this.checked;
                    }
                });
            }
        }

    var HBStatusSave = document.getElementById('HBStatusSave');
        if(HBStatusSave){
            HBStatusSave.addEventListener("submit", function(event) {

        event.preventDefault();

        var Status = document.getElementById('Status').value,
            HBNote = document.getElementById('HBNote').value,
            hbRequestId = document.getElementById('hbRequestId').value,
            hbuserid = document.getElementById('hbuserid').value,
            hborderid = document.getElementById('hborderid').value;

        var formData = new FormData();
        formData.append( 'action', 'hb_Note_requestPrice_action' );
        formData.append( 'hbRequestStatus', Status );
        formData.append( 'hbRequestNote', HBNote );
        formData.append( 'hbRequestId', hbRequestId );
        formData.append( 'hbuserid', hbuserid );
        formData.append( 'hborderid', hborderid );


        (async () => {
            const rawResponse = await fetch('/wp-admin/admin-ajax.php', {
                method: 'POST',
                body: formData
            })
                .then( res => res.text() )
                .then( function(data) {
                    if(data==0){
                        alert('Success');
                        location.reload();
                        return;
                    }else if(data==900){
                        alert('Error');
                        location.reload();
                        return;
                    }

                    alert('Error');
                })
                .catch(function() {
                    alert('Error');
                });

        })();

    });
        }
});

