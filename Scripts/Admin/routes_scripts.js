$(document).ready(function () {  
    btnRemove = $('.btn-warning');

    btnRemove.click(function () {  
        for (var index = 0; index < btnRemove.length; index++) {
            if (btnRemove[index] == event.target) {
                var position = index + 1,
                    emp = $('tr:nth-child('+ position + ') .d-none').text();
                    $.post('routes.php', {emp:emp}, function (response) {  
                        if (response.length != 0) {
                            alert(response);
                        } else {
                            window.location.reload();
                        }
                    })
                
            }
            
        }
    })
})