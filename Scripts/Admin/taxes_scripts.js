$(document).ready(function () {
    var btnDelete = $('.btn-danger'),
        btnEdit = $('.btn-warning'),
        btnFind = $('#btn-find'),
        btnOpen = $('#btn-open-container'),
        btnCreator = $('#btn-send'),
        findForm = $('#find-input'),
        creatorContainer = $('.creator-container'),
        descForm = $('#tax'),
        sizeForm = $('#size');

        creatorContainer.css('display', 'none');

    btnOpen.click(function () {
        creatorContainer.slideToggle();
    })

    btnDelete.click(function () {
        for (var index = 0; index < btnDelete.length; index++) {
            if (btnDelete[index] == event.target) {
                var position = index +1,
                    tax_id = $('tr:nth-child('+position+') .d-none').text();
                $.post('taxes.php', {id: tax_id}, function (response) {
                    if (response.length != 0) {
                        alert(response);
                    } else {
                        window.location.reload();
                    }
                })

            }
            
        }
    })

    btnEdit.click(function () {
      for (var index = 0; index < btnEdit.length; index++) {
          if(btnEdit[index] == event.target) {
            var position = index +1,
                tax_id = $('tr:nth-child('+position+') .d-none').text();
                window.location.href = 'Info/taxinfo.php?tax='+ tax_id;
          }
          
      }  
    })

    btnFind.click(function(){
        var desc = findForm.val();
            try {
                if (desc !== undefined && desc !== null && desc.length !=0) {
                    window.location.href = 'taxes.php?desc='+desc;
                } else {
                    throw new Error('Empty Find Error');
                }
            } catch (error) {
                if (error.message == 'Empty Find Error') {
                    findForm.addClass('is-invalid');
                    findForm.prop('placeholder', 'Вы не ввели описание');
                }
                else {
                    alert(error.message);
                }
            }
    })

    btnCreator.click(function () {  
        var desc = descForm.val(),
            size = sizeForm.val();

        if (ValidateDesc(desc) && ValidateSize(size)) {
            var tax = new Tax(desc, size);
            tax = JSON.stringify(tax);
            $.post('taxes.php', {tax: tax}, function (response) {  
                if (response.length != 0) {
                    alert(response);
                } else {
                    window.location.reload();
                }
            })
            function Tax (desc, size) {
                this.desc = desc,
                this.size = size
            }
        }

        function ValidateDesc(desc) {
            try {
                if (desc !== null && desc !== undefined && desc.length != 0) {
                    descLen = desc.length;
                    if ((descLen > 0) && (descLen <= 100)) {
                        if (/([а-яёА-ЯЁ0-9/ ])+/.exec(desc) !== null) {
                            if (/([а-яёА-ЯЁ0-9/ ])+/.exec(desc)[0] === desc) {
                                return true;
                            } else {
                                throw new Error('Uncorrect Desc Error');
                            }
                        } else {
                            throw new Error('Uncorrect Desc Error');        
                        }
                    }
                } else {
                    throw new Error('Empty Desc Error');
                }
            } catch (error) {
                if (error.message === 'Empty Desc Error') {
                    alert('Вы не ввели описание штрафа!');
                }
                if (error.message === 'Length Desc Error') {
                    alert('Длина штрафа не должна превышать 100 символов!');
                }
                if (error.message === 'Uncorrect Desc Error') {
                    alert('Описание штрафа должно состоять из букв русского алфавита, цифр и слеша!');
                }
            }
        }

        function ValidateSize(size) {
            try {
                if (size !== null && size !== undefined && size.length != 0) {
                    if (!isNaN(size)) {
                        if ((size > 0) && (size <= 50000)) {
                            return true;
                        } else {
                            throw new Error('Length Size Error');
                        }
                    } else {
                        throw new Error('Uncorrect Size Error');    
                    }
                } else {
                    throw new Error('Empty Size Error');
                }
            } catch (error) {
                if (error.message === 'Empty Size Error') {
                    alert('Вы не указали размер штрафа!');
                }
                if (error.message === 'Length Size Error') {
                    alert('Размер штрафа не может превышать 50 тыс. рублей!');
                }
                if (error.message === 'Uncorrect Size Error') {
                    alert('Размер штрафа должен состоять из цифр!');                    
                }
            }
        }
    })
})