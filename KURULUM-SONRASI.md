# Kurulum Sonrası Yapılacaklar

`composer require dehasoft/panel-agent` sonrası bir siteye entegre ederken sırayla bunları yap.

## 1. Panelde token oluştur

Kontrol Masası'nda ilgili sitenin detay sayfasına git → **Webhook** bölümü → **Token Oluştur**.
Token bir kez gösterilir, hemen kopyala. Kaybedersen "Yenile" ile yenisini üret (eskisi anında geçersiz olur).

## 2. Ortam değişkenlerini ekle

`.env` dosyasına:

```env
PANEL_AGENT_URL=https://panel-adresin
PANEL_AGENT_TOKEN=az_once_kopyaladigin_token
```

`.env.example`'a da satırları (değersiz olarak) ekle ki sonraki kurulumlarda unutulmasın.

## 3. (Opsiyonel) Config'i yayınla

Varsayılan ayarlar genelde yeterli. Timeout gibi bir şeyi özelleştirmek istersen:

```bash
php artisan vendor:publish --tag=panel-agent-config
```

## 4. İletişim formunun handler'ına ekle

Formun `store`/`send` metoduna, e-postayı/DB kaydını yaptıktan **sonra** ekle — panel'e gönderim,
ziyaretçinin form deneyimini bloklamasın diye `afterResponse()` ile arkaya at:

```php
use Dehasoft\PanelAgent\Laravel\Facades\PanelAgent;

dispatch(fn () => PanelAgent::contactMessage([
    'name' => $validated['name'] ?? null,
    'email' => $validated['email'] ?? null,
    'subject' => $validated['subject'] ?? null,
    'message' => $validated['message'],
]))->afterResponse();
```

Queue worker'ın yoksa `afterResponse()` senkron ama response'tan sonra çalışır — ek kurulum gerekmez.

## 5. Test et

Siteden gerçek bir form gönder, panelde sitenin detay sayfasında **İletişim Mesajları**
altında görünmeli. Görünmüyorsa:

- Token doğru mu, panelde "Token ayarlı" yazıyor mu kontrol et
- `PANEL_AGENT_URL`'de sonda `/` olmamalı (varsa da sorun değil, otomatik temizlenir ama kontrol iyi al huy)
- Sunucudan panel adresine dışarı istek atılabiliyor mu (firewall/DNS)

## 6. Üretime alırken

- `PANEL_AGENT_TOKEN`'ı asla git'e commit etme, sadece `.env`/secrets manager'da tutulsun
- Token sızarsa panelden "Yenile" ile anında iptal et
- Birden fazla ortam varsa (staging/production) her biri için **ayrı site kaydı ve ayrı token** oluştur — aynı token'ı paylaşmayın, mesajlar hangi ortamdan geldiği karışır

## Sırada ne var

Paket, panelde yeni özellik (ör. uptime/health-check raporlama) eklendikçe yeni bir metotla
büyüyecek — mevcut `contactMessage()` çağrıların bozulmadan `composer update dehasoft/panel-agent`
yeterli olacak.
