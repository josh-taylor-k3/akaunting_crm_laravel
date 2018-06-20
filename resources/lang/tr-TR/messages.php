<?php

return [

    'success' => [
        'added'             => ':type eklendi!',
        'updated'           => ':type güncellendi!',
        'deleted'           => ':type silindi!',
        'duplicated'        => ':type çoğaltıldı!',
        'imported'          => ':type içe aktarıldı!',
        'enabled'           => ':type etkinleştirildi!',
        'disabled'          => ':type devre dışı bırakıldı!',
    ],
    'error' => [
        'over_payment'      => 'Hata: Ödeme eklenmedi. Girilen tutar toplamı geçiyor.',
        'not_user_company'  => 'Hata: Bu şirketi yönetme yetkiniz yok!',
        'customer'          => 'Hata: Kullanıcı oluşturulamadı. :name bu e-posta adresini kullanmaktadır.',
        'no_file'           => 'Hata: Dosya seçilmedi!',
        'last_category'     => 'Hata: Son :type kategorisini silemezsiniz!',
        'invalid_token'     => 'Hata: Girilen token yanlış!',
    ],
    'warning' => [
        'deleted'           => 'Uyarı: <b>:name</b> silinemez çünkü :text ile ilişkilidir.',
        'disabled'          => 'Uyarı: <b>:name</b> devre dışı bırakılamaz çünkü :text ile ilişkilidir.',
    ],

];
