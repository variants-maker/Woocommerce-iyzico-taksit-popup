# iyzico Taksit Seçenekleri

Bu WordPress eklentisi, WooCommerce mağazanızda ürün sayfalarında iyzico üzerinden taksit seçeneklerini gösterir.

## Özellikler

- Ürün sayfasında taksit seçeneklerini otomatik gösterir
- Tüm banka kartları için taksit seçeneklerini listeler
- Mobil uyumlu tasarım
- WooCommerce ile tam entegrasyon
- Popup ile taksit detaylarını gösterme
- Responsive tasarım

## Kurulum

1. Eklenti dosyalarını `/wp-content/plugins/iyzico-taksit` dizinine yükleyin
2. WordPress yönetici panelinden eklentiyi etkinleştirin
3. iyzico API bilgilerinizi `config.php` dosyasında yapılandırın

## Yapılandırma

`config.php` dosyasında aşağıdaki ayarları yapılandırın:

```php
$options->setApiKey('YOUR_API_KEY');
$options->setSecretKey('YOUR_SECRET_KEY');
$options->setBaseUrl('https://api.iyzipay.com'); // Canlı ortam için
// veya
$options->setBaseUrl('https://sandbox-api.iyzipay.com'); // Test ortamı için
```

## Kullanım

Eklenti otomatik olarak ürün sayfalarında çalışır. Herhangi bir ek yapılandırma gerektirmez.

### Taksit Seçenekleri Gösterimi

- Ürün sayfasında "Sepete Ekle" butonunun yanında "Taksit Seçenekleri" butonu görünür
- Butona tıklandığında taksit seçenekleri popup olarak açılır
- Her banka kartı için ayrı bir kart gösterilir
- Her kartta taksit sayısı, taksit tutarı ve toplam tutar bilgileri yer alır

### Mobil Görünüm

- Mobil cihazlarda butonlar tam genişlikte gösterilir
- Taksit seçenekleri popup'ı mobil ekranlara uyumlu tasarıma sahiptir
- Responsive tasarım sayesinde tüm ekran boyutlarında düzgün görüntülenir

## Desteklenen Kartlar

- Bonus
- World
- Maximum
- Axess
- Cardfinans
- Paraf
- Bankkart Combo

## Gereksinimler

- WordPress 5.0 veya üzeri
- WooCommerce 3.0 veya üzeri
- PHP 7.0 veya üzeri
- iyzico API hesabı

## Güvenlik

- API anahtarları güvenli bir şekilde saklanır
- XSS koruması için çıktılar escape edilir
- CSRF koruması için nonce kullanılır

## Lisans

Bu eklenti GNU lisansı altında dağıtılmaktadır.

## Destek

Sorun bildirimleri ve öneriler için GitHub üzerinden issue açabilirsiniz.

## Yazar

- Pushigo
- Website: https://pushigo.com
- GitHub: https://github.com/variants-maker/Woocommerce-iyzico-taksit-popup


