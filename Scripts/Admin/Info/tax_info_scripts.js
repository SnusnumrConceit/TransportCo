$(document).ready( function () {
    var descForm = $('#desc'),
        sizeForm = $('#size'),
        btnSender = $('#btn-send');
    
    btnSender.click(function () {  
        var id = window.location.search.split('=');
            desc = descForm.val(),
            size = sizeForm.val();

            if (ValidateDesc(desc) && ValidateSize(size)) {
                var tax = new Tax(id, desc, size);
                tax = JSON.stringify(tax);
                $.post('taxinfo.php', {tax: tax}, function (response) {  
                    if (response.length != 0) {
                        alert(response);
                    } else {
                        window.location.href = '../taxes.php';
                    }
                })
                function Tax (id, desc, size) {
                    this.id = id[1],
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