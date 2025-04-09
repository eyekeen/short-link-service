<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>
<div class="container">
    <h1>Генератор коротких ссылок</h1>
    <div class="input-group mb-3">
        <input type="text" id="url-input" class="form-control" placeholder="Введите URL">
        <div class="input-group-append">
            <button id="generate-btn" class="btn btn-primary">ОК</button>
        </div>
    </div>
    <div id="result"></div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    $('#generate-btn').on('click', function() {
        let url = $('#url-input').val();
        $.ajax({
            url: '<?= Url::to(['site/generate']) ?>',
            method: 'POST',
            data: { url: url },
            success: function(response) {
                if (response.error) {
                    $('#result').html('<div class="alert alert-danger">' + response.error + '</div>');
                } else {
                    $('#result').html(`
                        <div class="alert alert-success">
                            <p>Короткая ссылка: <a href="${response.short_url}" target="_blank">${response.short_url}</a></p>
                            <img src="${response.qr_code}" alt="QR Code">
                        </div>
                    `);
                }
            }
        });
    });
});
</script>